<?php

include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');


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
.table-responsive {
            overflow-x: auto;
            margin-top: 20px;
        }


.card-custom {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.custom-excel-btn {
    background-color: #217346 !important;  /* Verde estilo Excel */
    color: white !important;
    border-radius: 6px;
    padding: 6px 12px;
    border: none;
    font-weight: bold;
}

.custom-excel-btn i {
    margin-right: 5px;
}
    </style>     

<div id="contenido-principal">
     <!-- HTML -->
<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-12">
        <div class="card-custom">
            <h1 class="text-center">Listado de Ausencias</h1>
            
               <!-- Filtros de fechas -->
<div class="row mb-3">
    <div class="col-md-3">
        <label for="start_date">Fecha Inicio</label>
        <input type="date" id="start_date" class="form-control">
    </div>
    <div class="col-md-3">
        <label for="end_date">Fecha Fin</label>
        <input type="date" id="end_date" class="form-control">
    </div>
    <div class="col-md-3 d-flex gap-2 align-items-end">
        <button id="applyFilter" class="btn btn-primary">Filtrar</button>
        <button id="resetFilter" class="btn btn-secondary ml-2">Restablecer</button>
       
    </div>
    
</div>
</div>
  <button id="exportExcel" class="btn btn-success">
    <i class="fas fa-file-excel"></i> Exportar Excel
  </button>
      <div class="table-responsive">
        
        
        <table id="tablaAusencias" class="table table-striped table-bordered w-100">
              <thead class="bg-light text-dark">
                <tr>
                  <th>#</th>
                  <th>Formulario</th>
                  <th>Registro</th>
                  <th>Trabajador</th>
                  <th>Tareas</th>
                  <th>comprobantes</th>
                  <th>Ver detalle</th>
                </tr>
              </thead>
              <tbody id="tbody-ausencias">
              <!-- Se rellenará con JS -->
            </tbody>
          
        </table>
      </div>
</div>
      
    </div>
  </div>
  <!-- Modal para mostrar tareas -->
<div class="modal fade" id="modalTareas" tabindex="-1" aria-labelledby="modalTareasLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTareasLabel">Listado de Tareas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Tarea</th>
              <th>Responsable</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody id="modalTareasBody">
            <!-- Se llenará por JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Modal “Ver detalle” -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle de ausencia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <dl class="row">
          <dt class="col-sm-4">Formulario</dt><dd class="col-sm-8" id="det_form"></dd>
          <dt class="col-sm-4">Documento</dt><dd class="col-sm-8" id="det_doc"></dd>
          <dt class="col-sm-4">Campaña</dt><dd class="col-sm-8" id="det_camp"></dd>
          <dt class="col-sm-4">Jefe</dt><dd class="col-sm-8" id="det_jefe"></dd>
          <dt class="col-sm-4">Tipo</dt><dd class="col-sm-8" id="det_tipo"></dd>
          <dt class="col-sm-4">Desde</dt><dd class="col-sm-8" id="det_inicio"></dd>
          <dt class="col-sm-4">Hasta</dt><dd class="col-sm-8" id="det_fin"></dd>
          <dt class="col-sm-4">Observaciones</dt><dd class="col-sm-8" id="det_obs"></dd>
           <dt class="col-sm-4">Estado Team Lead</dt>
            <dd class="col-sm-8" id="det_estado_tl"></dd>
            <dt class="col-sm-4">Fecha Team Lead</dt>
            <dd class="col-sm-8" id="det_fecha_tl"></dd>
            <dt class="col-sm-4">Estado RRHH</dt>
            <dd class="col-sm-8" id="det_estado_rrhh"></dd>
            <dt class="col-sm-4">Fecha RRHH</dt>
            <dd class="col-sm-8" id="det_fecha_rrhh"></dd>
            <dt class="col-sm-4">Razón de Rechazo</dt>
            <dd class="col-sm-8" id="det_razon_rechazo"></dd>
        </dl>
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

<!-- 14) xlsx (Excel de la tabla) -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>



<script>
    (function() {
        
        $(document).ready(function () {
    console.log('Document ready, initializing DataTable...');
    
    

    const tabla = $('#tablaAusencias').DataTable({
    // 1) 'dom' configura dónde van los elementos:

    // Resto de opciones
    scrollX:    true,
    responsive: true,
    pageLength: 5,
    lengthChange: false,
    autoWidth:  false,
    paging:     true,
    info:       false,
    searching:  true,
      layout: {
        topStart: {
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        }
    },
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json',
      paginate: { previous: '‹', next: '›', first: '«', last: '»' }
    }
  });


  
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var start = $('#start_date').val();
            var end = $('#end_date').val();

            if (!start || !end) return true;

            var min = new Date(start);
            var max = new Date(end);

            var dateStr = data[2]; // Asumiendo que columna 1 tiene fecha 'YYYY-MM-DD'
            var date = new Date(dateStr);

            return date >= min && date <= max;
        }
    );

    
    $('#applyFilter').on('click', function () {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        if (!startDate || !endDate) {
            Swal.fire('Campos incompletos', 'Por favor selecciona ambas fechas', 'warning');
            return;
        }

        tabla.draw();
    });

    
    $('#resetFilter').on('click', function () {
        $('#start_date').val('');
        $('#end_date').val('');
        tabla.search('').columns().search('').draw(); 
    });
    cargarAusencias();
});
// Escapa texto para evitar inyección
function escapeHtml(text) {
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}


  // Escapa para evitar inyección
  function esc(t){ return $('<div>').text(t).html(); }

  $(function(){
   
$('#tablaAusencias').on('click', '.btn-tareas', function () {
  const raw = $(this).attr('data-tareas') || '[]';
  let tareas;

  try {
  tareas = JSON.parse(raw);
  if (!Array.isArray(tareas)) {
    tareas = [];
  }
} catch (e) {
  console.error('❌ Error al parsear tareas:', e, raw);
  tareas = [];
}

  const $b = $('#modalTareasBody').empty();

  if (tareas.length) {
    tareas.forEach(r => {
      $b.append(`
        <tr>
          <td>${esc(r.tarea)}</td>
          <td>${esc(r.responsable)}</td>
          <td>${esc(r.fecha)}</td>
        </tr>
      `);
    });
  } else {
    $b.append('<tr><td colspan="3">No hay tareas</td></tr>');
  }

  cerrarTodosLosModales();
  new bootstrap.Modal($('#modalTareas')[0]).show();
});

    // Modal de detalle
    $('#tablaAusencias').on('click','.btn-detalle',function(){
      const btn=$(this);
      $('#det_form').html(esc(btn.data('form')));
      $('#det_doc').html(esc(btn.data('documento')));
      $('#det_camp').html(esc(btn.data('campana')));
      $('#det_jefe').html(esc(btn.data('jefe')));
      $('#det_tipo').html(esc(btn.data('tipo')));
      $('#det_inicio').html(esc(btn.data('inicio')));
      $('#det_fin').html(esc(btn.data('fin')));
      $('#det_obs').html(esc(btn.data('obs')));
      $('#det_estado_tl').text(esc(btn.data('estadoTeamLead')));
        $('#det_fecha_tl').text(esc(btn.data('fechaTeamLead')));
        $('#det_estado_rrhh').text(esc(btn.data('estadoRrhh')));
        $('#det_fecha_rrhh').text(esc(btn.data('fechaRrhh')));
        $('#det_razon_rechazo').text(esc(btn.data('razonRechazo')));
        cerrarTodosLosModales();
        new bootstrap.Modal($('#modalDetalle')[0]).show();  
      
    });
    
  });
function cerrarTodosLosModales() {
  // Cierra cualquier modal visible
  document.querySelectorAll('.modal.show').forEach(modalEl => {
    const instance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    instance.hide();
  });

  // Elimina manualmente cualquier backdrop que haya quedado pegado
  document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());

  // Quita clases que Bootstrap pone al <body> cuando abre un modal
  document.body.classList.remove('modal-open');
  document.body.style = ''; // Limpia estilos inyectados
}
   
     // Función auxiliar: aplanar array de tareas a string
  function tarefasToString(raw){
  try {
    const arr = JSON.parse(raw);
    return arr.map(r=>
      `Tarea:${r.tarea} | responsable:${r.responsable} | fecha:${r.fecha}`
    ).join('; ');
  } catch {
    return '';
  }
}

  $('#exportExcel').on('click', function(){
    const rows = [];
    $('#tablaAusencias tbody tr').each(function(i, tr){
      const $tr = $(tr);

      // Datos de celdas visibles
      const formulario      = $tr.find('td').eq(1).text().trim();
      const registro        = $tr.find('td').eq(2).text().trim();
      const trabajador      = $tr.find('td').eq(3).text().trim();

      // Tareas en botón
      const btnTareas       = $tr.find('.btn-tareas');
      const tareasRaw       = btnTareas.length
                              ? btnTareas.attr('data-tareas')
                              : '[]';
      const tareasText      = tarefasToString(tareasRaw);

      // Acciones / comprobante
      const adjunto         = $tr.find('td').eq(5).find('a').attr('href') || '';

      // Detalles en data-*
      const btnDetalle      = $tr.find('.btn-detalle');
      const detalle = {
        documento:      btnDetalle.data('documento')     || '',
        campana:        btnDetalle.data('campana')       || '',
        jefe:           btnDetalle.data('jefe')          || '',
        tipo:           btnDetalle.data('tipo')          || '',
        desde:          btnDetalle.data('inicio')        || '',
        hasta:          btnDetalle.data('fin')           || '',
        observaciones:  btnDetalle.data('obs')           || '',
        estadoTL:       btnDetalle.data('estadeteamlead')|| '',
        fechaTL:        btnDetalle.data('fechateamlead') || '',
        estadoRRHH:     btnDetalle.data('estadorrhh')    || '',
        fechaRRHH:      btnDetalle.data('fecharrhh')     || '',
        razonRechazo:   btnDetalle.data('razonrechazo')  || ''
      };

      rows.push({
        Formulario:    formulario,
        FechaRegistro: registro,
        Trabajador:    trabajador,
        Tareas:        tareasText,
        Comprobante:   adjunto,
        Documento:     detalle.documento,
        Campaña:       detalle.campana,
        Jefe:          detalle.jefe,
        TipoAusencia:  detalle.tipo,
        Desde:         detalle.desde,
        Hasta:         detalle.hasta,
        Observaciones: detalle.observaciones,
        EstadoTL:      detalle.estadoTL,
        FechaTL:       detalle.fechaTL,
        EstadoRRHH:    detalle.estadoRRHH,
        FechaRRHH:     detalle.fechaRRHH,
        RazonRechazo:  detalle.razonRechazo
      });
    });

    if (!rows.length) {
      Swal.fire('Sin datos','No hay filas que exportar','info');
      return;
    }

    // Generar libro de SheetJS
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet(rows, { header: Object.keys(rows[0]) });
    XLSX.utils.book_append_sheet(wb, ws, 'Ausencias');

    // Desencadenar descarga
    XLSX.writeFile(wb, 'Ausencias_Export_'+Date.now()+'.xlsx');
  });  
  document.querySelector('#botonCerrarTodo')?.addEventListener('click', cerrarTodosLosModales);
  document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    cerrarTodosLosModales();
  }
});

window.cargarAusencias = function() {
  fetch('/intranet/sistema/pages/formularioAusencias/get_ausencias.php?t=' + Date.now())
    .then(res => res.json())
    .then(data => {
      const tabla = $('#tablaAusencias').DataTable();
      tabla.clear(); // Limpiar tabla

      data.forEach((a, i) => {
        const tareasBtn = Array.isArray(a.tareas) && a.tareas.length
  ? `<button class="btn btn-sm btn-info btn-tareas" data-tareas='${JSON.stringify(a.tareas)}'>Tareas</button>`
  : `<span class="text-muted">Sin tareas</span>`;

let comprobanteBtn = `<span class="text-muted">Sin foto</span>`;
if (a.comprobantes) {
  const rutas = a.comprobantes.split(',');
  comprobanteBtn = `<div class="d-flex flex-wrap gap-1">` + 
    rutas.map((ruta, idx) => `
      <a href="/intranet/Ausencia/${ruta.trim()}" 
         class="btn btn-sm btn-primary" 
         download 
         target="_blank">
        Adjunto ${idx + 1}
      </a>
    `).join('') + 
  `</div>`;
}

        tabla.row.add([
          i + 1,
          a.numero_formulario || '',
         a.fecha_registro ? a.fecha_registro.split('T')[0] : '',
          a.nombre_completo || '',
          tareasBtn,
          comprobanteBtn,
          `<button class="btn btn-sm btn-secondary btn-detalle"
              data-form="${a.numero_formulario}"
              data-documento="${a.numero_documento}"
              data-campana="${a.nombre_campana}"
              data-jefe="${a.nombre_jefe}"
              data-tipo="${a.tipo_ausencia}"
              data-inicio="${a.fecha_inicio}"
              data-fin="${a.fecha_fin}"
              data-obs="${a.observaciones}"
              data-estado-team-lead="${a.estado_team_lead}"
              data-fecha-team-lead="${a.fecha_team_lead}"
              data-estado-rrhh="${a.estado_rrhh}"
              data-fecha-rrhh="${a.fecha_rrhh}"
              data-razon-rechazo="${a.razon_rechazo}">
              Ver detalle
          </button>`
        ]);
      });

      tabla.draw(); // Redibuja la tabla
    })
    .catch(error => console.error('Error cargando ausencias:', error)); // <- ahora sí correcto
};


    })();

</script>






