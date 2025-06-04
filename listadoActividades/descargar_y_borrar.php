<?php
// Desactivar errores visibles (importante para evitar que se rompan cabeceras)
ini_set('display_errors', 0);
error_reporting(0);

// Configuración
$tmpDir = __DIR__ . '/tmp';
$archivo = $_GET['file'] ?? '';

// Ruta del log
define('LOG_FILE', __DIR__ . '/log_descargaryborrar.txt');

// Función de logging
function log_custom($mensaje) {
    file_put_contents(LOG_FILE, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

log_custom("Inicio de descarga para archivo: $archivo");

// Validar nombre de archivo
if (!preg_match('/^[a-zA-Z0-9._-]+\.xlsx$/', $archivo)) {
    log_custom("Nombre de archivo no válido: $archivo");
    http_response_code(400);
    echo 'Nombre de archivo no válido.';
    exit;
}

$rutaArchivo = $tmpDir . '/' . $archivo;
log_custom("Ruta del archivo construida: $rutaArchivo");

// Verificar existencia del archivo
if (!file_exists($rutaArchivo)) {
    log_custom("Archivo no encontrado en el servidor.");
    http_response_code(404);
    echo 'Archivo no encontrado.';
    exit;
}

log_custom("Archivo encontrado. Preparando para enviar.");

// Enviar encabezados para descarga
header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . basename($archivo) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($rutaArchivo));

log_custom("Cabeceras enviadas correctamente.");

// Limpiar buffers de salida
ob_clean();
flush();

// Enviar archivo
$result = readfile($rutaArchivo);
if ($result === false) {
    log_custom("Error al leer el archivo para descarga.");
    exit;
}
log_custom("Archivo enviado correctamente.");

// Borrar archivo del servidor
if (unlink($rutaArchivo)) {
    log_custom("Archivo eliminado del servidor.");
} else {
    log_custom("Error al eliminar el archivo del servidor.");
}

exit;
?>