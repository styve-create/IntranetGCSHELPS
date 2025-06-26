<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../../app/controllers/config.php');

header('Content-Type: application/json');

$stmt = $pdo->prepare("SELECT id_cliente, nombre_cliente FROM clientes WHERE estado = 'activo' ORDER BY nombre_cliente ASC");
$stmt->execute();
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($clientes);