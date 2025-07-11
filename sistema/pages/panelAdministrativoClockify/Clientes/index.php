<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../../app/controllers/config.php');
include_once(__DIR__ . '/../../../analisisRecursos.php');

try {
    $stmtClientes = $pdo->query("SELECT id_cliente, nombre_cliente, direccion, moneda, estado, fyh_creacion FROM clientes ORDER BY nombre_cliente ASC");
    $clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<pre>Error: " . $e->getMessage() . "</pre>";
    exit;
}
?>

 <link rel="stylesheet" href="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?php echo $URL; ?>/librerias/vendor/npm-asset/bootstrap/dist/css/bootstrap.min.css">
 
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
  <h4 class="mb-3 fw-bold traducible">Clientes</h4>

  <div class="d-flex gap-2 mb-3">
    <select id="filterEstado" class="form-select w-auto">
      <option value="" class="traducible">Mostrar todos</option>
      <option value="activo" class="traducible">Mostrar activos</option>
      <option value="inactivo" class="traducible">Mostrar inactivos</option>
    </select>
    <input id="searchInput" type="text" class="form-control" placeholder="Search by name" style="max-width: 250px;">
    <div class="ms-auto d-flex gap-2">
      <input id="nuevoCliente" type="text" class="form-control" placeholder="Add new Client">
      <button class="btn btn-primary traducible" id="btnAdd">Agregar</button>
    </div>
  </div>

 <table class="table table-hover bg-white rounded shadow-sm">
    <thead>
      <tr>
        <th scope="col" class="traducible">Nombre</th>
        <th scope="col" class="traducible">Direccion</th>
        <th scope="col" class="traducible">Moneda</th>
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
            <i class="bi bi-people" style="cursor:pointer;" title="Más opciones"></i>
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
        <h5 class="modal-title traducible">Editar Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editId">
        <div class="mb-2">
          <label class="traducible">Nombre</label>
          <input type="text" id="editNombre" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="traducible">Direccion</label>
          <textarea id="editDireccion" class="form-control"></textarea>
        </div>
        <div class="mb-2">
          <label class="traducible">Moneda</label>
          <select id="editMoneda" class="form-select">
            <option value="USD">USD</option>
            <option value="COP">COP</option>
          </select>
        </div>
        <div class="mb-2">
          <label class="traducible">Estatus</label>
          <select id="editEstado" class="form-select">
            <option value="activo" class="traducible">Activo</option>
            <option value="inactivo" class="traducible">Inactivo</option>
          </select>
        </div>
        <div class="mb-2">
          <label class="traducible">Horas Trabajadas</label>
          <input type="number" id="nuevaDuracionCliente" class="form-control">
        </div>
        <div class="mb-2">
          <label class="traducible">Precio Horas</label>
          <input type="number"  id="nuevaPrecioCliente" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-link traducible" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary traducible" id="btnSaveEdit">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Actividades del Cliente -->
<div class="modal fade" id="modalActividadesCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title traducible">Clientes Actividades</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="d-flex justify-content-end mb-2">
  <button class="btn btn-primary btn-sm traducible" id="btnNuevaActividad">
    <i class="bi bi-plus-circle"></i> Nueva Actividad
  </button>
</div>
        <table class="table">
          <thead>
            <tr>
              <th class="traducible">Actividad</th>
              <th class="traducible">Descripción</th>
              <th class="traducible">Horas trabajadas</th>
              <th class="traducible">Precio Horas</th>
              <th class="traducible">Acción</th>
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
        <h5 class="modal-title traducible">Crear Nueva Actividad</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="nuevaActividadClienteId">
        <div class="mb-2">
          <label class="traducible">Nombre de Actividad</label>
          <input type="text" id="nuevaNombre" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="traducible">Descripción</label>
          <textarea id="nuevaDescripcion" class="form-control"></textarea>
        </div>
        <div class="mb-2">
          <label class="traducible">Horas trabajadas</label>
          <input type="number" id="nuevaDuracion" class="form-control">
        </div>
        <div class="mb-2">
          <label class="traducible">Precio Horas</label>
          <input type="number"  id="nuevaPrecio" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-link traducible" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary traducible" id="btnGuardarNuevaActividad">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal: Trabajadores del Cliente -->
<div class="modal fade" id="modalTrabajadoresCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title traducible">Trabajadores Asignados</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="trabajadorClienteId">
        <div class="mb-3">
          <input type="text" id="buscarTrabajador" class="form-control traducible" placeholder="Buscar trabajador por nombre...">
        </div>
        <div id="resultadosBusqueda"></div>
        <hr>
        <h6 class="traducible">Trabajadores Asignados</h6>
        <ul class="list-group" id="listaTrabajadoresAsignados"></ul>
      </div>
    </div>
  </div>
</div>
</div>

<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $URL; ?>/sistema/traduccionsistema.php"></script>

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
    const estado = fila.dataset.estado || "activo"; 

    const coincideNombre = nombre.includes(filtro);
    const coincideEstado = !estadoFiltro || estado === estadoFiltro;

    fila.style.display = (coincideNombre && coincideEstado) ? "" : "none";
  });
}

  btnAdd.addEventListener("click", () => {
    const nombre = nuevoCliente.value.trim();
    if (nombre === "") return Swal.fire("Ingrese el nombre del cliente");

    fetch(`/intranet/sistema/pages/panelAdministrativoClockify/Clientes/clientes_add.php?t=${new Date().getTime()}`, {
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
   
  // Hacer consulta a la base de datos para obtener los datos actualizados
  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/Clientes/obtener_cliente.php?id_cliente=${id}&t=${new Date().getTime()}`)
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
   
    fetch(`/intranet/sistema/pages/panelAdministrativoClockify/Clientes/actividadesPorCliente.php?id_cliente=${clienteActivoId}&t=${new Date().getTime()}`)
      .then(res => res.text())
      .then(text => {
       

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
        <button class="btn btn-sm btn-outline-success btnGuardarActividad "><p class="traducible">Guardar</p></button>
         <button class="btn btn-sm btn-outline-danger btnEliminarActividad "><p class="traducible">Eliminar</p></button>
      </td>
    </tr>`;
});

       window.traducirTextos(tbody);
        const modal = new bootstrap.Modal(document.getElementById("modalActividadesCliente"));
        modal.show();
      });
  }
}
   if (e.target.classList.contains("bi-people")) {
    const clienteId = e.target.closest("tr").dataset.id;
  document.getElementById("trabajadorClienteId").value = clienteId;
  
  // Cargar trabajadores asignados
  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/Clientes/trabajadores_asignados.php?id_cliente=${clienteId}&t=${new Date().getTime()}`)
    .then(res => res.json())
    .then(data => {
      const lista = document.getElementById("listaTrabajadoresAsignados");
      lista.innerHTML = "";
      data.forEach(t => {
        const li = document.createElement("li");
        li.className = "list-group-item d-flex justify-content-between align-items-center";
        li.textContent = t.nombre_completo;

        const btn = document.createElement("button");
        btn.className = "btn btn-danger btn-sm ";
        btn.innerHTML = `<p class="traducible">Eliminar</p>`;
        btn.onclick = () => eliminarTrabajador(clienteId, t.id_trabajador);

        li.appendChild(btn);
        lista.appendChild(li);
        
      });
    window.traducirTextos(lista);
      const modal = new bootstrap.Modal(document.getElementById("modalTrabajadoresCliente"));
      modal.show();
    });
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

      fetch(`/intranet/sistema/pages/panelAdministrativoClockify/Clientes/actualizar_actividad.php?t=${new Date().getTime()}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id: idActividad, nombre_actividad: nombre, descripcion, duracion, precio })
      })
        .then(res => {
    console.log('Status:', res.status);
    return res.text();
  })
.then(text => {
  console.log('RESPUESTA RAW:', text);
  let resp;
  try {
    resp = JSON.parse(text);
  } catch (err) {
    console.error('No es JSON válido:', err);
    return;
  }

  if (resp.success) {
    Swal.fire({ icon: 'success', title: 'Actualizado', text: 'Actividad actualizada correctamente.', timer: 1200, showConfirmButton: false });
  } else {
    Swal.fire({ icon: 'error', title: 'Error', text: resp.message || 'Error desconocido al actualizar' });
  }
})
  .catch(err => {
    console.error('Fetch falló:', err);
    Swal.fire('Error', err.message, 'error');
  });


   

 
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

  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/Clientes/clientes_update.php?t=${new Date().getTime()}`, {
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

  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/Clientes/crear_actividad.php?t=${new Date().getTime()}`, {
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

document.getElementById("buscarTrabajador").addEventListener("input", function () {
  const query = this.value.trim();
  const clienteId = document.getElementById("trabajadorClienteId").value;

  if (query.length < 3) return;

  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/Clientes/buscar_trabajadores.php?q=${query}&t=${new Date().getTime()}`)
    .then(res => res.json())
    .then(data => {
        console.log("data", data);
      const cont = document.getElementById("resultadosBusqueda");
      cont.innerHTML = "";

      data.forEach(t => {
        const div = document.createElement("div");
        div.className = "d-flex justify-content-between align-items-center border p-2 mb-1";
        div.innerHTML = `<span>${t.nombre_completo}</span>
          <button class="btn btn-success btn-sm traducible">Agregar</button>`;

       div.querySelector("button").onclick = () => {
  console.log(`✅ Click en agregar trabajador ${t.id} al cliente ${clienteId}`);

  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/Clientes/agregar_trabajador.php?t=${new Date().getTime()}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id_cliente: clienteId, id_trabajador: t.id })
  })
  .then(res => res.text())
  .then(text => {
    console.log("📦 Respuesta de agregar_trabajador:", text);
    const resp = JSON.parse(text);

    if (resp.success) {
  Swal.fire({
    icon: 'success',
    title: 'Agregado',
    text: 'Trabajador asignado correctamente.',
    timer: 1200,
    showConfirmButton: false
  });
  cargarTrabajadores(clienteId);
} else {
  Swal.fire("Error", resp.message, "error");
}
  })
  .catch(err => {
    console.error("❌ Error en agregar_trabajador:", err);
    Swal.fire("Error", err.message, "error");
  });
};

        cont.appendChild(div);
      });
    });
});
function cargarTrabajadores(clienteId) {
  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/Clientes/trabajadores_asignados.php?id_cliente=${clienteId}&t=${new Date().getTime()}`)
    .then(res => res.json())
    .then(data => {
        console.log("data", data);
      const lista = document.getElementById("listaTrabajadoresAsignados");
      lista.innerHTML = "";

      if (!Array.isArray(data) || data.length === 0) {
        lista.innerHTML = '<li class="list-group-item text-muted traducible">No hay trabajadores asignados.</li>';
        return;
      }

data.forEach(t => {
  const idTrabajador = t.id_trabajador ?? 'sin trabajadores';
  const nombre = t.nombre_completo ?? 'Sin nombre';

  const li = document.createElement("li");
  li.className = "list-group-item d-flex justify-content-between align-items-center";
  li.textContent = nombre;

  const btn = document.createElement("button");
  btn.className = "btn btn-danger btn-sm traducible";
  btn.textContent = "Eliminar";
  btn.onclick = () => eliminarTrabajador(clienteId, idTrabajador);

  li.appendChild(btn);
  lista.appendChild(li);
});
    })
    .catch(err => {
      console.error("❌ Error al cargar trabajadores:", err);
      Swal.fire("Error", "No se pudieron cargar los trabajadores", "error");
    });
}

function eliminarTrabajador(id_cliente, id_trabajador) {
    console.log("id_cliente", id_cliente);
     console.log("id_trabajador", id_trabajador);
  Swal.fire({
    title: '¿Estás seguro?',
    text: 'Este trabajador será eliminado del cliente.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      const bodyData = `id_cliente=${encodeURIComponent(id_cliente)}&id_trabajador=${encodeURIComponent(id_trabajador)}`;
      const url = `/intranet/sistema/pages/panelAdministrativoClockify/Clientes/eliminar_trabajador.php?t=${new Date().getTime()}`;
      
      console.log("📤 Enviando a eliminar_trabajador.php:", { url, body: bodyData });

      fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: bodyData
      })
      .then(res => res.text())
      .then(text => {
        console.log("📦 Respuesta cruda de eliminar_trabajador.php:", text);

        let resp;
        try {
          resp = JSON.parse(text);
        } catch (e) {
          console.error("❌ Error al parsear JSON:", e);
          Swal.fire("Error", "Respuesta inválida del servidor", "error");
          return;
        }

        if (resp.success) {
          Swal.fire({
            icon: 'success',
            title: 'Eliminado',
            text: 'Trabajador eliminado correctamente',
            timer: 1200,
            showConfirmButton: false
          });
          cargarTrabajadores(id_cliente);
        } else {
          Swal.fire("Error", resp.message || "No se pudo eliminar", "error");
        }
      })
      .catch(err => {
        console.error("❌ Error en la petición fetch:", err);
        Swal.fire("Error inesperado", err.message, "error");
      });
    }
  });
}

// justo después de cargar bootstrap.bundle.js
document.querySelectorAll('.modal').forEach(modalEl => {
  modalEl.addEventListener('hidden.bs.modal', () => {
    // remover backdrop sobrante
    document.querySelectorAll('.modal-backdrop').forEach(back => back.remove());
    // restaurar overflow en body
    document.body.classList.remove('modal-open');
  });
});


})();
     
</script>
