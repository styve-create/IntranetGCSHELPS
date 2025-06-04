<?php

// Ruta del archivo de log
$log_index = __DIR__ . '/log_index.txt';

// Función para escribir en el log
function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

log_index("Cargado agregar_trabajador.php");

// Incluir configuración
include_once(__DIR__ . '/../../../app/controllers/config.php');

// Verificar que se envíen jefe_id y campana_id
log_index("Verificando datos enviados...");

if (!isset($_POST['jefe_id']) || !isset($_POST['campana_id'])) {
    log_index("Faltan datos: jefe_id o campana_id");
    echo json_encode(['ok' => false, 'mensaje' => 'Datos incompletos (jefe_id o campana_id)']);
    exit;
}

$jefe_id = intval($_POST['jefe_id']);
$campana_id = intval($_POST['campana_id']);

log_index("Datos recibidos: jefe_id = $jefe_id, campana_id = $campana_id");

// Obtener el cargo correspondiente a trabajador normal (nivel_jerarquia = 3)
$sql_cargo = "SELECT id, nombre FROM cargos WHERE nivel_jerarquia = 3 LIMIT 1";
log_index("Ejecutando consulta para obtener cargo para trabajador normal...");

$cargo = $pdo->query($sql_cargo)->fetch(PDO::FETCH_ASSOC);

if (!$cargo) {
    log_index("No se encontró cargo para trabajador normal");
    echo json_encode(['ok' => false, 'mensaje' => 'No se encontró cargo para trabajador normal']);
    exit;
}

log_index("Cargo encontrado: " . print_r($cargo, true));

// Obtener trabajadores no asignados a la campaña
$sql_trabajadores = "
    SELECT t.id, t.nombre_completo
FROM trabajadores t
LEFT JOIN trabajadores_campanas tc ON t.id = tc.trabajador_id AND tc.campana_id = :campana_id
LEFT JOIN tb_jerarquia_trabajadores tj ON t.id = tj.id_trabajador AND tj.id_campana = :campana_id
WHERE tc.campana_id IS NULL OR tj.id_trabajador IS NULL;
    )
";
log_index("Ejecutando consulta para obtener trabajadores no asignados a la campaña...");

$stmt = $pdo->prepare($sql_trabajadores);
$stmt->execute(['campana_id' => $campana_id]);
$trabajadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

log_index("Número de trabajadores encontrados: " . count($trabajadores));

// Generar el formulario
$formularioHTML = '
<form id="formAgregarTrabajador">
    <input type="hidden" name="jefe_id" value="' . htmlspecialchars($jefe_id) . '">
    <input type="hidden" name="campana_id" value="' . htmlspecialchars($campana_id) . '">
    <input type="hidden" name="cargo_id" value="' . htmlspecialchars($cargo['id']) . '">

    <div class="mb-3">
        <label for="trabajador_id" class="form-label">Seleccionar Trabajador</label>
        <select name="trabajador_id" id="trabajador_id" class="form-select" required>
            <option value="">-- Seleccionar --</option>';

foreach ($trabajadores as $trabajador) {
    $formularioHTML .= '<option value="' . $trabajador['id'] . '">' . htmlspecialchars($trabajador['nombre_completo']) . '</option>';
}

$formularioHTML .= '
        </select>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Agregar</button>
    </div>
</form>';

log_index("Formulario HTML generado correctamente.");

header('Content-Type: application/json');
log_index("Enviando respuesta JSON al cliente.");

echo json_encode([
    'ok' => true,
    'formulario' => $formularioHTML
]);

?>