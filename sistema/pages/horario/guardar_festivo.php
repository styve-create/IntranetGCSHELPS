<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$fecha = $data['fecha'] ?? null;

if (!$fecha) {
    echo json_encode(['status' => 'error', 'msg' => 'Fecha no válida']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT IGNORE INTO tb_festivos (fecha) VALUES (:fecha)");
    $stmt->execute(['fecha' => $fecha]);
    echo json_encode(['status' => 'ok']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}