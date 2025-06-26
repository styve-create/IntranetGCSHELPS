<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluye la conexión PDO
include_once(__DIR__ . '/../../../../app/controllers/config.php');

// Establece encabezado JSON
header('Content-Type: application/json');

try {
    // Lee el JSON de entrada
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['id']) || empty($data['id'])) {
        throw new Exception('ID no proporcionado');
    }

    $id = $data['id'];

    // Verifica si el registro existe
    $stmt = $pdo->prepare("SELECT * FROM tb_horario WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Registro no encontrado');
    }

    // Ejecuta eliminación
    $deleteStmt = $pdo->prepare("DELETE FROM tb_horario WHERE id = ?");
    $deleteStmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Registro eliminado correctamente'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
exit;
?>