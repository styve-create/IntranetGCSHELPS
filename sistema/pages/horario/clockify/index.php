<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Bogota');

require_once __DIR__ . '/../../../../app/controllers/config.php';
require_once __DIR__ . '/../../../analisisRecursos.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

$id_usuario = $_SESSION['usuario_info']['id'] ?? null;

function log_debug($mensaje) {
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, __DIR__ . '/log_fecha.txt');
}
log_debug("Fecha recibida: ");
?>

  <link rel="stylesheet" href="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?php echo $URL; ?>/librerias/vendor/npm-asset/bootstrap/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #f6f8fb; font-family: 'Inter', sans-serif; }
    .card { background-color: #ffffff; border-radius: 12px; }
    input.form-control { background-color: #f9f9f9; border-radius: 8px; padding: 10px 15px; }
    .btn-primary { background-color: #1E90FF; border: none; border-radius: 8px; }
    .btn-primary:hover { background-color: #1C86EE; }
    .text-muted { color: #a0a4aa !important; }
    .stopwatch-btn { padding: 10px 30px; border-radius: 30px; }
    .bg-purple { background-color: #6f42c1; color: white; }
    .time-entry-row { font-size: 14px; }
    .card-body { padding: 0.75rem 1rem; }
    .icon-cobro {transition: transform 0.2s ease, color 0.2s ease;}
.icon-cobro:hover {transform: scale(1.2);color: #0d6efd; }
.icon-cobro.selected {color: #0d6efd !important;}
.d-flex.align-items-center { flex-wrap: wrap; }
.dropdown-menu {max-height: 60px; overflow-y: auto;}

  </style>
  
<div id="contenido-principal">
   <div class="container py-4">
  <div class="bg-white rounded shadow-sm p-3 mb-4" style="position: sticky; top: 0; z-index: 1030;">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="flex-grow-1 d-flex align-items-center gap-2">
        <input type="text" class="form-control" placeholder="What are you working on?">
        <div class="dropdown">
          <button class="btn btn-outline-primary d-flex align-items-center gap-1 dropdown-toggle" id="projectDropdown" data-bs-toggle="dropdown">
            <i class="bi bi-plus-circle"></i> Project
          </button>
          <ul class="dropdown-menu" aria-labelledby="projectDropdown"></ul>
        </div>
      </div>
    </div>
    <hr>
    <div class="d-flex justify-content-end align-items-center gap-3">
      <div class="d-flex align-items-center gap-2  pe-3" id="timerSection">
        <div id="stopwatch" class="fw-bold fs-3 text-muted">00:00:00</div>
       <button id="minimizeBtn" class="btn btn-outline-secondary traducible">Minimizar</button>
        <button id="startStopBtn" class="btn btn-primary stopwatch-btn">Start</button>
      </div>
      
    </div>
  </div>

  <div class="mb-2 d-flex justify-content-between">
    <strong>This Week</strong><span id="thisWeekTotal">00:00:00</span>
  </div>

  <div class="bg-white rounded shadow-sm p-3 w-100" id="contenedorhoy" >
    <div class="mb-4 d-flex justify-content-between">
      <strong>Today</strong><span id="todayTotal">00:00:00</span>
    </div>
  </div>

  <div class="bg-white rounded shadow-sm p-3 mt-3 w-100" id="contenedorSemana" ></div>
  <div id="app" data-id-usuario="<?= htmlspecialchars($id_usuario) ?>"></div>
</div> 


</div>

<script src="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
 <script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/jquery/dist/jquery.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script>
(function(){

const btn = document.getElementById('startStopBtn');
const stopwatch = document.getElementById('stopwatch');
const todayTotalEl = document.getElementById('todayTotal');
const weekTotalEl = document.getElementById('thisWeekTotal');
const ID_USUARIO = document.getElementById('app')?.dataset.idUsuario || null;

let isRunning = false;
let seconds = 0;
let interval = null;
let sesiones = [];

let registroActividades = [];

const formatTime = (totalSeconds) => {
  const hrs = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
  const mins = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
  const secs = String(totalSeconds % 60).padStart(2, '0');
  return `${hrs}:${mins}:${secs}`;
};

const formatMinutes = (minutos) => {
  const hrs = Math.floor(minutos / 60);
  const mins = minutos % 60;
  return (hrs > 0 ? `${hrs}h ` : '') + `${mins}m`;
};

const fechaLegible = (fechaStr) => {
  const fecha = new Date(fechaStr);
  return fecha.toLocaleDateString('es-ES', {
    day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
  });
};

const fechaLegibleSinHora = (fechaStr) => {
  const fecha = new Date(fechaStr);
  const year = fecha.getFullYear();
  const month = String(fecha.getMonth() + 1).padStart(2, '0');
  const day = String(fecha.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

const fechaBonita = (fechaISO) => {
  const [anio, mes, dia] = fechaISO.split('-');
  const fecha = new Date(anio, mes - 1, dia);
  const opciones = { weekday: 'long', day: 'numeric', month: 'long' };
  return fecha.toLocaleDateString('es-ES', opciones).replace(/^[a-z]/, c => c.toUpperCase());
};

const horaStringASegundos = (hora) => {
  const [h, m] = hora.split(':').map(Number);
  return h * 3600 + m * 60;
};

const updateTotals = () => {
  const now = new Date();
  const hoyISO = now.toISOString().substring(0, 10); // YYYY-MM-DD

  let todaySeconds = 0;
  let weekSeconds = 0;

  const startOfWeek = new Date(now);
  startOfWeek.setDate(now.getDate() - now.getDay());
  startOfWeek.setHours(0, 0, 0, 0);

  registroActividades.forEach(act => {
    let rawFecha = act.fecha;
    if (!rawFecha) return;

   
    if (!rawFecha.includes('T')) rawFecha = rawFecha.replace(' ', 'T');
    if (!rawFecha.includes('+') && !rawFecha.includes('-')) rawFecha += '-05:00';

    const fecha = new Date(rawFecha);
    const fechaISO = fecha.toISOString().substring(0, 10);
    const duracion = parseInt(act.duracion);

    if (isNaN(fecha.getTime())) {
      console.warn('⚠️ Fecha inválida:', rawFecha);
      return;
    }

    if (fechaISO === hoyISO) todaySeconds += duracion;
    if (fecha >= startOfWeek) weekSeconds += duracion;
  });

  console.log("🔄 Totales calculados:", { todaySeconds, weekSeconds });
  todayTotalEl.textContent = formatTime(todaySeconds);
  weekTotalEl.textContent = formatTime(weekSeconds);
};


const iniciarTemporizador = () => {
  interval = setInterval(() => {
    seconds++;
    stopwatch.textContent = new Date(seconds * 1000).toISOString().substr(11, 8);
  }, 1000);
};

const detenerTemporizador = () => {
  clearInterval(interval);
};

const resetTemporizador = () => {
  seconds = 0;
  stopwatch.textContent = '00:00:00';
};

const limpiarFormulario = () => {
  document.querySelector('input.form-control').value = '';
  const projectBtn = document.getElementById('projectDropdown');
  projectBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Project';
  delete projectBtn.dataset.actividad;
  delete projectBtn.dataset.cliente;
};

const obtenerFechaActualFormateada = () => {
  const formatter = new Intl.DateTimeFormat("sv-SE", {
    timeZone: "America/Bogota",
    hour12: false,
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit"
  });
  const parts = formatter.formatToParts(new Date());
  const dateParts = Object.fromEntries(parts.map(p => [p.type, p.value]));
  return `${dateParts.year}-${dateParts.month}-${dateParts.day} ${dateParts.hour}:${dateParts.minute}:${dateParts.second}`;
};

btn.addEventListener('click', () => {
    
  const input = document.querySelector('input.form-control');
  const projectBtn = document.getElementById('projectDropdown');
  const actividad = projectBtn.dataset.actividad;
  const cliente = projectBtn.dataset.cliente;
  const descripcion = input.value.trim();

  if (!actividad || !cliente || descripcion === '') {
    alert('Por favor selecciona un proyecto y escribe una descripción antes de iniciar.');
    return;
  }

  isRunning = !isRunning;
  btn.textContent = isRunning ? 'Stop' : 'Start';
  btn.classList.toggle('btn-primary', !isRunning);
  btn.classList.toggle('btn-danger', isRunning);

  if (isRunning) {
    iniciarTemporizador();
  } else {
    detenerTemporizador();
    const now = new Date();
    const duration = seconds;
    const timestamp = obtenerFechaActualFormateada();

    const dataToSend = {
      cliente,
      actividad,
      descripcion,
      duracion: duration,
      fecha: timestamp,
      id_usuario: ID_USUARIO,
      cobrado: cobrarActividad ? 1 : 0
    };

    console.log("Enviando datos al servidor:", dataToSend);

    fetch('/intranet/sistema/pages/horario/clockify/saveActividad.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(dataToSend)
    })
      .then(res => res.text())
      .then(text => {
        console.log('📦 Respuesta cruda del servidor:', text);
        try {
          const json = JSON.parse(text);
          console.log('✅ JSON parseado:', json);
          if (json.success) {
            registroActividades.push(json.actividad);
            cargarActividades();
            updateTotals();
          } else {
            alert('Error al guardar en la base de datos');
            cargarActividades();
          }
        } catch (e) {
          console.error('❌ Error al parsear JSON:', e);
          alert('La respuesta del servidor no es JSON válido. Mira la consola para más detalles.');
        }
      });

    resetTemporizador();
    limpiarFormulario();
    updateTotals();
  }
});




let actividadActiva = null;
let intervalActividad = null;
let segundosActividad = 0;

function iniciarActividad(item, cardBody, playBtn) {
  if (actividadActiva) {
    alert("Ya hay una actividad en progreso. Detenla antes de iniciar otra.");
    return;
  }

  const horaInicio = item.hora_inicio;
  const [h, m, s] = horaInicio.split(':').map(Number);
  const inicioDate = new Date();
  inicioDate.setHours(h, m, s || 0, 0);

  const duracionText = cardBody.querySelector('.duracion-text');
  actividadActiva = { item, cardBody, duracionText, playBtn };
  

  const icon = playBtn.querySelector('i');
  icon.classList.remove('bi-play');
  icon.classList.add('bi-stop');

  intervalActividad = setInterval(() => {
    segundosActividad++;
    duracionText.textContent = formatTime((item.duracion || 0)  + segundosActividad);
  }, 1000);
  
}

function detenerActividad() {
  if (!actividadActiva) return;

  clearInterval(intervalActividad);
  const { item, cardBody, duracionText, playBtn } = actividadActiva;
    
 
  const ahora = new Date();
  const horaFinStr = ahora.toTimeString().split(' ')[0].substring(0, 5); // "HH:MM"

  
  const duracionFinalSegundos = (item.duracion || 0) + segundosActividad;
  


  
  cardBody.querySelector('.hora-fin').value = horaFinStr;
  duracionText.textContent = formatTime(duracionFinalSegundos);

  const id = cardBody.querySelector('.fecha-input')?.dataset.id;

  fetch('/intranet/sistema/pages/horario/clockify/updateHorasActividad.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      id,
      hora_inicio: item.hora_inicio,
      hora_fin: horaFinStr,
      duracion: duracionFinalSegundos
    })
  })
    .then(res => res.json())
    .then(response => {
      if (response.success) {
        cargarActividades();
        updateTotals();
      } else {
        alert('Error al actualizar actividad.');
        cargarActividades();
      }
    });

  
  const icon = playBtn.querySelector('i');
  icon.classList.remove('bi-stop');
  icon.classList.add('bi-play');

  actividadActiva = null;
  segundosActividad = 0;
}


let clickedHoraInput = null;
let cobrarActividad = false;



document.addEventListener('change', function (event) {

  if (event.target.matches('.fecha-input')) {
    const input = event.target;
    const nuevaFecha = input.value;
    const id = input.dataset.id;
    console.log("📅 Cambio en fecha:", nuevaFecha, "ID:", id);

    if (!nuevaFecha || !id) return;

    fetch('/intranet/sistema/pages/horario/clockify/updateFechaActividad.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, nueva_fecha: nuevaFecha })
    })
    .then(res => res.json())
    .then(response => {
      if (!response.success) {
          alert('Error al actualizar la fecha');
          cargarActividades();
      }
       cargarActividades();
    })
    .catch(err => console.error('❌ Error en fetch fecha:', err));
  }
});


function toggleCobro(icon, actividadId) {
  const isCurrentlyCobrada = icon.classList.contains('text-primary');
  const nowCobrada = !isCurrentlyCobrada;
  icon.classList.remove('text-primary', 'text-secondary');
  icon.classList.add(nowCobrada ? 'text-primary' : 'text-secondary');

  const data = { id: actividadId, cobrado: nowCobrada ? 1 : 0 };

  if (actividadId) {
    fetch('/intranet/sistema/pages/horario/clockify/updateCobroActividad.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(response => {
      if (response.success) {
        const index = registroActividades.findIndex(act => act.id == actividadId);
        if (index !== -1) {
          registroActividades[index].cobrado = nowCobrada ? 1 : 0;

          // Solo actualiza el icono, no toda la tarjeta
          const selector = `.icon-cobro[data-id="${actividadId}"]`;
          const iconoOriginal = document.querySelector(selector);
          if (iconoOriginal) {
            iconoOriginal.classList.remove('text-primary', 'text-secondary');
            iconoOriginal.classList.add(nowCobrada ? 'text-primary' : 'text-secondary');
          }

          updateTotals();
        }
      } else {
        alert('❌ Error al actualizar el estado de cobro');
      }
    });
  } else {
    cobrarActividad = nowCobrada;
  }
  cargarActividades();
}

function adaptarFormatoHoraInputs() {
  document.querySelectorAll('.hora-inicio, .hora-fin').forEach(input => {
    input.setAttribute('step', '60');
    input.setAttribute('pattern', '[0-9]{2}:[0-9]{2}');
  });
}


function renderActividad(item) {
  const div = document.createElement('div');
  div.className = 'mb-2';

  const duracionSegundos = (item.duracion || 0);
  const estaCobrada = item.cobrado == 1 || item.cobrado === true;
  const cobradoIcon = estaCobrada ? 'text-primary' : 'text-secondary';

  div.innerHTML = `
    <div class="card-body p-2 border rounded">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">

        <!-- Descripción + etiquetas -->
        <div class="d-flex align-items-center gap-2 flex-grow-1 overflow-hidden">
          <div class="text-truncate" style="max-width: 120px;">${item.descripcion || ''}</div>
          <span class="badge bg-primary text-nowrap">${item.cliente}</span>
          <span class="badge bg-secondary text-nowrap">${item.actividad}</span>
        </div>

        <!-- Fecha -->
        <div class="flex-shrink-0">
          <input type="date" class="form-control form-control-sm fecha-input" style="width: 150px;" value="${item.fecha.substring(0, 10)}" data-id="${item.id}" />
        </div>

        <!-- Hora inicio - fin -->
        <div class="d-flex align-items-center gap-1 flex-shrink-0">
          <input type="time" class="form-control form-control-sm hora-inicio" style="width: 150px;" value="${item.hora_inicio}">
          <span>-</span>
          <input type="time" class="form-control form-control-sm hora-fin" style="width: 150px;" value="${item.hora_fin}">
        </div>

        <!-- Duración -->
        <div class="fw-bold flex-shrink-0 duracion-text">${formatTime(duracionSegundos)}</div>

        <!-- Iconos -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
          <i class="bi bi-currency-dollar ${cobradoIcon} fs-5 cursor-pointer icon-cobro" data-id="${item.id}" title="Billable hours"></i>
          <button class="btn btn-sm btn-outline-secondary play-btn"><i class="bi bi-play"></i></button>
          <div class="dropdown">
            <a href="#" class="text-muted" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none;">
              <i class="bi bi-three-dots-vertical fs-5"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item traducible" href="#" data-action="duplicate" data-id="${item.id}">Duplicar</a></li>
              <li><a class="dropdown-item text-danger traducible" href="#" data-action="delete" data-id="${item.id}">Eliminar</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  `;

  div.querySelector(".hora-inicio").addEventListener("click", function () {
  clickedHoraInput = this;
});
div.querySelector(".hora-inicio").addEventListener("change", function (event) {
  if (event.target === clickedHoraInput) {
    console.log("Cambio detectado en .hora-inicio");
    calcularDuracion(event);
    clickedHoraInput = null;
  }
});

div.querySelector(".hora-fin").addEventListener("click", function () {
  clickedHoraInput = this;
});
div.querySelector(".hora-fin").addEventListener("change", function (event) {
  if (event.target === clickedHoraInput) {
    console.log("Cambio detectado en .hora-fin");
    calcularDuracion(event);
    clickedHoraInput = null;
  }
});
  div.querySelector(".icon-cobro").addEventListener('click', e => toggleCobro(e.target, item.id));

  const playBtn = div.querySelector('.play-btn');
  playBtn.addEventListener('click', () => {
    if (actividadActiva && actividadActiva.item.id === item.id) {
      detenerActividad();
    } else {
      iniciarActividad(item, div.querySelector('.card-body'), playBtn);
    }
  });

  return div;
}

setTimeout(adaptarFormatoHoraInputs, 500);

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function cargarActividades() {
  fetch('/intranet/sistema/pages/horario/clockify/getRegistroActividades.php')
    .then(res => res.json())
    .then(data => {
      let totalHoy = 0;
      let totalSemana = 0;

      registroActividades = [...data.hoy, ...data.semana];

      const contenedorHoy = document.getElementById('contenedorhoy');
      const contenedorSemana = document.getElementById('contenedorSemana');
      const thisWeekTotalEl = document.getElementById('thisWeekTotal');

      contenedorHoy.innerHTML = `
        <div class="mb-4 d-flex justify-content-between">
          <strong>Today</strong><span id="todayTotal">00:00:00</span>
        </div>`;

      const todayTotalEl = document.getElementById('todayTotal');
      todayTotalEl.textContent = formatTime(totalHoy);

      contenedorSemana.innerHTML = `<h5 class="mb-3 traducible">Resumen Semanal</h5>`;

      data.hoy.forEach(item => {
        totalHoy += (item.duracion || 0);
        contenedorHoy.appendChild(renderActividad(item));
      });

      const resumenSemanal = {};
      data.semana.forEach(item => {
        const fecha = item.fecha.substring(0, 10);
        const duracion = (item.duracion || 0);
        totalSemana += duracion;
        if (!resumenSemanal[fecha]) resumenSemanal[fecha] = { actividades: [], total: 0 };
        resumenSemanal[fecha].actividades.push(item);
        resumenSemanal[fecha].total += duracion;
      });

      for (const fecha in resumenSemanal) {
        const dia = resumenSemanal[fecha];
        const card = document.createElement('div');
        card.className = 'card mb-3';
        card.innerHTML = `
          <div class="card-header fw-bold bg-light d-flex justify-content-between">
            <span class="traducible" data-original-text="${fechaBonita(fecha)}">${fechaBonita(fecha)}</span>
            <span>${formatTime(dia.total)}</span>
          </div>
          <div class="card-body p-2"></div>`;

        const cardBody = card.querySelector('.card-body');
        dia.actividades.forEach(item => {
          cardBody.appendChild(renderActividad(item));
        });
        
        contenedorSemana.appendChild(card);
        window.traducirTextos(card);
      }

      todayTotalEl.textContent = formatTime(totalHoy);
      thisWeekTotalEl.textContent = formatTime(totalSemana);

      updateTotals();

      if (typeof window.traducirTextos === 'function') {
        window.traducirTextos(contenedorHoy);
        window.traducirTextos(contenedorSemana);
      }
    });
}


document.getElementById('projectDropdown').addEventListener('click', () => {
  console.log("🟢 Se hizo clic en el botón de Project");

 fetch(`/intranet/sistema/pages/horario/clockify/getActividades.php`
        + `?user_id=${encodeURIComponent(ID_USUARIO)}`
        + `&t=${new Date().getTime()}`)
    .then(res => res.json())
    .then(data => {
      const dropdownMenu = document.querySelector('#projectDropdown').parentElement.querySelector('.dropdown-menu');
      dropdownMenu.innerHTML = '';

      // Agrega barra de búsqueda
      const searchWrapper = document.createElement('div');
      searchWrapper.className = 'px-3 py-2';
      searchWrapper.innerHTML = `
        <input type="text" class="form-control form-control-sm" placeholder="Buscar cliente o actividad..." id="searchActividad">
        <hr class="dropdown-divider mt-2 mb-0">
      `;
      dropdownMenu.appendChild(searchWrapper);

      const renderDropdown = (filtro = '') => {
        
        dropdownMenu.querySelectorAll('.dropdown-header, .dropdown-item, hr').forEach(el => el.remove());

        const filtroLower = filtro.toLowerCase();

        for (const cliente in data.estructura) {
          const actividades = data.estructura[cliente];
          const clienteLower = cliente.toLowerCase();

          const coincideCliente = clienteLower.includes(filtroLower);
          const actividadesFiltradas = actividades.filter(a => a.toLowerCase().includes(filtroLower));

          
          if (coincideCliente || actividadesFiltradas.length > 0) {
            const header = document.createElement('h6');
            header.className = 'dropdown-header';
            header.textContent = cliente;
            dropdownMenu.appendChild(header);

            const actividadesAMostrar = coincideCliente ? actividades : actividadesFiltradas;

            actividadesAMostrar.forEach(actividad => {
              const item = document.createElement('a');
              item.className = 'dropdown-item';
              item.href = '#';
              item.textContent = actividad;

              item.addEventListener('click', (e) => {
                e.preventDefault();
                const btn = document.getElementById('projectDropdown');
                btn.innerHTML = `<i class="bi bi-check-circle-fill"></i> ${cliente} - ${actividad}`;
                btn.dataset.cliente = cliente;
                btn.dataset.actividad = actividad;
              });

              dropdownMenu.appendChild(item);
            });

            const divider = document.createElement('li');
            divider.innerHTML = '<hr class="dropdown-divider">';
            dropdownMenu.appendChild(divider);
          }
        }
      };

      
      renderDropdown();

      
      document.getElementById('searchActividad').addEventListener('input', function () {
        renderDropdown(this.value);
      });


      const dropdown = bootstrap.Dropdown.getOrCreateInstance(document.getElementById('projectDropdown'));
      dropdown.show();
    });
});



document.getElementById('contenedorhoy').addEventListener('click', async (e) => {
  const target = e.target.closest('a[data-action]');
  if (!target) return;

  const action = target.dataset.action;
  const id = target.dataset.id;

  if (action === 'delete') {
    if (!confirm("¿Estás seguro de que deseas eliminar esta actividad?")) return;
    target.closest('.card-body')?.remove();

    const res = await fetch('/intranet/sistema/pages/horario/clockify/delete_actividad.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    });
    const result = await res.json();
    if (result.success) {
      cargarActividades();
    } else {
      alert("Error al eliminar: " + (result.message || 'Error desconocido'));
      cargarActividades();
    }
  }

  if (action === 'duplicate') {
    const original = registroActividades.find(act => act.id == id);
    if (!original) return;

    const duplicado = { ...original };
    delete duplicado.id;

    const res = await fetch('/intranet/sistema/pages/horario/clockify/duplicate_actividad.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(duplicado)
    });
    const result = await res.json();

    if (result.success) {
      registroActividades.push(result.actividad);
      cargarActividades();
    } else {
      alert("Error al duplicar: " + (result.error || 'Error desconocido'));
      cargarActividades();
    }
  }
});


function calcularDuracion(event) {
  const cardBody = event.target.closest('.card-body');
  const horaInicioInput = cardBody.querySelector('.hora-inicio');
  const horaFinInput = cardBody.querySelector('.hora-fin');
  const duracionText = cardBody.querySelector('.duracion-text');
  const id = cardBody.querySelector('.fecha-input')?.dataset.id;

  const horaInicio = horaInicioInput.value;
  const horaFin = horaFinInput.value;

  console.log("horaInicio", horaInicio);
  console.log("horaFin", horaFin);

  const [h1, m1, s1] = horaInicio.split(':').map(Number);
  const [h2, m2, s2] = horaFin.split(':').map(Number);

  const segundosInicio = h1 * 3600 + m1 * 60 + (s1 || 0);
  const segundosFin = h2 * 3600 + m2 * 60 + (s2 || 0);

  const duracionSeg = Math.max(0, segundosFin - segundosInicio);
  duracionText.textContent = formatTime(duracionSeg);

  fetch('/intranet/sistema/pages/horario/clockify/updateHorasActividad.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, hora_inicio: horaInicio, hora_fin: horaFin, duracion: duracionSeg })
  })
  .then(res => res.json())
  .then(response => {
    if (response.success) {
      cargarActividades();
      updateTotals();
    } else {
      alert('Error al actualizar horas: ' + response.message);
      cargarActividades();
    }
  })
  .catch(err => console.error('❌ Error en la petición:', err));
}
cargarActividades();

document.getElementById('minimizeBtn')?.addEventListener('click', () => {
  const descripcion = document.querySelector('input.form-control').value;
  const cliente = document.getElementById('projectDropdown').dataset.cliente;
  const actividad = document.getElementById('projectDropdown').dataset.actividad;

  // Enviar a content_script
  window.postMessage({
    source: 'web_to_extension',
    type: 'MOSTRAR_STICKER',
    data: {
      descripcion,
      cliente,
      actividad,
      tiempo: stopwatch.textContent,
      corriendo: isRunning
    }
  }, '*');
});


})();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

