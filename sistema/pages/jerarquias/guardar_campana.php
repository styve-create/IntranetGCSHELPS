<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php');

$response = ['ok' => false];

// Validar datos
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$id_padre = isset($_POST['id_padre']) ? intval($_POST['id_padre']) : 0;
$id_responsable = isset($_POST['id_responsable']) ? intval($_POST['id_responsable']) : 0;

if ($nombre === '' || $id_padre <= 0 || $id_responsable <= 0) {
    $response['mensaje'] = 'Faltan datos obligatorios.';
    echo json_encode($response);
    exit;
}

// Insertar en la base de datos
try {
    $stmt = $pdo->prepare("INSERT INTO tb_campanas (nombre, id_padre, id_responsable) VALUES (?, ?, ?)");
    $stmt->execute([$nombre, $id_padre, $id_responsable]);

    $response['ok'] = true;
    $response['mensaje'] = 'Campaña creada correctamente.';
} catch (PDOException $e) {
    $response['mensaje'] = 'Error al guardar: ' . $e->getMessage();
}

echo json_encode($response);
?>