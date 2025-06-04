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

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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

 .anuncio-card {
  width: 100%;
  max-width: 540px;
  height: 370px; 
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  border-radius: .5rem;
  overflow: hidden;
  
  max-height: 370px;
  
}

.cardbody1 {
  display: flex;
  height: 240px;
  overflow: hidden;
}

.cardbody1 .col-md-4 {
  width: 240px;
  height: 240px;
  flex-shrink: 0;
  overflow: hidden;
  padding: 0;
}

.cardbody1 .col-md-8 {
  flex-grow: 1;
  height: 240px;
  padding: 0.75rem;
  overflow-y: auto;
}

.anuncioImg {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: .5rem 0 0 .5rem;
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
<div class="card shadow-sm w-100" style="border-radius: .5rem;">
    <div class="row g-0">
        <!-- Lado izquierdo: Información del trabajador -->
        <div class="col-md-4 bg-light text-dark d-flex flex-column align-items-center justify-content-center p-3">
            <div class="position-relative d-inline-block text-black-50 bg-white p-2 rounded-circle mb-2"
                style="width: 190px; height: 190px; display: flex; align-items: center; justify-content: center;">
                <img id="profile-img-preview"
                     src="<?= $URL . htmlspecialchars($trabajador['foto_perfil']) ?>"
                     class="img-fluid rounded-circle anuncioImg cursor-zoom"
                     data-bs-toggle="modal" data-bs-target="#modalImagenAnuncio"
                      onclick="ampliarImagen(this)"
                     style="width: 170px; height: 170px; object-fit: cover;"
                     alt="Foto de perfil">

                <label for="upload-photo"
                       class="btn btn-light position-absolute bottom-0 end-0 m-2 d-flex align-items-center justify-content-center shadow"
                       style="width: 40px; height: 40px; border-radius: 50%; cursor: pointer;">
                    <i class="bi bi-camera"></i>
                    <input type="file" id="upload-photo" name="foto" class="d-none" accept="image/*">
                </label>
            </div>

            <h5><?= htmlspecialchars($trabajador['nombre_completo']) ?></h5>
            <p><?= htmlspecialchars($trabajador['cargo_certificado'] ?? '') ?></p>

            <div class="card-body p-2 bg-white border rounded">
                <p class="text-muted mb-0"><strong>Documento: </strong><?= htmlspecialchars($trabajador['numero_documento']) ?></p>
                <p class="text-muted mb-0"><strong>Teléfono: </strong><?= htmlspecialchars($trabajador['celular']) ?></p>
                <p class="text-muted mb-0"><strong>Email: </strong><?= htmlspecialchars($trabajador['email']) ?></p>
                <p class="text-muted mb-0"><strong>RH+: </strong><?= htmlspecialchars($trabajador['grupo_sanguineo']) ?></p>
                <p class="text-muted mb-0"><strong>Dirección: </strong><?= htmlspecialchars($trabajador['domicilio']) ?></p>
                <p class="text-muted mb-0"><strong>Contacto Emergencia: </strong><?= htmlspecialchars($trabajador['nombre_contacto_emergencia']) ?></p>
                <p class="text-muted mb-0"><strong>Tel. Emergencia: </strong><?= htmlspecialchars($trabajador['numero_contacto_emergencia']) ?></p>
                <p class="text-muted mb-0"><strong>EPS: </strong><?= htmlspecialchars($trabajador['eps']) ?></p>
            </div>
        </div>

        <!-- Lado derecho: Carrusel y certificados -->
        <div class="col-md-8">
            <div id="carruselAnuncios" class="carousel slide" data-bs-ride="carousel" data-bs-interval="7000">
                <div class="carousel-inner">
                    <?php if (count($items) === 0): ?>
                        <div class="carousel-item active">
                            <div class="card anuncio-card shadow-sm d-flex flex-column text-center justify-content-center align-items-center">
                                <div class="card-body">
                                    <h5 class="text-muted">📭 No hay anuncios disponibles</h5>
                                    <p class="text-muted">Vuelve pronto para ver nuevos anuncios corporativos.</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($items as $index => $item): ?>
                            <?php
                            $tieneImagen = !empty($item['imagen_url']);
                            $ruta_imagen = $tieneImagen ? $URL . $item['imagen_url'] : null;
                            $isActive = $index === 0 ? 'active' : '';
                            ?>
                            <div class="carousel-item <?= $isActive ?>">
                                <div class="card anuncio-card shadow-sm d-flex flex-column">
                                    <div class="card-header text-center bg-primary text-white">📢 Anuncio Corporativo</div>

                                    <?php if ($tieneImagen): ?>
                                        <div class="row g-0 flex-grow-1 cardbody1">
                                            <div class="col-md-4">
                                                <img src="<?= $ruta_imagen ?>"
                                                 class="img-fluid anuncioImg cursor-zoom"
                                                 onclick="ampliarImagen(this)"
                                                 alt="Anuncio">
                                            </div>
                                            <div class="col-md-8">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= htmlspecialchars($item['titulo']) ?></h5>
                                                    <p class="card-text"><?= enlazarTexto($item['descripcion']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="row g-0 flex-grow-1 cardbody2">
                                            <div class="col-md-12 d-flex align-items-center justify-content-center">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title"><?= htmlspecialchars($item['titulo']) ?></h5>
                                                    <p class="card-text"><?= enlazarTexto($item['descripcion']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="card-footer text-muted text-center">
                                        🕒 Publicado: <?= date("d M Y", strtotime($item['creado_en'] ?? 'now')) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carruselAnuncios" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carruselAnuncios" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>

            <div class="card-body p-4 bg-white border rounded mt-3">
                <h6>Documentos disponibles</h6>
                <form id="form-certificados" method="POST" action="generar_certificados.php">
                    <input type="hidden" name="documento" value="<?= htmlspecialchars($trabajador['numero_documento']) ?>">
                    <label><input type="checkbox" name="certificados[]" value="Certificacion_ACTIVO_GCS.docx"> Certificación ACTIVO GCS</label><br>
                    <label><input type="checkbox" name="certificados[]" value="Certificacion_RETIRO_GCS.docx"> Certificación RETIRO GCS</label><br>
                    <button type="submit" class="btn btn-primary btn-sm mt-2"><i class="bi bi-download"></i> Descargar certificados</button>
                </form>
                <div id="status-certificados" class="mt-2"></div>
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
      document.getElementById('profile-img-preview').src = data.ruta;
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>





