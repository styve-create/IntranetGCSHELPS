<?php
require_once(__DIR__ . '/../../../app/controllers/config.php');
header('Content-Type: application/json');

$id = $_POST['formulario_id'] ?? null;
$equiposAEliminar = $_POST['equipos'] ?? [];

if (!$id || !is_array($equiposAEliminar)) {
    echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
    exit;
}

$stmt = $pdo->prepare("SELECT equipos, seriales FROM formularios_asignacion WHERE id = ?");
$stmt->execute([$id]);
$form = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$form) {
    echo json_encode(['status' => 'error', 'message' => 'Formulario no encontrado']);
    exit;
}

$equipos = json_decode($form['equipos'], true);
$seriales = json_decode($form['seriales'], true);

// Filtrar equipos y seriales
$equiposFiltrados = array_values(array_filter($equipos, function($eq) use ($equiposAEliminar) {
    return !in_array($eq, $equiposAEliminar);
}));

foreach ($equiposAEliminar as $eliminar) {
    unset($seriales[$eliminar]);
}

// Guardar
$stmt = $pdo->prepare("UPDATE formularios_asignacion SET equipos = ?, seriales = ? WHERE id = ?");
$exito = $stmt->execute([
    json_encode($equiposFiltrados),
    json_encode($seriales),
    $id
]);

if ($exito) {
    echo json_encode(['status' => 'success', 'message' => 'Equipos desasignados correctamente.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar los datos.']);
}