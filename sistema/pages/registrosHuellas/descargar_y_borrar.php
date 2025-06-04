<?php
if (!isset($_GET['file'])) {
    die("Archivo no especificado.");
}

$archivoRelativo = $_GET['file'];
$archivo = __DIR__ . '/' . $archivoRelativo;

if (!file_exists($archivo)) {
    die("Archivo no encontrado.");
}

header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . basename($archivo) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($archivo));

// Limpia el buffer de salida
flush();
readfile($archivo);

// Elimina el archivo temporal
unlink($archivo);
exit;