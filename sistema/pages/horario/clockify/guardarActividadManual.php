<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../../app/controllers/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

$id_usuario = $_SESSION['usuario_info']['id'] ?? null;

if (!$id_usuario) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

// Leer el JSON recibido
$data = json_decode(file_get_contents("php://input"), true);

$descripcion = $data['descripcion'] ?? '';
$hora_inicio = $data['hora_inicio'] ?? '';
$hora_fin = $data['hora_fin'] ?? '';
$fecha = $data['fecha'] ?? date('Y-m-d');
$cliente = $data['cliente'] ?? 'Sin cliente';
$actividad = $data['actividad'] ?? 'Sin actividad';

// Validaciones básicas
if (!$hora_inicio || !$hora_fin) {
    echo json_encode(['success' => false, 'error' => 'Faltan horas']);
    exit;
}

// Calcular duración en minutos
$duracion = (strtotime("1970-01-01 $hora_fin UTC") - strtotime("1970-01-01 $hora_inicio UTC")) / 60;
$duracion = max(0, round($duracion)); // nunca negativo

try {
    $stmt = $pdo->prepare("
        INSERT INTO tb_actividades 
        (id_usuario, descripcion, cliente, actividad, fecha, hora_inicio, hora_fin, duracion)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $id_usuario,
        $descripcion,
        $cliente,
        $actividad,
        $fecha,
        $hora_inicio,
        $hora_fin,
        $duracion
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}