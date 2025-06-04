<?php 
header('Content-Type: application/json');
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión a la base de datos
include_once(__DIR__ . '/../../../../app/controllers/config.php');

// Captura input una sola vez
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

// Depuración
file_put_contents('php://stderr', print_r($input, true));

$id = $input['id'] ?? null;
$hora_inicio = $input['hora_inicio'] ?? null;
$hora_fin = $input['hora_fin'] ?? null;
$duracion = $input['duracion'] ?? null;

if (!$id || !$hora_inicio || !$hora_fin || $duracion === null) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos',
        'data_recibida' => $input
    ]);
    exit;
}

$stmt = $pdo->prepare("
  UPDATE tb_actividades 
  SET hora_inicio = ?, hora_fin = ?, duracion = ? 
  WHERE id = ?
");

if ($stmt->execute([$hora_inicio, $hora_fin, $duracion, $id])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar',
        'error' => $stmt->errorInfo()
    ]);
}
?>