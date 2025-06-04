<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../../app/controllers/config.php');
$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $pdo->prepare("INSERT INTO tb_actividades (cliente, actividad, descripcion, duracion, fecha, hora_inicio, hora_fin, id_usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['cliente'],
        $data['actividad'],
        $data['descripcion'],
        $data['duracion'],
        $data['fecha'],
        $data['hora_inicio'],
        $data['hora_fin'],
        $data['id_usuario']
    ]);

    $new_id = $pdo->lastInsertId();

    // Recupera el registro recién insertado
    $stmt2 = $pdo->prepare("SELECT * FROM tb_actividades WHERE id = ?");
    $stmt2->execute([$new_id]);
    $new_activity = $stmt2->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "actividad" => $new_activity
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>