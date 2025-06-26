<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');

header('Content-Type: application/json');

$campanas = $pdo->query("SELECT id, nombre FROM tb_campanas WHERE estado = 'Activa'")->fetchAll(PDO::FETCH_ASSOC);
$cargos = $pdo->query("SELECT id, nombre FROM cargos")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "campanas" => $campanas,
    "cargos" => $cargos
]);
exit;
?>