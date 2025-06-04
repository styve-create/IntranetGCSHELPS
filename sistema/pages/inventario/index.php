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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Equipos Asignados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="<?php echo $URL; ?>/librerias/DataTables/datatables.min.css" rel="stylesheet">
    <link href="<?php echo $URL; ?>/librerias/DataTables/datatables.css" rel="stylesheet">
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
</head>
<body>
  
<div class="container-fluid">
    <div class="form-container">
        <div class="form-title">Asignación de Equipos</div>
        <form method="post" id="formAsignacion">
            <div class="mb-3">
                <label class="form-label">Seleccionar trabajador</label>
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
                    <button type="submit" class="btn btn-primary w-45" id="btnEnviar">Enviar</button>
                    <button type="reset" class="btn btn-secondary w-45">Cancelar</button>
                </div>
            </form>
        </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Autocompletar datos del trabajador
    document.getElementById('selectTrabajador').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        document.getElementById('nombre').value = selected.dataset.nombre || '';
        document.getElementById('documento').value = selected.dataset.documento || '';
        document.getElementById('email').value = selected.dataset.email || '';
    });

    // 2. Habilitar/deshabilitar input de serial al seleccionar equipo
    document.querySelectorAll('.equipo-check').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const serialInput = this.closest('.form-check').querySelector('.serial-input');
            if (this.checked && serialInput) {
                serialInput.disabled = false;
                serialInput.required = true;
            } else if (serialInput) {
                serialInput.disabled = true;
                serialInput.value = '';
                serialInput.removeAttribute('required');
            }
        });
    });

    // 3. Envío con fetch
    document.getElementById('formAsignacion').addEventListener('submit', function (e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const btnEnviar = document.getElementById('btnEnviar');
        const url = '/intranet/sistema/pages/inventario/save.php';

        console.log('Datos del formulario:', [...formData.entries()]);
        console.log('Ruta de la solicitud:', url);

        btnEnviar.disabled = true;
        btnEnviar.textContent = 'Enviando...';

        fetch(url, {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(res => {
            console.log("Respuesta del servidor:", res);
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Asignación completada',
                    text: res.message || 'Los equipos fueron asignados correctamente.',
                    confirmButtonColor: '#007ea7'
                }).then(() => {
                    form.reset();
                    btnEnviar.disabled = false;
                    btnEnviar.textContent = 'Enviar';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'Ocurrió un error al guardar la asignación.',
                    confirmButtonColor: '#d33'
                });
                btnEnviar.disabled = false;
                btnEnviar.textContent = 'Enviar';
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de servidor',
                text: 'No se pudo procesar la solicitud. Inténtalo más tarde.',
                confirmButtonColor: '#d33'
            });
            btnEnviar.disabled = false;
            btnEnviar.textContent = 'Enviar';
        });
    });
});
</script>
<script>
let beaconSent = false;

// Guardar el tiempo de inicio real cuando se abre la página
const tiempoInicio = performance.now();

window.addEventListener("beforeunload", function () {
    if (beaconSent) return;

    // Obtener uso de RAM si está disponible
    let ramUsageMb = null;
    try {
        if (performance.memory) {
            ramUsageMb = Math.round(performance.memory.usedJSHeapSize / 1024 / 1024); // en MB
        }
    } catch (e) {
        console.warn("No se pudo obtener uso de RAM:", e);
    }

    // Calcular tiempo de uso de la página (aproximado, en segundos)
    const tiempoCPU = Math.round(performance.now() / 1000);

    // Preparar datos para enviar al servidor
    const payload = {
        id_conexion: '<?php echo $_SESSION['id_conexion'] ?? ''; ?>',
        pagina: '<?php echo $_SERVER['REQUEST_URI']; ?>',
        tiempo_inicio: '<?php echo $_SESSION['tiempo_inicio'] ?? microtime(true); ?>',
        ram_usage_mb: ramUsageMb,
        tiempo_cpu: tiempoCPU
    };

    // Enviar con sendBeacon
    const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
    navigator.sendBeacon('<?php echo $URL; ?>/sistema/cerrar_pagina.php', blob);

    beaconSent = true;
});
</script>
</body>
</html>