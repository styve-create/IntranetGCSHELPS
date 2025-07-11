<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
header('Content-Type: application/json');

$sql = "SELECT * FROM formularios_asignacion ORDER BY fecha_registro DESC";
$sentencia = $pdo->query($sql);
$formularios = $sentencia->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($formularios);
?>