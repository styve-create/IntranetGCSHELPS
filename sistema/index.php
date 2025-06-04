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



?>
<div class="container-fluid">
  <div class="row align-items-start">
<!-- Sidebar unificada para todos los roles -->
<div class="col-md-3 sidebar text-white" >
  <nav class="nav flex-column p-3">

  <!-- Logo -->
  <a href="/" class="d-flex align-items-center mb-3">
    <img src="<?php echo $URL; ?>/imagen/GCS3.png" alt="Logo GCS" class="img-fluid" style="max-width: 80%; height: auto;">
  </a>
  <hr style="border: 1px solid white; opacity: 0.3; margin-bottom: 20px;">



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
          ['texto' => 'Projectos', 'icono' => 'fas fa-list', 'url' => 'Projectos'],
          
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
          ['texto' => 'Registro de Inventario', 'icono' => 'fas fa-list', 'url' => 'inventarioRegistro'],
          ['texto' => 'Listado de Inventario', 'icono' => 'fas fa-user-plus', 'url' => 'listadoInventario'],
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
      echo '<button class="btn btn-outline-light w-100 mb-2 text-start toggle-btn" data-target="#' . $seccion['id'] . '">';
      echo '<i class="' . $seccion['icono'] . ' me-2"></i>' . $seccion['titulo'];
      echo '</button>';

      echo '<div class="collapse" id="' . $seccion['id'] . '"><div class="ps-3 pb-3">';
      foreach ($seccion['items'] as $item) {
        echo '<a href="#" data-link="' . $item['url'] . '" class="d-block text-white mb-1 enlaceDinamicos">';
        echo '<i class="' . $item['icono'] . ' me-2"></i>' . $item['texto'];
        echo '</a>';
      }
      echo '</div></div>';
    }
  }
  ?>
  
  <hr style="border: 1px solid white; opacity: 0.3; margin-top: 20px;">

<a href="#" id="btnLogout" class="btn btn-danger w-100 mt-2">
  <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
</a>
</nav>
</div>


<div class="col-md-9">
  <div id="contenido-dinamico" class="p-3" style="min-height: 600px;"></div>

  </div>
</div>
  </div>
</div>

<script>
    console.log('Inicializando toggles de sidebar');
  document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.toggle-btn');

    buttons.forEach(button => {
      const targetId = button.getAttribute('data-target');
      const target = document.querySelector(targetId);

    
      target.classList.add('notransition');

      const collapse = new bootstrap.Collapse(target, {
        toggle: false
      });

      const state = localStorage.getItem(targetId);
      if (state === 'show') {
        collapse.show();
      } else {
        collapse.hide();
      }

      // Evento de click para abrir/cerrar y guardar en localStorage
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

      // Esperar un frame para aplicar visibilidad y transición normal
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
  const pageInicial = 'informacionTrabajador'; // Página que quieres cargar al inicio

  // Cargar automáticamente el contenido inicial
  const url = `/intranet/sistema/paginasdinamicas.php?page=${pageInicial}&ajax=1`;
  fetch(url, {
    method: 'GET',
    credentials: 'include'
  })
    .then(res => res.text())
    .then(html => {
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;

      const contenido = tempDiv.querySelector('#contenido-principal') || tempDiv;
      const contenedor = document.getElementById('contenido-dinamico');
      contenedor.innerHTML = '';
      contenedor.appendChild(contenido);

      // Ejecutar scripts embebidos
      tempDiv.querySelectorAll('script').forEach(script => {
        const nuevo = document.createElement('script');
        if (script.src) {
          nuevo.src = script.src;
        } else {
          nuevo.textContent = script.textContent;
        }
        document.body.appendChild(nuevo);
      });

      // Agrega estilos si vienen
      tempDiv.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
        if (![...document.head.querySelectorAll('link')].some(e => e.href === link.href)) {
          document.head.appendChild(link.cloneNode());
        }
      });

      // Actualiza la URL (opcional)
      history.pushState(null, '', `index.php?page=${pageInicial}`);
    })
    .catch(err => {
      document.getElementById('contenido-dinamico').innerHTML = '<div class="alert alert-danger">Error al cargar el contenido inicial.</div>';
      console.error('Error al cargar contenido inicial:', err);
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const contenedor = document.getElementById('contenido-dinamico');

  document.querySelectorAll('.enlaceDinamicos').forEach(link => {
    link.addEventListener('click', async function (e) {
      e.preventDefault();
      const page = this.dataset.link;
      if (!page) return;

      const url = `/intranet/sistema/paginasdinamicas.php?page=${page}&ajax=1`;

      try {
        const res = await fetch(url, {
          method: 'GET',
          credentials: 'include'
        });

        const html = await res.text();
        console.log("🧩 HTML recibido desde el servidor:", html);
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;

        const contenido = tempDiv.querySelector('#contenido-principal') || tempDiv;
        contenedor.innerHTML = '';
        contenedor.appendChild(contenido);

        // Ejecutar scripts embebidos
   tempDiv.querySelectorAll('script').forEach(script => {
  if (script.src) {
    const nuevo = document.createElement('script');
    nuevo.src = script.src;
    nuevo.async = false; // 👈 muy importante para que se cargue en orden
    nuevo.onload = () => console.log("✅ Script cargado:", nuevo.src);
    nuevo.onerror = () => console.error("❌ Error al cargar script:", nuevo.src);
    document.head.appendChild(nuevo); // 👈 usar HEAD en lugar de BODY
  } else {
    const nuevo = document.createElement('script');
    nuevo.textContent = script.textContent;
    document.body.appendChild(nuevo);
  }
});

        // Agregar estilos si los hay
        tempDiv.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
          if (![...document.head.querySelectorAll('link')].some(e => e.href === link.href)) {
            document.head.appendChild(link.cloneNode());
          }
        });

        // Actualizar URL
        history.pushState(null, '', `index.php?page=${page}`);

      } catch (err) {
        console.error("❌ Error cargando página dinámica:", err);
        contenedor.innerHTML = '<div class="alert alert-danger">Error al cargar la página seleccionada.</div>';
      }
    });
  });

  // Soporte para botón "atrás" del navegador
  window.addEventListener('popstate', function () {
    const params = new URLSearchParams(location.search);
    const page = params.get('page');
    if (page) {
      document.querySelector(`[data-link="${page}"]`)?.click();
    }
  });
});
</script>
<?php
include_once(__DIR__ . '/../layout/parte2.php');
?>