<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../app/controllers/config.php');

?>


<!-- 1) Bootstrap CSS -->
<link
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
  rel="stylesheet"
/>

<!-- 2) DataTables Bootstrap5 CSS -->
<link
  href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css"
  rel="stylesheet"
/>

<!-- 3) Buttons Bootstrap5 CSS -->
<link
  href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css"
  rel="stylesheet"
/>

<!-- 4) FontAwesome (para el icono Excel) -->
<link
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  rel="stylesheet"
/>

    <style>
        .table-responsive { overflow-x: auto; margin-top: 20px; }
        .card-custom {
            background: #fff; border-radius: 10px; padding: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .custom-excel-btn {
            background-color: #217346 !important;
            color: white !important;
            border-radius: 6px;
            padding: 6px 12px;
            font-weight: bold;
        }
        .custom-excel-btn i { margin-right: 5px; }
    </style>
    
<div id="contenido-principal">
    <div class="container-fluid mt-4">
    <div class="card-custom">
        <h2 class="text-center "><p class="traducible">Registros de Horarios -Para la Administrativos</p></h2>
        <br>
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="start_date" ><p class="traducible">Fecha Inicio</p></label>
                <input type="date" id="start_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="end_date"><p class="traducible">Fecha Fin</p></label>
                <input type="date" id="end_date" class="form-control">
            </div>
            <div class="col-md-3 d-flex gap-2 align-items-end">
                <button id="applyFilter" class="btn btn-primary "><p class="traducible">Filtrar</p></button>
                <button id="resetFilter" class="btn btn-secondary ml-2 "><p class="traducible">Restablecer</p></button>
            </div>
        </div>
        
<div class="row mb-3">
  <div class="col-auto d-flex gap-2">
    <button id="btnExportarExcel" class="btn btn-success ">
      <i class="fas fa-file-excel"></i><p class="traducible">Exportar Excel Avanzado</p> 
    </button>

    <button class="btn btn-warning traducible" data-bs-toggle="modal" data-bs-target="#modalFestivos">
      <i class="fas fa-calendar-plus"></i><p class="traducible">Registrar Festivos</p> 
    </button>
  </div>
</div>

        <div class="table-responsive">
           
                 <table class="table table-bordered table-striped" id="tablaHorariosAdministrativo" style="width:100%">
                <thead>
                    <tr>
                        <th><p class="traducible">Trabajador</p></th>
                        <th><p class="traducible">Inicio Turno</p></th>
                        <th><p class="traducible">Fecha</p></th>
                        <th><p class="traducible">Break 1 Inicio</p></th>
                        <th><p class="traducible">Break 1 Fin</p></th>
                        <th><p class="traducible">Break 2 Inicio</p></th>
                        <th><p class="traducible">Break 2 Fin</p></th>
                        <th><p class="traducible">Break 3 Inicio</p></th>
                        <th><p class="traducible">Break 3 Fin</p></th>
                        <th><p class="traducible">Fin Turno</p></th>
                        <th><p class="traducible">Fecha Registro</p></th>
                    </tr>
                </thead>
              <tbody id="tabla-body"></tbody>
            </table>
        </div>
        <!-- Modal -->
<div class="modal fade" id="modalFestivos" tabindex="-1" aria-labelledby="modalFestivosLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title " id="modalFestivosLabel"><p class="traducible">Registrar Días Festivos</p></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="date" id="inputFestivo" class="form-control mb-2">
        <button class="btn btn-primary w-100 mb-3 " onclick="agregarFestivo()"><p class="traducible">Agregar Festivo</p></button>
        <ul id="listaFestivos" class="list-group"></ul>
      </div>
    </div>
  </div>
</div>
    </div>
</div>
</div>


<!-- 5) jQuery (requerido por DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- 6) Bootstrap Bundle JS (incluye Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- 7) DataTables core JS -->
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<!-- 8) DataTables Bootstrap5 integration -->
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

<!-- 9) Buttons core JS -->
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>

<!-- 10) Buttons Bootstrap5 integration -->
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>

<!-- 11) JSZip (necesario para excelHtml5) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- 12) HTML5 export (Excel) -->
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

<!-- 13) SweetAlert2 (si usas alertas) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function(){
  window.cargarHorariosDesdeServidor = function () {
    fetch(`/intranet/sistema/pages/horario/get_horarios.php?t=${Date.now()}`)
      .then(res => res.json())
      .then(data => {
        const tbody = document.getElementById('tabla-body');
        tbody.innerHTML = ''; // Limpiar tabla

        if (data.length === 0) {
          console.warn("⚠️ No hay datos para mostrar.");
          if ($.fn.DataTable.isDataTable('#tablaHorariosAdministrativo')) {
            $('#tablaHorariosAdministrativo').DataTable().clear().draw();
          }
          return;
        }

        data.forEach(h => {
          const fila = document.createElement('tr');
          fila.innerHTML = `
            <td>${h.nombre_completo}</td>
            <td>${h.hora_inicio_turno}</td>
            <td>${h.fecha}</td>
            <td>${h.hora_inicio_break1}</td>
            <td>${h.hora_fin_break1}</td>
            <td>${h.hora_inicio_break2}</td>
            <td>${h.hora_fin_break2}</td>
            <td>${h.hora_inicio_break3}</td>
            <td>${h.hora_fin_break3}</td>
            <td>${h.hora_fin_turno}</td>
            <td>${h.fyh_creacion}</td>
          `;
          tbody.appendChild(fila);
        });

        // Destruir y recrear DataTable
        if ($.fn.DataTable.isDataTable('#tablaHorariosAdministrativo')) {
          $('#tablaHorariosAdministrativo').DataTable().clear().destroy();
        }

        $('#tablaHorariosAdministrativo').DataTable({
          scrollX: true,
          responsive: true,
          pageLength: 5,
          lengthChange: false,
          autoWidth: false,
          layout: {
            topStart: {
              buttons: [
                {
                  extend: 'excel',
                  text: '<i class="fas fa-file-excel"></i> Excel',
                  className: 'btn btn-success custom-excel-btn'
                }
              ]
            }
          },
          language: {
            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
          }
        });
      })
      .catch(err => console.error("❌ Error al cargar horarios:", err));
  };

  $(document).ready(function () {
    cargarHorariosDesdeServidor(); // ✅ carga inicial
   

    // Filtro personalizado
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
      var start = $('#start_date').val();
      var end = $('#end_date').val();
      if (!start || !end) return true;

      var date = new Date(data[2]); // Columna 2 = fecha
      return date >= new Date(start) && date <= new Date(end);
    });

    $('#applyFilter').on('click', function () {
      if (!$('#start_date').val() || !$('#end_date').val()) {
        Swal.fire('Campos incompletos', 'Por favor selecciona ambas fechas', 'warning');
        return;
      }
      $('#tablaHorariosAdministrativo').DataTable().draw();
    });

    $('#resetFilter').on('click', function () {
      $('#start_date').val('');
      $('#end_date').val('');
      $('#tablaHorariosAdministrativo').DataTable().search('').columns().search('').draw();
    });
  });
 window.descargarExcelAvanzado = async function() { 
  const start = document.getElementById('start_date').value;
  const end = document.getElementById('end_date').value;

  if (!start || !end) {
    Swal.fire('Rango requerido', 'Selecciona fechas de inicio y fin', 'warning');
    return;
  }

  const festivosRes = await fetch(`/intranet/sistema/pages/horario/get_festivos.php?t=${Date.now()}`);
const festivos = await festivosRes.json();
const festivosEnRango = festivos.filter(f => f >= start && f <= end);

  const htmlFestivos = festivosEnRango.length
    ? `<ul>${festivosEnRango.map(f => `<li>${f}</li>`).join('')}</ul>`
    : '<p><strong class="traducible">No hay festivos registrados en este rango.</strong></p>';

  const { isConfirmed } = await Swal.fire({
    title: '¿Incluir festivos en el reporte?',
    html: `
      <p class="traducible">Se tendrán en cuenta los siguientes festivos registrados:</p>
      ${htmlFestivos}
      <p class="text-danger traducible">Si el calendario no está actualizado, los registros podrían verse afectados.</p>
    `,
    icon: 'info',
    showCancelButton: true,
    confirmButtonText: 'Sí, continuar',
    cancelButtonText: 'Cancelar'
  });

  if (!isConfirmed) return;

  const url = `/intranet/sistema/pages/horario/exportar_excel_avanzado.php?start=${start}&end=${end}&t=${Date.now()}`;
  window.open(url, '_blank');
}

document.getElementById('btnExportarExcel').addEventListener('click', descargarExcelAvanzado);

window.agregarFestivo = async function() {
  const input = document.getElementById('inputFestivo');
  const fecha = input.value;
  if (!fecha) return;

  const res = await fetch(`/intranet/sistema/pages/horario/guardar_festivo.php?t=${Date.now()}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ fecha })
  });

  const result = await res.json();
  if (result.status === 'ok') {
    mostrarFestivos();
    input.value = '';
  } else {
    Swal.fire('Error', result.msg || 'No se pudo guardar el festivo', 'error');
  }
}

window.mostrarFestivos = async function() {
  const lista = document.getElementById('listaFestivos');
  const res = await fetch(`/intranet/sistema/pages/horario/get_festivos.php?t=${Date.now()}`);
  const festivos = await res.json();

  lista.innerHTML = festivos.map(f =>
    `<li class="list-group-item d-flex justify-content-between">
      ${f}
      <button class="btn btn-sm btn-danger" onclick="eliminarFestivo('${f}')">✖</button>
    </li>`
  ).join('');
}

window.eliminarFestivo = function(fecha) {
  const festivos = JSON.parse(localStorage.getItem('festivos')) || [];
  const nuevos = festivos.filter(f => f !== fecha);
  localStorage.setItem('festivos', JSON.stringify(nuevos));
  mostrarFestivos();
}

mostrarFestivos();
})();
</script>
