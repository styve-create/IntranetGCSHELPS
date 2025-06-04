<?php
include_once(__DIR__ . '/../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $id_usuarios = $_POST['id_usuarios'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($id_usuarios) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'ID y email son obligatorios.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tb_usuarios WHERE id_usuarios = ? AND email = ?");
    $stmt->execute([$id_usuarios, $email]);

    if ($stmt->fetchColumn()) {
        $stmt = $pdo->prepare("DELETE FROM tb_usuarios WHERE id_usuarios = ? AND email = ?");
        $stmt->execute([$id_usuarios, $email]);

        echo json_encode(['status' => 'success', 'message' => 'Usuario eliminado correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado.']);
    }
    exit;
}
?>