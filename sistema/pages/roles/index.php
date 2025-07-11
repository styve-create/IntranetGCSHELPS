<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../../analisisRecursos.php');
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div id="contenido-principal">
    <div class="container mt-4">
        <h2>Gestión de Roles</h2>

        <!-- Dropdown para seleccionar rol -->
        <div class="mb-3">
            <label for="id_rol" class="form-label">Seleccionar un rol para editar:</label>
            <select id="id_rol" class="form-select">
                <option value="">-- Selecciona un rol --</option>
               
            </select>
        </div>

        <!-- Formulario de edición cargado por JS -->
        <div id="form_edicion"></div>

        <hr>

        <!-- Crear nuevo rol -->
        <div class="mb-4">
            <label for="nuevo_rol" class="form-label">Nuevo Rol</label>
            <input type="text" id="nuevo_rol" class="form-control mb-2" placeholder="Nombre del nuevo rol" required>
            <button type="button" id="btn_crear_rol" class="btn btn-primary">Crear Rol</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function() {
  window.editarRegistro = function() {
    // Función para cargar roles al dropdown
    async function cargarListadoRoles(seleccionarID = null) {
      const select = document.getElementById('id_rol');
      select.innerHTML = '<option value="">-- Selecciona un rol --</option>';

      try {
        const res = await fetch('/intranet/sistema/pages/roles/listar_roles.php?t=' + new Date().getTime());
        const data = await res.json();

        if (data.status === 'success') {
          data.roles.forEach(rol => {
            const option = document.createElement('option');
            option.value = rol.id_rol;
            option.textContent = rol.rol;
            if (seleccionarID && rol.id_rol == seleccionarID) {
              option.selected = true;
            }
            select.appendChild(option);
          });
        } else {
          Swal.fire('Error', 'No se pudieron cargar los roles.', 'error');
        }
      } catch (err) {
        console.error('Error al cargar roles:', err);
      }
    }

    // Crear nuevo rol
    document.getElementById('btn_crear_rol').addEventListener('click', async () => {
      const input = document.getElementById('nuevo_rol');
      const rol = input.value.trim();
      if (!rol) {
        Swal.fire('Error', 'El nombre del rol no puede estar vacío.', 'error');
        return;
      }

      const res = await fetch(`/intranet/sistema/pages/roles/roles_api.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ accion: 'crear', rol })
      });

      const data = await res.json();
      Swal.fire(data.status === 'success' ? 'Éxito' : 'Error', data.message, data.status);

      if (data.status === 'success') {
        input.value = '';
        cargarListadoRoles(data.id); // selecciona el nuevo rol
      }
    });

    // Cargar datos del rol seleccionado
    document.getElementById('id_rol').addEventListener('change', async function () {
      const id = this.value;
      const contenedor = document.getElementById('form_edicion');
      if (!id) return contenedor.innerHTML = '';

      const res = await fetch(`/intranet/sistema/pages/roles/get_rol.php?id=${id}&t=${new Date().getTime()}`);
      const data = await res.json();

      contenedor.innerHTML = `
        <form id="form_edicion_rol">
          <input type="hidden" name="id_rol_edit" value="${data.id_rol}">
          <div class="mb-3">
            <label class="form-label">Editar nombre del rol</label>
            <input type="text" name="nombre_rol_editado" class="form-control" value="${data.rol}" required>
          </div>
          <button type="submit" class="btn btn-success">Guardar Cambios</button>
          <button type="button" class="btn btn-danger" id="btn_eliminar_rol">Eliminar Rol</button>
        </form>
      `;
    });

    // Editar rol
    document.addEventListener('submit', async (e) => {
      if (e.target.id === 'form_edicion_rol') {
        e.preventDefault();
        const form = e.target;
        const id_rol = form.querySelector('input[name="id_rol_edit"]').value;
        const rol = form.querySelector('input[name="nombre_rol_editado"]').value;

        const res = await fetch(`/intranet/sistema/pages/roles/roles_api.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ accion: 'editar', id_rol, rol })
        });

        const data = await res.json();
        Swal.fire(data.status === 'success' ? 'Éxito' : 'Error', data.message, data.status);

        if (data.status === 'success') {
          document.getElementById('form_edicion').innerHTML = '';
          cargarListadoRoles(id_rol); // refresca y mantiene seleccionado
        }
      }
    });

    // Eliminar rol
    document.addEventListener('click', async (e) => {
      if (e.target.id === 'btn_eliminar_rol') {
        const confirm = await Swal.fire({
          title: '¿Estás seguro?',
          text: 'Esta acción no se puede deshacer.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar'
        });

        if (confirm.isConfirmed) {
          const id = document.querySelector('input[name="id_rol_edit"]').value;

          const res = await fetch(`/intranet/sistema/pages/roles/roles_api.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'eliminar', id_rol: id })
          });

          const data = await res.json();
          Swal.fire(data.status === 'success' ? 'Eliminado' : 'Error', data.message, data.status);

          if (data.status === 'success') {
            document.getElementById('form_edicion').innerHTML = '';
            cargarListadoRoles(); // refresca sin seleccionar nada
          }
        }
      }
    });

    // Inicial
    cargarListadoRoles();
  };

  window.editarRegistro();
})();
</script>



