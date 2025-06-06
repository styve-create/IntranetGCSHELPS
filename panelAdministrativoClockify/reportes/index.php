<?php 
include_once(__DIR__ . '/../../../../app/controllers/config.php');
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
  #graficaBarrasContainer {
    max-height: 300px;
    overflow-x: auto;
    overflow-y: hidden;
  }

  .contenedor-tabla-grafico {
    min-height: 250px;
  }

  #graficaCircular {
    max-width: 100%;
    height: auto;
  }
</style>

<div id="contenido-principal">
    <div class="container-fluid p-4">
        <h4 class="mb-4">⏱️ Reporte de Tiempos</h4>

        <!-- Filtros: Sección para seleccionar los filtros de cliente, actividad y fechas -->
        <div class="row g-2 mb-4">
            <!-- Filtro de cliente -->
            <div class="col-md-2">
                <select id="filtroCliente" class="form-select form-select-sm">
                    <option value="">Cargando clientes...</option>
                </select>
            </div>

            <!-- Filtro de actividad -->
            <div class="col-md-2">
                <select id="filtroActividad" class="form-select form-select-sm">
                    <option value="all">Todas las actividades</option>
                </select>
            </div>

            <!-- Filtro de fecha de inicio -->
            <div class="col-md-2">
                <input type="date" id="fechaInicio" class="form-control form-control-sm" />
            </div>

            <!-- Filtro de fecha de fin -->
            <div class="col-md-2">
                <input type="date" id="fechaFin" class="form-control form-control-sm" />
            </div>

            <!-- Botón para aplicar los filtros -->
            <div class="col-md-2">
                <button id="btnFiltrar" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-funnel"></i> Aplicar Filtro
                </button>
            </div>
        </div>

        <!-- Gráfica de barras: Sección para mostrar la gráfica de barras -->
        <div id="graficaBarrasContainer" class="mb-4">
            <canvas id="graficaBarras"></canvas>
        </div>

        <!-- Contenedor para la tabla y gráfica circular -->
        <div class="row contenedor-tabla-grafico align-items-start">
            <!-- Tabla de registros: Muestra los datos dinámicos -->
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>💲 Cobro General</th>
                                <th>💲 Cobro Actividad</th>
                                <th>Usuario</th>
                                <th>Cliente</th>
                                <th>Actividad</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Fecha</th>
                                <th>Duración</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaResumen">
                            <!-- Aquí se llenan los registros dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Gráfico circular: Muestra la gráfica circular de actividades -->
            <div class="col-md-4 d-flex justify-content-center align-items-center">
                <canvas id="graficaCircular" width="150" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDICION -->
<div class="modal fade" id="modalEditarActividad" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">Editar Actividad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarActividad">
                    <input type="hidden" id="editar_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editar_usuario" class="form-label">Usuario</label>
                            <input type="text" id="editar_usuario" class="form-control" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="editar_cliente" class="form-label">Cliente</label>
                            <select id="editar_cliente" class="form-select" onchange="cargarActividadesClienteEditar(this.value)">
                                <option value="">Seleccione un cliente</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editar_actividad" class="form-label">Actividad</label>
                            <select id="editar_actividad" class="form-select">
                                <option value="">Seleccione una actividad</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="editar_inicio" class="form-label">Hora Inicio</label>
                            <input type="time" id="editar_inicio" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="editar_fin" class="form-label">Hora Fin</label>
                            <input type="time" id="editar_fin" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editar_fecha" class="form-label">Fecha</label>
                            <input type="date" id="editar_fecha" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="editar_cobrado" class="form-label">¿Factura Actividad?</label>
                            <select id="editar_cobrado" class="form-select">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="editar_cobradoGeneral" class="form-label">¿Factura General?</label>
                            <select id="editar_cobradoGeneral" class="form-select">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editar_descripcion" class="form-label">Descripción</label>
                        <textarea id="editar_descripcion" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
(function() {
    console.log("Script cargado correctamente");

  function editarRegistro(id) {
    const fila = document.querySelector(`#tablaResumen tr button[data-id='${id}']`).closest('tr');  // Cambié el selector para evitar el error
    document.getElementById('editar_id').value = id;

    // Rellenar los campos del modal con los datos de la fila seleccionada
    document.getElementById('editar_usuario').value = fila.children[2].textContent;
    const cliente = fila.children[3].textContent;
    const actividad = fila.children[4].textContent;

    // Cargar clientes
    fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportes/get_clientes.php?t=${new Date().getTime()}`)
        .then(res => res.json())
        .then(clientes => {
            const selectCliente = document.getElementById('editar_cliente');
            selectCliente.innerHTML = '<option value="">Seleccione un cliente</option>';
            clientes.forEach(c => {
                const option = document.createElement('option');
                option.value = c.id_cliente;
                option.textContent = c.nombre_cliente;
                if (c.nombre_cliente === cliente) option.selected = true;

                // Depuración: Verificar lo que se está añadiendo al select
                console.log("Añadiendo cliente:", c.nombre_cliente);
                selectCliente.appendChild(option);
            });

            // Obtener id del cliente seleccionado
            const clienteSeleccionado = clientes.find(c => c.nombre_cliente === cliente);
            const idCliente = clienteSeleccionado ? clienteSeleccionado.id_cliente : '';
            cargarActividadesClienteEditar(idCliente, actividad);
        });

    document.getElementById('editar_inicio').value = formatearHora(fila.children[5].textContent);
    document.getElementById('editar_fin').value = formatearHora(fila.children[6].textContent);
    document.getElementById('editar_fecha').value = fila.children[7].textContent;

    // Configurar los valores de cobrado
    document.getElementById('editar_cobrado').value = fila.children[1].querySelector('i') ? '1' : '0';
    document.getElementById('editar_cobradoGeneral').value = fila.children[0].querySelector('i') ? '1' : '0';

    // La descripción no está en la tabla, por lo tanto necesitas otra forma de traerla
    document.getElementById('editar_descripcion').value = fila.dataset.descripcion || '';

    // Mostrar el modal de edición
    const modal = new bootstrap.Modal(document.getElementById('modalEditarActividad'));
    modal.show();
}

function formatearHora(horaTexto) {
    if (!horaTexto) return '';
    const date = new Date(`1970-01-01T${horaTexto}`);
    const horas = String(date.getHours()).padStart(2, '0');
    const minutos = String(date.getMinutes()).padStart(2, '0');
    return `${horas}:${minutos}`;
}

function cargarActividadesClienteEditar(idCliente, actividadSeleccionada = '') {
    fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportes/get_actividades.php?cliente_id=${encodeURIComponent(idCliente)}&t=${new Date().getTime()}`)
        .then(res => res.json())
        .then(actividades => {
            const selectActividad = document.getElementById('editar_actividad');
            selectActividad.innerHTML = '<option value="">Seleccione una actividad</option>';
            actividades.forEach(a => {
                const option = document.createElement('option');
                option.value = a.nombre_actividad;
                option.textContent = a.nombre_actividad;

                // Depuración: Verificar el contenido de las actividades
                console.log("Añadiendo actividad:", a.nombre_actividad);
                if (a.nombre_actividad === actividadSeleccionada) option.selected = true;
                selectActividad.appendChild(option);
            });
        });
}

    // Evento para aplicar los filtros
    document.getElementById('btnFiltrar').addEventListener('click', () => {
        const cliente = document.getElementById('filtroCliente').value;
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;

        const actividadSelect = document.getElementById('filtroActividad');
        const selectedOptions = Array.from(actividadSelect.selectedOptions).map(opt => opt.value);
        const actividadesParam = selectedOptions.includes('all') || selectedOptions.length === 0
            ? ''
            : selectedOptions.join(',');

        // Mostrar los filtros seleccionados en la consola
        console.log("Cliente:", cliente);
        console.log("Actividades seleccionadas:", selectedOptions);
        console.log("Param actividades:", actividadesParam);
        console.log("Inicio:", fechaInicio, "Fin:", fechaFin);

        fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportes/api_reporte.php?cliente=${cliente}&actividades=${encodeURIComponent(actividadesParam)}&inicio=${fechaInicio}&fin=${fechaFin}&t=${new Date().getTime()}`)
            .then(res => res.json())
            .then(data => {
                renderTabla(data.registros);
                renderGraficaBarras(data.barras);
                renderGraficaCircularData(data.porActividad, data.totalHoras);
            });
    });

    // Función para renderizar la tabla con los registros
    function renderTabla(registros) {
        const tbody = document.getElementById('tablaResumen');
        tbody.innerHTML = '';

        registros.forEach((r, i) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${r.cobrado_general == 1 ? '<i class="bi bi-currency-dollar text-success"></i>' : ''}</td>
                <td>${r.cobrado == 1 ? '<i class="bi bi-currency-dollar text-success"></i>' : ''}</td>
                <td>${r.usuario}</td>
                <td>${r.cliente}</td>
                <td>${r.actividad}</td>
                <td>${r.hora_inicio}</td>
                <td>${r.hora_fin}</td>
                <td>${r.fecha}</td>
                <td><strong>${r.duracion}</strong></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="editarRegistro(${r.id})">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Función para renderizar la gráfica de barras
    function renderGraficaBarras(datos) {
        const total = datos.reduce((acc, d) => acc + d.horas, 0);
        const ctx = document.getElementById('graficaBarras').getContext('2d');
        if (window.barChart) window.barChart.destroy();

        window.barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: datos.map(d => d.fecha),
                datasets: [{
                    label: `Horas trabajadas (Total: ${total.toFixed(2)}h)`,
                    data: datos.map(d => d.horas),
                    backgroundColor: 'rgba(75,192,192,0.5)',
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }

    // Función para renderizar la gráfica circular
    function renderGraficaCircularData(actividades, total) {
        const ctx = document.getElementById('graficaCircular').getContext('2d');
        if (window.pieChart) window.pieChart.destroy();

        const labels = actividades.map(a => `${a.actividad} (${a.horas}h)`);
        const data = actividades.map(a => a.horas);
        const colores = actividades.map(() =>
            `hsl(${Math.floor(Math.random() * 360)}, 70%, 60%)`
        );

        window.pieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: colores
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: `Total: ${total.toFixed(2)} h`
                    },
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });
    }
document.getElementById('filtroCliente').addEventListener('change', function () {
  const clienteId = this.value;
  const actividadSelect = document.getElementById('filtroActividad');
  actividadSelect.innerHTML = '<option value="all">Todas las actividades</option>';

  fetch(/intranet/sistema/pages/panelAdministrativoClockify/reportes/get_actividades.php?cliente_id=${clienteId}&t=${new Date().getTime()})
    .then(res => res.json())
    .then(data => {
    data.forEach(actividad => {
  const option = document.createElement('option');
  option.value = actividad.nombre_actividad;
  option.textContent = actividad.nombre_actividad;
  actividadSelect.appendChild(option);
});
    })
    .catch(err => {
      actividadSelect.innerHTML = '<option value="all">Error al cargar actividades</option>';
      console.error('Error al obtener actividades:', err);
    });
});
const filtroCliente = document.getElementById('filtroCliente');

filtroCliente.addEventListener('click', () => {
  const selectedValue = filtroCliente.value;

  fetch(/intranet/sistema/pages/panelAdministrativoClockify/reportes/get_clientes.php?t=${new Date().getTime()})
    .then(res => res.json())
    .then(data => {
      filtroCliente.innerHTML = '<option value="">Cliente</option>';
      data.forEach(cliente => {
        const option = document.createElement('option');
        option.value = cliente.id_cliente;
        option.textContent = cliente.nombre_cliente;
        filtroCliente.appendChild(option);
      });
      filtroCliente.value = selectedValue; // restaurar selección
    })
    .catch(err => {
      filtroCliente.innerHTML = '<option value="">Error al cargar</option>';
      console.error('Error al obtener clientes:', err);
    });
});
document.getElementById('formEditarActividad').addEventListener('submit', function(e) {
  e.preventDefault();

  const cobrado = document.getElementById('editar_cobrado').value;
  const cobradoGeneral = document.getElementById('editar_cobradoGeneral').value;

  // 🚫 Validar que no estén ambos en "1"
  if (cobrado === '1' && cobradoGeneral === '1') {
    Swal.fire({
      icon: 'warning',
      title: '❌ No permitido',
      text: 'Solo puedes marcar uno de los cobros como "Sí": Actividad o General.',
      confirmButtonText: 'Entendido'
    });
    return;
  }

  const datos = {
    id: document.getElementById('editar_id').value,
    cliente: document.getElementById('editar_cliente').value,
    actividad: document.getElementById('editar_actividad').value,
    hora_inicio: document.getElementById('editar_inicio').value,
    hora_fin: document.getElementById('editar_fin').value,
    fecha: document.getElementById('editar_fecha').value,
    cobrado: cobrado,
    cobradoGeneral: cobradoGeneral,
    descripcion: document.getElementById('editar_descripcion').value,
  };

  // Confirmación previa con SweetAlert
  Swal.fire({
    title: '¿Guardar cambios?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, guardar',
    cancelButtonText: 'Cancelar'
  }).then(result => {
    if (result.isConfirmed) {
      fetch(/intranet/sistema/pages/panelAdministrativoClockify/reportes/actualizar_actividad.php?t=${new Date().getTime()}, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
      })
      .then(res => res.json())
      .then(resp => {
        if (resp.success) {
          Swal.fire({
            icon: 'success',
            title: 'Actividad actualizada',
            showConfirmButton: false,
            timer: 1200
          });

          bootstrap.Modal.getInstance(document.getElementById('modalEditarActividad')).hide();

          // Volver a aplicar filtros y recargar la tabla
          const cliente = document.getElementById('filtroCliente').value;
          const fechaInicio = document.getElementById('fechaInicio').value;
          const fechaFin = document.getElementById('fechaFin').value;
          const actividadSelect = document.getElementById('filtroActividad');
          const selectedOptions = Array.from(actividadSelect.selectedOptions).map(opt => opt.value);
          const actividadesParam = selectedOptions.includes('all') || selectedOptions.length === 0
            ? ''
            : selectedOptions.join(',');

          fetch(/intranet/sistema/pages/panelAdministrativoClockify/reportes/api_reporte.php?cliente=${cliente}&actividades=${encodeURIComponent(actividadesParam)}&inicio=${fechaInicio}&fin=${fechaFin}&t=${new Date().getTime()})
            .then(res => res.json())
            .then(data => {
              renderTabla(data.registros);
              renderGraficaBarras(data.barras);
              renderGraficaCircularData(data.porActividad, data.totalHoras);
            });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error al guardar',
            text: resp.message
          });
        }
      })
      .catch(err => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudo guardar. Revisa la consola para más detalles.'
        });
        console.error('Error al guardar:', err);
      });
    }
  });
});
})();

</script>