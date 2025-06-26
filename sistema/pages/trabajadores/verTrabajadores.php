<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');


$id = $_GET['id'] ?? null;
if (!$id) {
    mostrarSweetAlert('Error', 'ID de trabajador no proporcionado.', 'error');
    exit;
}


?>
<div class="container mt-5 mb-5" id="contenedor-detalle-trabajador">
  <div class="text-center">Cargando datos...</div>
</div>

<script>
    (function() {
        const idTrabajador = <?= json_encode($id); ?>;

if (idTrabajador) {
  fetch(`/intranet/sistema/pages/trabajadores/api_trabajador.php?id=${idTrabajador}&t=${Date.now()}`)
    .then(res => res.json())
    .then(data => {
      if (!data || !data.id) {
        document.getElementById('contenedor-detalle-trabajador').innerHTML = '<div class="alert alert-danger">Trabajador no encontrado</div>';
        return;
      }

      const campos_personales = [
        'nombre_completo', 'grupo_sanguineo', 'estado_civil', 'edad', 'tipo_documento', 'numero_documento',
        'fecha_expedicion', 'lugar_expedicion', 'fecha_nacimiento', 'lugar_nacimiento', 'nivel_estudio',
        'profesion', 'domicilio', 'ciudad', 'celular', 'email'
      ];

      const campos_laborales = [
        'tipo_contrato', 'horas_contratadas', 'salario_basico', 'auxilio_transporte', 'eps',
        'codigo_pension', 'codigo_cesantias', 'estado', 'fecha_ingreso_gcs', 'fecha_retiro_gcs',
        'tipo_retiro', 'campana', 'cargo', 'finalizacion_contractual', 'cargo_certificado'
      ];

      const campos_contacto = ['nombre_contacto_emergencia', 'numero_contacto_emergencia'];

      function crearCampo(label, valor) {
        return `<div class="col-md-4 mb-3">
                  <label class="fw-bold">${label}</label>
                  <div class="form-control bg-light">${valor || '-'}</div>
                </div>`;
      }

      function formatearFecha(fecha) {
        if (!fecha || fecha === '0000-00-00') return '-';
        const [y, m, d] = fecha.split("-");
        return `${d}/${m}/${y}`;
      }

      const renderCampos = (campos, categoria) => {
        return campos.map(campo => {
          let raw = data[campo] ?? '';
          let valor = ['fecha_expedicion', 'fecha_nacimiento', 'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'finalizacion_contractual'].includes(campo)
                        ? formatearFecha(raw)
                        : raw;
          return crearCampo(campo.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()), valor);
        }).join('');
      };

      const html = `
      <div class="card text-center shadow">
        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs" id="infoTabs" role="tablist">
            <li class="nav-item">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#datos-personales">Datos Personales</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#info-laboral">Información Laboral</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#contacto">Contacto de Emergencia</button>
            </li>
          </ul>
        </div>
        <div class="card-body tab-content">
          <div class="tab-pane fade show active" id="datos-personales">
            <div class="row">${renderCampos(campos_personales)}</div>
          </div>
          <div class="tab-pane fade" id="info-laboral">
            <div class="row">${renderCampos(campos_laborales)}</div>
          </div>
          <div class="tab-pane fade" id="contacto">
            <div class="row">${renderCampos(campos_contacto)}</div>
          </div>
        </div>
        <div class="card-footer text-muted">
          <a href="#" class="btn btn-primary enlaceDinamicos" data-link="update_trabajadores" data-id="${data.id}">Editar</a>
          <a href="#" class="btn btn-secondary enlaceDinamicos" data-link="trabajadores">Cancelar</a>
        </div>
      </div>
      `;

      document.getElementById('contenedor-detalle-trabajador').innerHTML = html;
    })
    .catch(err => {
      document.getElementById('contenedor-detalle-trabajador').innerHTML = '<div class="alert alert-danger">Error al cargar los datos</div>';
      console.error(err);
    });
} else {
  document.getElementById('contenedor-detalle-trabajador').innerHTML = '<div class="alert alert-warning">ID no especificado</div>';
}
        
    })();

</script>