<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include_once(__DIR__ . '/../../../../app/controllers/config.php');

$id_cliente = $_GET['id_cliente'] ?? null;

if (!$id_cliente) {
    echo json_encode(['error' => 'Falta id_cliente']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT t.id AS id_trabajador, t.nombre_completo 
                           FROM cliente_trabajador ct 
                           JOIN trabajadores t ON ct.id_trabajador = t.id
                           WHERE ct.id_cliente = ?");
    $stmt->execute([$id_cliente]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>