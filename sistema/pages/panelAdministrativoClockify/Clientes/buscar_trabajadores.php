<?php
header('Content-Type: application/json');
include_once(__DIR__ . '/../../../../app/controllers/config.php');
$q = '%' . $_GET['q'] . '%';

$stmt = $pdo->prepare("SELECT id, nombre_completo FROM trabajadores WHERE estado = 'activo' AND nombre_completo LIKE ? LIMIT 10");
$stmt->execute([$q]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>