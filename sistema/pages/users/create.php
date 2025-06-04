<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');

// Obtener los roles disponibles
$stmt_roles = $pdo->query("SELECT * FROM tb_roles");
$roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $nombres = trim($_POST['nombres'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password_user'] ?? '';
    $id_rol = $_POST['id_rol'] ?? '';

    if (!empty($nombres) && !empty($email) && !empty($password) && !empty($id_rol)) {
        try {
            // Verificar si el correo ya existe
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM tb_usuarios WHERE email = ?");
            $stmt_check->execute([$email]);
            $email_exists = $stmt_check->fetchColumn();

            if ($email_exists) {
                echo json_encode(['status' => 'error', 'message' => 'El correo ya está registrado.']);
                exit;
            }

            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO tb_usuarios (nombres, email, password_user, id_rol, fyh_creacion, fyh_actualizacion)
                VALUES (?, ?, ?, ?, NOW(), NOW())
            ");
            $success = $stmt->execute([$nombres, $email, $password_hashed, $id_rol]);

            if ($success) {
                $id_nuevo_usuario = $pdo->lastInsertId(); // Capturamos el ID generado automáticamente
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Usuario creado exitosamente.',
                    'id' => $id_nuevo_usuario
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al crear el usuario.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Excepción: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
    }
    exit;
}
?>
<html>
    <head>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">  
    </head>
    <body>
        <!-- HTML -->
<div class="container mt-4">
    <h2>Nuevo Usuario</h2>
    <form id="formCrearUsuario">
        <div class="mb-2">
            <label for="nombres" class="form-label">Nombre Usuario</label>
            <input type="text" name="nombres" class="form-control" required>
        </div>
        <div class="mb-2">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-2">
            <label for="password_user" class="form-label">Contraseña</label>
            <input type="password" name="password_user" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="id_rol" class="form-label">Rol</label>
            <select name="id_rol" class="form-select" required>
                <option value="" disabled selected>Seleccione un rol</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?= htmlspecialchars($rol['id_rol']) ?>">
                        <?= htmlspecialchars($rol['rol']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="id_rol" class="form-label">Trabajdor</label>
            <select name="id_rol" class="form-select" required>
                <option value="" disabled selected>Seleccione un trabajador</option>
                <?php foreach ($trabajadores as $trabajadore): ?>
                    <option value="<?= htmlspecialchars($rol['id_rol']) ?>">
                        <?= htmlspecialchars($rol['rol']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success" id="btnGuardar">Guardar</button>
        <div id="mensaje" class="mt-3"></div>
    </form>
</div>

<!-- JS -->
<script>
$(document).ready(function() {
    $('#formCrearUsuario').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btnGuardar');
        btn.prop('disabled', true).text('Guardando...');

        $.post('pages/users/create.php', $(this).serialize())
            .done(function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message
                    });
                    $('#formCrearUsuario')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            })
            .fail(function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error inesperado',
                    text: 'No se pudo procesar la solicitud: ' + error
                });
            })
            .always(function() {
                btn.prop('disabled', false).text('Guardar');
            });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>



