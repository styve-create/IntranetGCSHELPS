<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include_once(__DIR__ . '/../../../app/controllers/config.php');

$campana_id = intval($_POST['campana_id'] ?? 0);

if ($campana_id <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'ID de campaña no válido.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Eliminar jerarquía
    $stmt = $pdo->prepare("DELETE FROM tb_jerarquia_trabajadores WHERE id_campana = ?");
    $stmt->execute([$campana_id]);

    // Eliminar relación trabajadores-campaña
    $stmt = $pdo->prepare("DELETE FROM trabajadores_campanas WHERE campana_id = ?");
    $stmt->execute([$campana_id]);

    // Eliminar campaña
    $stmt = $pdo->prepare("DELETE FROM tb_campanas WHERE id = ?");
    $stmt->execute([$campana_id]);

    $pdo->commit();

    echo json_encode(['ok' => true, 'mensaje' => 'Campaña eliminada correctamente.']);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensaje' => 'Error al eliminar campaña: ' . $e->getMessage()]);
}

?>