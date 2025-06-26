<?php 
header('Content-Type: application/json');
include_once(__DIR__ . '/../../../../app/controllers/config.php');
$data = json_decode(file_get_contents("php://input"), true);

$id_cliente = $data['id_cliente'];
$id_trabajador = $data['id_trabajador'];

$stmt = $pdo->prepare("INSERT IGNORE INTO cliente_trabajador (id_cliente, id_trabajador) VALUES (?, ?)");
echo json_encode(['success' => $stmt->execute([$id_cliente, $id_trabajador])]);
?>