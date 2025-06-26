<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php');



$items = $pdo
  ->query("SELECT 
             id,
             titulo,
             descripcion,
             DATE_FORMAT(fecha_inicio, '%Y-%m-%d') AS fecha_inicio,
             DATE_FORMAT(fecha_fin,   '%Y-%m-%d') AS fecha_fin,
             imagen_url,
             estado
           FROM tb_carrusel
           ORDER BY orden ASC")
  ->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($items);

?>