<?php
  ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Bogota');

include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');

?>

<link rel="stylesheet" href="<?php echo $URL; ?>/librerias/vendor/npm-asset/bootstrap/dist/css/bootstrap.min.css">

<style>
  .custom-excel-btn {
    background-color: #217346 !important;
    color: white !important;
    border-radius: 6px;
    padding: 6px 12px;
    font-weight: bold;
  }
  .custom-excel-btn i {
    margin-right: 5px;
  }
</style>

<div id="contenido-principal" class="p-4">
  <!-- Botón para abrir modal -->
  <button class="btn btn-primary mb-3 traducible" id="btnAgregarAnuncio">
    <i class="bi bi-plus-circle"></i> Agregar Anuncio
  </button>

  <!-- Tu tabla existente -->
 <table class="table" id="tablaCarrusel">
  <thead>
    <tr>
      <th class="traducible">Imagen</th>
      <th class="traducible">Título</th>
      <th class="traducible">Descripción</th>
      <th class="traducible">Estado</th>
      <th class="traducible">Acciones</th>
    </tr>
  </thead>
  <tbody id="carruselBody">
    <!-- Aquí irá el contenido vía JS -->
  </tbody>
</table>
  
  <!-- MODAL: Formulario de Carrusel -->
<div class="modal fade" id="modalCarrusel" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="formCarrusel" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title traducible">Agregar / Editar Anuncio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="id" value="">
          <!-- guardamos la URL de la imagen previa -->
          <input type="hidden" name="imagen_actual" value="">

          <!-- Contenedor de vista previa -->
          <div class="mb-3" id="previewContainer" style="display:none;">
            <label class="form-label traducible">Imagen actual:</label>
            <img id="previewImagen" src="" class="img-fluid rounded mb-2" style="max-height:200px;">
          </div>

          <div class="mb-2">
            <label class="form-label traducible">Título</label>
            <input type="text" name="titulo" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label traducible">Descripción</label>
            <textarea name="descripcion" class="form-control" required></textarea>
          </div>
          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="form-label traducible">Fecha Inicio</label>
              <input type="date" name="fecha_inicio" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label traducible">Fecha Fin</label>
              <input type="date" name="fecha_fin" class="form-control" required>
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label traducible">Imagen (opcional)</label>
            <input type="file" name="imagen" id="inputImagen" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary traducible" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary traducible">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/jquery/dist/jquery.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/sweetalert2/dist/sweetalert2.all.min.js"></script>

<script>
(function() {
  const BASE_URL = 'https://gcshelps.com/intranet';
window.CargarRegistro = async function() {
    
fetch(`/intranet/sistema/pages/informacion/cargar_anuncios.php?t=${new Date().getTime()}`)
            .then(res => res.json())
            .then(data => {
                console.log("data", data);
              initCarruselTable(data)
            })
            .catch(err => {
                
                console.error('Error al obtener clientes:', err);
            });

};

function initCarruselTable(data) {
   if ( $.fn.dataTable.isDataTable('#tablaCarrusel') ) {
    const table = $('#tablaCarrusel').DataTable();
    table.clear().rows.add(data).draw();
    return;
  }

   $('#tablaCarrusel').DataTable({
  data: data,
  scrollX: true,
  responsive: true,
  pageLength: 5,
  lengthChange: false,
  autoWidth: false,

  buttons: [
    {
      extend: 'excelHtml5',
      text: '<i class="bi bi-file-earmark-excel"></i> Excel',
      className: 'btn btn-success custom-excel-btn',
      exportOptions: {
        columns: ':not(:last-child)'  // si quieres excluir la columna de acciones
      }
    }
  ],
      columns: [
        {
          data: 'imagen_url',
          render(src) {
            if (src) {
              return `<img src="${BASE_URL + src}" width="80" class="img-thumbnail">`;
            }
            return `<span class="text-muted">Sin imagen</span>`;
          }
        },
        { data: 'titulo' },
        { data: 'descripcion' },
        { data: 'estado' },
        {
          data: 'id',
          orderable: false,
          render(id) {
            return `
              <button class="btn btn-sm btn-warning me-1" data-action="edit" data-id="${id}">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-sm btn-danger" data-action="delete" data-id="${id}">
                <i class="bi bi-trash"></i>
              </button>
            `;
          }
        }
      ],
      responsive: true,
      language: {
    url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json',
    paginate: {
      previous: '‹', next: '›', first: '«', last: '»'
    }
      }
    });
  }


 
  // 1) Abrir modal “Agregar Anuncio”
  $('#btnAgregarAnuncio').on('click', function() {
    const form = $('#formCarrusel')[0];
    form.reset();
    form.elements.id.value = '';
    $('#previewContainer').hide();
    // mostrar modal
    new bootstrap.Modal($('#modalCarrusel')[0]).show();
  });

  // 2) Delegación: Editar / Eliminar desde la tabla
  $('#tablaCarrusel tbody').on('click', 'button', function() {
    const action = $(this).data('action');
    const id     = $(this).data('id');
    const dt     = $('#tablaCarrusel').DataTable();
    const row    = dt.row($(this).closest('tr')).data();

    if (action === 'edit') {
      // 2a) Rellenar formulario
      const f = $('#formCarrusel')[0];
      f.elements.id.value           = id;
      f.elements.titulo.value       = row.titulo;
      f.elements.descripcion.value  = row.descripcion;
      f.elements.fecha_inicio.value = row.fecha_inicio;
      f.elements.fecha_fin.value    = row.fecha_fin;
      if (row.imagen_url) {
        $('#previewImagen').attr('src', BASE_URL + row.imagen_url);
        $('#previewContainer').show();
        f.elements.imagen_actual.value = row.imagen_url;
      } else {
        $('#previewContainer').hide();
        f.elements.imagen_actual.value = '';
      }
      // mostrar modal
      new bootstrap.Modal($('#modalCarrusel')[0]).show();

    } else if (action === 'delete') {
      // 2b) Confirmar y eliminar
      Swal.fire({
        title: '¿Eliminar anuncio?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then(async ({ isConfirmed }) => {
        if (!isConfirmed) return;
        await fetch(`/intranet/sistema/pages/informacion/save_carrusel.php?t=${new Date().getTime()}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ eliminar: 1, id })
        });
        Swal.fire('Eliminado', 'El anuncio ha sido eliminado.', 'success');
        window.CargarRegistro();
      });
    }
  });

  // 3) Submit del formulario (Agregar / Editar)
  $('#formCarrusel').on('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    try {
      const res    = await fetch(`/intranet/sistema/pages/informacion/save_carrusel.php?t=${new Date().getTime()}`, {
        method: 'POST',
        body: formData
      });
      const result = await res.json();
      if (result.success) {
        Swal.fire({
          icon: 'success',
          title: '¡Listo!',
          text: 'Anuncio guardado correctamente.',
          timer: 1500,
          showConfirmButton: false
        });
        // cerrar modal
        bootstrap.Modal.getInstance($('#modalCarrusel')[0]).hide();
        // recargar tabla
        window.CargarRegistro();
      } else {
        Swal.fire('Error', result.message || 'No se pudo guardar', 'error');
      }
    } catch (err) {
      console.error(err);
      Swal.fire('Error', 'Ocurrió un error en la petición', 'error');
    }
  });

  // 4) Vista previa al cambiar la imagen
  $('#inputImagen').on('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      $('#previewImagen').attr('src', e.target.result);
      $('#previewContainer').show();
    };
    reader.readAsDataURL(file);
  });
 
 window.CargarRegistro();
})();
</script>