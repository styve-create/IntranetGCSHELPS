<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración
include_once(__DIR__ . '/../../../app/controllers/config.php');

// Ruta del archivo de log
$log_index = __DIR__ . '/log_index.txt';

// Función para escribir en el log
function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

log_index("Cargado index.php");

// Cabeceras para la respuesta
header('Content-Type: application/json');
log_index("Cabecera Content-Type: application/json establecida");


$id = intval($_POST['jefe_id'] ?? 0);
$campana_id = intval($_POST['campana_id'] ?? 0);

if ($id <= 0 || $campana_id <= 0) {
    log_index("ID de jefe o campaña no proporcionado.");
    echo json_encode([
        'ok' => false,
        'mensaje' => 'ID de jefe o campaña no proporcionado.'
    ]);
    exit;
}


log_index("ID del jefe recibido: $id");

// Obtener datos del jefe
$sql = "SELECT t.id, t.nombre_completo, tc.campana_id, tc.cargo_id 
        FROM trabajadores t
        INNER JOIN trabajadores_campanas tc ON t.id = tc.trabajador_id
        WHERE t.id = ? AND tc.campana_id = ?";
log_index("Consulta para obtener datos del jefe ejecutada: $sql");
$stmt = $pdo->prepare($sql);
$stmt->execute([$id, $campana_id]);
$jefe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jefe) {
    log_index("Jefe no encontrado para ID: $id");
    echo json_encode([
        'ok' => false,
        'mensaje' => 'Jefe no encontrado.'
    ]);
    exit;
}
log_index("Datos del jefe obtenidos: " . json_encode($jefe));

// Consultas auxiliares
$campanas = $pdo->query("SELECT id, nombre FROM tb_campanas WHERE estado = 'activa'")->fetchAll(PDO::FETCH_ASSOC);
log_index("Campañas activas obtenidas: " . count($campanas) . " campañas");

$cargos = $pdo->query("SELECT id, nombre FROM cargos")->fetchAll(PDO::FETCH_ASSOC);
log_index("Cargos obtenidos: " . count($cargos) . " cargos");

$trabajadores = $pdo->query("SELECT id, nombre_completo, numero_documento FROM trabajadores")->fetchAll(PDO::FETCH_ASSOC);
log_index("Trabajadores obtenidos: " . count($trabajadores) . " trabajadores");

// Crear formulario
$formularioHTML = '
<form id="formEditarJefe" method="POST">
    <input type="hidden" name="id" value="' . htmlspecialchars($jefe['id']) . '">
    <input type="hidden" name="campana_id" value="' . htmlspecialchars($jefe['campana_id']) . '">
    <input type="hidden" name="cargo_id" value="' . htmlspecialchars($jefe['cargo_id']) . '">

    <div class="mb-3">
        <label class="form-label">Nombre Completo</label>
        <input type="text" class="form-control" name="nombre" value="' . htmlspecialchars($jefe['nombre_completo']) . '" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">Nuevo Jefe</label>
        <select name="nuevo_jefe_id" class="form-select">
            <option value="">Seleccione un trabajador</option>
            ' . implode('', array_map(function($trabajador) {
                return "<option value='{$trabajador['id']}'>" . htmlspecialchars($trabajador['nombre_completo']) . " - " . htmlspecialchars($trabajador['numero_documento']) . "</option>";
            }, $trabajadores)) . '
        </select>
    </div>

    <div class="text-end">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
    </div>
</form>
';
log_index("Formulario HTML generado para el jefe con ID: " . $jefe['id']);

// Enviar respuesta
echo json_encode([
    'ok' => true,
    'formulario' => $formularioHTML
]);
log_index("Respuesta JSON enviada con el formulario");

// Salir del script
exit;
log_index("Proceso completado y script terminado.");
?>