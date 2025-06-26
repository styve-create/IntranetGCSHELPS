<?php
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

$id_usuario1 = $_SESSION['usuario_info']['id'] ?? null;
if (!$id_usuario1) {
    http_response_code(401);
    echo json_encode(['success' => false, 'msg' => 'No autenticado']);
    exit;
}

// Obtener el trabajador_id
$sql = "SELECT trabajador_id FROM tb_usuarios WHERE id_usuarios = :id_usuario";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id_usuario' => $id_usuario1]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$trabajador_id = $row['trabajador_id'] ?? null;

// Obtener información del trabajador
$sqlTrabajador = "SELECT * FROM trabajadores WHERE id = :trabajador_id";
$stmtTrabajador = $pdo->prepare($sqlTrabajador);
$stmtTrabajador->execute(['trabajador_id' => $trabajador_id]);
$trabajador = $stmtTrabajador->fetch(PDO::FETCH_ASSOC);

if (!$trabajador) {
    echo "<div class='alert alert-danger'>No se encontró la información del trabajador.</div>";
    exit;
}
$sqlCarrusel = "SELECT * FROM tb_carrusel WHERE fecha_fin >= CURDATE() ORDER BY orden ASC";
$items = $pdo->query($sqlCarrusel)->fetchAll(PDO::FETCH_ASSOC);


function enlazarTexto($texto) {
    // Convierte URLs en enlaces clicables
    $texto = preg_replace(
        '~(https?://[^\s<]+)~i',
        '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
        $texto
    );
    // Reemplaza saltos de línea por <br>
    return nl2br($texto);
}

if (!isset($_GET['ajax']) || $_GET['ajax'] != '1') {
    echo "<div class='alert alert-danger'>Error: Carga no permitida directamente.</div>";
    exit;
}

?>

        <link rel="stylesheet" href="<?php echo $URL; ?>/librerias/vendor/npm-asset/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap-icons/font/bootstrap-icons.css">
<style>
  .gradient-custom {
    background: #f6d365;
    background: -webkit-linear-gradient(to right bottom, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1));
    background: linear-gradient(to right bottom, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1));
  }

  .profile-img {
    width: 80px;
    border-radius: 50%;
  }

.ladoDerecho {
  display: flex;
  flex-direction: column;
  height: 100vh;         /* o la altura que necesites */
}

.carruselAnuncios{
 padding: 10px;
  flex: 50%;   
}
.certificados {
padding: 10px;
  flex: 50%;
}

/* 1) Tarjeta fija */
.anuncio-card {
  display: flex;
  flex-direction: column;
  height: 370px;
  overflow: hidden;
}

/* Header y footer fijos */
.anuncio-card .card-header,
.anuncio-card .card-footer {
  flex-shrink: 0;
}

/* 2) Cuerpo: un solo flex-container */
.anuncio-card .cardbody1 {
  display: flex;
  flex: 1 1 auto;
  overflow: hidden;
  min-height: 0; /* importante para que clippee bien */
}

/* 3) Columna imagen: ancho fijo */
.anuncio-card .image-col {
  flex: 0 0 240px;     /* aquí defines un ancho fijo */
  height: 100%;
  overflow: hidden;
}

/* 4) Columna texto: rellena todo lo que queda */
.anuncio-card .text-col {
  flex: 1 1 auto;
  height: 100%;
  overflow-y: auto;
}

/* 5) Imagen ocupa todo su contenedor */
.anuncioImg {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}


.card-body {
  flex-grow: 1;
  overflow-y: auto;
  padding: 0.75rem;
}

.card-title,
.card-text {
  white-space: normal;
  text-overflow: initial;
  overflow: visible;
  
}

.card-footer {
  height: 40px;
  font-size: 0.85rem;
  line-height: 1.2rem;
}
.carousel-control-prev-icon,
.carousel-control-next-icon {
  filter: brightness(0); 
}
  .cursor-zoom {
    cursor: zoom-in;
    transition: transform 0.3s;
  }
.modal-body {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}

.modal-body img#imagenAmpliada {
  max-width: 100%;
  max-height: 80vh;
  width: auto;
  height: auto;
  object-fit: contain;
  display: block;
}
</style>

<div id="contenido-principal">
<div class="container d-flex justify-content-center my-4">
  <div class="card shadow-sm w-100" style="max-width: 960px;">
    <div class="row g-0">
      <!-- Menú lateral -->
      <div class="col-md-4 border-end bg-light p-3">
        <h5 class="mb-3"><strong>Datos Personales</strong></h5>
        <div class="nav flex-column nav-pills" id="v-tabs" role="tablist">
          <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#perfil" type="button">Mi perfil</button>
          <button class="nav-link" data-bs-toggle="pill" data-bs-target="#Documentos" type="button">Documentos</button>
        </div>
      </div>

      <!-- Contenido -->
      <div class="col-md-8 p-4">
        <div class="tab-content">
          <div class="tab-pane fade show active" id="perfil">
            <?php include 'tab_perfil.php'; ?>
          </div>
          <div class="tab-pane fade" id="Documentos">
            <?php include 'tab_Documentos.php'; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  

    <!-- Modal de imagen ampliada -->
    <div class="modal fade" id="modalImagenAnuncio" tabindex="-1" aria-labelledby="modalImagenLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg" >
        <div class="modal-content">
          <div class="modal-body p-0">
            <img id="imagenAmpliada" src="" class="img-fluid  rounded" alt="Imagen ampliada">
          </div>
        </div>
      </div>
    </div>    
    
</div>
        

<!-- Script para previsualizar imagen -->
<script>
    (function(){ 
  const uploadInput = document.getElementById('upload-photo');
  const previewImg = document.getElementById('profile-img-preview');

  uploadInput.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        previewImg.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  });
  
  document.getElementById('upload-photo').addEventListener('change', function () {
  const file = this.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('foto', file);
  formData.append('id_usuario', '<?= $id_usuario1 ?>');
  fetch('/intranet/sistema/pages/informacion/upload_foto_perfil.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
 .then(data => {
  if (data.success) {
    // Añadir timestamp para forzar recarga y evitar caché
    const timestamp = new Date().getTime();
    document.getElementById('profile-img-preview').src = data.ruta + '?t=' + timestamp;
  } else {
    alert(data.message || 'Error al subir imagen');
  }
})
  .catch(err => {
    console.error('Error:', err);
    alert('Error al subir imagen');
  });
})
document.getElementById('form-certificados').addEventListener('submit', function(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);

  fetch('/intranet/sistema/pages/informacion/generar_certificados.php', {
    method: 'POST',
    body: formData
  })
  .then(resp => resp.blob())
  .then(blob => {
    if (blob.type === 'application/zip') {
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = "certificados.zip";
      document.body.appendChild(a);
      a.click();
      a.remove();
      document.getElementById('status-certificados').innerHTML = '<div class="alert alert-success">Descarga iniciada.</div>';
    } else {
      document.getElementById('status-certificados').innerHTML = '<div class="alert alert-danger">No se generaron certificados. Asegúrate de seleccionar al menos uno.</div>';
    }
  })
  .catch(err => {
    console.error(err);
    document.getElementById('status-certificados').innerHTML = '<div class="alert alert-danger">Error al procesar la solicitud.</div>';
  });
});
window.ampliarImagen = function (img) {
  const modalImg = document.getElementById('imagenAmpliada');
  const modal = new bootstrap.Modal(document.getElementById('modalImagenAnuncio'));
  modalImg.src = img.src;
  modal.show();
};
  
    })();
</script>
<script src="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap/dist/js/bootstrap.bundle.min.js"></script>






