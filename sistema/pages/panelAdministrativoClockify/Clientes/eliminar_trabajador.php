<?php
header('Content-Type: application/json');
include_once(__DIR__ . '/../../../../app/controllers/config.php');
$id_cliente = $_POST['id_cliente'];
$id_trabajador = $_POST['id_trabajador'];

$stmt = $pdo->prepare("DELETE FROM cliente_trabajador WHERE id_cliente = ? AND id_trabajador = ?");
echo json_encode(['success' => $stmt->execute([$id_cliente, $id_trabajador])]);
?>