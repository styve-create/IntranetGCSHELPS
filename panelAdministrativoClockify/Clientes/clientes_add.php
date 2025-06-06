<?php
include_once(__DIR__ . '/../../../../app/controllers/config.php');

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$nombre = trim($data['nombre'] ?? '');

if ($nombre === '') {
  echo json_encode(['success' => false, 'message' => 'Nombre vacío']);
  exit;
}

try {
  $stmt = $pdo->prepare("INSERT INTO clientes (nombre_cliente) VALUES (?)");
  $stmt->execute([$nombre]);
  echo json_encode(['success' => true]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>