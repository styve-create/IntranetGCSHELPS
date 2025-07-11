<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión
require_once(__DIR__ . '/../../../app/controllers/config.php'); 

include_once(__DIR__ . '/../../analisisRecursos.php');


// Ruta del archivo de log
$log_index = __DIR__ . '/log_index.txt';

// Función para escribir en el log
function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

log_index("Cargado index.php");

// Obtener trabajadores desde la base de datos
try {
    $stmt = $pdo->query("SELECT id, nombre_completo, numero_documento, email FROM trabajadores");
    $trabajadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    log_index("Trabajadores cargados correctamente.");
} catch (Exception $e) {
    log_index("Error al obtener trabajadores: " . $e->getMessage());
}

// Equipos a asignar
$equipos = [
    'silla' => true,
    'audifonos' => true,
    'computador' => true,
    'mouse' => true,
    'monitor' => true,
    'carnet' => false,
    'cinta porta carnet' => false
];

log_index("Equipos a asignar definidos.");
?>


 <link rel="stylesheet" href="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?php echo $URL; ?>/librerias/vendor/npm-asset/bootstrap/dist/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f4f6f8;
            font-family: 'Segoe UI', sans-serif;
        }
        .form-container {
            max-width: 720px;
            background: white;
            padding: 35px 30px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            margin: 60px auto;
        }
        .form-title {
            font-weight: bold;
            font-size: 1.8rem;
            margin-bottom: 25px;
            color: #007ea7;
            text-align: center;
        }
        .serial-input {
            margin-top: 10px;
        }
        .btn {
            padding: 10px 20px;
            font-weight: 500;
        }
        .form-check-sm .form-check-input,
.form-check-sm .form-check-label {
    font-size: 0.9rem;
    line-height: 1.2;
}


    </style>

  <div id="contenido-principal">
      <div class="container-fluid">
    <div class="form-container">
        <div class="form-title">Asignación de Equipos</div>
        <form method="post" id="formAsignacion">
           <div class="mb-3">
              
              <label class="form-label" for="selectTrabajador">Seleccionar trabajador</label>
              <select id="selectTrabajador" name="trabajador_id" class="form-select" autocomplete="off">
                <option value="">-- Manual --</option>
                <?php foreach ($trabajadores as $t): ?>
                <option value="<?= $t['id'] ?>"
                        data-nombre="<?= htmlspecialchars($t['nombre_completo'] ?? '') ?>"
                        data-documento="<?= htmlspecialchars($t['numero_documento'] ?? '') ?>"
                        data-email="<?= htmlspecialchars($t['email'] ?? '') ?>">
                  <?= htmlspecialchars($t['nombre_completo'] ?? '') ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            
                   <div class="row mb-3">
                  <div class="col-md-4">
                    <label class="form-label">Nombre del Trabajador</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required autocomplete="name">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Documento</label>
                    <input type="text" name="documento" id="documento" class="form-control" required pattern="\d+">
                  </div>
                  <div class="col-md-5">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                  </div>
                </div>
            <div class="mb-3">
                <label class="form-label mb-2">Equipos Asignados</label>
                <div class="card p-3">
                    <div class="row">
                        <?php foreach ($equipos as $equipo => $requiere_serial): ?>
                            <div class="col-md-6 col-12 mb-2">
                                <div class="form-check d-flex align-items-center" style="font-size: 1.1rem;">
                                    <input class="form-check-input equipo-check me-3" type="checkbox" name="equipos[]" value="<?= $equipo ?>" id="check_<?= $equipo ?>" style="transform: scale(1.3);">
                                    <label class="form-check-label fw-bold me-3" for="check_<?= $equipo ?>">
                                        <?= ucfirst($equipo) ?>
                                    </label>
                                    <?php if ($requiere_serial): ?>
                                        <input type="text" class="form-control form-control-sm serial-input" name="seriales[<?= $equipo ?>]" placeholder="Serial"  disabled>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
         </div>
                <div class="mb-3">
                    <label class="form-label">Fecha de Registro (opcional- si no se pone una fecha se utiliza la fecha del momento de creacion del registro)</label>
                    <input type="date" name="fecha_registro" id="fecha_registro" class="form-control">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary " id="btnEnviar">Enviar</button>
                    <a href="#" class="btn btn-secondary enlaceDinamicos" data-link="listadoInventario">Cancelar</a>
                    
                </div>
            </form>
        </div>
</div>
</div>

<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/jquery/dist/jquery.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function(){
    
    $('#selectTrabajador').select2({
  placeholder: 'Escribe para buscar…',
  width: '100%'
});
    
   $(document).on('input', '#searchTrabajador', function(){
    const term = this.value.toLowerCase();
    $('#selectTrabajador option').each(function(){
      const $opt = $(this);
      if ($opt.val() === '' || $opt.text().toLowerCase().includes(term)) {
        $opt.show();
      } else {
        $opt.hide();
      }
    });
  });

  // 2) Autocompletar datos
  $(document).on('change', '#selectTrabajador', function(){
    const $sel = $(this).find('option:selected');
    $('#nombre').val( $sel.data('nombre')   || '' );
    $('#documento').val( $sel.data('documento') || '' );
    $('#email').val( $sel.data('email')     || '' );
  });
  
    // 3) Serial inputs
  $(document).on('change', '.equipo-check', function(){
    const $inp = $(this).closest('.form-check').find('.serial-input');
    if (this.checked) $inp.prop({disabled:false, required:true});
    else             $inp.prop({disabled:true, required:false}).val('');
  });

  // 2. Habilitar/deshabilitar serial
  document.querySelectorAll('.equipo-check').forEach(chk => {
    chk.addEventListener('change', function(){
      const inp = this.closest('.form-check').querySelector('.serial-input');
      if (this.checked && inp) {
        inp.disabled = false;
        inp.required = true;
      } else if (inp) {
        inp.disabled = true;
        inp.value = '';
        inp.removeAttribute('required');
      }
    });
  });

  // 3. Envío con fetch
  document.getElementById('formAsignacion').addEventListener('submit', function(e){
    e.preventDefault();
    const form = this;
    const btn  = document.getElementById('btnEnviar');
    const fd   = new FormData(form);

    btn.disabled = true;
    btn.textContent = 'Enviando…';

    fetch('<?= $URL ?>/sistema/pages/inventario/save.php', {  // usa aquí tu $URL correcto
      method: 'POST',
      body: fd
    })
    .then(r => r.json())
    .then(res => {
      if (res.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Asignación completada',
          text: res.message || 'Los equipos fueron asignados correctamente.',
          confirmButtonColor: '#007ea7'
        }).then(() => form.reset());
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: res.message || 'Ocurrió un error al guardar la asignación.',
          confirmButtonColor: '#d33'
        });
      }
    })
    .catch(() => {
      Swal.fire({
        icon: 'error',
        title: 'Error de servidor',
        text: 'No se pudo procesar la solicitud. Inténtalo más tarde.',
        confirmButtonColor: '#d33'
      });
    })
    .finally(() => {
      btn.disabled = false;
      btn.textContent = 'Enviar';
    });
  });
})();
</script>
