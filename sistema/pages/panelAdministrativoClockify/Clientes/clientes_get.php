<?php
include_once(__DIR__ . '/../../../../app/controllers/config.php');
header('Content-Type: application/json');

try {
    header('Content-Type: application/json');
    $stmt = $pdo->query("SELECT id_cliente, nombre_cliente, direccion, moneda, estado, fyh_creacion FROM clientes ORDER BY nombre_cliente  ASC");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($clientes);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener clientes']);
}

?>