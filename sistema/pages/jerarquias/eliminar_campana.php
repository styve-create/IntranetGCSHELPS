<?php

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../app/controllers/config.php');

// Ruta del archivo de log
$log_index = __DIR__ . '/log_index.txt';
// Función para escribir en el log
function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

log_index("Inicio del script eliminar_campana.php");




$campana_id = intval($_POST['campana_id'] ?? 0);

log_index("Datos recibidos campana_id: $campana_id ");

if ($campana_id <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'ID de campaña inválido.']);
    exit;
}

// Buscar el nombre de la campaña
$stmt = $pdo->prepare("SELECT nombre FROM tb_campanas WHERE id = ?");
$stmt->execute([$campana_id]);
$campana = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$campana) {
    echo json_encode(['ok' => false, 'mensaje' => 'La campaña no existe.']);
    exit;
}

$nombre_campana = htmlspecialchars($campana['nombre'], ENT_QUOTES, 'UTF-8');

$formularioHTML = '
<form id="formEliminarCampana" method="POST">
    <input type="hidden" name="campana_id" value="' . $campana_id . '">

    <div class="mb-4">
        <h5 class="fw-bold text-danger">Eliminar Campaña</h5>
        <p class="text-muted">
            ¿Estás seguro de que deseas eliminar la campaña 
            <span class="fw-semibold text-dark">"' . $nombre_campana . '"</span>?
            <br>
            <span class="text-danger fw-semibold">Esta acción es irreversible y eliminará también toda la jerarquía asociada a esta campaña.</span>
        </p>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <button type="submit" class="btn btn-danger">
            Sí, eliminar
        </button>
    </div>
</form>';

echo json_encode([
    'ok' => true,
    'formulario' => $formularioHTML
]);
?>