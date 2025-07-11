<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');


// Determinar cuántos equipos máximos hay en un formulario
$maxEquipos = 0;
foreach ($formularios as $formulario) {
    $equipos = !empty($formulario['equipos']) ? array_map('trim', explode(',', $formulario['equipos'])) : [];
    $maxEquipos = max($maxEquipos, count($equipos));
}

function parseList($string) {
    $decoded = json_decode($string, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        return $decoded; // no usar array_map aquí si es asociativo
    } else {
        return array_map('trim', explode(',', $string));
    }
}

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
    <div class="container-fluid mt-4">
    <div class="card-custom">
         <h2 class="text-center">Formulario de Asignaciones</h2>
       <br>  
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
        <button id="exportExcelFiltro" class="btn btn-success custom-excel-btn mb-3">
  <i class="fas fa-file-excel"></i> Descargar Excel Filtrado
</button>

    <div class="table-responsive">
        <table id="tablaFormularios" class="table table-bordered table-striped">
                <thead>
  <tr data-id="<?= $formulario['id'] ?>">
    <th>N° Formulario</th>
    <th>Fecha Registro</th>
    <th>Nombre</th>
    <th>Documento</th>
    <th>Ver Detalle</th>
    <th>Actualizar</th>
    <th>Recibir Equipos</th>
  </tr>
</thead>
<tbody id="tbodyFormularios">
  <!-- Aquí se insertarán las filas dinámicamente -->
</tbody>
        </table>
    </div>
    </div>
   
</div>
        <div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalles del Formulario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="detalleFormulario">
        <!-- Aquí se insertará el contenido dinámicamente -->
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalRecibir" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Recibir Equipos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formRecibirEquipos">
        <div class="modal-body" id="contenedorEquipos"></div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Desasignar Equipos</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
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
    (function() {
        
   window.cargarFormularios = function() {
  fetch('/intranet/sistema/pages/inventario/get_formularios.php?t=' + Date.now())
    .then(res => res.json())
    .then(data => {
      const tbody = document.getElementById('tbodyFormularios');
      tbody.innerHTML = '';

      data.forEach(formulario => {
        const equipos = JSON.parse(formulario.equipos || '[]');
        const seriales = JSON.parse(formulario.seriales || '{}');

        const detalle = JSON.stringify(formulario).replace(/"/g, '&quot;');

        const fila = `
          <tr data-id="${formulario.id}">
            <td>${formulario.numero_formulario}</td>
            <td>${formulario.fecha_registro.split(' ')[0]}</td>
            <td>${formulario.nombre}</td>
            <td>${formulario.documento}</td>
            <td>
              <button class="btn btn-info btn-sm ver-detalle" data-formulario="${detalle}">
                <i class="fas fa-eye"></i> Ver
              </button>
            </td>
            <td>
              <a href="#" class="btn btn-warning enlaceDinamicos" data-link="inventarioActualizarRegistros" data-id="${formulario.id}">
                <i class="fas fa-edit"></i> Actualizar
              </a>
            </td>
            <td>
              <button class="btn btn-success btn-recibir"
                      data-id="${formulario.id}"
                      data-equipos='${JSON.stringify(equipos)}'
                      data-seriales='${JSON.stringify(seriales)}'>
                <i class="fas fa-box-open"></i> Recibir
              </button>
            </td>
          </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', fila);
      });

      // 🔥 Destruir DataTable existente y volver a crear
      if ($.fn.DataTable.isDataTable('#tablaFormularios')) {
        $('#tablaFormularios').DataTable().destroy();
      }

      $('#tablaFormularios').DataTable({
        scrollX: true,
        responsive: true,
        pageLength: 5,
        lengthChange: false,
        autoWidth: false,
        paging: true,
        info: false,
        searching: true,
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="fas fa-file-excel"></i> Excel',
            className: 'btn btn-success custom-excel-btn'
          }
        ],
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json',
          paginate: {
            previous: '‹',
            next: '›',
            first: '«',
            last: '»'
          }
        }
        
      });

    })
    .catch(err => {
      console.error('Error al cargar formularios:', err);
    });
};

        $(document).ready(function () {
            
    console.log('Document ready, initializing DataTable...');
    window.cargarFormularios();
    
   // Filtro personalizado de fechas
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var start = $('#start_date').val();
            var end = $('#end_date').val();

            if (!start || !end) return true;

            var min = new Date(start);
            var max = new Date(end);

            var dateStr = data[1]; // Asumiendo que columna 1 tiene fecha 'YYYY-MM-DD'
            var date = new Date(dateStr);

            return date >= min && date <= max;
        }
    );

    // Botón FILTRAR
    $('#applyFilter').on('click', function () {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        if (!startDate || !endDate) {
            Swal.fire('Campos incompletos', 'Por favor selecciona ambas fechas', 'warning');
            return;
        }

        tabla.draw();
    });

    // Botón RESTABLECER
    $('#resetFilter').on('click', function () {
        $('#start_date').val('');
        $('#end_date').val('');
        tabla.search('').columns().search('').draw(); // <- Esto reinicia todo el filtrado
    });
    
    $('#exportExcelFiltro').on('click', function () {
    const start = $('#start_date').val();
    const end = $('#end_date').val();

    if (!start || !end) {
        Swal.fire('Fechas requeridas', 'Selecciona una fecha de inicio y fin para exportar.', 'warning');
        return;
    }

    const url = `/intranet/sistema/pages/inventario/exportar_excel.php?start=${start}&end=${end}&t=${Date.now()}`;
    window.open(url, '_blank');
});
    
    $(document).on('click', '.ver-detalle', function () {
  const data = JSON.parse($(this).attr('data-formulario'));
  let html = `<p><strong>Número:</strong> ${data.numero_formulario}</p>
              <p><strong>Nombre:</strong> ${data.nombre}</p>
              <p><strong>Documento:</strong> ${data.documento}</p>
              <p><strong>Email:</strong> ${data.email}</p>
              <p><strong>Fecha Registro:</strong> ${data.fecha_registro}</p>
              <p><strong>Estado:</strong> ${data.estado_trabajador}</p>
              <p><strong>Comentario:</strong> ${data.comentarios_trabajador}</p>`;

  try {
    const equipos = JSON.parse(data.equipos);
    const seriales = JSON.parse(data.seriales);
    html += `<h5>Equipos:</h5><ul>`;
    equipos.forEach(eq => {
      html += `<li>${eq} - Serial: ${seriales[eq] || '-'}</li>`;
    });
    html += `</ul>`;
  } catch (e) {}

  $('#detalleFormulario').html(html);
  $('#modalDetalle').modal('show');
});

$(document).on('click', '.btn-recibir', function () {
  const id = $(this).data('id');

  fetch(`/intranet/sistema/pages/inventario/get_detalle_formulario.php?id=${id}&t=${Date.now()}`)
    .then(res => res.json())
    .then(data => {
      const equipos = JSON.parse(data.equipos || '[]');
      const seriales = JSON.parse(data.seriales || '{}');

      let html = `<input type="hidden" name="formulario_id" value="${id}">`;
      html += `<p>Selecciona los equipos a desasignar:</p>`;

      equipos.forEach(eq => {
        const serial = seriales[eq] || 'N/A';
        html += `
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="equipos[]" value="${eq}">
            <label class="form-check-label">${eq} - Serial: ${serial}</label>
          </div>`;
      });

      $('#contenedorEquipos').html(html);
      $('#modalRecibir').modal('show');
    })
    .catch(err => {
      console.error('Error al obtener detalles del formulario:', err);
      Swal.fire('Error', 'No se pudieron obtener los datos actualizados.', 'error');
    });
});

// Enviar el formulario
$('#formRecibirEquipos').on('submit', function (e) {
  e.preventDefault();
  const formData = $(this).serialize();
  const id = $(this).data('id');

  Swal.fire({
    title: '¿Estás seguro?',
    text: "Los equipos seleccionados serán desasignados.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, desasignar',
    cancelButtonText: 'Cancelar'
  }).then(result => {
    if (result.isConfirmed) {
      fetch(`/intranet/sistema/pages/inventario/desasignar_equipos.php?t=${Date.now()}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          Swal.fire('¡Desasignado!', data.message, 'success').then(() => {
  $('#modalRecibir').modal('hide');

  // 🔄 Actualizar la fila de la tabla con nueva información por AJAX
  fetch(`/intranet/sistema/pages/inventario/get_fila_formulario.php?id=${id}&t=${Date.now()}`)
    .then(r => r.text())
    .then(html => {
      // Reemplaza la fila de la tabla por el nuevo contenido
      const row = $(`#tablaFormularios tr[data-id="${id}"]`);
      if (row.length) {
        row.replaceWith(html);
      }
    });
});
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      });
    }
  });
});

});



})();

</script>

