<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');

$jefe_id = intval($_POST['jefe_id'] ?? 0);
$campana_id = intval($_POST['campana_id'] ?? 0);

if ($jefe_id <= 0 || $campana_id <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'Datos incompletos para eliminar.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Eliminar la jerarquía donde este jefe esté asignado
    $stmt1 = $pdo->prepare("DELETE FROM tb_jerarquia_trabajadores WHERE id_jefe = :jefe_id AND id_campana = :campana_id");
    $stmt1->execute(['jefe_id' => $jefe_id, 'campana_id' => $campana_id]);

    // 2. Eliminar al jefe como trabajador en esa campaña
    $stmt2 = $pdo->prepare("DELETE FROM trabajadores_campanas WHERE trabajador_id = :jefe_id AND campana_id = :campana_id");
    $stmt2->execute(['jefe_id' => $jefe_id, 'campana_id' => $campana_id]);

    $pdo->commit();

    echo json_encode(['ok' => true, 'mensaje' => 'Jefe eliminado correctamente.']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['ok' => false, 'mensaje' => 'Error al eliminar: ' . $e->getMessage()]);
}
?>