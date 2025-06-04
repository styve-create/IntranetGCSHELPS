<?php
// Configuración inicial
include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Bogota');

$items = $pdo->query("SELECT * FROM tb_carrusel ORDER BY orden ASC")->fetchAll();

?>

        <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div id="contenido-principal">
    
          <form id="formCarrusel" enctype="multipart/form-data">
          <input type="hidden" name="id" value="">
          <div class="mb-2">
            <label>Título</label>
            <input type="text" name="titulo" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control" required></textarea>
          </div>
          <div class="row">
          <div class="col-md-6 mb-2">
            <label>Fecha Inicio</label>
            <input type="date" name="fecha_inicio" class="form-control" required>
          </div>
          <div class="col-md-6 mb-2">
            <label>Fecha Fin</label>
            <input type="date" name="fecha_fin" class="form-control" required>
          </div>
        </div>
          <div class="mb-2">
            <label>Imagen</label>
            <input type="file" name="imagen" class="form-control">
          </div>
          <button type="submit" class="btn btn-primary">Guardar</button>
    </form>

<br><hr>
  
  <table class="table" id="tablaCarrusel">
  <thead>
    <tr>
      <th>Imagen</th>
      <th>Título</th>
      <th>Descripción</th>
      <th>Estado</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($items as $item): ?>
      <tr>
        <td>
          <?php if (!empty($item['imagen_url'])): ?>
            <?php $final_url =  $item['imagen_url']; ?>
            <img src="<?= htmlspecialchars($final_url) ?>" alt="Imagen carrusel" width="100">
          <?php else: ?>
            <span class="text-muted">Sin foto</span>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($item['titulo']) ?></td>
        <td><?= htmlspecialchars($item['descripcion']) ?></td>
        <td><?= $item['estado'] ?></td>
        <td>
          <button class="btn btn-sm btn-warning" data-id="<?= $item['id'] ?>">Editar</button>
          <button class="btn btn-sm btn-danger" data-id="<?= $item['id'] ?>">Eliminar</button>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
  
</div>




<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (function(){
document.getElementById('formCarrusel').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);

  try {
    const response = await fetch('/intranet/sistema/pages/informacion/save_carrusel.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      alert('Elemento guardado correctamente');
      form.reset();
      $('#tablaCarrusel').DataTable().ajax.reload();
    } else {
      alert('Error al guardar: ' + (result.message || 'Error desconocido'));
    }
  } catch (error) {
    console.error('Error en la solicitud:', error);
    alert('Ocurrió un error al procesar la solicitud.');
  }
});

$(document).ready(function () {
  console.log('Document ready, initializing DataTable...');

  $('#tablaCarrusel').DataTable({
    ajax: {
      url: '/intranet/sistema/pages/informacion/save_carrusel.php?listar=1',
      dataSrc: ''
    },
    columns: [
      {
        data: 'imagen_url',
        render: function (data) {
          const url = data ? ('<?= $URL ?>' + data) : '/intranet/imagen/sinFondo.jpg';
          return `<img src="${url}" width="100">`;
        }
      },
      { data: 'titulo' },
      { data: 'descripcion' },
      { data: 'estado' },
      {
        data: 'id',
        render: function (data) {
          return `
            <button class="btn btn-sm btn-warning" data-id="${data}">Editar</button>
            <button class="btn btn-sm btn-danger" data-id="${data}">Eliminar</button>
          `;
        }
      }
    ],
    responsive: true,
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
    }
  });
});

})();

</script>
 
 