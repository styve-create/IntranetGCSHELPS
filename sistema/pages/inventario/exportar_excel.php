<?php

require_once(__DIR__ . '/../../../app/controllers/config.php'); 
require_once(__DIR__ . '/../../../librerias/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';

if (!$start || !$end) {
    exit('Fechas inválidas.');
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT numero_formulario, fecha_registro, nombre, documento, email, equipos, seriales, estado_trabajador, comentarios_trabajador
        FROM formularios_asignacion
        WHERE DATE(fecha_registro) BETWEEN ? AND ?
        ORDER BY fecha_registro DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$start, $end]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Obtener todos los nombres únicos base de equipos (sin " cambio")
$tiposEquiposUnicos = [];

foreach ($data as $row) {
    $equipos = json_decode($row['equipos'], true) ?? [];
    foreach ($equipos as $e) {
        $base = str_replace(' cambio', '', strtolower(trim($e)));
        $tiposEquiposUnicos[$base] = true;
    }
}

$tiposEquiposUnicos = array_keys($tiposEquiposUnicos);

// Encabezados
$headers = ['N° Formulario', 'Fecha Registro', 'Nombre', 'Documento', 'Email'];
foreach ($tiposEquiposUnicos as $tipo) {
    $headers[] = ucfirst($tipo);
}
$headers[] = 'Estado Trabajador';
$headers[] = 'Comentarios';

// Preparar filas
$rows = [];
foreach ($data as $row) {
    $equipos = json_decode($row['equipos'], true);
    $seriales = json_decode($row['seriales'], true);

    if (!is_array($equipos)) $equipos = [];
    if (!is_array($seriales)) $seriales = [];

    $fila = [
        $row['numero_formulario'],
        $row['fecha_registro'],
        $row['nombre'],
        $row['documento'],
        $row['email']
    ];

    // Añadir equipos y seriales
       // Agrupar equipos por tipo base
    $agrupados = [];

    foreach ($equipos as $e) {
        $base = str_replace(' cambio', '', strtolower(trim($e)));
        $serial = $seriales[$e] ?? null;
        $etiqueta = $serial ? "$e: $serial" : $e;

        if (!isset($agrupados[$base])) {
            $agrupados[$base] = [$etiqueta];
        } else {
            $agrupados[$base][] = $etiqueta;
        }
    }

    // Insertar los equipos agrupados en columnas fijas
    foreach ($tiposEquiposUnicos as $tipo) {
        if (isset($agrupados[$tipo])) {
            $fila[] = implode(' / ', $agrupados[$tipo]);
        } else {
            $fila[] = '-';
        }
    }

    // Resto de campos
    $fila[] = $row['estado_trabajador'];
    $fila[] = $row['comentarios_trabajador'];

    $rows[] = $fila;
}

// Crear Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->fromArray($headers, NULL, 'A1');
$sheet->fromArray($rows, NULL, 'A2');

// Descargar
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="formularios_filtrados.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>