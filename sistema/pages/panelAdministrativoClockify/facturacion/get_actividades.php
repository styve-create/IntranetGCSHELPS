<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../../app/controllers/config.php');


header('Content-Type: application/json');

$clienteId = isset($_GET['cliente_id']) ? intval($_GET['cliente_id']) : 0;

if ($clienteId > 0) {
    $stmt = $pdo->prepare("SELECT id_actividad, nombre_actividad FROM actividades WHERE id_cliente = ? ORDER BY nombre_actividad ASC");
    $stmt->execute([$clienteId]);
    $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($actividades);
} else {
    echo json_encode([]);
}
?>