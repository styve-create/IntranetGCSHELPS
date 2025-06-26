<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../app/controllers/config.php');
header('Content-Type: application/json');


$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM trabajadores WHERE id = ?");
$stmt->execute([$id]);
$trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trabajador) {
    echo json_encode(['error' => 'Trabajador no encontrado']);
    exit;
}

echo json_encode($trabajador);
exit;
?>