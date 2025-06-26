<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');

// Obtener los roles disponibles
$stmt_roles = $pdo->query("SELECT * FROM tb_roles");
$roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

// Obtener los trabajadores disponibles
$stmt_trabajadores = $pdo->query("SELECT id, nombre_completo FROM trabajadores WHERE estado = 'activo' ORDER BY nombre_completo ASC");
$trabajadores = $stmt_trabajadores->fetchAll(PDO::FETCH_ASSOC);


?>

<!-- 1) Bootstrap CSS -->
<link
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
  rel="stylesheet"
/>
<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div id="contenido-principal">
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
                    <label for="trabajador_id" class="form-label">Trabajador</label>
                    <select name="trabajador_id" id="trabajador_id" class="form-select" required>
                        <option value="">Seleccione un trabajador</option>
                        <?php foreach ($trabajadores as $trabajador): ?>
                            <option value="<?= htmlspecialchars($trabajador['id']) ?>">
                                <?= htmlspecialchars($trabajador['nombre_completo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success" id="btnGuardar">Guardar</button>
                <div id="mensaje" class="mt-3"></div>
            </form>
        </div> 
</div>  




<!-- 6) Bootstrap Bundle JS (incluye Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- 13) SweetAlert2 (si usas alertas) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Select2 CSS & JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
(function () {
    window.enviaralback = function () {
        const form = document.getElementById('formCrearUsuario');
        const btn = document.getElementById('btnGuardar');

        if (!form || !btn) {
            console.warn("Formulario o botón no encontrado.");
            return;
        }

        $('#trabajador_id').select2({
            placeholder: "Buscar trabajador...",
            width: '100%',
            allowClear: true
        });

        form.addEventListener('submit', async function (e) {
    e.preventDefault();

    btn.disabled = true;
    btn.textContent = 'Guardando...';

    const formData = new FormData(form);
    const url = `/intranet/sistema/pages/users/createUsuario.php?t=${new Date().getTime()}`;

    console.log("📡 URL del fetch:", url);

    for (let [key, value] of formData.entries()) {
        console.log(`📤 Enviando: ${key} = ${value}`);
    }

    try {
        const res = await fetch(url, {
            method: 'POST',
            body: JSON.stringify(Object.fromEntries(formData.entries())),
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include'
        });

        const text = await res.text();
        console.log('📥 Respuesta cruda:', text);

        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            throw new Error('Respuesta no es JSON válido');
        }

        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message
            });
            form.reset();
            $('#trabajador_id').val(null).trigger('change');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }

    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Error inesperado',
            text: 'No se pudo procesar la solicitud: ' + err.message
        });
    } finally {
        btn.disabled = false;
        btn.textContent = 'Guardar';
    }
});
    };

    window.enviaralback();
})();
</script>
  


