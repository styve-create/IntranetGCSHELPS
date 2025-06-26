<?php
// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../analisisRecursos.php');
include_once(__DIR__ . '/funciones_mostrar_jerarquia.php');

// Conexión
require_once(__DIR__ . '/../../../app/controllers/config.php'); // Usa $pdo

// Ruta del archivo de log
$log_index = __DIR__ . '/log_index.txt';

// Función para escribir en el log
function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

log_index("Cargado index.php");





// Obtener campañas sin responsable (nivel raíz)
$sql = "SELECT * FROM tb_campanas WHERE id_responsable IS NULL";
$stmt = $pdo->query($sql);
$campanas_raiz = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<link rel="stylesheet" href="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?php echo $URL; ?>/librerias/vendor/npm-asset/bootstrap/dist/css/bootstrap.min.css">

<style>
/* Base general */
body {
    font-family: 'Segoe UI', Roboto, sans-serif;
    background-color: #f8f9fa;
}

.container {
    max-width: 900px;
    margin: 30px auto;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

/* Botones */
.tree-controls button {
    margin-right: 10px;
    font-size: 0.9rem;
    padding: 8px 12px;
}
.action-buttons button {
    margin: 6px 4px 0 0;
    font-size: 0.85rem;
    padding: 6px 10px;
}

/* Estilos base para todos los nodos */
.list-group-item {
    padding: 10px 12px;
    margin-bottom: 10px;
    border-radius: 6px;
    font-size: 0.85rem;
    transition: all 0.2s ease;
    position: relative;
    z-index: 1;
}



/* Campañas */
.list-group-item[data-tipo='campana'] {
    background-color: #eaf7fb;
    border-left: 6px solid #00a9c6;
}

.list-group-item[data-tipo='campana']:hover {
    background-color: #d0f0fa;
}

/* Subcampañas */
.list-group-item[data-tipo='subcampana'] {
    background-color: #d8f5f5;
    border-left: 6px solid #00c68a;
}

.list-group-item[data-tipo='subcampana']:hover {
    background-color: #c2f2f2;
}

/* Jefes */
.list-group-item[data-tipo='jefe'] {
    background-color: #007c94;
    color: white;
    border-left: 6px solid #005f72;
}

.list-group-item[data-tipo='jefe']:hover {
    background-color: #009bb5;
}

/* Trabajadores */
.list-group-item[data-tipo='trabajador'] {
    background-color: #f0f0f0;
    border-left: 6px solid #999;
}

.list-group-item[data-tipo='trabajador']:hover {
    background-color: #e0e0e0;
}

/* Dragging */
.dragging {
    opacity: 0.85;
    transform: scale(1.02);
    box-shadow: 0 6px 16px rgba(0, 169, 198, 0.3);
    z-index: 10;
}

/* Drag handle */
[draggable="true"] {
    cursor: grab;
}

/* Drop targets */
.drop-target {
    border: 2px dashed #00a9c6;
    background-color: rgba(0, 169, 198, 0.08);
}

.drop-target-subcampana {
    border: 2px dashed #ff4500;
    background-color: rgba(255, 69, 0, 0.08);
}

.drop-target-campana {
    border: 2px dashed #008000;
    background-color: rgba(0, 128, 0, 0.08);
}
</style>
<div id="contenido-principal">
    <div class="container">
    <h2 class="text-center"> Jerarquía de Campañas</h2>
    <br>
<?php foreach ($campanas_raiz as $campana): ?>
    <ul class="list-group list-group-flush">
        <li class="list-group-item list-group-item-info" 
            draggable="true" 
            data-tipo="campana" 
            data-jerarquia="1"
            data-campana="<?= $campana['id'] ?>">
            
            <details data-id="campana-<?= $campana['id'] ?>">
                <summary>
                    <i class="fas fa-globe-americas"></i> <?= htmlspecialchars($campana['nombre']) ?> (Campaña Principal)
                    <button class="add-btn btn btn-sm btn-primary ms-2" 
                            data-accion="agregar_jefe" 
                            data-jefe="" 
                            data-campana="<?= $campana['id'] ?>">
                        <i class="fas fa-user-tie"></i> Agregar Jefe Directo
                    </button>
                     
                </summary>
                <?php mostrarJerarquiaCampana($campana['id'], $pdo); ?>
            </details>

        </li>
    </ul>
<?php endforeach; ?>
</div>
<!-- Modal mejorado con Bootstrap -->
<div class="modal fade" id="modalFormulario" tabindex="-1" aria-labelledby="tituloModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Centrado y más ancho -->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4" id="modalContenido">
        <!-- Aquí se carga el formulario con AJAX -->
      </div>
    </div>
  </div>
</div>
</div>


<script src="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script>
    (function() {
        function isDraggableValid(target) {
  const tipo = target.dataset.tipo;
  const jerarquia = parseInt(target.dataset.jerarquia);
  if (tipo === 'campana' && jerarquia === 1) return false;
  return tipo === 'subcampana' || tipo === 'jefe' || (tipo === 'campana' && jerarquia > 1);
}

// 🌟 Drag start
function handleDragStart(e) {
  e.stopPropagation();
  if (!isDraggableValid(this)) {
    console.warn('Elemento no válido para arrastrar.');
    e.preventDefault();
    return;
  }

  draggedItem = {
    id: this.dataset.id,
    tipo: this.dataset.tipo,
    campana: this.dataset.campana,
    jefe: this.dataset.tipo === 'jefe' ? this.dataset.id : this.dataset.jefe || null
  };
 console.log("📦 Arrastrando:", draggedItem);

  this.classList.add('dragging');
  e.dataTransfer.effectAllowed = 'move';
}

// 🌟 Drag end
function handleDragEnd() {
  this.classList.remove('dragging');
}

// 🌟 Drag over
function handleDragOver(e) {
  e.preventDefault();
  if (draggedItem.tipo === 'subcampana') {
    this.classList.add('drop-target-subcampana');
    this.classList.remove('drop-target-campana');
  } else if (draggedItem.tipo === 'campana') {
    this.classList.add('drop-target-campana');
    this.classList.remove('drop-target-subcampana');
  }
}

// 🌟 Drag leave
function handleDragLeave() {
  this.classList.remove('drop-target');
}

// 🌟 Drop
function handleDrop(e) {
  e.preventDefault();
  e.stopPropagation();
  const target = e.target.closest('[data-campana]');
  const destinoCampanaId = target?.dataset?.campana || null;
  if (!destinoCampanaId) {
    Swal.fire('Error', 'No se pudo identificar la campaña de destino', 'error');
    return;
  }

  let jefeDestino = null;
  if (target.dataset.tipo === 'jefe') {
    jefeDestino = target.dataset.id;
  } else {
    const jefes = target.querySelectorAll('[data-tipo="jefe"]');
    if (jefes.length > 0) jefeDestino = jefes[0].dataset.id;
  }

  moverElemento(draggedItem, destinoCampanaId, jefeDestino);
}

// 🌟 Mover elemento vía AJAX
function moverElemento(item, destinoCampanaId, destinoJefeId) {
  if (!item || isProcessing) return;
  isProcessing = true;

  const formData = new FormData();
  formData.append('id', item.id);
  formData.append('tipo', item.tipo);
  formData.append('origen_campana', item.campana);
  formData.append('destino_campana', destinoCampanaId);
  formData.append('destino_jefe', destinoJefeId);

  fetch('/intranet/sistema/pages/jerarquias/mover_elemento.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(result => {
      if (result.ok) {
        Swal.fire('Éxito', result.mensaje, 'success');
        actualizarJerarquia();
      } else {
        Swal.fire('Error', result.mensaje, 'error');
        actualizarJerarquia();
      }
    })
    .catch(err => {
      console.error(err);
      Swal.fire('Error', 'Error al mover el elemento.', 'error');
    });
}

// 🌟 Re-renderizar toda la jerarquía sin recargar
function actualizarJerarquia() {
  const modal = bootstrap.Modal.getInstance(document.getElementById('modalFormulario'));
  if (modal) modal.hide();

  // ✅ Capturar TODOS los <details> que están abiertos
  const abiertos = Array.from(document.querySelectorAll('details[open]'))
                        .map(detail => detail.dataset.id);

  fetch('/intranet/sistema/pages/jerarquias/render_jerarquia.php')
    .then(res => res.text())
    .then(text => {
      try {
        const data = JSON.parse(text);
        if (data.ok) {
          const contenedor = document.querySelector('.container');
          contenedor.innerHTML = `<h2 class="text-center">Jerarquía de Campañas</h2><br>${data.html}`;

          // ✅ Reabrir todos los <details> que estaban abiertos antes
         document.querySelectorAll('details[data-id]').forEach(detail => {
  const id = detail.getAttribute('data-id');
  if (abiertos.includes(id)) {
    detail.setAttribute('open', '');
  } else {
    detail.removeAttribute('open'); // O puedes dejarlo sin tocar
  }
});

          inicializarEventosJerarquia();
        } else {
          Swal.fire('Error', 'No se pudo actualizar la jerarquía', 'error');
        }
      } catch (e) {
        console.error("❌ JSON inválido:", e);
        console.error("Contenido recibido:", text);
        Swal.fire('Error', 'Respuesta inválida del servidor.', 'error');
      }
    })
    .catch(err => {
      console.error("Error en fetch:", err);
      Swal.fire('Error', 'Hubo un error al conectar con el servidor.', 'error');
    });
}

// 🌟 Asignar todos los eventos al DOM dinámico
function inicializarEventosJerarquia() {
  document.querySelectorAll('[draggable="true"]').forEach(item => {
    item.addEventListener('dragstart', handleDragStart);
    item.addEventListener('dragend', handleDragEnd);
  });

  document.querySelectorAll('.list-group-item-info, .list-group-item-primary').forEach(area => {
    area.addEventListener('dragover', handleDragOver);
    area.addEventListener('dragleave', handleDragLeave);
    area.addEventListener('drop', handleDrop);
  });

document.querySelectorAll('.edit-btn, .add-btn, .delete-btn').forEach(function (boton) {
        
        boton.addEventListener('click', function () {
           
            const id = this.dataset.id || '';
            const accion = this.dataset.accion;
            let saveUrl = ''; // Asegúrate de definir saveUrl aquí

            switch (accion) {
                case 'agregar_jefe':
                    saveUrl = '/intranet/sistema/pages/jerarquias/agregar_jefe.php';
                    break;
                case 'editar_jefe':
                    saveUrl = '/intranet/sistema/pages/jerarquias/editar_jefe.php';
                    break;
                case 'agregar_trabajador':
                    saveUrl = '/intranet/sistema/pages/jerarquias/agregar_trabajador.php';
                    break;
                case 'eliminar_jefe':
                    saveUrl = '/intranet/sistema/pages/jerarquias/eliminar_jefe.php';
                    break;
                case 'eliminar_trabajador':
                    saveUrl = '/intranet/sistema/pages/jerarquias/eliminar_trabajador.php';
                    break;
                case 'agregar_campana':
                    saveUrl = '/intranet/sistema/pages/jerarquias/agregar_campana.php';
                    break;
                case 'eliminar_campana':
                    saveUrl = '/intranet/sistema/pages/jerarquias/eliminar_campana.php';
                    break;
                default:
                    Swal.fire('Error', 'Acción no reconocida', 'error');
                    return;
            }
            

            const formData = new FormData();

           if (accion === 'agregar_trabajador') {
                formData.append('campana_id', this.dataset.campana || '');
                formData.append('jefe_id', this.dataset.jefe || '');
            } else {
                
                if (accion === 'agregar_campana') {
                    formData.append('id_padre', this.dataset.campana || '');
                    formData.append('id_responsable', this.dataset.jefe || '');
                } else {
                    formData.append('campana_id', this.dataset.campana || '');
                   
                    formData.append('jefe_id', this.dataset.jefe || '');
                    formData.append('trabajador_id', this.dataset.trabajador || '');
                }
            }

            // Realizamos la solicitud fetch
            fetch(saveUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                
                try {
                    const data = JSON.parse(text);
                    if (data.ok && data.formulario) {
                        document.getElementById('modalContenido').innerHTML = data.formulario;

                        const modal = new bootstrap.Modal(document.getElementById('modalFormulario'));
                        modal.show();

                        // Ahora, manejamos el formulario dentro del modal
                        const form = document.querySelector('form');
                        if (form) {
                            form.addEventListener('submit', function (e) {
                                e.preventDefault(); // Evita la redirección del formulario

                                const formData = new FormData(form);

                                // Asignamos la URL para guardar los datos
                                switch (accion) {
                                    case 'agregar_jefe':
                                        saveUrl = '/intranet/sistema/pages/jerarquias/guardar_nuevo_jefe.php';
                                        break;
                                    case 'editar_jefe':
                                        saveUrl = '/intranet/sistema/pages/jerarquias/guardar_jefe.php';
                                        break;
                                    case 'agregar_trabajador':
                                        saveUrl = '/intranet/sistema/pages/jerarquias/guardar_trabajador.php';
                                        break;
                                    case 'eliminar_jefe':
                                        saveUrl = '/intranet/sistema/pages/jerarquias/Guardareliminar_jefe.php';
                                        break;
                                    case 'eliminar_trabajador':
                                        saveUrl = '/intranet/sistema/pages/jerarquias/Guardareliminar_trabajador.php';
                                        break;
                                    case 'agregar_campana':
                                        saveUrl = '/intranet/sistema/pages/jerarquias/guardar_campana.php';
                                        break;
                                    case 'eliminar_campana':
                                        saveUrl = '/intranet/sistema/pages/jerarquias/Guardareliminar_campana.php';
                                        break;
                                    default:
                                        Swal.fire('Error', 'Acción no reconocida', 'error');
                                        return;
                                }

                                // Realizamos el POST con el formulario
                                fetch(saveUrl, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(res => res.json())
                                .then(result => {
                                    if (result.ok) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Éxito',
                                            text: result.mensaje || 'Cambios guardados correctamente.',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            
                                           actualizarJerarquia();
                                        });
                                    } else {
                                        Swal.fire('Error', result.mensaje || 'Hubo un error al guardar.', 'error');
                                         console.error('Error al parsear JSON:', result.mensaje);

                                    }
                                })
                                .catch(error => {
                                    console.error(error);
                                    console.error('Error en la solicitud AJAX:', error);
                                    Swal.fire('Error', 'Error en la solicitud AJAX.', 'error');
                                    
                                    
                                });
                            });
                        }
                    } else {
                        Swal.fire('Error', 'No se pudo cargar el formulario.', 'error');
                         
                       
                    }
                } catch (e) {
                    console.error('Error al parsear JSON:', e, text);
                    Swal.fire('Error', 'La respuesta del servidor no es válida.', 'error');
                     
                    
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo cargar el contenido.', 'error');
                
            });
        });
    });

}

// 🧠 Variables globales controladas
let draggedItem = null;
let isProcessing = false;

inicializarEventosJerarquia();
})();


</script>




