<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id_rol, rol FROM tb_roles ORDER BY rol ASC");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'roles' => $roles]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al obtener roles']);
}