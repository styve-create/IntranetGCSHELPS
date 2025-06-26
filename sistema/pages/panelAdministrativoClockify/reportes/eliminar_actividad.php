<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include_once(__DIR__ . '/../../../../app/controllers/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

// 2) Leer el JSON de entrada
$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? intval($input['id']) : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
    exit;
}

try {
    // 3) Elimina la actividad por su ID
    $stmt = $pdo->prepare('DELETE FROM tb_actividades WHERE id = :id');
    $stmt->execute([':id' => $id]);

    echo json_encode(['success' => true]);
} catch (\PDOException $e) {
    error_log("Error borrando actividad {$id}: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error interno al eliminar la actividad.'
    ]);
}