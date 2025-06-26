<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM tb_roles WHERE id_rol = ?");
    $stmt->execute([$id]);
    $rol = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($rol ?: []);
} else {
    echo json_encode([]);
}
exit;
?>