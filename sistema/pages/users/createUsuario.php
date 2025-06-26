<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include_once(__DIR__ . '/../../../app/controllers/config.php');

$log_index1 = __DIR__ . "/log_index1.txt";
function log_index1($mensaje) {
    global $log_index1;
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, $log_index1);
}

log_index1(">>> Inicio del script");

session_name('mi_sesion_personalizada');
session_start();
log_index1("Sesión iniciada. Usuario ID: " . ($_SESSION['usuario_info']['id'] ?? 'N/A'));

// Leer cuerpo sin procesar
$rawData = file_get_contents('php://input');
log_index1("Contenido recibido: " . $rawData);

$data = json_decode($rawData, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    log_index1("❌ Error al decodificar JSON: " . json_last_error_msg());
    echo json_encode(['status' => 'error', 'message' => 'JSON inválido.']);
    exit;
}

log_index1("✅ JSON decodificado correctamente");

// Extraer datos
$nombres        = trim($data['nombres'] ?? '');
$email          = trim($data['email'] ?? '');
$password       = $data['password_user'] ?? '';
$id_rol         = $data['id_rol'] ?? '';
$trabajador_id  = $data['trabajador_id'] ?? null;

log_index1("Datos extraídos: nombres={$nombres}, email={$email}, rol={$id_rol}, trabajador_id={$trabajador_id}");

// Validación básica
if (!empty($nombres) && !empty($email) && !empty($password) && !empty($id_rol) && !empty($trabajador_id)) {
    try {
        log_index1("Iniciando verificación de correo duplicado");

        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM tb_usuarios WHERE email = ?");
        $stmt_check->execute([$email]);
        $email_exists = $stmt_check->fetchColumn();

        log_index1("Correo duplicado: " . ($email_exists ? "SÍ" : "NO"));

        if ($email_exists) {
            log_index1("Correo ya registrado, abortando");
            echo json_encode(['status' => 'error', 'message' => 'El correo ya está registrado.']);
            exit;
        }

        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        log_index1("Password hasheada correctamente");

        $stmt = $pdo->prepare("
            INSERT INTO tb_usuarios 
            (nombres, email, password_user, id_rol, trabajador_id, fyh_creacion, fyh_actualizacion)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $success = $stmt->execute([$nombres, $email, $password_hashed, $id_rol, $trabajador_id]);

        if ($success) {
            $id_nuevo_usuario = $pdo->lastInsertId();
            log_index1("✅ Usuario creado exitosamente. ID: $id_nuevo_usuario");

            echo json_encode([
                'status' => 'success',
                'message' => 'Usuario creado exitosamente.',
                'id' => $id_nuevo_usuario
            ]);
        } else {
            log_index1("❌ Error al ejecutar la inserción en la base de datos");
            echo json_encode(['status' => 'error', 'message' => 'Error al crear el usuario.']);
        }

    } catch (Exception $e) {
        log_index1("❌ Excepción atrapada: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Excepción: ' . $e->getMessage()]);
    }
} else {
    log_index1("❌ Validación fallida. Faltan campos obligatorios.");
    echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
}

log_index1("<<< Fin del script");
exit;
?>