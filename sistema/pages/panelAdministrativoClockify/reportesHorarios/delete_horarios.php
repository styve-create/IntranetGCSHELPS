<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../../app/controllers/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

// 1) Leer y decodificar el body JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!isset($data['ids']) || !is_array($data['ids']) || count($data['ids']) === 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Se requiere un array non‐empty de ids'
    ]);
    exit;
}

// 2) Sanitizar y construir placeholders dinámicos
$ids = array_values(array_filter($data['ids'], function($v){
    return is_numeric($v);
}));

if (count($ids) === 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'No hay IDs válidos para eliminar'
    ]);
    exit;
}

// crear lista de marcadores :id0, :id1, ...
$placeholders = [];
$params = [];
foreach ($ids as $i => $id) {
    $ph = ":id{$i}";
    $placeholders[] = $ph;
    $params[$ph] = (int)$id;
}

$sql = "DELETE FROM tb_horario WHERE id IN (" . implode(',', $placeholders) . ")";

try {
    // 3) Ejecutar DELETE
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode([
        'success'    => true,
        'deleted'    => $stmt->rowCount()
    ]);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar horarios: ' . $e->getMessage()
    ]);
}
exit;
?>