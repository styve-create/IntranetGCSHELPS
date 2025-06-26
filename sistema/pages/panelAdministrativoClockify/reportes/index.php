<?php 
include_once(__DIR__ . '/../../../../app/controllers/config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}
$id_usuario = $_SESSION['usuario_info']['id'] ?? null;
?>
<link rel="stylesheet" href="<?= $URL; ?>/librerias/vendor/npm-asset/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?php echo $URL; ?>/librerias/vendor/npm-asset/bootstrap/dist/css/bootstrap.min.css">

<style>
:root {
  --step-1: clamp(0.3686rem, 0.6197rem + -0.3249vw, 0.5549rem);
  --step-2: clamp(0.4608rem, 0.6812rem + -0.2843vw, 0.6243rem);
  --step-3: clamp(0.576rem, 0.775rem + -0.234vw, 0.725rem);
  --step-4: clamp(0.720rem, 0.900rem + -0.180vw, 0.900rem);
}

  #graficaBarrasContainer {
    max-height: 300px;
    overflow-x: auto;
    overflow-y: hidden;
  }
  #graficaBarrasContainer .card-title {
  font-size: var(--step-4);
  /* puedes también ajustar márgenes, peso, color, etc. */
}

/* Botón de exportar */
#graficaBarrasContainer #exportarPDF {
  font-size: var(--step-3);
}

/* Los spans con totales */
#graficaBarrasContainer .d-flex span {
  font-size: var(--step-2);
}

/* Si quisieras un texto más pequeño */
#graficaBarrasContainer .d-flex span strong {
  font-size: var(--step-1);
}

  .contenedor-tabla-grafico {
    min-height: 250px;
  }
  
#tablaContenedor .table-responsive {
  overflow-x: visible;
}
 
#horasChart {
  height: 250px;
  max-height: none;
}


</style>

<div id="contenido-principal">
            <div class="container-fluid p-4">
                <h4 class="mb-4"><strong >Reporte de Tiempos</strong></h4>
                <div id="app" data-id-usuario="<?= htmlspecialchars($id_usuario) ?>"></div>
                <!-- Filtros: Sección para seleccionar los filtros de cliente, actividad y fechas -->
                <div class="row g-2 mb-4">
                    <!-- Filtro de cliente -->
                    <div class="col-md-2"><select id="filtroCliente" class="form-select form-select-sm"><option value="">Cargando clientes...</option></select></div>
                    <!-- Filtro de actividad -->
                    <div class="col-md-2"><select id="filtroActividad" class="form-select form-select-sm"><option value="all">Todas las actividades</option></select></div>
                    <!-- Filtro de fecha de inicio -->
                    <div class="col-md-2"><input type="date" id="fechaInicio" class="form-control form-control-sm" /></div>
                    <!-- Filtro de fecha de fin -->
                    <div class="col-md-2"><input type="date" id="fechaFin" class="form-control form-control-sm" /></div>
                    <!-- Botón para aplicar los filtros -->
                    <div class="col-md-2"><button id="btnFiltrar" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel"></i> Aplicar Filtro</button></div>
                </div>
        
                <!-- Gráfica de barras: Sección para mostrar la gráfica de barras -->
                <div id="graficaBarrasContainer" class="card mb-4 border-0 shadow-none">
                    <div class="card card border-0 shadow-none">
                         <div class="d-flex justify-content-between align-items-center">
                          <h6 class="card-title mb-0"><strong >Resumen de Horas y Cobro</strong></h6>
                          <button id="exportarPDF" class="btn btn-danger btn-md"> <i class="bi bi-file-earmark-pdf-fill me-1"></i>PDF</button>
                        </div>
                        <br>
                            <div class="d-flex justify-content-between">
                                <span>Total de horas: <strong id="totalHoras"></strong></span>
                                <span>Total a cobrar: <strong id="totalCobro"> </strong> <strong class ="MonedaCliente"> </strong> </span>
                                <span>Total cobrar General: <strong id="totalCobroGeneral"> </strong> <strong class ="MonedaCliente"> </strong> </span>
                                <span>Total cobrar Actitividad: <strong id="totalCobroActividad"> </strong> <strong class ="MonedaCliente"> </strong> </span>
                            </div>
                            
                    </div>
                    <br>
                     <div class="card card border-0 shadow-none"><canvas id="horasChart" style="max-width:auto; height:300px;"></canvas></div>
                    <div id="status-certificados" class="mt-2"></div>         
              </div>
        
               
             
                    <!-- Tabla de registros: Muestra los datos dinámicos -->
                    <div id="tablaContenedor" class="col-md-10 d-none">
                        <div class="table-responsive">
                            <table class="table  table-bordered text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Duración</th>
                                        <th>Ver Detalle</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaResumenTotal">
                                    <!-- Aquí se llenan los registros dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                
                    
           </div>
                            
                <!-- Modal Detalle Actividades -->
                    <div class="modal fade" id="modalDetalleActividades" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
                      <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">
                              Detalle de Actividades de <span id="detalleUsuario"></span>
                            </h5>
                           
                            <button id="btnAgregarActividad" class="btn btn-sm btn-success me-2">
                              <i class="bi bi-plus-circle"></i> Agregar actividad
                            </button>
                            
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <table id="tablaDetalle" class="table table-sm table-bordered text-center align-middle">
                              <thead class="table-light">
                                <tr>
                                  <th>Usuario</th><th>Actividad</th><th>Fecha</th><th>Duración</th><th>Acciones</th>
                                </tr>
                              </thead>
                              <tbody id="tablaDetalleBody"><!-- ¡Aquí vamos a volcar filas dinámicamente! --></tbody>
                            </table>
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
                                            <select id="editar_cliente" class="form-select">
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
                <!-- Nuevo modal para crear -->
                            <div class="modal fade" id="modalCrearActividad" tabindex="-1">
                              <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Crear nueva actividad</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>
                                         <div class="modal-body">
                                            <form id="formCrearActividad">
                                              <!-- Usuario (autocompletado) -->
                                              <div class="row mb-3">
                                                <div class="col-md-6">
                                                  <label for="crear_usuario" class="form-label">Usuario</label>
                                                  <input type="text" id="crear_usuario" class="form-control" disabled>
                                                </div>
                                                <div class="col-md-6">
                                                  <label for="crear_cliente" class="form-label">Cliente</label>
                                                  <select id="crear_cliente" class="form-select">
                                                    <option value="">Cargando clientes...</option>
                                                  </select>
                                                </div>
                                              </div>
                                              <!-- Actividad y horas -->
                                              <div class="row mb-3">
                                                <div class="col-md-6">
                                                  <label for="crear_actividad" class="form-label">Actividad</label>
                                                  <select id="crear_actividad" class="form-select">
                                                    <option value="">Seleccione un cliente primero</option>
                                                  </select>
                                                </div>
                                                <div class="col-md-3">
                                                  <label for="crear_inicio" class="form-label">Hora Inicio</label>
                                                  <input type="time" id="crear_inicio" class="form-control">
                                                </div>
                                                <div class="col-md-3">
                                                  <label for="crear_fin" class="form-label">Hora Fin</label>
                                                  <input type="time" id="crear_fin" class="form-control">
                                                </div>
                                              </div>
                                              <!-- Fecha y facturación -->
                                              <div class="row mb-3">
                                                <div class="col-md-4">
                                                  <label for="crear_fecha" class="form-label">Fecha</label>
                                                  <input type="date" id="crear_fecha" class="form-control">
                                                </div>
                                                <div class="col-md-4">
                                                  <label for="crear_cobrado" class="form-label">¿Factura Actividad?</label>
                                                  <select id="crear_cobrado" class="form-select">
                                                    <option value="1">Sí</option>
                                                    <option value="0">No</option>
                                                  </select>
                                                </div>
                                                <div class="col-md-4">
                                                  <label for="crear_cobradoGeneral" class="form-label">¿Factura General?</label>
                                                  <select id="crear_cobradoGeneral" class="form-select">
                                                    <option value="1">Sí</option>
                                                    <option value="0">No</option>
                                                  </select>
                                                </div>
                                              </div>
                                              <!-- Descripción y submit -->
                                              <div class="mb-3">
                                                <label for="crear_descripcion" class="form-label">Descripción</label>
                                                <textarea id="crear_descripcion" class="form-control" rows="3"></textarea>
                                              </div>
                                              <div class="text-end">
                                                <button type="submit" class="btn btn-primary">Guardar actividad</button>
                                              </div>
                                            </form>
                                          </div>
                                </div>
                              </div>
                            </div>
    </div>

 

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/jquery/dist/jquery.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="<?= $URL; ?>/librerias/vendor/npm-asset/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="<?= $URL; ?>/librerias/vendor/npm-asset/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $URL; ?>/librerias/vendor/npm-asset/chart.js/dist/chart.umd.js"></script>
<script src="<?= $URL; ?>/librerias/vendor/npm-asset/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>



<script>
(function() {
    console.log("Script cargado correctamente");

    // Variables para manipular elementos en el DOM
    const filtroClienteElement = document.getElementById('filtroCliente');
    const filtroActividadElement = document.getElementById('filtroActividad');
    const btnFiltrarElement = document.getElementById('btnFiltrar');
   
     const tablaResumenTotalElement = document.getElementById('tablaResumenTotal');
    let registrosData = [];
    let tablaDT;
    let detalleDT;
    let clientesCargados = [];
    
    const ID_USUARIO = document.getElementById('app')?.dataset.idUsuario || null;

 
    
    // Función para editar registro
window.editarRegistro = function(id) {
  const modalEl = document.getElementById('modalEditarActividad');
  if (!modalEl) {
    console.error('modalEditarActividad no existe');
    return;
  }

  // 1) Guardamos el id
  document.getElementById('editar_id').value = id;

  // 2) Buscamos el registro completo en registrosData
  const act = registrosData.find(r => r.id == id);
  if (!act) {
    console.error('Registro no encontrado en registrosData:', id);
    return;
  }
  const clienteId = act.cliente_id;
  const userId    = act.usuario_id;
  console.log("clienteId", clienteId);
  console.log("userId", userId);

  // 3) Rellenamos los campos básicos
  document.getElementById('editar_usuario').value         = act.usuario         || '';
  document.getElementById('editar_inicio').value         = act.hora_inicio     || '';
  document.getElementById('editar_fin').value            = act.hora_fin        || '';
  document.getElementById('editar_fecha').value          = act.fecha           || '';
  document.getElementById('editar_cobrado').value        = act.cobrado         || '0';
  document.getElementById('editar_cobradoGeneral').value = act.cobrado_general || '0';
  document.getElementById('editar_descripcion').value    = act.descripcion     || '';

 
  //    A) Cargo clientes usando el userId del registro
  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportes/get_clientes.php?user_id=${encodeURIComponent(userId)}&t=${Date.now()}`)
    .then(res => {
      if (!res.ok) throw new Error('Error al cargar clientes');
      return res.json();
    })
    .then(data => {
        
     
      const clientes = Array.isArray(data) ? data : data.clientes;
      if (!Array.isArray(clientes)) throw new Error('Formato inesperado de clientes');

      const selCli = document.getElementById('editar_cliente');
      selCli.innerHTML = '<option value="">Seleccione un cliente</option>';
      clientes.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id_cliente;
        opt.textContent = c.nombre_cliente;
        selCli.appendChild(opt);
      });
      // bindeo el cliente del registro
      selCli.value = clienteId;

      return clienteId;
    })
    // B) Cargo actividades del cliente recién seleccionado
    .then(clienteId => {
      return fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportes/get_actividades.php?cliente_id=${encodeURIComponent(clienteId)}&t=${Date.now()}`)
        .then(res => {
          if (!res.ok) throw new Error('Error al cargar actividades');
          return res.json();
        });
    })
    .then(data => {
      // detecto si viene array o viene { actividades: […] }
      const actividades = Array.isArray(data) ? data : data.actividades;
      if (!Array.isArray(actividades)) throw new Error('Formato inesperado de actividades');

      const selAct = document.getElementById('editar_actividad');
      selAct.innerHTML = '<option value="">Seleccione una actividad</option>';
      actividades.forEach(a => {
        const opt = document.createElement('option');
        opt.value = a.nombre_actividad;
        opt.textContent = a.nombre_actividad;
        selAct.appendChild(opt);
      });
      // bindeo la actividad del registro
      selAct.value = act.actividad;
    })
    // C) Al final, muestro el modal
    .then(() => {
      new bootstrap.Modal(modalEl).show();
    })
    .catch(err => {
      console.error('✖️ editarRegistro:', err);
      Swal.fire('Error', err.message, 'error');
    });
};



    // Función para formatear hora
    function formatearHora(horaTexto) {
        if (!horaTexto) return '';
        const date = new Date(`1970-01-01T${horaTexto}`);
        const horas = String(date.getHours()).padStart(2, '0');
        const minutos = String(date.getMinutes()).padStart(2, '0');
        return `${horas}:${minutos}`;
    }

    // Función para cargar actividades al editar
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

                    console.log("Añadiendo actividad:", a.nombre_actividad);
                    if (a.nombre_actividad === actividadSeleccionada) option.selected = true;
                    selectActividad.appendChild(option);
                });
            });
    }
    const editarClienteSelect = document.getElementById('editar_cliente');
if (editarClienteSelect) {
  editarClienteSelect.addEventListener('change', function() {
    // Cuando cambie el cliente, recarga las actividades
    cargarActividadesClienteEditar(this.value);
  });
}

    // Evento para aplicar los filtros
    btnFiltrarElement.addEventListener('click', () => {
        document.getElementById('tablaContenedor').classList.add('d-none');
        const clienteId = filtroClienteElement.value;
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;

        const selectedOptions = Array.from(filtroActividadElement.selectedOptions).map(opt => opt.value);
        const actividadesParam = selectedOptions.includes('all') || selectedOptions.length === 0
            ? ''
            : selectedOptions.join(',');

        fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportes/api_reporte.php?cliente=${clienteId}&actividades=${encodeURIComponent(actividadesParam)}&inicio=${fechaInicio}&fin=${fechaFin}&t=${new Date().getTime()}`)
            .then(res => res.json())
            .then(data => {
                console.log("data", data);
               
                renderTablaRegistros(data.registros)
                registrosData = (data.registros);
                console.log("registrosData",registrosData);
                renderGraficaBarras(data.barras);
                document.getElementById('tablaContenedor').classList.remove('d-none');
               
                // Actualizar los totales
                const totalCobro = data.totalCobroGeneral + data.totalCobroActividad;
                document.getElementById("totalCobro").textContent = totalCobro.toFixed(2);
                document.getElementById("totalHoras").textContent = data.totalHoras ? data.totalHoras.toFixed(2) : "0.00";
                document.getElementById("totalCobroGeneral").textContent = data.totalCobroGeneral ? data.totalCobroGeneral.toFixed(2) : "0.00";
                document.getElementById("totalCobroActividad").textContent = data.totalCobroActividad ? data.totalCobroActividad.toFixed(2) : "0.00";
            });
    });
 
    // Función para renderizar la tabla con los registros
 // Instancia de DataTable



function hmsA_Segundos(hms) {
  const [h, m, s] = hms.split(':').map(Number);
  return h*3600 + m*60 + s;
}

// 2) Agrupa por usuario y devuelve la duración en horas decimales
function agruparPorUsuario(registros) {
  const mapa = {};
  registros.forEach(r => {
    const user = r.usuario;
    const seg = hmsA_Segundos(r.duracion);
    mapa[user] = (mapa[user] || 0) + seg;
  });
  return Object.entries(mapa).map(([usuario, totalSeg]) => ({
    usuario,
    // duración en horas decimales con 2 dígitos
    duracionDecimal: (totalSeg / 3600).toFixed(2)
  }));
}

// 3) Renderiza la tabla con un renglón por usuario y la duración en decimales
function renderTablaRegistros(registros) {
  const $table = $('#tablaResumenTotal').closest('table');

  // 0) si ya hay DataTable, destrúyela primero:
  if ( $.fn.DataTable.isDataTable( $table ) ) {
    $table.DataTable().destroy();
  }

  // 1) vacía el <tbody>
  const tbody = document.getElementById('tablaResumenTotal');
  tbody.innerHTML = '';

  // 2) inyecta filas nuevas
  const resumen = agruparPorUsuario(registros);
  resumen.forEach(r => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${r.usuario}</td>
      <td><strong>${r.duracionDecimal} h</strong></td>
      <td>
        <button class="btn btn-sm btn-outline-primary btn-ver-detalle">
          Ver detalle
        </button>
      </td>`;
    tbody.appendChild(tr);
  });

  // 3) inicializa de nuevo DataTable
 tablaDT = $table.DataTable({
  paging:       false,
  searching:    true,    // ← ahora activado
  info:         false,
  ordering:     true,
  lengthChange: false,
  autoWidth:    false,
  responsive:   true
});

  // 4) vuelve a delegar el click
  $('#tablaResumenTotal')
    .off('click', '.btn-ver-detalle')
    .on('click', '.btn-ver-detalle', function() {
      const usuario = $(this).closest('tr').find('td').first().text();
      mostrarModalDetalle(usuario);
      
    });
}

function mostrarModalDetalle(usuario) {
  // 1) Ponemos el nombre arriba
  document.getElementById('detalleUsuario').textContent = usuario;

  // 2) Filtramos los registros
  const detalles = registrosData.filter(r => r.usuario === usuario);

  // 3) Si aún no está inicializado, lo creamos
  if (!detalleDT) {
    detalleDT = $('#tablaDetalle').DataTable({
      paging:       true,
      searching:    false,
      info:         false,
      ordering:     true,
      lengthChange: false,
      responsive:   true,
      columnDefs:   [{ orderable: false, targets: -1 }]
    });

    // Delegamos eventos SOLO 1 vez
    $('#tablaDetalle')
      .on('click', '.btn-editar', function() {
        editarRegistro(this.dataset.id);
      })
       .off('click', '.btn-eliminar')
          .on('click', '.btn-eliminar', function() {
            const id = this.dataset.id;
            Swal.fire({
              title: '¿Seguro que quieres eliminar esta actividad?',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Sí, eliminar',
              cancelButtonText: 'Cancelar'
            }).then(result => {
              if (!result.isConfirmed) return;
        
              // 1) Llamada al servidor
              fetch('/intranet/sistema/pages/panelAdministrativoClockify/reportes/eliminar_actividad.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
              })
              .then(res => res.json())
           .then(resp => {
  if (!resp.success) throw new Error(resp.message || 'Error al eliminar');

  // 2) Actualiza tu array en memoria
  registrosData = registrosData.filter(r => r.id != id);

  // 3) Cierra el modal actual y elimina el backdrop
  const modalEl = document.getElementById('modalDetalleActividades');
  const modalInstance = bootstrap.Modal.getInstance(modalEl);
  modalInstance.hide();
  document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
  document.body.classList.remove('modal-open');

  // 4) Espera un momento y vuelve a abrirlo con los datos actualizados
  const usuarioActual = document.getElementById('detalleUsuario').textContent;
  setTimeout(() => {
    mostrarModalDetalle(usuarioActual);
    renderTablaRegistros(registrosData);
  }, 200);

  Swal.fire('Eliminado', 'La actividad ha sido borrada.', 'success');
})
              .catch(err => {
                console.error('Error al eliminar actividad:', err);
                Swal.fire('Error', err.message, 'error');
              });
            });
          });
  }

  // 4) Limpiamos y añadimos nuevas filas
  detalleDT.clear();
  detalles.forEach(r => {
    detalleDT.row.add([
      r.usuario,
      r.actividad,
      r.fecha,
      `<strong>${r.duracion}</strong>`,
      `<button class="btn btn-sm btn-outline-primary btn-editar" data-id="${r.id}">
         <i class="bi bi-pencil-square"></i>
       </button>
       <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${r.id}">
         <i class="bi bi-trash"></i>
       </button>`
    ]);
  });
  detalleDT.draw();

  // 5) Mostramos el modal
  new bootstrap.Modal(document.getElementById('modalDetalleActividades')).show();
}

    // Función para renderizar la gráfica de barras
function renderGraficaBarras(datos) {
  if (!Array.isArray(datos) || datos.length === 0) return;

  // ✅ 1) Ordenar por fecha ascendente
  datos.sort((a, b) => new Date(a.fecha) - new Date(b.fecha));

  // ✅ 2) Formatear etiquetas y valores
  const labels = datos.map(d => {
    const [yyyy, mm, dd] = d.fecha.split('-');
    const dt = new Date(yyyy, mm - 1, dd);
    return dt.toLocaleDateString('en-US', {
      weekday: 'short',
      month: 'short',
      day: 'numeric'
    });
  });

  const values = datos.map(d => d.horas);
  const maxValor = Math.max(...values);

  // ✅ 3) Definir altura extra dinámica (agrega 2 líneas)
  const maxY = Math.ceil(maxValor + 2); // 2 horas extra
  const stepY = maxY >= 12 ? 2 : 1; // si es muy grande, más espacio entre líneas

  // ✅ 4) Preparar canvas
  const canvas = document.getElementById('horasChart');
  const ctx = canvas.getContext('2d');

  if (window.barChart) {
    window.barChart.destroy();
    canvas.removeAttribute('width');
    canvas.removeAttribute('height');
  }

  // ✅ 5) Crear gráfico
  window.barChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        data: values,
        backgroundColor: '#8BC34A',
        borderRadius: 6
      }]
    },
    options: {
      responsive: false,
      maintainAspectRatio: false,
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: '#666' },
          categoryPercentage: 0.6,
          barPercentage: 0.9
        },
        y: {
          beginAtZero: true,
          max: maxY,
          ticks: {
            stepSize: stepY,
            callback: v => `${v}h`,
            color: '#666'
          },
          grid: {
            color: '#bbb',
            borderDash: [2, 4]
          }
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => `${ctx.parsed.y.toFixed(2)}h`
          }
        },
        datalabels: {
          anchor: 'end',
          align: 'end',
          offset: 4,
          color: '#444',
          font: { weight: 'bold' },
          formatter: value => {
            const h = Math.floor(value);
            const m = Math.round((value - h) * 60);
            return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}h`;
          }
        }
      }
    },
    plugins: [ChartDataLabels]
  });
}

function calcularDuracion(horaInicio, horaFin) {
  const parseHms = s => {
    const parts = s.split(':').map(Number);
    if (parts.length === 2) parts.push(0); // [h, m] → [h, m, 0]
    return parts;
  };

  const [h1, m1, s1] = parseHms(horaInicio);
  const [h2, m2, s2] = parseHms(horaFin);
  let inicioSeg = h1 * 3600 + m1 * 60 + s1;
  let finSeg    = h2 * 3600 + m2 * 60 + s2;

  // Si fin < inicio, asumimos pasó a la noche siguiente
  if (finSeg < inicioSeg) finSeg += 24 * 3600;

  let diff = finSeg - inicioSeg;
  const h = Math.floor(diff / 3600); diff %= 3600;
  const m = Math.floor(diff / 60);   diff %= 60;
  const s = diff;

  const pad = n => String(n).padStart(2, '0');
  return `${pad(h)}:${pad(m)}:${pad(s)}`;
}
  
// Cuando abrimos el modal para crear una nueva actividad:
function abrirModalCrear(usuarioId, usuarioNombre) {
 document.getElementById('crear_usuario').value = usuarioNombre;
  const selCli = document.getElementById('crear_cliente');
  selCli.innerHTML = '<option value="">Cargando clientes...</option>';
  document.getElementById('crear_actividad').innerHTML = '<option value="">Seleccione un cliente primero</option>';

  // 3) Cargamos clientes del usuario
  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportes/get_clientes.php?user_id=${usuarioId}&t=${Date.now()}`)
    .then(r => r.json())
    .then(data => {
      const clientes = Array.isArray(data) ? data : data.clientes;
      selCli.innerHTML = '<option value="">Seleccione un cliente</option>';
      clientes.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id_cliente;
        opt.textContent = c.nombre_cliente;
        selCli.appendChild(opt);
      });
    });

  // 4) Listener: cuando cambie cliente, cargar sus actividades
  selCli.onchange = () => {
    const clienteId = selCli.value;
    const selAct = document.getElementById('crear_actividad');
    selAct.innerHTML = '<option value="">Cargando actividades...</option>';
    fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportes/get_actividades.php?cliente_id=${clienteId}&t=${Date.now()}`)
      .then(r => r.json())
      .then(data => {
        const acts = Array.isArray(data) ? data : data.actividades;
        selAct.innerHTML = '<option value="">Seleccione una actividad</option>';
        acts.forEach(a => {
          const opt = document.createElement('option');
          opt.value = a.nombre_actividad;
          opt.textContent = a.nombre_actividad;
          selAct.appendChild(opt);
        });
      });
  };

  // 5) Abrimos el modal
  new bootstrap.Modal(document.getElementById('modalCrearActividad')).show();
}


const btnAgregar = document.getElementById('btnAgregarActividad');
btnAgregar.addEventListener('click', () => {
  const usuarioNombre = document.getElementById('detalleUsuario').textContent;
  const usuarioId     = document.getElementById('app').dataset.idUsuario;
  abrirModalCrear(usuarioId, usuarioNombre);
});
// Manejo del submit de creación
document.getElementById('formCrearActividad').addEventListener('submit', e => {
  e.preventDefault();
  const payload = {
    usuario_id:      document.getElementById('app').dataset.idUsuario,
    cliente_id:      document.getElementById('crear_cliente').value,
    actividad:       document.getElementById('crear_actividad').value,
    hora_inicio:     document.getElementById('crear_inicio').value,
    hora_fin:        document.getElementById('crear_fin').value,
    fecha:           document.getElementById('crear_fecha').value,
    cobrado:         document.getElementById('crear_cobrado').value,
    cobradoGeneral:  document.getElementById('crear_cobradoGeneral').value,
    descripcion:     document.getElementById('crear_descripcion').value
  };
  // Aquí envías payload a tu endpoint para guardar
  fetch('/intranet/sistema/pages/panelAdministrativoClockify/reportes/crear_actividad.php', {
    method: 'POST', headers: {'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  })
  .then(r => r.text())
  .then(txt => {
    console.log('RESPUESTA CRUDA:', txt);
    return JSON.parse(txt);  // o bien lanzas un error
  })
  .then(resp => {
  if (resp.success) {
    Swal.fire({ icon:'success', title:'¡Actividad creada!', timer:1500, showConfirmButton:false })
      .then(() => {
 
const modalNewActivity = bootstrap.Modal.getInstance(document.getElementById('modalCrearActividad'));
    modalNewActivity.hide();
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
        // 1) Mete el nuevo registro en memoria
   registrosData.push({
  id: resp.id,
  usuario_id:      payload.usuario_id,
  cliente_id:      payload.cliente_id,
  usuario:         document.getElementById('detalleUsuario').textContent,
  actividad:       payload.actividad,
  fecha:           payload.fecha,
  hora_inicio:     payload.hora_inicio,   // <-- aquí
  hora_fin:        payload.hora_fin,      // <-- y aquí
  duracion:        calcularDuracion(payload.hora_inicio, payload.hora_fin),
  cobrado:         payload.cobrado,
  cobrado_general: payload.cobradoGeneral,
  descripcion:     payload.descripcion
});

        
        mostrarModalDetalle(
          document.getElementById('detalleUsuario').textContent
        );
      });
  } else {
    Swal.fire('Error', resp.message, 'error');
  }
  });
});
    
document.getElementById("exportarPDF").addEventListener("click", function() {
    if (registrosData.length === 0) {
        alert("No hay registros para exportar.");
        return;
    }
    const fechaInicio = document.getElementById("fechaInicio").value;
    const fechaFin    = document.getElementById("fechaFin").value;
    // 1. Recolectar los datos de la tabla (se realizará después de renderizarla)
    const totalHoras = document.getElementById("totalHoras").textContent;
    const totalCobro = document.getElementById("totalCobro").textContent;
    const totalCobroGeneral = document.getElementById("totalCobroGeneral").textContent;
    const totalCobroActividad = document.getElementById("totalCobroActividad").textContent;
    const canvasBarras = document.getElementById("horasChart");
    const imgDataBarras = canvasBarras.toDataURL("image/png");
    

    console.log("Datos a enviar al servidor:", {
         inicio: fechaInicio,      
         fin:    fechaFin,        
        totalHoras,
        totalCobro,
        totalCobroGeneral,
        totalCobroActividad,
        imgDataBarras,
       
        registrosData
    });

        
        fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportes/generate_pdf.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                inicio: fechaInicio,      
                fin:    fechaFin, 
                totalHoras,
                totalCobro,
                totalCobroGeneral,
                totalCobroActividad,
                imgDataBarras,

               registros: registrosData
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al generar el PDF, respuesta no válida.');
            }
            return response.blob();  // Recibe el blob (PDF)
        })
        .then(blob => {
            console.log('Blob recibido:', blob);
            if (blob.type === 'application/pdf') {
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = "reporte_de_tiempos.pdf";
    document.body.appendChild(a);
    a.click();
    a.remove();

    const statusEl = document.getElementById('status-certificados');
    // 1) Mensaje de inicio
    statusEl.innerHTML = '<div class="alert alert-info">Descarga iniciada…</div>';

    // 2) Tras 1s, lo cambiamos a “completada”
    setTimeout(() => {
      statusEl.innerHTML = '<div class="alert alert-success">Descarga completada</div>';
    }, 1000);

    // 3) Ocultamos todo al cabo de 4s desde el inicio
    setTimeout(() => {
      statusEl.innerHTML = '';
    }, 4000);

} else {
    document.getElementById('status-certificados').innerHTML = '<div class="alert alert-danger">No se generó el PDF.</div>';
}
        })
        .catch(error => {
            console.error('Error al generar el PDF:', error);
            document.getElementById('status-certificados').innerHTML = '<div class="alert alert-danger">Error al procesar la solicitud.</div>';
        });
    
});


    // Evento para cargar actividades basado en cliente
    filtroClienteElement.addEventListener('change', function () {
          const selectedId = filtroClienteElement.value;
          const clienteSeleccionado = clientesCargados.find(c => c.id_cliente == selectedId);
          if (clienteSeleccionado) {
              document.querySelectorAll(".MonedaCliente").forEach(el => {
  el.textContent = clienteSeleccionado.moneda;
});
           
          } else {
              document.querySelectorAll(".MonedaCliente").forEach(el => {
  el.textContent = '';
});
           
          }
        const clienteId = this.value;
        const actividadSelect = filtroActividadElement;
        actividadSelect.innerHTML = '<option value="all">Todas las actividades</option>';

        fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportes/get_actividades.php?cliente_id=${clienteId}&t=${new Date().getTime()}`)
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

    // Cargar clientes al hacer clic en el filtro de cliente
    filtroClienteElement.addEventListener('click', () => {
        const selectedValue = filtroClienteElement.value;

        fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportes/get_clientes.php`
        + `?user_id=${encodeURIComponent(ID_USUARIO)}`
        + `&t=${new Date().getTime()}`)
          .then(res => res.json())
  .then(data => {
     
          clientesCargados = data.clientes || [];
    // 1) Vaciamos el select
    filtroClienteElement.innerHTML = '<option value="">Cliente</option>';
    

    if (!Array.isArray(clientesCargados)) {
      console.error('⚠️ Esperaba data.clientes como array, llegó:', data);
      return;
    }
    // 3) Recorremos ese array
    clientesCargados.forEach(cliente => {
      const option = document.createElement('option');
      option.value = cliente.id_cliente;
      option.textContent = cliente.nombre_cliente;
      filtroClienteElement.appendChild(option);
    });
    // 4) Restauramos la selección previa
    filtroClienteElement.value = selectedValue;
    
        const clienteSeleccionado = clientesCargados.find(c => c.id_cliente == selectedValue);
      if (clienteSeleccionado) {
       document.querySelectorAll(".MonedaCliente").forEach(el => {
  el.textContent = clienteSeleccionado.moneda;
});
       
      } else {
       document.querySelectorAll(".MonedaCliente").forEach(el => {
  el.textContent = '';
});
       
      }
  })
  
  .catch(err => {
    filtroClienteElement.innerHTML = '<option value="">Error al cargar</option>';
    console.error('Error al obtener clientes:', err);
  });
});
  

    document.getElementById('formEditarActividad').addEventListener('submit', function (e) {
    e.preventDefault();

    const datos = {
        id: document.getElementById('editar_id').value,
        cliente: document.getElementById('editar_cliente').value,
        actividad: document.getElementById('editar_actividad').value,
        hora_inicio: document.getElementById('editar_inicio').value,
        hora_fin: document.getElementById('editar_fin').value,
        fecha: document.getElementById('editar_fecha').value,
        cobrado: document.getElementById('editar_cobrado').value,
        cobradoGeneral: document.getElementById('editar_cobradoGeneral').value,
        descripcion: document.getElementById('editar_descripcion').value
    };

    // Validación de campos obligatorios
    if (!datos.cliente || !datos.actividad || !datos.hora_inicio || !datos.hora_fin || !datos.fecha) {
        Swal.fire("Campos incompletos", "Por favor, completa todos los campos obligatorios.", "warning");
        return;
    }

    // Validación: no permitir ambos cobros al mismo tiempo
    if (datos.cobrado === '1' && datos.cobradoGeneral === '1') {
        Swal.fire("Incompatibilidad", "No puedes seleccionar ambos cobros al mismo tiempo.", "warning");
        return;
    }

    fetch('/intranet/sistema/pages/panelAdministrativoClockify/reportes/actualizar_actividad.php?t=' + new Date().getTime(), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            Swal.fire({
                icon: 'success',
                title: 'Actualizado',
                text: 'La actividad se guardó correctamente',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
    const modalEdit = bootstrap.Modal.getInstance(document.getElementById('modalEditarActividad'));
    modalEdit.hide();
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');

    // 1) actualiza registrosData en memoria:
    const idx = registrosData.findIndex(r => r.id == datos.id);
    if (idx > -1) {
      registrosData[idx] = { 
      ...registrosData[idx], 
        cliente_id: datos.cliente,
        actividad:  datos.actividad,
        hora_inicio: datos.hora_inicio,
        hora_fin:    datos.hora_fin,
        fecha:       datos.fecha,
        duracion:    calcularDuracion(datos.hora_inicio, datos.hora_fin),
        cobrado:     datos.cobrado,
        cobrado_general: datos.cobradoGeneral,
        descripcion: datos.descripcion
      };
    }

   
    renderTablaRegistros(registrosData);

    // 3) si tienes abierto el modal detalle para ese usuario, refresca también:
    if ($('#modalDetalleActividades').hasClass('show')) {
      const usuarioActual = document.getElementById('detalleUsuario').textContent;
      mostrarModalDetalle(usuarioActual);
    }
  });
} else {
            throw new Error(resp.message || 'Error desconocido del servidor');
        }
    })
    .catch(err => {
        console.error("❌ Error al actualizar:", err);
        Swal.fire("Error", err.message || "Error inesperado", "error");
    });
});

})();
</script>