<?php
// Mostrar errores durante desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php');


if (session_status() === PHP_SESSION_NONE) {
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



function mostrarSweetAlert($titulo, $mensaje, $icono) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '$titulo',
                text: '$mensaje',
                icon: '$icono',
                confirmButtonText: 'Aceptar'
            });
        });
    </script>";
}

$id_usuario = $_GET['id'] ?? null;
if (!$id_usuario) {
    mostrarSweetAlert('Error', 'ID de usuario no proporcionado.', 'error');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM tb_usuarios WHERE id_usuarios = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    mostrarSweetAlert('Error', 'Usuario no encontrado.', 'error');
    exit;
}

$stmt_roles = $pdo->query("SELECT * FROM tb_roles");
$roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

$deshabilitarBoton = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = trim($_POST['nombres'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nueva_password = $_POST['password'] ?? '';
    $id_rol_nuevo = $_POST['id_rol'] ?? $usuario['id_rol'];

    if ($nombres && $email && $id_rol_nuevo) {
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM tb_usuarios WHERE email = ? AND id_usuarios != ?");
        $stmt_check->execute([$email, $id_usuario]);
        $email_exists = $stmt_check->fetchColumn();

        if ($email_exists) {
            mostrarSweetAlert('Error', 'El correo ya está en uso por otro usuario.', 'error');
        } else {
            if (!empty($nueva_password)) {
                $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
                $update = $pdo->prepare("UPDATE tb_usuarios SET nombres = ?, email = ?, password_user = ?, id_rol = ? WHERE id_usuarios = ?");
                $update->execute([$nombres, $email, $password_hash, $id_rol_nuevo, $id_usuario]);
            } else {
                $update = $pdo->prepare("UPDATE tb_usuarios SET nombres = ?, email = ?, id_rol = ? WHERE id_usuarios = ?");
                $update->execute([$nombres, $email, $id_rol_nuevo, $id_usuario]);
            }

            mostrarSweetAlert('Éxito', 'Usuario actualizado correctamente.', 'success');
            $deshabilitarBoton = true;
        }
    } else {
        mostrarSweetAlert('Error', 'Todos los campos son obligatorios.', 'error');
    }
}
?>
<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    </head>
    <body>
        <div class="container mt-4">
    <h2>Editar Usuario</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="nombres" class="form-label">Nombres</label>
            <input type="text" class="form-control" id="nombres" name="nombres" value="<?= htmlspecialchars($usuario['nombres']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Nueva Contraseña (opcional)</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Dejar en blanco para mantener la actual">
        </div>

        <div class="mb-3">
            <label for="id_rol" class="form-label">Rol</label>
            <select name="id_rol" id="id_rol" class="form-select" required>
                <option value="" disabled>Selecciona un rol</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?= $rol['id_rol'] ?>" <?= ($usuario['id_rol'] == $rol['id_rol']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($rol['rol']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success" <?= $deshabilitarBoton ? 'disabled' : '' ?>>Guardar Cambios</button>
        <a href="<?= $URL ?>/sistema/index.php?page=users" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<script>
let beaconSent = false;

window.addEventListener("beforeunload", function () {
    if (beaconSent) return;

    // Obtener uso de RAM si está disponible
    let ramUsageMb = null;
    try {
        if (performance.memory) {
            ramUsageMb = performance.memory.usedJSHeapSize / 1024 / 1024; // en MB
        }
    } catch (e) {
        console.warn("No se pudo obtener uso de RAM:", e);
    }

    // Obtener info de GPU si es posible
    let gpuInfo = null;
    try {
        const canvas = document.createElement('canvas');
        const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
        const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
        gpuInfo = debugInfo ? gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL) : 'No disponible';
    } catch (e) {
        console.warn("No se pudo obtener info de GPU:", e);
    }

    // Preparar datos para enviar al servidor
    const payload = {
        id_conexion: '<?php echo $_SESSION['id_conexion'] ?? ''; ?>',
        pagina: '<?php echo $_SERVER['REQUEST_URI']; ?>',
        tiempo_inicio: '<?php echo $_SESSION['tiempo_inicio'] ?? microtime(true); ?>',
        ram_usage_mb: ramUsageMb,
        gpu_info: gpuInfo
    };

    // Enviar con sendBeacon
    const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
    navigator.sendBeacon('<?php echo $URL; ?>/sistema/cerrar_pagina.php', blob);

    beaconSent = true;
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
