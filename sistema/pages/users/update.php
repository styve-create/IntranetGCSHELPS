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
$id_usuario = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM tb_usuarios WHERE id_usuarios = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);



$stmt_roles = $pdo->query("SELECT * FROM tb_roles");
$roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);


?>

<!-- 1) Bootstrap CSS -->
<link
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
  rel="stylesheet"
/>
<!-- 3) Buttons Bootstrap5 CSS -->
<link
  href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css"
  rel="stylesheet"
/>
  
  <div id="contenido-principal">
        <div class="container mt-4">
                    <h2>Editar Usuario</h2>
               <form id="formEditarUsuario">
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

  <button type="submit" class="btn btn-success" id="btnGuardarCambios">Guardar Cambios</button>
  <a href="#" class="btn btn-secondary enlaceDinamicos" data-link="users">Cancelar</a>
</form>
             </div>
</div> 
        <script>
window.actualizarRegistro = function() {
  const form = document.getElementById('formEditarUsuario');
  const btn = document.getElementById('btnGuardarCambios');
  const idUsuario = <?= json_encode($id_usuario) ?>;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    const formData = new FormData(form);
    const payload = Object.fromEntries(formData.entries());

    try {
      const res = await fetch(`/intranet/sistema/pages/users/updateUsuario.php?id=${idUsuario}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
        credentials: 'include'
      });

      const data = await res.json();
      if (data.status === 'success') {
        Swal.fire({ icon: 'success', title: 'Éxito', text: data.message });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
      }
    } catch (err) {
      Swal.fire({ icon: 'error', title: 'Error inesperado', text: err.message });
    } finally {
      btn.disabled = false;
      btn.textContent = 'Guardar Cambios';
    }
  });
};
window.actualizarRegistro ();
</script>

   