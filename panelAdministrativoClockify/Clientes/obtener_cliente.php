<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include_once(__DIR__ . '/../../../../app/controllers/config.php');

if (!isset($_GET['id_cliente'])) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

$id = intval($_GET['id_cliente']);
$stmt = $pdo->prepare("SELECT id_cliente, nombre_cliente, direccion, moneda, estado, duracion_cliente, precio_cliente FROM clientes WHERE id_cliente = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cliente) {
    // Convertir duracion_cliente de segundos a horas
    if (isset($cliente['duracion_cliente'])) {
        $cliente['duracion_cliente'] = $cliente['duracion_cliente'] / 3600;  // Convertir segundos a horas
    }
    
    echo json_encode($cliente);
} else {
    echo json_encode(['error' => 'Cliente no encontrado']);
}
?>