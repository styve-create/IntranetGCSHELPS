<?php
header('Content-Type: application/json');
include_once(__DIR__ . '/../../../app/controllers/config.php');

$sql = "SELECT * FROM tb_carrusel WHERE fecha_fin >= CURDATE() ORDER BY orden ASC";
$items = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($items);
exit;
?>