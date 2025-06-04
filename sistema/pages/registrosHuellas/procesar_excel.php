<?php
ob_start();
$log_excel = __DIR__ . '/log_procesar_excel.txt';

function log_excel($mensaje) {
    global $log_excel;
    file_put_contents($log_excel, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

log_excel("Archivo procesar_excel.php llamado");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    log_excel("POST recibido");
    log_excel("Contenido POST: " . print_r($_POST, true));
    log_excel("Archivos: " . print_r($_FILES, true));
} else {
    log_excel("NO se recibió POST");
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../librerias/vendor/autoload.php';
include_once(__DIR__ . '/../../../app/controllers/config.php');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

header('Content-Type: application/json');

define('LOG_FILE', __DIR__ . '/log_procesamiento.txt');

function log_custom($mensaje) {
    file_put_contents(LOG_FILE, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_excel'])) {
    $inputFileName = $_FILES['archivo_excel']['tmp_name'];

    try {
        log_custom("Inicio del procesamiento del archivo.");
        $reader = IOFactory::createReaderForFile($inputFileName);
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $registros = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            if (count($row) < 4) {
                log_custom("Fila $i: Tiene menos de 4 columnas. Datos: " . json_encode($row));
                continue;
            }

            $nombre = isset($row[1]) && trim($row[1]) !== '' ? trim($row[1]) : null;
            $fechaOriginal = isset($row[2]) && trim($row[2]) !== '' ? trim($row[2]) : null;
            $tipo = isset($row[3]) && trim($row[3]) !== '' ? trim($row[3]) : null;

            if (empty($nombre) || empty($fechaOriginal) || empty($tipo)) {
                log_custom("Fila $i: Datos incompletos - Nombre: '$nombre', Fecha: '$fechaOriginal', Tipo: '$tipo'");
                continue;
            }

            $fecha = convertirA24Horas($fechaOriginal);
            if (!$fecha) {
                log_custom("Fila $i: Fecha no válida o vacía -> '$fechaOriginal'");
                continue;
            }

            $fechaObj = DateTime::createFromFormat('d/m/Y H:i:s', $fecha);
            if (!$fechaObj) {
                log_custom("Fila $i: Error al convertir fecha -> $fecha");
                continue;
            }

            $fechaStr = $fechaObj->format('d/m/Y');
            $hora = $fechaObj->format('H:i:s');

            $key = $nombre . '_' . $fechaStr;
            if (!isset($registros[$key])) {
                $registros[$key] = [
                    'nombre' => $nombre,
                    'fecha' => $fechaStr,
                    'entradas' => [],
                    'salidas' => [],
                ];
            }

            if (strtolower($tipo) === 'entrada') {
                $registros[$key]['entradas'][] = $hora;
            } elseif (strtolower($tipo) === 'salida') {
                $registros[$key]['salidas'][] = $hora;
            }
        }

        $output = new Spreadsheet();
        $outputSheet = $output->getActiveSheet();
        $outputSheet->fromArray([
            ['Nombre', 'Fecha', 'Horas Trabajadas', 'Rango Horas Trabajadas',
            'Break 1 Tiempo', 'Break 1 Rango', 'Break 2 Tiempo', 'Break 2 Rango',
            'Break 3 Tiempo', 'Break 3 Rango', 'Estado']
        ], null, 'B1');

        $fila = 2;
        foreach ($registros as $persona) {
            $entrada = obtenerPrimera($persona['entradas']);
            $salida = obtenerUltima($persona['salidas']);

            if (!$entrada || !$salida) {
                log_custom("{$persona['nombre']} ({$persona['fecha']}): Faltan entrada o salida.");
                escribirFilaVacia($outputSheet, $fila, $persona['nombre'], $persona['fecha']);
                $fila++;
                continue;
            }

            $tiempoTrabajado = calcularHoras($entrada, $salida);
            if ($tiempoTrabajado < 0 || $tiempoTrabajado > 24) {
                log_custom("{$persona['nombre']} ({$persona['fecha']}): Tiempo inválido: $tiempoTrabajado");
                escribirFilaVacia($outputSheet, $fila, $persona['nombre'], $persona['fecha']);
                $fila++;
                continue;
            }

            $breaks = obtenerBreaks($persona['entradas'], $persona['salidas'], $entrada, $salida);
            $estado = $tiempoTrabajado > 0 ? 'Ok' : 'No';

            $outputSheet->fromArray([
                $persona['nombre'],
                $persona['fecha'],
                number_format($tiempoTrabajado, 2),
                "$entrada - $salida",
                ...$breaks,
                $estado
            ], null, "B$fila");
            $fila++;
        }

        $filename = 'tmp/procesado_' . time() . '_' . rand(1000, 9999) . '.xlsx';
        $filepath = __DIR__ . '/' . $filename;

        try {
            $writer = new Xlsx($output);
            $writer->save($filepath);
            log_custom("Archivo generado correctamente: $filename");
            log_custom("Envío de respuesta exitosa con URL: " . $URL . '/sistema/pages/registrosHuellas/descargar_y_borrar.php?file=' . $filename);
            echo json_encode([
                
                'success' => true,
                 'url' => $URL . '/sistema/pages/registrosHuellas/descargar_y_borrar.php?file=' . $filename
            ]);
            exit;

        } catch (Exception $e) {
            log_custom("Error al guardar archivo Excel: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al guardar el archivo procesado.']);
            exit;
        }

    } catch (Exception $e) {
        log_custom("Error durante el procesamiento: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error al procesar el archivo.']);
        exit;
    }
}

// -------------------- FUNCIONES AUXILIARES --------------------

function convertirA24Horas($fechaHora) {
    if (empty($fechaHora) || !is_string($fechaHora)) return null;

    $fechaHora = trim($fechaHora);
    $fechaHora = preg_replace('/[\x{00A0}\x{200B}\x{202F}]/u', ' ', $fechaHora);
    $fechaHora = preg_replace('/\s+/', ' ', $fechaHora);
    $fechaHora = preg_replace_callback('/([ap])\.*\s*m\.?/iu', function ($match) {
        return strtoupper($match[1]) . 'M';
    }, $fechaHora);

    $date = DateTime::createFromFormat('d/m/Y h:i:s A', $fechaHora);
    if (!$date) {
        $date = DateTime::createFromFormat('d/m/Y H:i:s', $fechaHora);
    }

    return $date ? $date->format('d/m/Y H:i:s') : null;
}

function obtenerPrimera($horas) {
    sort($horas);
    return $horas[0] ?? null;
}

function obtenerUltima($horas) {
    sort($horas);
    return end($horas) ?: null;
}

function calcularHoras($entrada, $salida) {
    $start = DateTime::createFromFormat('H:i:s', $entrada);
    $end = DateTime::createFromFormat('H:i:s', $salida);
    if (!$start || !$end) return -1;
    $diff = $end->getTimestamp() - $start->getTimestamp();
    return round($diff / 3600, 2);
}

function obtenerBreaks($entradas, $salidas, $entradaPrincipal, $salidaPrincipal) {
    $breaks = [];
    for ($i = 0; $i < count($salidas) - 1 && count($breaks) < 6; $i++) {
        // Validar que $entradas[$i + 1] existe
        if (!isset($entradas[$i + 1])) {
            continue;
        }

        $s = DateTime::createFromFormat('H:i:s', $salidas[$i]);
        $e = DateTime::createFromFormat('H:i:s', $entradas[$i + 1]);

        if (!$s || !$e) continue;

        // Validar que los breaks estén dentro del rango de entrada y salida principal
        if ($s->format('H:i:s') > $entradaPrincipal && $e->format('H:i:s') < $salidaPrincipal) {
            $mins = round(($e->getTimestamp() - $s->getTimestamp()) / 60, 2);
            $breaks[] = "$mins minutos";
            $breaks[] = $s->format('H:i:s') . ' - ' . $e->format('H:i:s');
        }
    }

    // Rellenar hasta 6 posiciones (3 breaks * 2 columnas)
    while (count($breaks) < 6) $breaks[] = '';

    return $breaks;
}

function escribirFilaVacia($sheet, $fila, $nombre, $fecha) {
    $sheet->fromArray([$nombre, $fecha, 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No'], null, "B$fila");
}

register_shutdown_function(function () {
    $contenido = ob_get_contents();
    if ($contenido) {
        file_put_contents(__DIR__ . '/debug_output.txt', $contenido);
    }
});
?>