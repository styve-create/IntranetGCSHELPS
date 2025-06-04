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

<script >
        (function(){
console.log("📢 Script asistencia.js cargado correctamente");
const estado =  <?= json_encode($estado) ?>;;
    const btnInicioTurno = document.getElementById('btnInicioTurno');
    const btnFinTurno = document.getElementById('btnFinTurno');
    const btnInicioExtra = document.getElementById('btnInicioExtra');
const btnFinExtra = document.getElementById('btnFinExtra');
const timerExtra = document.getElementById('timerExtra');
let inicioExtraTime = null;
let extraInterval = null;
    const breakConfig = { 1: { max: 15 }, 2: { max: 30 }, 3: { max: 15 } };
    const timerIntervals = {};

    // Convertir fechas ISO en objetos Date
    if (estado.inicio_turno) estado.inicio_turno = new Date(estado.inicio_turno);
    if (estado.fin_turno) estado.fin_turno = new Date(estado.fin_turno);
    for (let i = 1; i <= 3; i++) {
        if (estado.breaks[i].inicio) estado.breaks[i].inicio = new Date(estado.breaks[i].inicio);
        if (estado.breaks[i].fin) estado.breaks[i].fin = new Date(estado.breaks[i].fin);
    }

    function inicializar() {
        const yaIniciado = !!estado.inicio_turno;
        const yaFinalizado = !!estado.fin_turno;

        btnInicioTurno.disabled = yaIniciado;
        btnFinTurno.disabled = !yaIniciado || yaFinalizado;
        habilitarBreaks(yaIniciado && !yaFinalizado);
        // Tiempo Extra: solo disponible si fin_turno ya fue marcado
        const turnoFinalizado = !!estado.fin_turno;
        btnInicioExtra.disabled = !turnoFinalizado;
        btnFinExtra.disabled = true;
        timerExtra.innerText = '00:00';

        for (let i = 1; i <= 3; i++) {
            const b = estado.breaks[i];
            const btnInicio = document.querySelector(`.btnInicioBreak[data-break="${i}"]`);
            const btnFin = document.querySelector(`.btnFinBreak[data-break="${i}"]`);
            const timerBadge = document.getElementById(`timerBreak${i}`);

            if (b.inicio && b.fin) {
                btnInicio.disabled = true;
                btnFin.disabled = true;
                timerBadge.innerText = tiempoFormateado(b.inicio, b.fin);
            } else if (b.inicio && !b.fin) {
                btnInicio.disabled = true;
                btnFin.disabled = false;
                iniciarTimer(i, b.inicio);
            } else {
                btnInicio.disabled = !yaIniciado || yaFinalizado;
                btnFin.disabled = true;
                timerBadge.innerText = '00:00';
            }
        }
    }

    function habilitarBreaks(valor) {
        document.querySelectorAll('.btnInicioBreak').forEach(b => b.disabled = !valor);
        document.querySelectorAll('.btnFinBreak').forEach(b => b.disabled = true);
    }

    function tiempoFormateado(inicio, fin) {
        const diffMs = fin - inicio;
        const totalSeconds = Math.floor(diffMs / 1000);
        const mins = Math.floor(totalSeconds / 60);
        const secs = totalSeconds % 60;
        return `${String(mins).padStart(2,'0')}:${String(secs).padStart(2,'0')}`;
    }

    function iniciarTimer(breakNum, inicioDate) {
        const timerBadge = document.getElementById(`timerBreak${breakNum}`);
        if (timerIntervals[breakNum]) clearInterval(timerIntervals[breakNum]);

        timerIntervals[breakNum] = setInterval(() => {
            const ahora = new Date();
            const diff = (ahora - inicioDate) / 1000;
            const mins = Math.floor(diff / 60);
            const secs = Math.floor(diff % 60);
            timerBadge.innerText = `${String(mins).padStart(2,'0')}:${String(secs).padStart(2,'0')}`;
            if (mins >= breakConfig[breakNum].max) {
                timerBadge.classList.remove('bg-secondary');
                timerBadge.classList.add('bg-danger');
            } else {
                timerBadge.classList.add('bg-secondary');
                timerBadge.classList.remove('bg-danger');
            }
        }, 1000);
    }

    // Eventos de turno
    btnInicioTurno.addEventListener('click', () => {
        console.log("click");
        fetch('/intranet/sistema/pages/horario/asistencia.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ accion: 'inicio_turno' })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                estado.inicio_turno = new Date();
                btnInicioTurno.disabled = true;
                btnFinTurno.disabled = false;
                habilitarBreaks(true);
            } else {
                alert('Error al iniciar turno');
            }
        });
    });

    btnFinTurno.addEventListener('click', () => {
        fetch('/intranet/sistema/pages/horario/asistencia.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ accion: 'fin_turno' })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                estado.fin_turno = new Date();
                btnFinTurno.disabled = true;
                habilitarBreaks(false);
                for (let i = 1; i <= 3; i++) {
                    clearInterval(timerIntervals[i]);
                    const badge = document.getElementById(`timerBreak${i}`);
                    badge.innerText = '00:00';
                    badge.classList.remove('bg-danger');
                    badge.classList.add('bg-secondary');
                }
            } else {
                alert('Error al finalizar turno');
            }
        });
    });

    // Eventos de break
    document.querySelectorAll('.btnInicioBreak').forEach(btn => {
        btn.addEventListener('click', () => {
            const breakNum = btn.dataset.break;
            fetch('/intranet/sistema/pages/horario/asistencia.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ accion: 'inicio_break', break_num: breakNum })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const now = new Date();
                    estado.breaks[breakNum].inicio = now;
                    estado.breaks[breakNum].fin = null;
                    btn.disabled = true;
                    const btnFin = document.querySelector(`.btnFinBreak[data-break="${breakNum}"]`);
                    btnFin.disabled = false;
                    iniciarTimer(breakNum, now);
                } else {
                    alert('Error al iniciar break');
                }
            });
        });
    });

    document.querySelectorAll('.btnFinBreak').forEach(btn => {
        btn.addEventListener('click', () => {
            const breakNum = btn.dataset.break;
            fetch('/intranet/sistema/pages/horario/asistencia.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ accion: 'fin_break', break_num: breakNum })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const now = new Date();
                    estado.breaks[breakNum].fin = now;
                    btn.disabled = true;
                    const badge = document.getElementById(`timerBreak${breakNum}`);
                    clearInterval(timerIntervals[breakNum]);
                    badge.innerText = tiempoFormateado(estado.breaks[breakNum].inicio, now);
                } else {
                    alert('Error al finalizar break');
                }
            });
        });
    });
    
    btnInicioExtra.addEventListener('click', () => {
      inicioExtraTime = new Date();
      btnInicioExtra.disabled = true;
      btnFinExtra.disabled = false;
    
      if (extraInterval) clearInterval(extraInterval);
      extraInterval = setInterval(() => {
        const now = new Date();
        const diff = Math.floor((now - inicioExtraTime) / 1000);
        const mins = String(Math.floor(diff / 60)).padStart(2, '0');
        const secs = String(diff % 60).padStart(2, '0');
        timerExtra.innerText = `${mins}:${secs}`;
      }, 1000);
    });
    
        btnFinExtra.addEventListener('click', () => {
      btnFinExtra.disabled = true;
      clearInterval(extraInterval);
      alert('Tiempo extra finalizado.'); // Puedes enviar esto al backend si deseas registrarlo
    });

    inicializar();


})();
  
</script>

