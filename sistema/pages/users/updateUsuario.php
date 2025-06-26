<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include_once(__DIR__ . '/../../../app/controllers/config.php');

$log_file = __DIR__ . "/log_update_usuario.txt";
function log_update($mensaje) {
    global $log_file;
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, $log_file);
}

log_update(">>> Inicio de updateUsuario.php");

// Obtener ID de usuario
$id_usuario = $_GET['id'] ?? null;
if (!$id_usuario) {
    log_update("ID de usuario no recibido.");
    echo json_encode(['status' => 'error', 'message' => 'ID de usuario no especificado']);
    exit;
}

// Obtener datos JSON del fetch
$inputRaw = file_get_contents("php://input");
log_update("Raw input: $inputRaw");

$data = json_decode($inputRaw, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    log_update("Error al decodificar JSON: " . json_last_error_msg());
    echo json_encode(['status' => 'error', 'message' => 'JSON inválido.']);
    exit;
}

// Extraer datos
$nombres    = trim($data['nombres'] ?? '');
$email      = trim($data['email'] ?? '');
$password   = $data['password'] ?? '';
$id_rol     = $data['id_rol'] ?? '';

log_update("Datos recibidos: nombres=$nombres, email=$email, id_rol=$id_rol");

// Validar campos obligatorios
if (empty($nombres) || empty($email) || empty($id_rol)) {
    log_update("Campos requeridos incompletos");
    echo json_encode(['status' => 'error', 'message' => 'Todos los campos obligatorios deben completarse.']);
    exit;
}

// Verificar si el correo ya existe en otro usuario
$stmt_check = $pdo->prepare("SELECT COUNT(*) FROM tb_usuarios WHERE email = ? AND id_usuarios != ?");
$stmt_check->execute([$email, $id_usuario]);
$email_exists = $stmt_check->fetchColumn();

if ($email_exists) {
    log_update("Correo $email ya está registrado en otro usuario");
    echo json_encode(['status' => 'error', 'message' => 'El correo ya está registrado por otro usuario.']);
    exit;
}

// Actualizar usuario
try {
    if (!empty($password)) {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE tb_usuarios SET nombres = ?, email = ?, password_user = ?, id_rol = ?, fyh_actualizacion = NOW() WHERE id_usuarios = ?");
        $stmt->execute([$nombres, $email, $password_hashed, $id_rol, $id_usuario]);
        log_update("Usuario $id_usuario actualizado con nueva contraseña.");
    } else {
        $stmt = $pdo->prepare("UPDATE tb_usuarios SET nombres = ?, email = ?, id_rol = ?, fyh_actualizacion = NOW() WHERE id_usuarios = ?");
        $stmt->execute([$nombres, $email, $id_rol, $id_usuario]);
        log_update("Usuario $id_usuario actualizado sin cambiar contraseña.");
    }

    echo json_encode(['status' => 'success', 'message' => 'Usuario actualizado correctamente.']);
} catch (Exception $e) {
    log_update("Error en la actualización: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el usuario.']);
}
exit;
?>