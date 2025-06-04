<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../../app/controllers/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['cobrado'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$id = (int) $data['id'];
$cobrado = $data['cobrado'] ? 1 : 0;

try {
    $stmt = $pdo->prepare("UPDATE tb_actividades SET cobrado = :cobrado WHERE id = :id");
    $stmt->execute([
        ':cobrado' => $cobrado,
        ':id' => $id
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}