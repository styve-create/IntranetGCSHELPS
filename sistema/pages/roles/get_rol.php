<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');


if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM tb_roles WHERE id_rol = ?");
    $stmt->execute([$id]);
    $rol = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($rol);
}
?>