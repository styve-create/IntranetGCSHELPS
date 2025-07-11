<?php
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$log_index1 = __DIR__ . "/log_index1.txt";
function log_index1($mensaje) {
    global $log_index1;
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, $log_index1);
}

log_index1(">>> Inicio del script");

session_name('mi_sesion_personalizada');
session_start();

log_index1("Sesión iniciada con ID de sesión PHP: " . session_id());

if (!isset($_SESSION['usuario_info']['id']) || !isset($_SESSION['id_conexion'])) {
    
    log_index1("No hay sesión activa (usuario_id o id_conexion faltante). Redirigiendo a login.");
    header('Location: /intranet/index.php');
    exit;
}

if (!isset($_SESSION['idioma'])) {
    $_SESSION['idioma'] = 'ES'; // valor por defecto
}

$usuario_id = $_SESSION['usuario_info']['id'];
$id_conexion_sesion = $_SESSION['id_conexion'];

log_index1("Usuario ID en sesión: $usuario_id");
log_index1("ID de conexión en sesión: $id_conexion_sesion");

try {
  
    if (!isset($pdo)) {
        include_once(__DIR__ . '/../app/controllers/config.php');
    }

    $sql = "SELECT id_conexion FROM usuarios_conectados WHERE id_usuario = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);
    $id_conexion_bd = $stmt->fetchColumn();

    log_index1("ID de conexión recuperado desde BD para usuario $usuario_id: " . var_export($id_conexion_bd, true));

    if ($id_conexion_bd === false) {
        log_index1("No se encontró registro de conexión para el usuario en BD. Procediendo con sesión normal.");
        // Opcional: podrías forzar logout o registro aquí
    } else if ($id_conexion_bd !== $id_conexion_sesion) {
        log_index1("Discrepancia en id_conexion: sesión tiene '$id_conexion_sesion', pero BD tiene '$id_conexion_bd'. Cerrando sesión.");
        session_destroy();
        header('Location: /intranet/index.php');
        exit;
    } else {
        log_index1("ID de conexión coincide entre sesión y BD. Usuario autorizado a continuar.");
    }

} catch (Exception $e) {
    log_index1("Error en consulta BD: " . $e->getMessage());
    // Manejo de error según tu preferencia
}

$usuario_rol = $_SESSION['usuario_info']['rol'];
log_index1("Asignando usuario_id: $usuario_id a la sesión");
log_index1("Asignando id_conexion: $id_conexion_sesion a la sesión");
include_once(__DIR__ . '/../layout/parte1.php');


log_index1("Contenido de la sesión: " . var_export($_SESSION, true));
?>

<div style="display: flex; height: 100vh;">

  <!-- Sidebar -->
  <div id="sidebar" class="sidebar text-white">
    <!-- ... contenido de la sidebar ... -->
      <nav class="nav flex-column p-3">

  <!-- Logo -->
  <a href="/" class="d-flex align-items-center mb-3">
    <img src="<?php echo $URL; ?>/imagen/GCS3.png" alt="Logo GCS" class="img-fluid" style="max-width: 80%; height: auto;">
  </a>
  <hr style="border: 1px solid white; opacity: 0.3; margin-bottom: 20px;">
 <a href="#" data-link="home" class="btn btn-outline-light w-100 mb-3 text-start enlaceDinamicos d-flex align-items-center">
  <div class="icono-wrapper me-2">
    <i class="fas fa-home"></i>
  </div>
  <span class="texto-btn"><p class="traducible">inicio</p></span>
</a>

  <?php
  $menu_por_rol = [
    1 => [ // SuperAdmin
    [
        'titulo' => 'Informacion',
        'icono' => 'fas fa-cogs',
        'id' => 'collapseInformacionTrabajador',
        'items' => [
          ['texto' => 'Informacion personal', 'icono' => 'fas fa-cog', 'url' => 'informacionTrabajador'],
          ['texto' => 'Panel Administrativo Anuncios', 'icono' => 'fas fa-cog', 'url' => 'panelAdministrativoAnuncios'],
        ],
      ],
    [
        'titulo' => 'Horario',
        'icono' => 'fas fa-users',
        'id' => 'collapseHorarios',
        'items' => [
          ['texto' => 'Horarios', 'icono' => 'fas fa-list', 'url' => 'horario'],
          ['texto' => 'clockify', 'icono' => 'fas fa-list', 'url' => 'horarioclockify'],
         
        ],
      ],
      [
        'titulo' => ' Panel de Horarios',
        'icono' => 'fas fa-users',
        'id' => 'collapsePanelHorarios',
        'items' => [
         
          ['texto' => 'Registros Horarios Trabajadores', 'icono' => 'fas fa-list', 'url' => 'horarioTrabajadores'],
          ['texto' => 'Registros Horarios Administrativos', 'icono' => 'fas fa-list', 'url' => 'horarioAdministrativo'],
          
         
        ],
      ],
      [
        'titulo' => 'Panel Administrativo clockify',
        'icono' => 'fas fa-users',
        'id' => 'collapePanelAdministrativo',
        'items' => [
          ['texto' => 'Clientes', 'icono' => 'fas fa-list', 'url' => 'Clientes'],
           ['texto' => 'Reportes', 'icono' => 'fas fa-list', 'url' => 'reportes'],
           ['texto' => 'Reportes OPS', 'icono' => 'fas fa-list', 'url' => 'reportesHoraios'],
           ['texto' => 'facturas', 'icono' => 'fas fa-list', 'url' => 'facturas'],
          
        ],
      ],
      [
        'titulo' => 'Gestión de Usuarios',
        'icono' => 'fas fa-users',
        'id' => 'collapseUsuarios',
        'items' => [
          ['texto' => 'Listado de Usuarios', 'icono' => 'fas fa-list', 'url' => 'users'],
          ['texto' => 'Creación de Usuario', 'icono' => 'fas fa-user-plus', 'url' => 'edit_user'],
          ['texto' => 'Roles', 'icono' => 'fas fa-cog', 'url' => 'roles'],
          ['texto' => 'Usuarios Activos', 'icono' => 'fas fa-cog', 'url' => 'controlusers'],
        ],
      ],
    
       
    ],
    
    2 => [ // Gestión RRHH
       [
        'titulo' => 'Horario',
        'icono' => 'fas fa-users',
        'id' => 'collapseHorarios',
        'items' => [
          ['texto' => 'horario', 'icono' => 'fas fa-list', 'url' => 'horario'],
          ['texto' => 'Registros Horarios Administrativos', 'icono' => 'fas fa-list', 'url' => 'horarioAdministrativo'],
         
        ],
      ],
      [
        'titulo' => 'Registros y Formularios',
        'icono' => 'fas fa-folder-open',
        'id' => 'collapseRegistros',
        'items' => [
          ['texto' => 'Registros Huellas', 'icono' => 'fas fa-user', 'url' => 'registros_huellas'],
          ['texto' => 'Formulario de Ausencias', 'icono' => 'fas fa-cogs', 'url' => 'formularioAusencias'],
        ],
      ],
      
      [
        'titulo' => 'Gestión de Trabajadores',
        'icono' => 'fas fa-users-cog',
        'id' => 'collapseTrabajadores',
        'items' => [
          ['texto' => 'Listado de Trabajadores', 'icono' => 'fas fa-list', 'url' => 'trabajadores'],
          ['texto' => 'Creación de Trabajador', 'icono' => 'fas fa-user-plus', 'url' => 'new_trabajadores'],
        ],
      ],
      
      [
        'titulo' => 'jerarquias',
        'icono' => 'fas fa-users-cog',
        'id' => 'collapseJerarquias',
        'items' => [
          ['texto' => 'Jerarquias', 'icono' => 'fas fa-list', 'url' => 'jerarquias'],
         
        ],
      ],
      [
        'titulo' => 'Informacion',
        'icono' => 'fas fa-cogs',
        'id' => 'collapseInformacionTrabajador',
        'items' => [
          ['texto' => 'Informacion', 'icono' => 'fas fa-cog', 'url' => 'informacionTrabajador'],
        ],
      ],
      
    ],
    
     3 => [ // Administrador
        [
        'titulo' => 'Horario',
        'icono' => 'fas fa-users',
        'id' => 'collapseHorarios',
        'items' => [
          ['texto' => 'horario', 'icono' => 'fas fa-list', 'url' => 'horario'],
         
        ],
      ],
      [
        'titulo' => 'Gestión de Inventario',
        'icono' => 'fas fa-users',
        'id' => 'collapseUsuarios',
        'items' => [
          ['texto' => 'Registro de Inventario', 'icono' => 'fas fa-user-plus', 'url' => 'inventarioRegistro'],
          ['texto' => 'Listado de Inventario', 'icono' => 'fas fa-list ', 'url' => 'listadoInventario'],
        ],
      ],
    [
        'titulo' => 'Informacion',
        'icono' => 'fas fa-cogs',
        'id' => 'collapseInformacionTrabajador',
        'items' => [
          ['texto' => 'Informacion', 'icono' => 'fas fa-cog', 'url' => 'informacionTrabajador'],
        ],
      ],
      
    ],
    
//fin menu rol    
  ];
  if (isset($menu_por_rol[$usuario_rol])) {
    foreach ($menu_por_rol[$usuario_rol] as $seccion) {
      echo '<button class="btn btn-outline-light w-100 mb-2 text-start toggle-btn d-flex align-items-center justify-content-start" data-target="#' . $seccion['id'] . '">';
echo '<div class="icono-wrapper me-2"><i class="' . $seccion['icono'] . '"></i></div>';
echo '<span class="texto-btn traducible">' . $seccion['titulo'] . '</span>';
echo '</button>';

      echo '<div class="collapse" id="' . $seccion['id'] . '"><div class="ps-3 pb-3">';
      foreach ($seccion['items'] as $item) {
       echo '<a href="#" data-link="' . $item['url'] . '" class="d-block text-white mb-1 enlaceDinamicos d-flex align-items-center">';
echo '<div class="icono-wrapper me-2"><i class="' . $item['icono'] . '"></i></div>';
echo '<span class="texto-btn traducible">' . $item['texto'] . '</span>';
echo '</a>';
      }
      echo '</div></div>';
    }
  }
  ?>
  
  <hr style="border: 1px solid white; opacity: 0.3; margin-top: 20px;">

<a href="#" id="btnLogout" class="btn btn-danger w-100 mt-2 d-flex align-items-center">
  <div class="icono-wrapper me-2">
    <i class="fas fa-sign-out-alt"></i>
  </div>
  <span class="texto-btn">Cerrar Sesión</span>
</a>
</nav>
  </div>

  <!-- Contenedor derecho -->
  <div class="main-wrapper">
   <div class="navbar-custom">
  <button id="toggleSidebar" class="toggle-btn"><i class="fas fa-bars"></i></button>
  <span style="font-weight: bold;">Bienvenido<?php echo isset($_SESSION['usuario_info']['nombre']) ? ', ' . htmlspecialchars($_SESSION['usuario_info']['nombre']) : ''; ?></span>
  <button id="btnLanguage" class="btn btn-md ms-2">
    <?php echo htmlspecialchars($_SESSION['idioma']); ?>
</button>
  </div>
    <div class="main-content" style="flex: 1; overflow-y: auto;">
      <div id="contenido-dinamico" style="padding: 20px;">
        <!-- contenido dinámico -->
      </div>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
     
    const btnLang = document.getElementById('btnLanguage');

    btnLang.addEventListener('click', () => {
        const nuevoIdioma = btnLang.textContent.trim() === 'EN' ? 'ES' : 'EN';

        fetch(`/intranet/sistema/setIdioma.php?t=${Date.now()}`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ idioma: nuevoIdioma })
        })
        .then(res => res.json())
        .then(data => {
            console.log("Respuesta setIdioma.php:", data);
            if (data.success) {
                btnLang.textContent = nuevoIdioma;
                window.currentLang = nuevoIdioma;

                // Ahora llamamos a traduccionsistema.php para actualizar el estado en cliente
                fetch(`/intranet/sistema/traduccionsistema.php?t=${Date.now()}`, {
                    credentials: 'include'
                })
                .then(res => res.text())
                .then(script => {
                    console.log("Respuesta traduccionsistema.php cargada.");
                    // Ejecutamos el código que devuelve traduccionsistema.php
                    const s = document.createElement('script');
                    s.textContent = script;
                    document.body.appendChild(s);

                    if (typeof window.traducirTextos === 'function') {
                        window.traducirTextos(document);
                    }
                })
                .catch(err => {
                    console.error("Error al llamar a traduccionsistema.php:", err);
                });

            } else {
                console.error("Error cambiando idioma:", data.message);
            }
        })
        .catch(err => {
            console.error("Error en fetch setIdioma.php:", err);
        });
    });
});
</script>
<script>
    window.cargarTraducciones = () => {
    fetch('/intranet/sistema/traduccionsistema.php?t=' + Date.now(), {
        credentials: 'include'
    })
    .then(res => res.text())
    .then(script => {
        const s = document.createElement('script');
        s.textContent = script;
        document.head.appendChild(s);

        if (typeof window.traducirTextos === 'function') {
            // Traducimos todo el documento actual
            window.traducirTextos(document);
        }
    })
    .catch(err => {
        console.error("❌ Error al cargar traducciones:", err);
    });
};
</script>
<script>
    console.log('Inicializando toggles de sidebar');
  document.addEventListener('DOMContentLoaded', function () {
      
const btnToggle = document.getElementById("toggleSidebar");
  const sidebar = document.querySelector(".sidebar");
  const mainWrapper = document.querySelector(".main-wrapper");

  btnToggle.addEventListener("click", () => {
    sidebar.classList.toggle("colapsada");
    mainWrapper.classList.toggle("colapsada");
  });

  // Expansión temporal con hover solo si está colapsada
  sidebar.addEventListener("mouseenter", () => {
    if (sidebar.classList.contains("colapsada")) {
      sidebar.classList.add("expandida-hover");
      mainWrapper.classList.add("expandida-hover");
    }
  });

  sidebar.addEventListener("mouseleave", () => {
    sidebar.classList.remove("expandida-hover");
    mainWrapper.classList.remove("expandida-hover");
  });
    const buttons = document.querySelectorAll('.toggle-btn');

   buttons.forEach(button => {
  const targetId = button.getAttribute('data-target');
  if (!targetId) return; // ⛔ No tiene target, saltar

  const target = document.querySelector(targetId);
  if (!target) {
    console.warn(`⚠️ No se encontró el elemento colapsable para el ID: ${targetId}`);
    return;
  }

  target.classList.add('notransition');

  const collapse = new bootstrap.Collapse(target, { toggle: false });

  const state = localStorage.getItem(targetId);
  if (state === 'show') {
    collapse.show();
  } else {
    collapse.hide();
  }

  button.addEventListener('click', function () {
    const isShown = target.classList.contains('show');
    if (isShown) {
      collapse.hide();
      localStorage.setItem(targetId, 'hide');
    } else {
      collapse.show();
      localStorage.setItem(targetId, 'show');
    }
  });

  requestAnimationFrame(() => {
    target.classList.remove('notransition');
    target.classList.add('ready');
  });
});
  });
</script>

<style>
  /* Evita animaciones mientras se inicializa */
  .notransition {
    transition: none !important;
  }

  /* Evita parpadeo inicial si el collapse no debe mostrarse */
  .collapse:not(.show):not(.ready) {
    display: none !important;
    visibility: hidden;
  }
</style>

<script>
const idConexion = "<?php echo $id_conexion_sesion ?>";
console.log("ID de conexión desde sesión PHP:", idConexion);

if (!idConexion) {
    console.log("No se encontró id_conexion. No se enviará el beacon.");
} else {
    console.log("ID de conexión encontrado: ", idConexion);
}

// Función para cerrar sesión y enviar la solicitud al servidor (PHP)
function cerrarSesion() {
    if (!idConexion) {
        console.log("No se puede hacer logout sin id_conexion.");
        return;
    }

    const urlLogout = '/intranet/sistema/logout.php';
    const fetchOptions = {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            logout_automatico: false,  
            id_conexion: idConexion,
           
        })
    };


    fetch(urlLogout, fetchOptions)
        .then(response => {
            console.log("Respuesta del servidor:", response);
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        .then(data => {
            console.log("Respuesta JSON del servidor: ", data);
            if (data.success) {
                console.log("Logout exitoso. Redirigiendo a /intranet/index.php");
                window.location.href = "/intranet/index.php";
            } else {
                console.error("Logout fallido:", data);
            }
        })
        .catch(error => {
            console.error("Error en fetch:", error);
        });
}

// Botón de logout manual
const btnLogout = document.getElementById('btnLogout');
if (btnLogout) {
    btnLogout.addEventListener('click', function (e) {
        e.preventDefault();  // Prevenir la acción por defecto del botón
        console.log("Botón de logout presionado.");
        cerrarSesion();  // Llamar a la función para cerrar sesión
    });
}

let pingInterval = null;

if (idConexion) {
    console.log("✅ idConexion existe:", idConexion);

    if (!navigator.geolocation) {
        console.error("❌ Geolocalización no está soportada por este navegador.");
    }

    navigator.geolocation.getCurrentPosition(
        function(position) {
            console.log("📍 Ubicación obtenida inicialmente:", position.coords);

            if (!pingInterval) {
                pingInterval = setInterval(() => {
                    const data = {
                        id_conexion: idConexion,
                        hora_ping: new Date().toISOString(),
                        latitud: position.coords.latitude,
                        longitud: position.coords.longitude
                    };

                    console.log("📤 Enviando datos al servidor:", data);

                    fetch('/intranet/sistema/ping_usuario.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        credentials: 'include',
                        body: JSON.stringify(data)
                    })
                    .then(res => {
                        console.log("🔄 Respuesta cruda:", res);
                        return res.json();
                    })
                    .then(response => {
                        console.log("📬 Respuesta JSON:", response);
                        if (response.success) {
                            console.log("✅ Ping enviado correctamente. Distancia:", response.distancia);
                        } else {
                            console.warn("⚠️ Ping fallido. Mensaje:", response.message);
                        }
                    })
                    .catch(err => {
                        console.error("❌ Error al enviar ping:", err);
                    });
                }, 60000); // cada 60 segundos
            } else {
                console.warn("⚠️ Intervalo de ping ya estaba activo.");
            }
        },
        function(error) {
            console.error("❌ Error al obtener la ubicación:", error.message);
        }
    );
} else {
    console.warn("⚠️ idConexion no está disponible.");
}

</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
  const contenedor = document.getElementById('contenido-dinamico');

  // ✅ Detectar página inicial (de URL o home)
  const params = new URLSearchParams(window.location.search);
  let page = params.get('page') || 'home';
  let id = params.get('id');

  cargarPagina(page, id, true);

  // 📄 Función para cargar página dinámica
  async function cargarPagina(page, id = null, replaceState = false) {
    const url = `/intranet/sistema/paginasdinamicas.php?page=${page}&ajax=1` + (id ? `&id=${id}` : '');
    try {
      const res = await fetch(url, { method: 'GET', credentials: 'include' });
      const html = await res.text();

      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;

      const contenido = tempDiv.querySelector('#contenido-principal') || tempDiv;
      contenedor.innerHTML = '';
      contenedor.appendChild(contenido);

      if (typeof window.traducirTextos === 'function') {
        window.traducirTextos(contenedor);
      }

      // 📄 Cargar scripts embebidos
      const externos = Array.from(tempDiv.querySelectorAll('script[src]'));
      const inlines = Array.from(tempDiv.querySelectorAll('script:not([src])'));

      for (let scr of externos) {
        console.log('👉 Cargando externo', scr.src);
        await new Promise((resolve, reject) => {
          const s = document.createElement('script');
          s.src = scr.src;
          s.async = false;
          s.onload = resolve;
          s.onerror = reject;
          document.body.appendChild(s);
        });
      }

      inlines.forEach(script => {
        const s = document.createElement('script');
        s.textContent = script.textContent;
        document.body.appendChild(s);
        console.log('🖋️ Inline ejecutado');
      });

      // 📄 Agregar estilos si los hay
      tempDiv.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
        if (![...document.head.querySelectorAll('link')].some(e => e.href === link.href)) {
          document.head.appendChild(link.cloneNode());
        }
      });

      // 📄 Actualizar URL
      const nuevaURL = `index.php?page=${page}${id ? `&id=${id}` : ''}`;
      if (replaceState) {
        history.replaceState(null, '', nuevaURL);
      } else {
        history.pushState(null, '', nuevaURL);
      }

      if (typeof window.cargarTraducciones === 'function') {
        window.cargarTraducciones();
      }

    } catch (err) {
      console.error("❌ Error al cargar página:", err);
      contenedor.innerHTML = '<div class="alert alert-danger">Error al cargar la página seleccionada.</div>';
    }
  }

  // 📄 Manejo de clics en enlaces dinámicos
  document.addEventListener('click', function (e) {
    const enlace = e.target.closest('.enlaceDinamicos');
    if (!enlace) return;

    e.preventDefault();
    const page = enlace.dataset.link;
    const id = enlace.dataset.id || null;
    if (!page) return;

    cargarPagina(page, id, false);
  });

  // 📄 Soporte para botón "atrás/adelante"
  window.addEventListener('popstate', function () {
    const params = new URLSearchParams(location.search);
    const page = params.get('page') || 'home';
    const id = params.get('id') || null;

    cargarPagina(page, id, true);
  });
});
</script>

<?php
include_once(__DIR__ . '/../layout/parte2.php');
?>