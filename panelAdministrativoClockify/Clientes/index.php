<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../../app/controllers/config.php');


try {
    $stmtClientes = $pdo->query("SELECT id_cliente, nombre_cliente, direccion, moneda, estado, fyh_creacion FROM clientes ORDER BY nombre_cliente ASC");
    $clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<pre>Error: " . $e->getMessage() . "</pre>";
    exit;
}
?>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
 
  <style>
    body {
      background-color: #f5f8fb;
    }
    .table thead th {
      color: #888;
      font-weight: 600;
    }
    .form-control, .form-select {
      border-radius: 6px;
    }
  </style>
<div id="contenido-principal">
<div class="container py-4">
  <h4 class="mb-3 fw-bold">Clients</h4>

  <div class="d-flex gap-2 mb-3">
    <select id="filterEstado" class="form-select w-auto">
      <option value="">Show all</option>
      <option value="activo">Show active</option>
      <option value="inactivo">Show inactive</option>
    </select>
    <input id="searchInput" type="text" class="form-control" placeholder="Search by name" style="max-width: 250px;">
    <div class="ms-auto d-flex gap-2">
      <input id="nuevoCliente" type="text" class="form-control" placeholder="Add new Client">
      <button class="btn btn-primary" id="btnAdd">ADD</button>
    </div>
  </div>

 <table class="table table-hover bg-white rounded shadow-sm">
    <thead>
      <tr>
        <th scope="col">NAME</th>
        <th scope="col">ADDRESS</th>
        <th scope="col">CURRENCY</th>
        <th></th>
      </tr>
    </thead>
    <tbody id="clientesBody">
      <?php foreach ($clientes as $cliente): ?>
       <tr data-id="<?= $cliente['id_cliente'] ?>" data-estado="<?= $cliente['estado'] ?>">
          <td><?= htmlspecialchars($cliente['nombre_cliente']?? '') ?></td>
          <td><?= htmlspecialchars($cliente['direccion']?? '') ?></td>
          <td><?= htmlspecialchars($cliente['moneda']?? '') ?></td>
          <td class="text-center">
            <i class="bi bi-file-earmark-text me-3" style="cursor:pointer;" title="Ver" data-id="<?= $cliente['id_cliente'] ?>"></i>
            <i class="bi bi-pencil me-3" style="cursor:pointer;" title="Editar"></i>
            <i class="bi bi-three-dots-vertical" style="cursor:pointer;" title="Más opciones"></i>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<!-- Modal: Editar Cliente -->
<div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Edit Client</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editId">
        <div class="mb-2">
          <label>Name</label>
          <input type="text" id="editNombre" class="form-control" required>
        </div>
        <div class="mb-2">
          <label>Address</label>
          <textarea id="editDireccion" class="form-control"></textarea>
        </div>
        <div class="mb-2">
          <label>Currency</label>
          <select id="editMoneda" class="form-select">
            <option value="USD">USD</option>
            <option value="COP">COP</option>
          </select>
        </div>
        <div class="mb-2">
          <label>Status</label>
          <select id="editEstado" class="form-select">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
          </select>
        </div>
        <div class="mb-2">
          <label>Duración (horas)</label>
          <input type="number" id="nuevaDuracionCliente" class="form-control">
        </div>
        <div class="mb-2">
          <label>Precio</label>
          <input type="number"  id="nuevaPrecioCliente" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="btnSaveEdit">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Actividades del Cliente -->
<div class="modal fade" id="modalActividadesCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Client Activities</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="d-flex justify-content-end mb-2">
  <button class="btn btn-primary btn-sm" id="btnNuevaActividad">
    <i class="bi bi-plus-circle"></i> Nueva Actividad
  </button>
</div>
        <table class="table">
          <thead>
            <tr>
              <th>Actividad</th>
              <th>Descripción</th>
              <th>Duración</th>
              <th>Price</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody id="tablaActividadesCliente"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Nueva Actividad -->
<div class="modal fade" id="modalNuevaActividad" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Crear Nueva Actividad</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="nuevaActividadClienteId">
        <div class="mb-2">
          <label>Nombre de Actividad</label>
          <input type="text" id="nuevaNombre" class="form-control" required>
        </div>
        <div class="mb-2">
          <label>Descripción</label>
          <textarea id="nuevaDescripcion" class="form-control"></textarea>
        </div>
        <div class="mb-2">
          <label>Duración (horas)</label>
          <input type="number" id="nuevaDuracion" class="form-control">
        </div>
        <div class="mb-2">
          <label>Precio</label>
          <input type="number"  id="nuevaPrecio" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-link" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" id="btnGuardarNuevaActividad">Guardar</button>
      </div>
    </div>
  </div>
</div>
</div>



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){ 
    console.log("🛠️ Script cargado correctamente");
let clienteActivoId = null;

  const searchInput = document.getElementById("searchInput");
  const filterEstado = document.getElementById("filterEstado");
  const nuevoCliente = document.getElementById("nuevoCliente");
  const btnAdd = document.getElementById("btnAdd");
 
  
  function actualizarTablaClientes() {
  const filtro = searchInput.value.toLowerCase();
  const estadoFiltro = filterEstado.value;
  const filas = document.querySelectorAll("#clientesBody tr");

  filas.forEach(fila => {
    const nombre = fila.children[0].textContent.toLowerCase();
    const estado = fila.dataset.estado || "activo"; // Puedes incluir esto en PHP si lo deseas

    const coincideNombre = nombre.includes(filtro);
    const coincideEstado = !estadoFiltro || estado === estadoFiltro;

    fila.style.display = (coincideNombre && coincideEstado) ? "" : "none";
  });
}

  btnAdd.addEventListener("click", () => {
    const nombre = nuevoCliente.value.trim();
    if (nombre === "") return Swal.fire("Ingrese el nombre del cliente");

    fetch('/intranet/sistema/pages/panelAdministrativoClockify/Clientes/clientes_add.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({nombre})
    })
    .then(res => res.json())
    .then(resp => {
      if (resp.success) {
        nuevoCliente.value = "";
        Swal.fire({ icon: 'success', text: 'Cliente agregado correctamente', timer: 1500, showConfirmButton: false });
       
      } else {
        Swal.fire({ icon: 'error', text: resp.message });
      }
    });
  });

  searchInput.addEventListener("input", actualizarTablaClientes);
filterEstado.addEventListener("change", actualizarTablaClientes);
  
document.addEventListener("click", function(e) {
if (e.target.classList.contains("bi-pencil")) {
   
  const id = e.target.closest("tr").dataset.id;
   const timestamp1 = new Date().getTime(); 
  // Hacer consulta a la base de datos para obtener los datos actualizados
  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/Clientes/obtener_cliente.php?id_cliente=${id}&_=${timestamp1}`)
    .then(res => res.json())
    .then(cliente => {
     
      if (!cliente || !cliente.id_cliente) {
        Swal.fire("Error al cargar datos del cliente.");
        return;
      }

      // Rellenar valores del modal
      document.getElementById("editId").value = cliente.id_cliente;
      document.getElementById("editNombre").value = cliente.nombre_cliente;
      document.getElementById("editDireccion").value = cliente.direccion || '';
      document.getElementById("editMoneda").value = cliente.moneda;
      document.getElementById("editEstado").value = cliente.estado;
      document.getElementById("nuevaDuracionCliente").value = cliente.duracion_cliente;
      document.getElementById("nuevaPrecioCliente").value = cliente.precio_cliente;

      // Mostrar el modal
      const modal = new bootstrap.Modal(document.getElementById("modalEditarCliente"));
      modal.show();
    })
    .catch(err => {
      console.error("❌ Error al obtener cliente:", err);
      Swal.fire("Error al obtener datos del cliente.");
    });
}
if (e.target.classList.contains("bi-file-earmark-text")) {
    console.log("bi-file");
  clienteActivoId = e.target.closest("tr").dataset.id;
    console.log("clienteActivoId",clienteActivoId);
  if (clienteActivoId) {
    const timestamp = new Date().getTime(); // Forzar nueva consulta
    fetch(`/intranet/sistema/pages/panelAdministrativoClockify/Clientes/actividadesPorCliente.php?id_cliente=${clienteActivoId}&_=${timestamp}`)
      .then(res => res.text())
      .then(text => {
        console.log("📦 Respuesta cruda del servidor:", text); 

        let data;
        try {
          data = JSON.parse(text);
        } catch (e) {
          console.error("❌ No se pudo parsear el JSON:", e);
          return;
        }

        const tbody = document.getElementById("tablaActividadesCliente");
        tbody.innerHTML = "";

   data.forEach(act => {
  const duracionHoras = (parseFloat(act.duracion) / 3600).toFixed(2); // convertir segundos a horas

  tbody.innerHTML += `
    <tr data-id="${act.id}">
      <td><input class="form-control form-control-sm" value="${act.nombre_actividad}"></td>
      <td><input class="form-control form-control-sm" value="${act.descripcion}"></td>
      <td><input class="form-control form-control-sm" type="number" min="0" step="0.1" value="${duracionHoras}"></td>
      <td><input class="form-control form-control-sm" type="number" min="0" step="0.01" value="${act.precio}"> ${act.moneda}</td>
      <td>
        <button class="btn btn-sm btn-outline-success btnGuardarActividad">Guardar</button>
      </td>
    </tr>`;
});

        const modal = new bootstrap.Modal(document.getElementById("modalActividadesCliente"));
        modal.show();
      });
  }
}
   if (e.target.classList.contains("bi-three-dots-vertical")) {
    alert("Ver tres puntos del cliente");
  }


    
});
  document.addEventListener("click", function(e) {
    if (e.target.classList.contains("btnGuardarActividad")) {
      const row = e.target.closest("tr");
      const idActividad = row.dataset.id;
      const nombre = row.children[0].querySelector("input").value.trim();
      const descripcion = row.children[1].querySelector("input").value.trim();
      const duracion = row.children[2].querySelector("input").value;
      const precio = row.children[3].querySelector("input").value;

      fetch('/intranet/sistema/pages/panelAdministrativoClockify/Clientes/actualizar_actividad.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id: idActividad, nombre_actividad: nombre, descripcion, duracion, precio })
      })
      .then(res => res.json())
      .then(resp => {
      if (resp.success) {
    Swal.fire({
      icon: 'success',
      title: 'Actualizado',
      text: 'Actividad actualizada correctamente.',
      timer: 1200,
      showConfirmButton: false
    });

  } else {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: resp.message || 'Error desconocido al actualizar'
    });
  }
      })
      .catch(err => Swal.fire({ icon: 'error', title: 'Error', text: err.message }));
    } else if (e.target.id === "btnSaveEdit") {
  const id = document.getElementById("editId").value;
  const nombre = document.getElementById("editNombre").value.trim();
  const direccion = document.getElementById("editDireccion").value.trim();
  const moneda = document.getElementById("editMoneda").value;
  const estado = document.getElementById("editEstado").value;
  const duracion_cliente = document.getElementById("nuevaDuracionCliente").value;
  const precio_cliente = document.getElementById("nuevaPrecioCliente").value;

  if (nombre === "") {
    Swal.fire("El nombre del cliente es obligatorio.");
    return;
  }

  fetch('/intranet/sistema/pages/panelAdministrativoClockify/Clientes/clientes_update.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, nombre, direccion, moneda, estado, duracion_cliente, precio_cliente })
  })
  .then(res => res.json())
  .then(resp => {
    if (resp.success) {
      Swal.fire({
        icon: 'success',
        title: 'Guardado',
        text: 'Cliente actualizado correctamente.',
        timer: 1200,
        showConfirmButton: false
      });

      // 🔁 Cierra el modal
      bootstrap.Modal.getInstance(document.getElementById("modalEditarCliente")).hide();

      // 🔄 Actualiza visualmente la fila directamente en la tabla
      const fila = document.querySelector(`#clientesBody tr[data-id="${id}"]`);
      if (fila) {
        fila.children[0].textContent = nombre;
        fila.children[1].textContent = direccion;
        fila.children[2].textContent = moneda;
        fila.setAttribute('data-estado', estado);
      }
    } else {
      Swal.fire({ icon: 'error', text: resp.message || 'Error al actualizar cliente' });
    }
  })
  .catch(err => Swal.fire({ icon: 'error', text: err.message }));
}
  });

document.addEventListener("click", function (e) {
  if (e.target && e.target.id === "btnNuevaActividad") {
    if (clienteActivoId) {
      document.getElementById("nuevaActividadClienteId").value = clienteActivoId;
      document.getElementById("nuevaNombre").value = "";
      document.getElementById("nuevaDescripcion").value = "";
      document.getElementById("nuevaDuracion").value = "";
      document.getElementById("nuevaPrecio").value = "";

      const modal = new bootstrap.Modal(document.getElementById("modalNuevaActividad"));
      modal.show();
    }
  }
});

document.getElementById("btnGuardarNuevaActividad").addEventListener("click", () => {
  const idCliente = document.getElementById("nuevaActividadClienteId").value;
  const nombre = document.getElementById("nuevaNombre").value.trim();
  const descripcion = document.getElementById("nuevaDescripcion").value.trim();
  const duracion = document.getElementById("nuevaDuracion").value;
  const precio = document.getElementById("nuevaPrecio").value;

  if (!nombre || !precio) {
    Swal.fire("Nombre y precio son obligatorios");
    return;
  }

  fetch('/intranet/sistema/pages/panelAdministrativoClockify/Clientes/crear_actividad.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id_cliente: idCliente, nombre_actividad: nombre, descripcion, duracion, precio })
  })
  .then(res => res.json())
  .then(resp => {
    if (resp.success) {
      Swal.fire({
        icon: 'success',
        text: 'Actividad creada exitosamente',
        timer: 1200,
        showConfirmButton: false
      });
      bootstrap.Modal.getInstance(document.getElementById("modalNuevaActividad")).hide();
      document.querySelector(`.bi-file-earmark-text[data-id="${idCliente}"]`)?.click(); // refrescar actividades
    } else {
      Swal.fire("Error al crear actividad", resp.message || "", "error");
    }
  })
  .catch(err => Swal.fire("Error", err.message, "error"));
});




})();
     
</script>
