<?php
// Ruta del archivo de log
$log_index = __DIR__ . '/log_index.txt';

// Función para escribir en el log
function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

log_index("Inicio del script eliminar_trabajador.php");

include_once(__DIR__ . '/../../../app/controllers/config.php');
log_index("Incluido config.php");

$trabajador_id = intval($_POST['trabajador_id'] ?? 0);
$campana_id = intval($_POST['campana_id'] ?? 0);
$jefe_id = intval($_POST['jefe_id'] ?? 0);

log_index("Datos recibidos - trabajador_id: $trabajador_id, campana_id: $campana_id, jefe_id: $jefe_id");

// Validar que los datos no estén vacíos
if ($trabajador_id <= 0 || $campana_id <= 0 || $jefe_id <= 0) {
    log_index("Datos incompletos");
    echo json_encode(['ok' => false, 'mensaje' => 'Datos incompletos.']);
    exit;
}

// Validar que el trabajador exista en la campaña
$sql_validar_trabajador = "
    SELECT COUNT(*) AS total
    FROM trabajadores t
    INNER JOIN trabajadores_campanas tc ON t.id = tc.trabajador_id
    WHERE t.id = :trabajador_id AND tc.campana_id = :campana_id
";
log_index("Validando si el trabajador pertenece a la campaña");
$stmt = $pdo->prepare($sql_validar_trabajador);
$stmt->execute(['trabajador_id' => $trabajador_id, 'campana_id' => $campana_id]);
$trabajador_existente = $stmt->fetch(PDO::FETCH_ASSOC);

if ($trabajador_existente['total'] == 0) {
    log_index("El trabajador no está asociado a esta campaña.");
    echo json_encode(['ok' => false, 'mensaje' => 'El trabajador no está asociado a esta campaña.']);
    exit;
}

log_index("El trabajador está asociado a la campaña");

// Validar que el trabajador está vinculado al jefe adecuado en esta campaña
$sql_validar_jefe = "
    SELECT COUNT(*) AS total
    FROM tb_jerarquia_trabajadores jt
    INNER JOIN trabajadores_campanas tc ON jt.id_trabajador = tc.trabajador_id
    WHERE jt.id_trabajador = :trabajador_id AND jt.id_campana = :campana_id AND jt.id_jefe = :jefe_id
";
log_index("Validando si el trabajador está vinculado al jefe");
$stmt = $pdo->prepare($sql_validar_jefe);
$stmt->execute(['trabajador_id' => $trabajador_id, 'campana_id' => $campana_id, 'jefe_id' => $jefe_id]);
$jefe_vinculado = $stmt->fetch(PDO::FETCH_ASSOC);

if ($jefe_vinculado['total'] == 0) {
    log_index("El trabajador no está vinculado al jefe especificado en esta campaña.");
    echo json_encode(['ok' => false, 'mensaje' => 'El trabajador no está vinculado al jefe especificado en esta campaña.']);
    exit;
}

log_index("El trabajador está correctamente vinculado al jefe");

// Armar formulario
$formularioHTML = '
<form id="formEliminarTrabajador" method="POST">
    <input type="hidden" name="trabajador_id" value="' . $trabajador_id . '">
    <input type="hidden" name="campana_id" value="' . $campana_id . '">
    <input type="hidden" name="jefe_id" value="' . $jefe_id . '">

    <div class="mb-4">
        <h5 class="fw-bold text-danger">Eliminar Trabajador</h5>
        <p class="text-muted">
            ¿Estás seguro de que deseas eliminar este trabajador de la campaña? 
            <span class="text-danger fw-semibold">Esto también eliminará su jerarquía si está asignado a un jefe.</span>
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


log_index("Formulario generado correctamente");

echo json_encode([
    'ok' => true,
    'formulario' => $formularioHTML
]);
log_index("Fin del script con éxito");
?>