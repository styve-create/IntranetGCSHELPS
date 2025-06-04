<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../librerias/vendor/autoload.php';
include_once(__DIR__ . '/../app/controllers/config.php');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// Ruta del log
define('LOG_FILE', __DIR__ . '/log_procesamiento.txt');

// Función de logging
function log_custom($mensaje) {
    file_put_contents(LOG_FILE, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_excel'])) {
    $archivoTemporal = $_FILES['archivo_excel']['tmp_name'];

        try {
            log_custom("Procesando archivo: $archivoTemporal");
    
            // Intentando leer el archivo Excel
            try {
                $reader = IOFactory::createReaderForFile($archivoTemporal);
                $spreadsheet = $reader->load($archivoTemporal);
            } catch (Exception $e) {
                log_custom("Error al cargar el archivo Excel: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error al procesar el archivo Excel.']);
                exit;
            }
    
            $sheet = $spreadsheet->getActiveSheet();
            $filas = $sheet->toArray();
    
            log_custom("Total de filas en el archivo: " . count($filas));
    
            $datos = [];
    
            for ($i = 1; $i < count($filas); $i++) {
                $fila = $filas[$i];
    
                // Extracción de los valores de la fila
                $fechaRaw = $fila[1] ?? null;
                $horaRaw = $fila[2] ?? null;
                $nombre = trim($fila[3] ?? '');
                $cargo = trim($fila[4] ?? '');
    
               
    
                if (!$fechaRaw || !$horaRaw || !$nombre || !$cargo) {
                    log_custom("Fila $i ignorada: Datos incompletos.");
                    continue;
                }
    
                // Convertir fecha
                try {
                                // Convertir fecha
                        if (is_numeric($fechaRaw)) {
                            $fechaObj = Date::excelToDateTimeObject($fechaRaw);
                            $fechaStr = $fechaObj->format('Y-m-d'); // mantener formato estandarizado si es número
                        } else {
                            $fechaStr = trim($fechaRaw); // mantener el formato textual original
                        }
                        
                        // Convertir hora
                        if (is_numeric($horaRaw)) {
                            $horaObj = Date::excelToDateTimeObject($horaRaw);
                            $horaStr = $horaObj->format('H:i:s');
                        } else {
                            $horaObj = DateTime::createFromFormat('H:i:s', trim($horaRaw));
                            if (!$horaObj) {
                                $horaObj = DateTime::createFromFormat('H:i', trim($horaRaw));
                            }
                        
                            if (!$horaObj) {
                                log_custom("Fila $i ignorada: Hora inválida '$horaRaw'.");
                                continue;
                            }
                        
                            $horaStr = $horaObj->format('H:i:s');
                        }
    
                    if (!isset($datos[$nombre])) {
                        $datos[$nombre] = [
                            'cargo' => $cargo,
                            'fechas' => []
                        ];
                    }
    
                    if (!isset($datos[$nombre]['fechas'][$fechaStr]) || $horaStr < $datos[$nombre]['fechas'][$fechaStr]) {
                        $datos[$nombre]['fechas'][$fechaStr] = $horaStr;
                    }
    
                } catch (Exception $e) {
                    log_custom("Error al procesar fecha u hora en la fila $i: " . $e->getMessage());
                    continue; // Si hay un error con una fila, seguimos con la siguiente
                }
            }
    
            // Crear Excel de salida
            try {
                log_custom("Generando cuerpo del Excel");
    
                $salida = new Spreadsheet();
                $hoja = $salida->getActiveSheet();
    
                $fechasUnicas = [];
                foreach ($datos as $persona) {
                    foreach ($persona['fechas'] as $fecha => $_) {
                        $fechasUnicas[$fecha] = true;
                    }
                }
                ksort($fechasUnicas); // ordenar por fecha
                $fechasUnicas = array_keys($fechasUnicas);
    
                // Cabecera: Cargo, Nombre, y luego fechas
                $cabecera = array_merge(['Cargo', 'Nombre'], $fechasUnicas);
                $hoja->fromArray($cabecera, NULL, 'A1');
    
             // Cuerpo: una fila por persona
                    $filaExcel = 2;
                    foreach ($datos as $nombre => $persona) {
                        log_custom("Generando fila para: $nombre (Fila Excel: $filaExcel)");
                    
                        try {
                            // Columna 1: Cargo
                            $columnaExcel = 1;
                            $colLetra = Coordinate::stringFromColumnIndex($columnaExcel);
                            $hoja->setCellValue($colLetra . $filaExcel, $persona['cargo']);
                            log_custom("Escribiendo cargo '{$persona['cargo']}' en fila $filaExcel, columna $columnaExcel");
                    
                            // Columna 2: Nombre
                            $columnaExcel = 2;
                            $colLetra = Coordinate::stringFromColumnIndex($columnaExcel);
                            $hoja->setCellValue($colLetra . $filaExcel, $nombre);
                            
                            log_custom("Escribiendo nombre '$nombre' en fila $filaExcel, columna $columnaExcel");
                    
                            // A partir de la columna 3: primer registro del día por cada fecha
                            $columnaExcel = 3;
                            foreach ($fechasUnicas as $fecha) {
                                $registro = $persona['fechas'][$fecha] ?? '';
                                $colLetra = Coordinate::stringFromColumnIndex($columnaExcel);
                                $hoja->setCellValue($colLetra . $filaExcel, $registro);
                                log_custom("Escribiendo registro '$registro' en fila $filaExcel, columna $columnaExcel (fecha $fecha)");
                                $columnaExcel++;
                            }
                    
                        } catch (Throwable $e) {
                            log_custom("❌ Error al escribir datos de $nombre en fila $filaExcel: " . $e->getMessage());
                        }
                    
                        $filaExcel++;
                    }
    
                // Antes de guardar el archivo, logueamos que estamos en esa parte.
                log_custom("Listo para guardar el archivo Excel.");
    
                // Guardar archivo de salida
                $nombreArchivo = 'procesado_' . time() . '_' . rand(1000, 9999) . '.xlsx';
                $rutaArchivo = __DIR__ . '/tmp/' . $nombreArchivo;
    
                log_custom("Guardando archivo Excel en: $rutaArchivo");
    
                if (!is_writable(__DIR__ . '/tmp/')) {
                    log_custom("Error: No se tiene permiso para escribir en la carpeta temporal.");
                    echo json_encode(['success' => false, 'message' => 'Error: No se tiene permiso para escribir en la carpeta temporal.']);
                    exit;
                }
                try {
                    $writer = new Xlsx($salida);
                    $writer->save($rutaArchivo);
                    log_custom("Archivo generado correctamente: $nombreArchivo");
                } catch (Exception $e) {
                    log_custom("Error al intentar guardar el archivo Excel: " . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'Error al generar el archivo Excel.']);
                    exit;
                }
    
            } catch (Exception $e) {
                log_custom("Error al generar el archivo Excel: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error al generar el archivo Excel.']);
                exit;
            }
    
            // Responder con la URL del archivo generado
            $URL = "https://gcshelps.com/intranet"; 
            log_custom("Generando URL de descarga: " . $URL . '/listadoActividades/descargar_y_borrar.php?file=' . $nombreArchivo);
    
        
                echo json_encode([
                    'success' => true,
                    'url' => $URL . '/listadoActividades/descargar_y_borrar.php?file=' . $nombreArchivo
                ]);
                exit;
    
        } catch (Exception $e) {
            log_custom("Error general: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al procesar el archivo.']);
            exit;
        }
} else {
    log_custom("No se recibió archivo válido por POST.");
    echo json_encode(['success' => false, 'message' => 'No se recibió archivo Excel.']);
    exit;
}
?>