<?php
require_once(__DIR__ . '/../../../app/controllers/config.php');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

$stmt = $pdo->prepare("SELECT equipos, seriales FROM formularios_asignacion WHERE id = ?");
$stmt->execute([$id]);
$formulario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($formulario) {
    echo json_encode($formulario);
} else {
    echo json_encode(['error' => 'Formulario no encontrado']);
}