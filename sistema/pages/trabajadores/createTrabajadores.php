<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div id="contenido-principal">
  <div class="container mt-5 mb-5">
    <div class="card text-center shadow">
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="infoTabs" role="tablist">
          <li class="nav-item">
            <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#datos-personales">Datos Personales</button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#info-laboral">Información Laboral</button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#contacto">Contacto de Emergencia</button>
          </li>
        </ul>
      </div>
      <form id="form-trabajador">
        <div class="card-body tab-content">
          <div class="tab-pane fade show active" id="datos-personales">
            <div class="row" id="campos-personales"></div>
          </div>
          <div class="tab-pane fade" id="info-laboral">
            <div class="row" id="campos-laborales">
              <div class="col-md-6 mb-3">
                <label class="form-label">Campaña</label>
                <select name="campana" class="form-select" required id="select-campana"></select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Cargo</label>
                <select name="cargo" class="form-select" required id="select-cargo"></select>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="contacto">
            <div class="row" id="campos-contacto"></div>
          </div>
        </div>
        <div class="card-footer text-muted">
          <button type="submit" class="btn btn-primary">Crear</button>
          <a href="#" class="btn btn-secondary enlaceDinamicos" data-link="trabajadores">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(async function () {
  const campos = [
    'nombre_completo', 'grupo_sanguineo', 'estado_civil', 'edad', 'tipo_documento', 'numero_documento',
    'fecha_expedicion', 'lugar_expedicion', 'fecha_nacimiento', 'lugar_nacimiento', 'nivel_estudio',
    'profesion', 'domicilio', 'ciudad', 'celular', 'email',
    'cuenta_bancaria', 'banco', 'tipo_cuenta',
    'tipo_contrato', 'horas_contratadas', 'salario_basico', 'auxilio_transporte', 'eps',
    'codigo_pension', 'codigo_cesantias', 'estado',
    'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'finalizacion_contractual', 'tipo_retiro', 'cargo_certificado',
    'nombre_contacto_emergencia', 'numero_contacto_emergencia'
  ];

  const camposFecha = [
    'fecha_expedicion', 'fecha_nacimiento', 'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'finalizacion_contractual'
  ];

  const personales = [
    'nombre_completo', 'grupo_sanguineo', 'estado_civil', 'edad', 'tipo_documento', 'numero_documento',
    'fecha_expedicion', 'lugar_expedicion', 'fecha_nacimiento', 'lugar_nacimiento', 'nivel_estudio',
    'profesion', 'domicilio', 'ciudad', 'celular', 'email'
  ];

  const laborales = [
    'cuenta_bancaria', 'banco', 'tipo_cuenta',
    'tipo_contrato', 'horas_contratadas', 'salario_basico', 'auxilio_transporte',
    'eps', 'codigo_pension', 'codigo_cesantias', 'estado',
    'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'finalizacion_contractual',
    'tipo_retiro', 'cargo_certificado'
  ];

  const contacto = [
    'nombre_contacto_emergencia', 'numero_contacto_emergencia'
  ];

  const datosSelect = await fetch(`/intranet/sistema/pages/trabajadores/api_datos_selects.php?t=${Date.now()}`).then(r => r.json());

  datosSelect.campanas.forEach(c => {
    const opt = document.createElement('option');
    opt.value = c.nombre;
    opt.textContent = c.nombre;
    document.getElementById('select-campana').appendChild(opt);
  });

  datosSelect.cargos.forEach(c => {
    const opt = document.createElement('option');
    opt.value = c.id;
    opt.textContent = c.nombre;
    document.getElementById('select-cargo').appendChild(opt);
  });

  const renderCampo = (campo) => {
    const label = campo.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    const tipo = camposFecha.includes(campo) ? 'date' : 'text';
    return `
      <div class="col-md-4 mb-3">
        <label class="form-label">${label}</label>
        <input type="${tipo}" name="${campo}" class="form-control">
      </div>
    `;
  };

  document.getElementById('campos-personales').innerHTML = personales.map(renderCampo).join('');
  document.getElementById('campos-laborales').innerHTML += laborales.map(renderCampo).join('');
  document.getElementById('campos-contacto').innerHTML = contacto.map(renderCampo).join('');

  document.getElementById('form-trabajador').addEventListener('submit', async e => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target).entries());

    const res = await fetch(`/intranet/sistema/pages/trabajadores/api_crear_trabajador.php?t=${Date.now()}`, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(data)
    });

    const result = await res.json();
    if (result.success) {
      Swal.fire('Éxito', result.message, 'success');
      e.target.reset();
    } else {
      Swal.fire('Error', result.message, 'error');
    }
  });
})();
</script>