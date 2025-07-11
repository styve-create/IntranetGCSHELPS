<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<div class='alert alert-danger m-4'>ID de trabajador no proporcionado</div>";
    exit;
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div id="contenido-principal">
  <div class="container mt-5 mb-5">
    <div class="card text-center shadow" id="contenedor-edicion">
      <div class="card-body">Cargando datos del trabajador...</div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function() {
  const id = <?= json_encode($id) ?>;
  if (!id) return;

  fetch(`/intranet/sistema/pages/trabajadores/api_trabajador.php?id=${id}&t=${Date.now()}`)
    .then(res => res.json())
    .then(async data => {
      if (!data || !data.id) {
        document.getElementById('contenedor-edicion').innerHTML = '<div class="alert alert-danger">Trabajador no encontrado</div>';
        return;
      }

      const selects = await fetch(`/intranet/sistema/pages/trabajadores/api_datos_selects.php?t=${Date.now()}`)
        .then(r => r.json());

      const campos_personales = ['nombre_completo', 'grupo_sanguineo', 'estado_civil', 'edad', 'tipo_documento', 'numero_documento', 'fecha_expedicion', 'lugar_expedicion', 'fecha_nacimiento', 'lugar_nacimiento', 'nivel_estudio', 'profesion', 'domicilio', 'ciudad', 'celular', 'email'];
      const campos_laborales = ['tipo_contrato', 'horas_contratadas', 'salario_basico', 'auxilio_transporte', 'eps', 'codigo_pension', 'codigo_cesantias', 'estado', 'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'tipo_retiro', 'finalizacion_contractual', 'cargo_certificado'];
      const campos_contacto = ['nombre_contacto_emergencia', 'numero_contacto_emergencia'];
      const campos_fecha = ['fecha_expedicion', 'fecha_nacimiento', 'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'finalizacion_contractual'];

      const crearInput = (campo, valor = '') => {
        const label = campo.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        const type = campos_fecha.includes(campo) ? 'date' : 'text';
        const val = valor ? (type === 'date' ? valor.split(" ")[0] : valor) : '';
        return `<div class="col-md-4 mb-3">
                  <label class="form-label">${label}</label>
                  <input name="${campo}" class="form-control" type="${type}" value="${val || ''}">
                </div>`;
      };

      const crearSelect = (campo, opciones, valorSeleccionado) => {
        const label = campo.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        const options = opciones.map(opt => {
          const value = opt.id ?? opt.nombre;
          const nombre = opt.nombre;
          const selected = value == valorSeleccionado ? 'selected' : '';
          return `<option value="${value}" ${selected}>${nombre}</option>`;
        }).join('');
        return `<div class="col-md-4 mb-3">
                  <label class="form-label">${label}</label>
                  <select name="${campo}" class="form-select">${options}</select>
                </div>`;
      };

      const html = `
        <form id="form-edicion">
          <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
              <li class="nav-item"><button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#datos-personales">Datos Personales</button></li>
              <li class="nav-item"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#info-laboral">Información Laboral</button></li>
              <li class="nav-item"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#contacto">Contacto</button></li>
            </ul>
          </div>
          <div class="card-body tab-content">
            <div class="tab-pane fade show active" id="datos-personales">
              <div class="row">${campos_personales.map(c => crearInput(c, data[c])).join('')}</div>
            </div>
            <div class="tab-pane fade" id="info-laboral">
              <div class="row">
                ${campos_laborales.map(c => crearInput(c, data[c])).join('')}
                ${crearSelect('campana', selects.campanas, data.id_campana)}
                ${crearSelect('cargo', selects.cargos, data.puesto_id)}
              </div>
            </div>
            <div class="tab-pane fade" id="contacto">
              <div class="row">${campos_contacto.map(c => crearInput(c, data[c])).join('')}</div>
            </div>
          </div>
          <div class="card-footer text-muted d-flex gap-3 justify-content-center">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="#" class="btn btn-secondary enlaceDinamicos" data-link="trabajadores">Cancelar</a>
          </div>
        </form>`;

      document.getElementById('contenedor-edicion').innerHTML = html;

      document.getElementById('form-edicion').addEventListener('submit', async e => {
  e.preventDefault();
  const form = e.target;
  const dataToSend = Object.fromEntries(new FormData(form).entries());
  dataToSend.id = id;

  const camposFecha = ['fecha_expedicion', 'fecha_nacimiento', 'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'finalizacion_contractual'];
  camposFecha.forEach(f => {
    if (dataToSend[f] === '') {
      dataToSend[f] = null;
    }
  });

  const res = await fetch(`/intranet/sistema/pages/trabajadores/api_actualizar_trabajador.php?t=${Date.now()}`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(dataToSend)
  });

  const respuesta = await res.json();
  if (respuesta.success) {
    Swal.fire('Éxito', respuesta.message, 'success');
  } else {
    Swal.fire('Error', respuesta.message, 'error');
  }
});
    });
})();
</script>