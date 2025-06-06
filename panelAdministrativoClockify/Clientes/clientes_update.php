<?php
include_once(__DIR__ . '/../../../../app/controllers/config.php');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$id = $data['id'];
$nombre = $data['nombre'] ?? '';
$direccion = $data['direccion'] ?? '';
$moneda = $data['moneda'] ?? '';
$estado = $data['estado'] ?? 'activo';
$duracion_cliente = isset($data['duracion_cliente']) ? $data['duracion_cliente'] * 3600 : null;
$precio_cliente = isset($data['precio_cliente']) ? $data['precio_cliente'] : null;

// Validaciones
if (empty($nombre) || empty($direccion) || empty($moneda)) {
    echo json_encode(['success' => false, 'message' => 'Nombre, dirección y moneda son obligatorios']);
    exit;
}

if ($duracion_cliente === null || !is_numeric($duracion_cliente)) {
    echo json_encode(['success' => false, 'message' => 'Duración del cliente es inválida']);
    exit;
}

if ($precio_cliente === null || !is_numeric($precio_cliente) || $precio_cliente < 0) {
    echo json_encode(['success' => false, 'message' => 'Precio del cliente es inválido']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE clientes SET nombre_cliente = ?, direccion = ?, moneda = ?, estado = ?, duracion_cliente = ?, precio_cliente = ? WHERE id_cliente = ?");
    $stmt->execute([$nombre, $direccion, $moneda, $estado, $duracion_cliente, $precio_cliente, $id]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}