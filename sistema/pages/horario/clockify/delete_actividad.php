<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../../app/controllers/config.php');
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if ($id) {
  $stmt = $pdo->prepare("DELETE FROM tb_actividades WHERE id = ?");
  // Si usas PDO, puedes enlazar parámetros así:
  $stmt->bindValue(1, $id, PDO::PARAM_INT);

  if ($stmt->execute()) {
    echo json_encode(["success" => true]);
  } else {
    $errorInfo = $stmt->errorInfo();
    echo json_encode(["success" => false, "error" => $errorInfo[2] ?? 'Error desconocido']);
  }
} else {
  echo json_encode(["success" => false, "error" => "ID inválido"]);
}
?>