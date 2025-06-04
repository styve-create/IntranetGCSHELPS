<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');

include_once(__DIR__ . '/../../analisisRecursos.php');

function mostrarSweetAlert($titulo, $mensaje, $icono, $redirigir = false) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '$titulo',
                text: '$mensaje',
                icon: '$icono',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed && $redirigir) {
                    window.location.href = window.location.href;
                }
            });
        });
    </script>";
}

// Crear nuevo rol
if (isset($_POST['crear_rol'])) {
    $nuevo_rol = trim($_POST['nuevo_rol']);
    if (!empty($nuevo_rol)) {
        $stmt = $pdo->prepare("INSERT INTO tb_roles (rol, fyh_creacion, fyh_actualizacion) VALUES (?, NOW(), NOW())");
        $stmt->execute([$nuevo_rol]);
        mostrarSweetAlert('Éxito', 'Rol creado exitosamente.', 'success', true);
    } else {
        mostrarSweetAlert('Error', 'El nombre del rol no puede estar vacío.', 'error');
    }
}

// Editar rol
if (isset($_POST['editar_rol'])) {
    $id_rol_edit = $_POST['id_rol_edit'];
    $nuevo_nombre = trim($_POST['nombre_rol_editado']);
    if (!empty($nuevo_nombre)) {
        $stmt = $pdo->prepare("UPDATE tb_roles SET rol = ?, fyh_actualizacion = NOW() WHERE id_rol = ?");
        $stmt->execute([$nuevo_nombre, $id_rol_edit]);
        mostrarSweetAlert('Éxito', 'Rol actualizado correctamente.', 'success', true);
    } else {
        mostrarSweetAlert('Error', 'El nombre del rol no puede estar vacío.', 'error');
    }
}

// Eliminar rol
if (isset($_POST['eliminar_rol'])) {
    $id_rol_delete = $_POST['id_rol_edit'];
    $stmt = $pdo->prepare("DELETE FROM tb_roles WHERE id_rol = ?");
    $stmt->execute([$id_rol_delete]);
    mostrarSweetAlert('Atención', 'Rol eliminado.', 'warning', true);
}

// Obtener roles
$stmt = $pdo->query("SELECT * FROM tb_roles");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener rol seleccionado si se pasó por GET
$rol_seleccionado = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM tb_roles WHERE id_rol = ?");
    $stmt->execute([$_GET['id']]);
    $rol_seleccionado = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    </head>
    <body>
        <div class="container mt-4">
    <h2>Gestión de Roles</h2>

    <!-- Dropdown con JS dinámico -->
    <div class="mb-3">
        <label for="id_rol" class="form-label">Seleccionar un rol para editar:</label>
        <select id="id_rol" class="form-select">
            <option value="">-- Selecciona un rol --</option>
            <?php foreach ($roles as $rol): ?>
                <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['rol']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Aquí se insertará el formulario por JS -->
    <div id="form_edicion"></div>

    <script>
    document.getElementById('id_rol').addEventListener('change', function () {
        const id = this.value;
        const contenedor = document.getElementById('form_edicion');

        if (id) {
            fetch('pages/roles/get_rol.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    contenedor.innerHTML = `
                        <form id="form_edicion_rol" method="POST">
                            <input type="hidden" name="id_rol_edit" value="${data.id_rol}">
                            <div class="mb-3">
                                <label class="form-label">Editar nombre del rol</label>
                                <input type="text" name="nombre_rol_editado" class="form-control" value="${data.rol}" required>
                            </div>
                            <button type="submit" name="editar_rol" class="btn btn-success">Guardar Cambios</button>
                            <button type="button" class="btn btn-danger" id="btn_eliminar_rol">Eliminar Rol</button>
                        </form>
                    `;

                    document.getElementById('btn_eliminar_rol').addEventListener('click', function () {
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: "Esta acción no se puede deshacer",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const form = document.getElementById('form_edicion_rol');
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'eliminar_rol';
                                input.value = '1';
                                form.appendChild(input);
                                form.submit();
                            }
                        });
                    });
                });
        } else {
            contenedor.innerHTML = '';
        }
    });
    </script>
    
    <br>
    <!-- Crear nuevo rol -->
    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="nuevo_rol" class="form-label">Nuevo Rol</label>
            <input type="text" name="nuevo_rol" id="nuevo_rol" class="form-control" placeholder="Nombre del nuevo rol" required>
        </div>
        <button type="submit" name="crear_rol" class="btn btn-primary">Crear Rol</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>




