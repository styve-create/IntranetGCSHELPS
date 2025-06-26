<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('America/Bogota');


include_once(__DIR__ . '/../../../app/controllers/config.php');

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }

    .carousel-img {
      max-height: 300px;
      object-fit: cover;
    }

    .social-icons a {
      display: block;
      margin-bottom: 10px;
      font-size: 1.5rem;
    }

    .benefits-card img {
      max-width: 100%;
      border-radius: 8px;
    }

    .cta-section .btn {
      width: 100%;
      padding: 15px;
      font-size: 1rem;
    }

    .cta-section {
      background-color: #eee;
      padding: 2rem 0;
    }
  </style>

<div id="contenido-principal">
    <div class="container py-4">
        <div class="row g-4">
            <div id="carruselAnuncios" class="carousel slide carruselAnuncios" data-bs-ride="carousel" data-bs-interval="7000">
                <div class="carousel-inner carouselDiv" id="carruselAnunciosContenido">
                        
                            </div>             
                            <button class="carousel-control-prev" type="button" data-bs-target="#carruselAnuncios" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carruselAnuncios" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </button>
                        
                </div>
            </div>
        </div>
        <div class="container mt-4">
            <div class="row g-3">
              <!-- Ausencias -->
              <div class="col-12 col-sm-6 col-lg-3">
                <a href="https://gcshelps.com/intranet/Ausencia/index.php" target="_blank" class="text-decoration-none">
                  <div class="card text-white bg-primary h-100 shadow">
                    <div class="card-body d-flex flex-column justify-content-between">
                      <div>
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <h3 class="fw-bold">Ausencias</h3>
                            <p class="mb-0">Formulario de ausencias</p>
                          </div>
                          <i class="bi bi-calendar-x display-5"></i>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer text-white text-center bg-primary-subtle">
                      Más información <i class="bi bi-arrow-right-circle"></i>
                    </div>
                  </div>
                </a>
              </div>
        
              <!-- Horarios -->
              <div class="col-12 col-sm-6 col-lg-3">
                <a href="https://gcshelps.com/intranet/listadoActividades/index.php" target="_blank" class="text-decoration-none">
                  <div class="card text-white bg-success h-100 shadow">
                    <div class="card-body d-flex flex-column justify-content-between">
                      <div>
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <h3 class="fw-bold">Actividades</h3>
                            <p class="mb-0">Formulario de Actividades</p>
                          </div>
                          <i class="bi bi-clock-history display-5"></i>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer text-white text-center bg-success-subtle">
                      Más información <i class="bi bi-arrow-right-circle"></i>
                    </div>
                  </div>
                </a>
              </div>
            </div>
        </div>
    </div>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (function() { 
         const BASE_URL = "<?= $URL ?>";
         
        window.editarRegistro = function() {
    fetch(`/intranet/sistema/pages/home/api_carrusel.php?t=${Date.now()}`)
    .then(response => response.json())
    .then(data => renderCarrusel(data))
    .catch(err => console.error("Error al cargar carrusel:", err));  
        };



function renderCarrusel(items) {
  const container = document.getElementById('carruselAnunciosContenido');
  container.innerHTML = ''; // limpia el contenido anterior

  if (items.length === 0) {
    container.innerHTML = `
      <div class="carousel-item active">
        <div class="card anuncio-card shadow-sm d-flex flex-column text-center justify-content-center align-items-center">
          <div class="card-body">
            <h5 class="text-muted">📭 No hay anuncios disponibles</h5>
            <p class="text-muted">Vuelve pronto para ver nuevos anuncios corporativos.</p>
          </div>
        </div>
      </div>
    `;
    return;
  }

  items.forEach((item, index) => {
    const isActive = index === 0 ? 'active' : '';
    const tieneImagen = item.imagen_url && item.imagen_url.trim() !== '';
    const rutaImagen = tieneImagen ? `${BASE_URL}${item.imagen_url}` : '';
    const imagenHTML = tieneImagen
      ? `<div class="col-md-4">
          <img src="${rutaImagen}" class="img-fluid anuncioImg cursor-zoom" onclick="ampliarImagen(this)" alt="Anuncio">
         </div>`
      : '';

    const cuerpoHTML = `
      <div class="row g-0 flex-grow-1 cardbody1">
        ${tieneImagen ? imagenHTML : ''}
        <div class="${tieneImagen ? 'col-md-8' : 'col-md-12'}">
          <div class="card-body text-${tieneImagen ? '' : 'center'}">
            <h5 class="card-title">${escapeHTML(item.titulo || '')}</h5>
            <p class="card-text">${enlazarTexto(item.descripcion || '')}</p>
          </div>
        </div>
      </div>
    `;

    const itemHTML = `
      <div class="carousel-item ${isActive}">
        <div class="card anuncio-card shadow-sm d-flex flex-column">
          <div class="card-header text-center bg-primary text-white">📢 Anuncio Corporativo</div>
          ${cuerpoHTML}
          <div class="card-footer text-muted text-center">
            🕒 Publicado: ${new Date(item.creado_en).toLocaleDateString()}
          </div>
        </div>
      </div>
    `;

    container.insertAdjacentHTML('beforeend', itemHTML);
  });
}

// Escapa texto para evitar XSS
function escapeHTML(str) {
  return str.replace(/[&<>"']/g, function (m) {
    return ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[m];
  });
}

// Convierte URLs en enlaces clicables
function enlazarTexto(texto) {
  const escaped = escapeHTML(texto);
  return escaped.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>');
}

window.editarRegistro ();
    })();

</script>

