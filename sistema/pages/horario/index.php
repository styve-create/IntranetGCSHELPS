<?php
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_name('mi_sesion_personalizada');
session_start();



if (!isset($_GET['ajax']) || $_GET['ajax'] !== '1') {
  die('<div class="alert alert-danger">Error: Carga no permitida directamente.</div>');
}


include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');

$id_usuarios = $_SESSION['usuario_info']['id'];
$hoy = date('Y-m-d');
echo "<script>console.log('PHP SESSION:', " . json_encode($_SESSION) . ");</script>";

$stmt = $pdo->prepare("SELECT * FROM tb_horario WHERE id_usuario = :uid AND fecha = :fecha LIMIT 1");
$stmt->execute([':uid' => $id_usuarios, ':fecha' => $hoy]);
$asistencia = $stmt->fetch(PDO::FETCH_ASSOC);


// Estado inicial por defecto
$estado = [
    'inicio_turno' => null,
    'fin_turno' => null,
    'breaks' => [
        1 => ['inicio' => null, 'fin' => null],
        2 => ['inicio' => null, 'fin' => null],
        3 => ['inicio' => null, 'fin' => null],
    ]
];

// Si hay registro, asignar
if ($asistencia) {
    $estado['inicio_turno'] = $asistencia['hora_inicio_turno'] ?? null;
    $estado['fin_turno'] = $asistencia['hora_fin_turno'] ?? null;

       function toISO8601($datetime) {
        if (!$datetime) return null;
        $dt = new DateTime($datetime);
        return $dt->format('c'); // formato ISO 8601 completo, ej: 2025-05-20T14:31:34+00:00
    }

    for ($i = 1; $i <= 3; $i++) {
        $estado['breaks'][$i]['inicio'] = toISO8601($asistencia["hora_inicio_break{$i}"] ?? null);
        $estado['breaks'][$i]['fin'] = toISO8601($asistencia["hora_fin_break{$i}"] ?? null);
    }
}

?>


<link rel="stylesheet" href="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?php echo $URL; ?>/librerias/vendor/npm-asset/bootstrap/dist/css/bootstrap.min.css">
   
<div id="contenido-principal">
        <div class="container mt-4">
              <div class="bg-white p-4 rounded shadow-sm">
                <h3 class="mb-4 text-center">Control de Asistencia</h3>
            
                    <div class="d-flex flex-wrap gap-4 justify-content-center align-items-start">
                
                              <!-- INICIO TURNO -->
                              <div class="d-flex flex-column align-items-center">
                                <div class="btn-group mb-1">
                                  <button id="btnInicioTurno" class="btn btn-success">Inicio Turno</button>
                                </div>
                                <div class="badge bg-light px-3 py-2 invisible">00:00</div>
                              </div>
                
                              <!-- BREAKS 1, 2 y 3 -->
                              <?php for ($i=1; $i<=3; $i++): ?>
                              <div class="d-flex flex-column align-items-center">
                                <div class="btn-group mb-1" style="gap: 0.5rem;">
                                  <button class="btn btn-warning btnInicioBreak" data-break="<?= $i ?>" disabled> Break <?= $i ?></button>
                                  <button class="btn btn-primary btnFinBreak" data-break="<?= $i ?>" disabled>Fin</button>
                                </div>
                                <div class="badge bg-secondary px-3 py-2" id="timerBreak<?= $i ?>">00:00</div>
                              </div>
                              <?php endfor; ?>
                
                              <!-- FIN TURNO -->
                              <div class="d-flex flex-column align-items-center">
                                <div class="btn-group mb-1">
                                  <button id="btnFinTurno" class="btn btn-danger" disabled>Fin Turno</button>
                                </div>
                                <div class="badge bg-light px-3 py-2 invisible">00:00</div>
                              </div>
                                <!-- TIEMPO EXTRA -->
                            <div class="d-flex flex-column align-items-center">
                              <div class="btn-group mb-1">
                                <button id="btnInicioExtra" class="btn btn-outline-info" disabled>Inicio Tiempo Extra</button>
                                <button id="btnFinExtra" class="btn btn-outline-dark" disabled>Fin Tiempo Extra</button>
                              </div>
                              <div class="badge bg-secondary px-3 py-2" id="timerExtra">00:00</div>
                            </div>
                    </div>
              </div>
          
        
        </div>


</div>
<script src="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function(){
  console.log("📢 Script asistencia.js cargado correctamente");
  const estado = <?= json_encode($estado) ?>;

  // Botones y badges
  const btnInicioTurno = document.getElementById('btnInicioTurno');
  const btnFinTurno    = document.getElementById('btnFinTurno');
  const btnInicioExtra = document.getElementById('btnInicioExtra');
  const btnFinExtra    = document.getElementById('btnFinExtra');
  const timerExtra     = document.getElementById('timerExtra');
  let inicioExtraTime, extraInterval;

  // Configuración de cada break
  const breakConfig = {1:{max:15},2:{max:30},3:{max:15}};
  const timerIntervals = {};

  // Parseamos fechas ISO a objetos Date
  if (estado.inicio_turno) estado.inicio_turno = new Date(estado.inicio_turno);
  if (estado.fin_turno)    estado.fin_turno    = new Date(estado.fin_turno);
  for (let i = 1; i <= 3; i++) {
    if (estado.breaks[i].inicio) estado.breaks[i].inicio = new Date(estado.breaks[i].inicio);
    if (estado.breaks[i].fin)    estado.breaks[i].fin    = new Date(estado.breaks[i].fin);
  }

  function tiempoFormateado(inicio, fin) {
    const diff = Math.floor((fin - inicio)/1000);
    const m = Math.floor(diff/60), s = diff%60;
    return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
  }

  function iniciarTimer(n, inicioDate) {
    const badge = document.getElementById(`timerBreak${n}`);
    if (timerIntervals[n]) clearInterval(timerIntervals[n]);
    timerIntervals[n] = setInterval(()=>{
      const diff = Math.floor((Date.now() - inicioDate)/1000);
      const m = Math.floor(diff/60), s = diff%60;
      badge.innerText = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
      badge.classList.toggle('bg-danger', m >= breakConfig[n].max);
      badge.classList.toggle('bg-secondary', m < breakConfig[n].max);
    },1000);
  }

  function inicializar() {
    const started = !!estado.inicio_turno;
    const ended   = !!estado.fin_turno;

    // 1. Turno
    btnInicioTurno.disabled = started;
    btnFinTurno.disabled    = !started || ended;

    // 2. Tiempo extra
    btnInicioExtra.disabled = !ended;
    btnFinExtra.disabled    = true;
    timerExtra.innerText    = '00:00';

    // 3. Resetea todos los breaks
    for (let i = 1; i <= 3; i++) {
      document.querySelector(`.btnInicioBreak[data-break="${i}"]`).disabled = true;
      document.querySelector(`.btnFinBreak[data-break="${i}"]`).disabled    = true;
      document.getElementById(`timerBreak${i}`).innerText = '00:00';
    }

    // 4. Si shift iniciado y no terminado, habilita solo Break 1
    if (started && !ended) {
      document.querySelector('.btnInicioBreak[data-break="1"]').disabled = false;
    }

    // 5. Por cada break con estado:
    for (let i = 1; i <= 3; i++) {
      const b    = estado.breaks[i];
      const ini  = document.querySelector(`.btnInicioBreak[data-break="${i}"]`);
      const fin  = document.querySelector(`.btnFinBreak[data-break="${i}"]`);
      const badge= document.getElementById(`timerBreak${i}`);

      if (b.inicio && !b.fin) {
        // break en curso
        ini.disabled = true;
        fin.disabled = false;
        iniciarTimer(i, b.inicio);

      } else if (b.inicio && b.fin) {
        // break ya terminado
        ini.disabled    = true;
        fin.disabled    = true;
        badge.innerText = tiempoFormateado(b.inicio, b.fin);

        // habilita siguiente break si existe y shift sigue activo
        if (i < 3 && !ended) {
          document.querySelector(`.btnInicioBreak[data-break="${i+1}"]`).disabled = false;
        }
      }
    }
  }

  // ====== Handlers ======
  btnInicioTurno.addEventListener('click', ()=>{
    fetch('/intranet/sistema/pages/horario/asistencia.php',{
      method:'POST', headers:{'Content-Type':'application/json'},
      body:JSON.stringify({accion:'inicio_turno'})
    }).then(r=>r.json()).then(d=>{
      if(d.success){ estado.inicio_turno = new Date(); inicializar(); }
      else alert('Error al iniciar turno');
    });
  });

  btnFinTurno.addEventListener('click', ()=>{
    fetch('/intranet/sistema/pages/horario/asistencia.php',{
      method:'POST', headers:{'Content-Type':'application/json'},
      body:JSON.stringify({accion:'fin_turno'})
    }).then(r=>r.json()).then(d=>{
      if(d.success){
        estado.fin_turno = new Date();
        Object.values(timerIntervals).forEach(clearInterval);
        inicializar();
      } else alert('Error al finalizar turno');
    });
  });

  document.querySelectorAll('.btnInicioBreak').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const n = btn.dataset.break;
      fetch('/intranet/sistema/pages/horario/asistencia.php',{
        method:'POST', headers:{'Content-Type':'application/json'},
        body:JSON.stringify({accion:'inicio_break',break_num:n})
      }).then(r=>r.json()).then(d=>{
        if(d.success){ estado.breaks[n].inicio = new Date(); inicializar(); }
        else alert('Error al iniciar break '+n);
      });
    });
  });

  document.querySelectorAll('.btnFinBreak').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const n = btn.dataset.break;
      fetch('/intranet/sistema/pages/horario/asistencia.php',{
        method:'POST', headers:{'Content-Type':'application/json'},
        body:JSON.stringify({accion:'fin_break',break_num:n})
      }).then(r=>r.json()).then(d=>{
        if(d.success){ estado.breaks[n].fin = new Date(); clearInterval(timerIntervals[n]); inicializar(); }
        else alert('Error al finalizar break '+n);
      });
    });
  });

  btnInicioExtra.addEventListener('click', ()=>{
    inicioExtraTime = Date.now();
    btnInicioExtra.disabled = true;
    btnFinExtra.disabled    = false;
    if(extraInterval) clearInterval(extraInterval);
    extraInterval = setInterval(()=>{
      const diff = Math.floor((Date.now()-inicioExtraTime)/1000);
      const m = Math.floor(diff/60), s=diff%60;
      timerExtra.innerText = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    },1000);
  });

  btnFinExtra.addEventListener('click', ()=>{
    clearInterval(extraInterval);
    btnFinExtra.disabled = true;
    alert('Tiempo extra finalizado.');
  });


  inicializar();
})();
</script>

