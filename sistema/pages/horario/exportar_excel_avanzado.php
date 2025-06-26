<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../librerias/vendor/autoload.php';
include_once(__DIR__ . '/../../../app/controllers/config.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function calcularHoras($inicio, $fin, $inicioBreak, $finBreak) {
    if (!$inicio || !$fin) return 0;
    $start = new DateTime($inicio);
    $end = new DateTime($fin);
    $total = $start->diff($end)->h + $start->diff($end)->i / 60;

    if ($inicioBreak && $finBreak) {
        $b1 = new DateTime($inicioBreak);
        $b2 = new DateTime($finBreak);
        $break = $b1->diff($b2)->h + $b1->diff($b2)->i / 60;
        $total -= $break;
    }
    return max(0, $total);
}

function esFestivo($fecha, $festivos) {
    return in_array($fecha, $festivos);
}

function esNocturno($hora) {
    $h = (int) date("H", strtotime($hora));
    return ($h >= 21 || $h < 6);
}

// Entrada
$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

if (!$start || !$end) {
    http_response_code(400);
    echo "Fechas inválidas";
    exit;
}

// Festivos del rango
$stmtFestivos = $pdo->prepare("SELECT fecha FROM tb_festivos WHERE fecha BETWEEN :start AND :end");
$stmtFestivos->execute(['start' => $start, 'end' => $end]);
$festivos = array_column($stmtFestivos->fetchAll(PDO::FETCH_ASSOC), 'fecha');

// Horarios del rango
$stmt = $pdo->prepare("
  SELECT 
    th.*, 
    t.nombre_completo 
  FROM tb_horario th
  JOIN tb_usuarios u ON th.id_usuario = u.id_usuarios
  JOIN trabajadores t ON u.trabajador_id = t.id
  WHERE th.fecha BETWEEN :start AND :end
");
$stmt->execute(['start' => $start, 'end' => $end]);
$horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preparar Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->mergeCells('A1:A3')->setCellValue('A1', 'Agentes');
$sheet->mergeCells('B1:B3')->setCellValue('B1', 'Rango de fechas');
$dias = ['Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo'];
$col = 'C';
foreach ($dias as $d) {
    $sheet->mergeCells("{$col}2:" . chr(ord($col)+1) . "2")->setCellValue("{$col}2", $d);
    $sheet->setCellValue("{$col}3", "Diurno");
    $col++;
    $sheet->setCellValue("{$col}3", "Nocturno");
    $col++;
}
$sheet->setCellValue("Q2", "Total Diurno L-S")->setCellValue("Q3", '');
$sheet->setCellValue("R2", "Total Diurno Dom/Fest")->setCellValue("R3", '');
$sheet->setCellValue("S2", "Total Nocturno L-S")->setCellValue("S3", '');
$sheet->setCellValue("T2", "Total Nocturno Dom/Fest")->setCellValue("T3", '');
$sheet->setCellValue("U2", "Total Horas")->mergeCells("U2:U3");

// Llenar datos
// Paso 1: Agrupar por trabajador
$datosAgrupados = [];

foreach ($horarios as $h) {
    $idUsuario = $h['id_usuario'];
    
    $fecha = $h['fecha'];
    $diaNum = date('w', strtotime($fecha)); // 0=domingo, 1=lunes, ..., 6=sábado
    $esDomingo = $diaNum == 0;
    $esFestivo = esFestivo($fecha, $festivos);
    $esNocturna = esNocturno($h['hora_inicio_turno']);
    
    $horasTurno = calcularHoras(
        $h['hora_inicio_turno'],
        $h['hora_fin_turno'],
        $h['hora_inicio_break2'],
        $h['hora_fin_break2']
    );

    $horasExtra = 0;
    if ($h['hora_inicio_extra'] && $h['hora_fin_extra']) {
        $horasExtra = calcularHoras($h['hora_inicio_extra'], $h['hora_fin_extra'], null, null);
    }

  if (!isset($datosAgrupados[$idUsuario])) {
    $datosAgrupados[$idUsuario] = [
        'nombre' => $h['nombre_completo'],
        'rango' => [$fecha, $fecha],
        'diurno' => array_fill(0, 7, 0),
        'nocturno' => array_fill(0, 7, 0),
        'total_d_lunes_sabado' => 0,
        'total_d_domingo_festivo' => 0,
        'total_n_lunes_sabado' => 0,
        'total_n_domingo_festivo' => 0,
        'total_horas' => 0,
    ];
}

    // Actualiza rango de fechas
      $datosAgrupados[$idUsuario]['rango'][0] = min($datosAgrupados[$idUsuario]['rango'][0], $fecha);
    $datosAgrupados[$idUsuario]['rango'][1] = max($datosAgrupados[$idUsuario]['rango'][1], $fecha);

    if ($esNocturna) {
        $datosAgrupados[$idUsuario]['nocturno'][$diaNum] += $horasTurno;
        if ($esDomingo || $esFestivo) {
            $datosAgrupados[$idUsuario]['total_n_domingo_festivo'] += $horasTurno;
        } else {
            $datosAgrupados[$idUsuario]['total_n_lunes_sabado'] += $horasTurno;
        }
    } else {
        $datosAgrupados[$idUsuario]['diurno'][$diaNum] += $horasTurno;
        if ($esDomingo || $esFestivo) {
            $datosAgrupados[$idUsuario]['total_d_domingo_festivo'] += $horasTurno;
        } else {
            $datosAgrupados[$idUsuario]['total_d_lunes_sabado'] += $horasTurno;
        }
    }

    $datosAgrupados[$idUsuario]['total_horas'] += $horasTurno + $horasExtra;
}
$fila = 4;
foreach ($datosAgrupados as $id => $data) {
    $sheet->setCellValue("A$fila", $data['nombre']);
    $sheet->setCellValue("B$fila", $data['rango'][0] . ' a ' . $data['rango'][1]);

    $mapaColumnas = [
        0 => ['O', 'P'], // Domingo
        1 => ['C', 'D'], // Lunes
        2 => ['E', 'F'],
        3 => ['G', 'H'],
        4 => ['I', 'J'],
        5 => ['K', 'L'],
        6 => ['M', 'N'], // Sábado
    ];

    foreach ($mapaColumnas as $dia => [$colDiurno, $colNocturno]) {
        if ($data['diurno'][$dia] > 0) {
            $sheet->setCellValue("$colDiurno$fila", $data['diurno'][$dia]);
        }
        if ($data['nocturno'][$dia] > 0) {
            $sheet->setCellValue("$colNocturno$fila", $data['nocturno'][$dia]);
        }
    }

    $sheet->setCellValue("Q$fila", $data['total_d_lunes_sabado']);
    $sheet->setCellValue("R$fila", $data['total_d_domingo_festivo']);
    $sheet->setCellValue("S$fila", $data['total_n_lunes_sabado']);
    $sheet->setCellValue("T$fila", $data['total_n_domingo_festivo']);
    $sheet->setCellValue("U$fila", $data['total_horas']);

    $fila++;
}

// Salida
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_horarios.xlsx"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>