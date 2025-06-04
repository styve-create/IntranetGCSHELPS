<?php
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../librerias/vendor/autoload.php');

use PhpOffice\PhpWord\TemplateProcessor;

if (class_exists(TemplateProcessor::class)) {
    echo "✅ TemplateProcessor está disponible.";
} else {
    echo "❌ Error: TemplateProcessor no está disponible.";
}
include_once(__DIR__ . '/../../../app/controllers/config.php');

$log_index1 = __DIR__ . "/log_index1.txt";
function log_index1($mensaje) {
    global $log_index1;
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, $log_index1);
}

log_index1(">>> INICIO del script certificados");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    log_index1("➡ Se recibió una petición POST.");

    $documento = $_POST['documento'] ?? '';
    $certificados = $_POST['certificados'] ?? [];

    log_index1("📄 Documento recibido: $documento");
    log_index1("📁 Certificados seleccionados: " . json_encode($certificados));

    if (empty($certificados)) {
        log_index1("⚠ No se seleccionaron certificados. Finalizando.");
        die("No seleccionaste certificados.");
    }

    // Función para convertir números a letras
    function convertirNumeroALetras($numero) {
        $fmt = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);
        return $fmt->format($numero);
    }

    setlocale(LC_TIME, 'es_ES.UTF-8');

    try {
        $sql = "SELECT * FROM trabajadores WHERE numero_documento = :documento";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':documento', $documento);
        $stmt->execute();
        $trabajador = $stmt->fetch(PDO::FETCH_ASSOC);
        log_index1("✅ Consulta ejecutada correctamente para documento: $documento");
    } catch (Exception $e) {
        log_index1("❌ Error en consulta de trabajador: " . $e->getMessage());
        die("Error consultando trabajador.");
    }

    if (!$trabajador) {
        log_index1("❌ Trabajador no encontrado con documento: $documento");
        die("Trabajador no encontrado.");
    }

    log_index1("👤 Datos del trabajador: " . json_encode($trabajador));

    $tempDir = __DIR__ . '/tmp_' . uniqid();
    if (!mkdir($tempDir)) {
        log_index1("❌ Error creando directorio temporal: $tempDir");
        die("No se pudo crear directorio temporal.");
    }
    log_index1("📂 Directorio temporal creado: $tempDir");

    foreach ($certificados as $archivo) {
        $basename = basename($archivo);
        $ruta = __DIR__ . '/modelos_certificados/' . $basename;
        log_index1("🔧 Procesando archivo: $archivo (ruta: $ruta)");

        if (!file_exists($ruta)) {
            log_index1("❌ Archivo no encontrado: $ruta");
            continue;
        }

        $esDocx = substr($archivo, -5) === '.docx';
        $esPdf = substr($archivo, -4) === '.pdf';

        if ($esDocx) {
             try {
                    log_index1("🔄 Iniciando TemplateProcessor con: $ruta");
                    $tpl = new TemplateProcessor($ruta);
                
                    $fechaActual = new DateTime();
                    $salario = floatval($trabajador['salario_basico'] ?? 0);
                    log_index1("💰 Salario convertido a float: $salario");
                
                    log_index1("📝 Asignando valores al template...");
                    $tpl->setValue('nombre', $trabajador['nombre_completo']);
                    $tpl->setValue('documento', $trabajador['numero_documento']);
                    $tpl->setValue('expDocumento', $trabajador['lugar_expedicion'] ?? '');
                    $tpl->setValue('fechaIngresoGscTexto', date('d \d\e F \d\e Y', strtotime($trabajador['fecha_ingreso_gcs'])));
                    $tpl->setValue('fechaRetiroGscTexto', $trabajador['fecha_retiro_gcs'] ? date('d \d\e F \d\e Y', strtotime($trabajador['fecha_retiro_gcs'])) : '');
                    $tpl->setValue('cargo', $trabajador['cargo_certificado']?? '');
                    $tpl->setValue('salarioPesos', number_format($salario, 0, ',', '.'));
                    $tpl->setValue('salarioPesosLetras', convertirNumeroALetras($salario));
                    $tpl->setValue('diaSolicitudNumeros', $fechaActual->format('d'));
                    $tpl->setValue('diaSolicitudLetras', convertirNumeroALetras($fechaActual->format('d')));
                    $tpl->setValue('mesSolicitudLetras', strftime('%B', $fechaActual->getTimestamp()));
                    $tpl->setValue('mesSolicitudNumeros', $fechaActual->format('m'));
                    $tpl->setValue('añoSolicitudNumeros', $fechaActual->format('Y'));
                    $tpl->setValue('añoSolicitudLetras', convertirNumeroALetras($fechaActual->format('Y')));
                
                    $docxOutput = "$tempDir/" . pathinfo($archivo, PATHINFO_FILENAME) . '.docx';
                    log_index1("💾 Guardando archivo DOCX en: $docxOutput");
                    $tpl->saveAs($docxOutput);
                    log_index1("✅ Archivo DOCX generado: $docxOutput");
                } catch (Exception $e) {
                    log_index1("❌ Error al procesar plantilla DOCX: " . $e->getMessage());
                }
        } elseif ($esPdf) {
            $destino = "$tempDir/$basename";
            if (copy($ruta, $destino)) {
                log_index1("✅ Archivo PDF copiado: $destino");
            } else {
                log_index1("❌ Fallo al copiar PDF: $ruta");
            }
        } else {
            log_index1("⚠ Tipo de archivo no soportado: $archivo");
        }
    }

    // Crear ZIP
    $zipFile = "$tempDir.zip";
    $zip = new ZipArchive();

    if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
        log_index1("❌ No se pudo crear el ZIP: $zipFile");
        die("No se pudo crear el archivo ZIP.");
    }

    foreach (glob("$tempDir/*") as $file) {
        $zip->addFile($file, basename($file));
        log_index1("➕ Añadido al ZIP: $file");
    }

    $zip->close();
    log_index1("✅ ZIP creado correctamente: $zipFile");

    if (file_exists($zipFile)) {
        log_index1("⬇ Iniciando descarga del ZIP...");
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename="certificados_' . $trabajador['numero_documento'] . '.zip"');
        header('Content-Length: ' . filesize($zipFile));
        ob_clean();
        flush();
        readfile($zipFile);
        log_index1("✅ ZIP enviado al navegador.");
    } else {
        log_index1("❌ ZIP no encontrado en ruta esperada: $zipFile");
        die("Archivo ZIP no encontrado.");
    }

    // Limpieza
    foreach (glob("$tempDir/*") as $file) {
        unlink($file);
        log_index1("🧹 Archivo temporal eliminado: $file");
    }
    rmdir($tempDir);
    log_index1("🧹 Directorio temporal eliminado: $tempDir");

    unlink($zipFile);
    log_index1("🧹 ZIP eliminado tras la descarga: $zipFile");

    log_index1("✅ FIN del script sin errores.");
    exit;
} else {
    log_index1("❌ No se recibió una petición POST.");
}
?>