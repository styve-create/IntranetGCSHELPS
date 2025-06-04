<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

$id_usuario = $_POST['id_usuario'] ?? null;
if (!$id_usuario) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

// Obtener trabajador_id
$stmt = $pdo->prepare("SELECT trabajador_id FROM tb_usuarios WHERE id_usuarios = :id");
$stmt->execute(['id' => $id_usuario]);
$trabajador_id = $stmt->fetchColumn();

if (!$trabajador_id || empty($_FILES['foto'])) {
    echo json_encode(['success' => false, 'message' => 'Falta imagen o usuario']);
    exit;
}

// Validar imagen
$permitidos = ['image/jpeg', 'image/png', 'image/webp'];
$mime = mime_content_type($_FILES['foto']['tmp_name']);
if (!in_array($mime, $permitidos)) {
    echo json_encode(['success' => false, 'message' => 'Formato no permitido']);
    exit;
}

// Guardar imagen
$ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
$nombre = uniqid('perfil_', true) . '.' . $ext;
$carpeta = __DIR__ . '/uploads/perfil/';
$ruta_relativa = '/sistema/pages/informacion/uploads/perfil/' . $nombre;

if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

if (!move_uploaded_file($_FILES['foto']['tmp_name'], $carpeta . $nombre)) {
    echo json_encode(['success' => false, 'message' => 'Error al mover imagen']);
    exit;
}

// Actualizar en DB
$stmt = $pdo->prepare("UPDATE trabajadores SET foto_perfil = :ruta WHERE id = :id");
$stmt->execute([
    'ruta' => $ruta_relativa,
    'id' => $trabajador_id
]);

echo json_encode(['success' => true, 'ruta' => $ruta_relativa]);
