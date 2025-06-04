<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
date_default_timezone_set('America/Bogota');

// Fecha actual
$hoy = date('Y-m-d');

// Seleccionar anuncios vencidos
$stmt = $pdo->prepare("SELECT id, imagen_url FROM tb_carrusel WHERE fecha_fin < :hoy");
$stmt->execute([':hoy' => $hoy]);
$expirados = $stmt->fetchAll();

foreach ($expirados as $item) {
$raiz_sitio = realpath(__DIR__ . '/../../../'); // Esto apunta al root de tu proyecto (public_html/intranet)
$ruta_fisica = $raiz_sitio . $item['imagen_url']; // Une raíz + ruta relativa

if (!empty($item['imagen_url']) && file_exists($ruta_fisica)) {
    unlink($ruta_fisica);
}

    // Eliminar registro
    $del = $pdo->prepare("DELETE FROM tb_carrusel WHERE id = :id");
    $del->execute([':id' => $item['id']]);
}

?>