<?php
// Mostrar errores (desactivar en producción si es necesario)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

$id_conexion = $_SESSION['id_conexion'] ?? null;


if ($id_conexion) {
    $pagina = $_SERVER['REQUEST_URI'];
    $fecha_apertura = date("Y-m-d H:i:s");

    // Verificar si ya existe una entrada abierta para esta página y conexión
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM paginas_abiertas WHERE id_conexion = :id_conexion AND pagina = :pagina AND estado = 'abierta'");
    if (!$stmt) {
        die('Error al preparar la consulta: ' . implode(" | ", $pdo->errorInfo()));
    }

    $stmt->execute([
        ':id_conexion' => $id_conexion,
        ':pagina' => $pagina
    ]);

    $total = $stmt->fetchColumn();

    if ($total == 0) {
        // Si no existe, insertar una nueva entrada
        $stmt = $pdo->prepare("INSERT INTO paginas_abiertas (id_conexion, pagina, fecha_apertura, estado) VALUES (:id_conexion, :pagina, :fecha_apertura, 'abierta')");
        if (!$stmt) {
            die('Error al preparar la consulta de inserción: ' . implode(" | ", $pdo->errorInfo()));
        }

        $stmt->execute([
            ':id_conexion' => $id_conexion,
            ':pagina' => $pagina,
            ':fecha_apertura' => $fecha_apertura
        ]);
    }
}


// Devolver siempre JSON
header('Content-Type: application/json');

// Incluir conexión a base de datos
include_once(__DIR__ . '/../../../app/controllers/config.php');

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido. Se requiere POST.'
    ]);
    exit;
}

// Obtener datos
$id_usuarios = $_POST['id_usuarios'] ?? '';
$email = $_POST['email'] ?? '';

// Validar campos
if (empty($id_usuarios) || empty($email)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID y Email son obligatorios.'
    ]);
    exit;
}

try {
    // Verificar si el usuario existe
    $stmt_check = $pdo->prepare("SELECT 1 FROM tb_usuarios WHERE id_usuarios = ? AND email = ? LIMIT 1");
    $stmt_check->execute([$id_usuarios, $email]);

    if (!$stmt_check->fetch()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'El usuario no existe o ya fue eliminado.'
        ]);
        exit;
    }

    // Eliminar usuario
    $stmt_delete = $pdo->prepare("DELETE FROM tb_usuarios WHERE id_usuarios = ? AND email = ?");
    $stmt_delete->execute([$id_usuarios, $email]);

    if ($stmt_delete->rowCount() > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Usuario eliminado correctamente.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se pudo eliminar al usuario. Intenta nuevamente.'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}

exit;

?>
