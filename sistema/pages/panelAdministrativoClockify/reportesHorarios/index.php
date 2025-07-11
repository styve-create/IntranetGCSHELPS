<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../../app/controllers/config.php');
include_once(__DIR__ . '/../../../analisisRecursos.php');

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}
$id_usuario = $_SESSION['usuario_info']['id'] ?? null;
?>
<link rel="stylesheet" href="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?php echo $URL; ?>/librerias/vendor/npm-asset/bootstrap/dist/css/bootstrap.min.css">
<style>
/* Fija altura y scroll interno */
#modalDetalle .modal-body {
  max-height: 65vh;
  overflow-y: auto;
}

/* Un poco más de espacio en celdas */
#modalDetalle .table th,
#modalDetalle .table td {
  padding: .75rem 1rem;
}

/* Sombra suave a todo el modal */
#modalDetalle .modal-content {
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* Opcional: agranda ligeramente la fuente */
#modalDetalle .table {
  font-size: .95rem;
}
</style>
<div id="contenido-principal">
        <div class="container-fluid p-4">
                    <h4 class="mb-4"><strong ><p class="traducible">Reporte de Tiempos OPS</p></strong></h4>
                    <div id="app" data-id-usuario="<?= htmlspecialchars($id_usuario) ?>"></div>
                    <div class="row g-2 mb-4">
                        <!-- Filtro de cliente -->
                        <div class="col-md-2"><select id="filtroCliente" class="form-select form-select-sm"><option value=""><p class="traducible">Cargando clientes...</p></option></select></div>
                        <!-- Filtro de actividad -->
                        <div class="col-md-2"><select id="filtroActividad" class="form-select form-select-sm"><option value="all"><p class="traducible">Todas las actividades</p></option></select></div>
                        <!-- Filtro de fecha de inicio -->
                        <div class="col-md-2"><input type="date" id="fechaInicio" class="form-control form-control-sm" /></div>
                        <!-- Filtro de fecha de fin -->
                        <div class="col-md-2"><input type="date" id="fechaFin" class="form-control form-control-sm" /></div>
                        <!-- Botón para aplicar los filtros -->
                        <div class="col-md-2"><button id="btnFiltrar" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel"></i><p class="traducible">Aplicar Filtro</p></button></div>
                    </div>
                    
                    <!-- contenedor de la tabla -->
                    <div id="tablaFiltradaContainer" class="mt-4"></div>
                    
                    <!-- modal de asignar actividad (igual que antes) -->
                    <div class="modal fade" id="modalAsignar" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title"><p class="traducible">Asignar Actividad</p><span id="modalUsuario"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <select id="modalActividad" class="form-select"></select>
                          </div>
                          <div class="modal-footer">
                            <button id="modalGuardar" class="btn btn-primary"><p class="traducible">Guardar</p></button>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Modal de Detalle de Horarios -->
                  <div class="modal fade" id="modalDetalle" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered"><!-- centrado verticalmente -->
    <div class="modal-content">


     <div class="modal-header border-0 pb-0"><!-- quita el borde y padding-bottom -->
        <h5 class="modal-title"><p class="traducible">Detalle de Horarios </p><span id="detalleUsuario"></span></h5>
        <button id="btnNuevoRegistro" class="btn btn-sm btn-success ms-3"><p class="traducible">+ Nuevo Registro</p></button>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>


     <div class="modal-body"><!-- restaura padding para respirar -->
        <div class="table-responsive">  
          <table
           class="table table-striped table-hover align-middle text-center mb-0"
            id="tablaDetalle"
          >
            <thead class="table-light">
              <tr>
                <th><p class="traducible">Fecha</p></th>
                <th><p class="traducible">Inicio</p></th>
                <th><p class="traducible">Fin</p></th>
                <th><p class="traducible">Break 1</p></th>
                <th><p class="traducible">Break 2</p></th>
                <th><p class="traducible">Break 3</p></th>
                <th><p class="traducible">Horas Extras</p></th>
                <th><p class="traducible">Actividad</p></th>
                <th><p class="traducible">Acciones</p></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

     <!-- Footer centrado con botones grandes -->
     <div class="modal-footer border-0 justify-content-center">
       <button type="button" class="btn btn-success">
         <i class="bi bi-check-circle-fill"></i><p class="traducible">Guardar</p> 
       </button>
       <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
         <i class="bi bi-x-circle-fill"></i><p class="traducible">Salir</p> 
      </button>
   </div>

    </div>
  </div>
</div>
         </div>
</div>
<script src="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
 <script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/jquery/dist/jquery.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="<?= $URL; ?>/librerias/vendor/npm-asset/chart.js/dist/chart.umd.js"></script>
<script src="<?= $URL; ?>/librerias/vendor/npm-asset/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>
<script src="<?= $URL; ?>/sistema/traduccionsistema.php"></script>
<script>
(function(){
  // ——— 1) referencias al DOM ———
  const filtroClienteElement   = document.getElementById('filtroCliente');
  const filtroActividadElement = document.getElementById('filtroActividad');
  const fechaInicioElement     = document.getElementById('fechaInicio');
  const fechaFinElement        = document.getElementById('fechaFin');
  const btnFiltrarElement      = document.getElementById('btnFiltrar');
  const tablaContainer         = document.getElementById('tablaFiltradaContainer');
  const detalleUsuario         = document.getElementById('detalleUsuario');
  const ID_USUARIO             = document.getElementById('app').dataset.idUsuario;
  let usuarioModal = null;

  // ——— 2) carga inicial de clientes ———
  function cargarClientes() {
    fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportesHorarios/get_clientes.php?user_id=${ID_USUARIO}&t=${Date.now()}`)
      .then(r => r.json())
      .then(data => {
        const lista = Array.isArray(data) ? data : data.clientes;
        filtroClienteElement.innerHTML = '<option value=""><p class="traducible">Todos los clientes</p></option>';
        lista.forEach(c => {
          const opt = document.createElement('option');
          opt.value = c.id_cliente;
          opt.textContent = c.nombre_cliente;
          filtroClienteElement.appendChild(opt);
        });
      })
      .catch(() => {
        filtroClienteElement.innerHTML = '<option value=""><p class="traducible">Error al cargar clientes</p></option>';
      });
  }

  // ——— 3) cuando cambio cliente, cargo actividades ———
  filtroClienteElement.addEventListener('change', () => {
    const cli = filtroClienteElement.value;
    filtroActividadElement.innerHTML = '<option value="all"><p class="traducible">Todas las actividades</p></option>';
    if (!cli) return;
    fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportesHorarios/get_actividades.php?cliente_id=${cli}&t=${Date.now()}`)
      .then(r => r.json())
      .then(data => {
        const lista = Array.isArray(data) ? data : data.actividades;
        lista.forEach(a => {
          const o = document.createElement('option');
          o.value = a.nombre_actividad;
          o.text  = a.nombre_actividad;
          filtroActividadElement.appendChild(o);
        });
      })
      .catch(() => {
        filtroActividadElement.innerHTML = '<option value="all"><p class="traducible">Error al cargar actividades</p></option>';
      });
  });

  // ——— 4) renderizar tabla principal ———
  function renderTablaFiltrada(usuarios, actividades) {
       if ($.fn.dataTable.isDataTable('#tablaFiltrada')) {
    $('#tablaFiltrada').DataTable().destroy();
  }
  
    tablaContainer.innerHTML = `
      <table id="tablaFiltrada" class="table table-sm table-bordered">
        <thead>
          <tr>
            <th><p class="traducible">Usuario</p></th>
            <th><p class="traducible">Tiempo</p></th>
            <th><p class="traducible">Horas Extra</p></th>
            <th><p class="traducible">Asignar Actividad</p></th>
            <th><p class="traducible">Ver Detalle</p></th>
          </tr>
        </thead>
        <tbody>
          ${usuarios.map(u => `
            <tr data-usuario="${u.usuario}">
              <td>${u.usuario}</td>
              <td>${u.tiempo}</td>
              <td>${u.horas_extra}</td>
              <td>
                <select class="form-select form-select-sm asignarActividad">
                  ${actividades.map(a => `<option value="${a.nombre_actividad}">${a.nombre_actividad}</option>`).join('')}
                </select>
              </td>
              <td><button class="btn btn-sm btn-info btnVerDetalle"><p class="traducible">Ver Detalle</p></button></td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    `;
    
window.traducirTextos(document.querySelector('#tablaFiltrada'));

    // ✅ Inicializo DataTable solo cuando ya traduje
    $('#tablaFiltrada').DataTable({
      paging: true,
      ordering: true,
      info: false,
      autoWidth: false
    });
  
 

    // handler asignar
    $('#tablaFiltrada')
      .off('change', '.asignarActividad')
      .on('change', '.asignarActividad', function(){
        const tr = $(this).closest('tr');
        const user = tr.data('usuario');
        const act  = this.value;
        
      });

    // handler ver detalle
    
  $('#tablaFiltrada')
  .off('click', '.btnVerDetalle')
  .on('click', '.btnVerDetalle', function() {
    usuarioModal = $(this).closest('tr').data('usuario');
    $('#detalleUsuario').text(usuarioModal);
    const detalles = window._detallePorUsuario[usuarioModal] || [];
    const actList = window._actividadesList;

    const $tbody = $('#tablaDetalle tbody').empty();

    function buildRow(d, isNew = false) {
      // helper para los selects
      const options = actList.map(a =>
        `<option value="${a.nombre_actividad}" ${d.actividad === a.nombre_actividad ? 'selected' : ''}>
           ${a.nombre_actividad}
         </option>`
      ).join('');

      return $(`
        <tr data-id="${d.id||''}" class="${isNew?'table-success':''}">
        
          <td><input type="date" class="form-control form-control-sm fecha" value="${d.fecha||''}"></td>
          <td><input type="time" class="form-control form-control-sm inicio" value="${d.start_turno?d.start_turno.substr(11,5):''}"></td>
          <td><input type="time" class="form-control form-control-sm fin"    value="${d.end_turno?  d.end_turno.substr(11,5):''}"></td>
          <td><input type="time" class="form-control form-control-sm b1i"    value="${d.break1_start?d.break1_start.substr(11,5):''}">
              <input type="time" class="form-control form-control-sm b1f" value="${d.break1_end?  d.break1_end.substr(11,5):''}"></td>
          <td><input type="time" class="form-control form-control-sm b2i"    value="${d.break2_start?d.break2_start.substr(11,5):''}">
              <input type="time" class="form-control form-control-sm b2f" value="${d.break2_end?  d.break2_end.substr(11,5):''}"></td>
          <td><input type="time" class="form-control form-control-sm b3i"    value="${d.break3_start?d.break3_start.substr(11,5):''}">
              <input type="time" class="form-control form-control-sm b3f" value="${d.break3_end?  d.break3_end.substr(11,5):''}"></td>
          <td><input type="time" class="form-control form-control-sm inicioextra"    value="${d.inicio_extra? d.inicio_extra.substr(11,5):''}">
              <input type="time" class="form-control form-control-sm finextra" value="${d.fin_extra?  d.fin_extra.substr(11,5):''}"></td>
          <td>
            <select class="form-select form-select-sm actividad">
              ${options}
            </select>
          </td>
          <td>
            <button class="btn btn-sm btn-primary btnGuardar"><i class="bi bi-floppy"></i></button>
            <button class="btn btn-sm btn-danger btnEliminar"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
      `);
     
    }

    // filas existentes
    detalles.forEach(d => $tbody.append(buildRow(d)));

    // handler guardar/editar en cada fila
    
    $tbody.off('click', '.btnGuardar').on('click', '.btnGuardar', function() {
        const $btn = $(this);               // ← aquí
        const $tr  = $btn.closest('tr');
        const isNew = !$tr.data('id');
  
        function withTimestamp(url) {
          const sep = url.includes('?') ? '&' : '?';
          return `${url}${sep}t=${Date.now()}`;
        }

        // Uso:
        const url = withTimestamp(isNew
          ? '/intranet/sistema/pages/panelAdministrativoClockify/reportesHorarios/create_record.php'
          : '/intranet/sistema/pages/panelAdministrativoClockify/reportesHorarios/update_record.php');

              const payload = {
                id:        $tr.data('id'),
                usuario:   usuarioModal,         
                fecha:     $tr.find('.fecha').val(),
                start:     $tr.find('.inicio').val(),
                end:       $tr.find('.fin').val(),
                inicioextra: $tr.find('.inicioextra').val(),
                finextra:   $tr.find('.finextra').val(),
                breaks: [
                  { start:$tr.find('.b1i').val(), end:$tr.find('.b1f').val() },
                  { start:$tr.find('.b2i').val(), end:$tr.find('.b2f').val() },
                  { start:$tr.find('.b3i').val(), end:$tr.find('.b3f').val() },
                ],
                actividad:$tr.find('.actividad').val()
              };
  
                console.log('📤 Enviando a update_record.php:', payload);
                 $btn.prop('disabled', true).text('Guardando…');
                 $tr.find('input, select').prop('disabled', true);
    
                  fetch(url, {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    credentials: 'include',
                    body: JSON.stringify(payload)
                  })
                    .then(r => {
                    if (!r.ok) throw new Error(`HTTP ${r.status}`);
                    return r.text();
                  })
                  .then(text => {
                    console.log('>>> RAW RESPONSE:', text);
                    return JSON.parse(text);
                  })
                   .then(res => {
                        if (!res.success) throw new Error(res.msg||'Error al guardar');
                      
                        const detallesArr = window._detallePorUsuario[usuarioModal];
                        
                        if (isNew) {
                          payload.id = res.id;
                          detallesArr.push(payload);
                        } else {
                          const idx = detallesArr.findIndex(d => d.id === payload.id);
                          if (idx > -1) detallesArr[idx] = payload;
                        }
                
                  $tr.find('.fecha').val(payload.fecha);
                  $tr.find('.inicio').val(payload.start);
                  $tr.find('.fin').val(payload.end);
                  $tr.find('.actividad').val(payload.actividad);
                  
                      Swal.fire({
                        icon: 'success',
                        title: 'Registro guardado',
                        text: isNew
                          ? 'Se creó el registro correctamente.'
                          : 'Se actualizó el registro correctamente.',
                        confirmButtonColor: '#007ea7'
                      });
                      
                      btnFiltrarElement.click();
                    })
                    .catch(err => {
                      // 5) Error: re-habilita inputs y botón
                      $tr.find('input, select').prop('disabled', false);
                      $btn.prop('disabled', false).text('Guardar');
                      Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message,
                        confirmButtonColor: '#d33'
                      });
                    })
                    .finally(() => {
                      // 6) En todo caso, vuelve el texto del botón a “Guardar”
                      $btn.prop('disabled', false).text('Guardar');
                    });
   });
   $tbody.on  ('click', 'tr', function(){
    $(this).toggleClass('table-success');
    console.log('Seleccionadas:', $tbody.find('tr.table-success').length);
  });
  
  $tbody.off('click', '.btnEliminar').on('click', '.btnEliminar', function () {
  const $tr = $(this).closest('tr');
  const id  = $tr.data('id');

  if (!id) {
    $tr.remove();
    return;
  }

  Swal.fire({
    title: '¿Estás seguro?',
    text: 'Esta acción eliminará la actividad seleccionada.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#aaa'
  }).then(({ isConfirmed }) => {
    if (!isConfirmed) return;

    fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportesHorarios/delete_single_horario.php?t=${Date.now()}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    })
    .then(r => r.json())
    .then(res => {
      if (!res.success) throw new Error(res.message || 'No se pudo eliminar');
      $tr.remove();
      Swal.fire('Eliminado', 'La actividad fue eliminada correctamente.', 'success');
      btnFiltrarElement.click();
    })
    .catch(err => {
      Swal.fire('Error', err.message, 'error');
    });
  });
});
  

            // nuevo registro
            $('#btnNuevoRegistro').off('click').on('click', ()=> {
              const newRow = buildRow({
                id:'', fecha:'', start_turno:'', end_turno:'',
                break1_start:'',break1_end:'',
                break2_start:'',break2_end:'',
                break3_start:'',break3_end:'',
                duracion:null, actividad: ''
              }, true);
              $('#tablaDetalle tbody').prepend(newRow);
            });
            
    new bootstrap.Modal(document.getElementById('modalDetalle')).show();
  });

    // guardar desde el modal
    $('#modalGuardar')
      .off('click')
      .on('click', ()=>{
        const nueva = $('#modalActividad').val();
        // fetch a tu endpoint...
        new bootstrap.Modal(document.getElementById('modalAsignar')).hide();
      });
  }
  
  function fetchAndDebugJSON(url) {
  return fetch(url)
    .then(async response => {
      const text = await response.text();
      console.group(`🚨 Debug JSON from ${url}`);
      console.log('HTTP status:', response.status);
      console.log('Raw response text:', text);
      console.groupEnd();
      try {
        return JSON.parse(text);
      } catch (e) {
        throw new Error(`Invalid JSON from ${url}: ${e.message}`);
      }
    });
}


  // ——— 5) evento click filtrar ———
btnFiltrarElement.addEventListener('click', () => {
  const clienteId = filtroClienteElement.value;
  const inicio    = fechaInicioElement.value;
  const fin       = fechaFinElement.value;

  Promise.all([
    fetchAndDebugJSON(
      `/intranet/sistema/pages/panelAdministrativoClockify/reportesHorarios/api_reporte_horarios.php?cliente=${clienteId}&inicio=${inicio}&fin=${fin}&t=${Date.now()}`
    ),
    fetchAndDebugJSON(
      `/intranet/sistema/pages/panelAdministrativoClockify/reportesHorarios/get_actividades.php?cliente_id=${clienteId}&t=${Date.now()}`
    )
  ])
  .then(([dataUsers, actsData]) => {
    const actividades = Array.isArray(actsData) ? actsData : actsData.actividades;
    renderTablaFiltrada(dataUsers.summary, actividades);
    window._detallePorUsuario = dataUsers.details;
    window._actividadesList = actividades;
  })
  .catch(err => {
    console.error('Error al cargar datos de reporte:', err);
    alert('No se pudo cargar datos de reporte:\n' + err.message);
  });
});

$('#modalDetalle').on('shown.bs.modal', function(){
  const $btnGuardar = $('#modalDetalle .modal-footer .btn-success');

  $btnGuardar.off('click').on('click', function(){
    Swal.fire({
      title: '¿Enviar a reportes?',
      text: 'Se moverán estos registros a tb_actividades y luego se borrarán.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, adelante'
    }).then(({ isConfirmed })=>{
      if (!isConfirmed) return;

     // 1) IDs de tb_horario seleccionados
     const $filas = $('#tablaDetalle tbody tr');
      const ids = $filas.map((_,tr)=>$(tr).data('id')).get();

      // 2) Preparamos el payload completo de actividades
      const clienteId = $('#filtroCliente').val();        // cliente actual
      const usuarioId = usuarioModal;                      // lo sacas de tu variable global

      const actividades = $filas.map((_,tr) => {
  const $tr = $(tr);
  return {
    horario_id:       $tr.data('id'),
    cliente_id:       $('#filtroCliente').val(),
    usuario_id:       usuarioModal,
    fecha:            $tr.find('.fecha').val(),
    hora_inicio_turno:$tr.find('.inicio').val(),
    hora_fin_turno:   $tr.find('.fin').val(),
    break1_inicio:    $tr.find('.b1i').val(),
    break1_fin:       $tr.find('.b1f').val(),
    break2_inicio:    $tr.find('.b2i').val(),
    break2_fin:       $tr.find('.b2f').val(),
    break3_inicio:    $tr.find('.b3i').val(),
    break3_fin:       $tr.find('.b3f').val(),
    hora_inicio_extra:$tr.find('.inicioextra').val(),
    hora_fin_extra:   $tr.find('.finextra').val(),
    actividad:        $tr.find('.actividad').val()
  };
}).get();
    console.log("actividades", actividades);
      // 2) Insertar primero
      fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportesHorarios/insert_actividades.php?t=${Date.now()}`, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ actividades })
      })
      .then(r=>r.json())
      .then(res=>{
        if(!res.success) throw new Error(res.message||'Error insertando');
        // 3) Si insert ok, eliminar horarios
        return fetch(`/intranet/sistema/pages/panelAdministrativoClockify/reportesHorarios/delete_horarios.php?t=${Date.now()}`, {
          method:'POST',
          headers:{'Content-Type':'application/json'},
          body: JSON.stringify({ ids })
        });
      })
      .then(r=>r.json())
      .then(res=>{
        if(!res.success) throw new Error(res.message||'Error borrando');
        // 4) Éxito: limpia filas y refresca
        $filas.remove();
        $('#btnFiltrar').click();
        Swal.fire('¡Hecho!','Se han movido los registros.','success');
      })
      .catch(err=>{
        Swal.fire('Error',err.message,'error');
      });
    });
  });
});
  // arranco
  cargarClientes();
})();
</script>