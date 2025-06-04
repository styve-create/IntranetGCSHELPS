<?php

$log_index = __DIR__ . '/log_index.txt';

function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

log_index("== INICIO guardar_trabajador.php ==");

header('Content-Type: application/json');

// Incluir config
log_index("Incluyendo config.php...");
include_once(__DIR__ . '/../../../app/controllers/config.php');
log_index("Config incluido correctamente.");

// Verificar datos obligatorios
log_index("Verificando datos POST...");
if (!isset($_POST['trabajador_id'], $_POST['jefe_id'], $_POST['campana_id'], $_POST['cargo_id'])) {
    log_index("FALTAN datos requeridos en POST");
    echo json_encode(['ok' => false, 'mensaje' => 'Faltan datos requeridos']);
    exit;
}
log_index("Datos POST presentes.");

// Convertir a enteros
$trabajador_id = intval($_POST['trabajador_id']);
$jefe_id = intval($_POST['jefe_id']);
$campana_id = intval($_POST['campana_id']);
$cargo_id = intval($_POST['cargo_id']);
log_index("POST procesado: trabajador_id=$trabajador_id, jefe_id=$jefe_id, campana_id=$campana_id, cargo_id=$cargo_id");

// Verificar existencia en trabajadores_campanas
log_index("Verificando existencia en trabajadores_campanas...");
$sql_check_tc = "SELECT 1 FROM trabajadores_campanas WHERE trabajador_id = :trabajador_id AND campana_id = :campana_id";
$stmt = $pdo->prepare($sql_check_tc);
$stmt->execute(['trabajador_id' => $trabajador_id, 'campana_id' => $campana_id]);
$existe_tc = $stmt->fetch();
log_index($existe_tc ? "Ya existe en trabajadores_campanas." : "No existe en trabajadores_campanas, se insertará.");

// Insertar si no existe
if (!$existe_tc) {
    try {
        $sql_insert_tc = "
            INSERT INTO trabajadores_campanas (trabajador_id, campana_id, cargo_id)
            VALUES (:trabajador_id, :campana_id, :cargo_id)";
        $stmt = $pdo->prepare($sql_insert_tc);
        $stmt->execute([
            'trabajador_id' => $trabajador_id,
            'campana_id' => $campana_id,
            'cargo_id' => $cargo_id,
        ]);
        log_index("Insertado correctamente en trabajadores_campanas.");
    } catch (Exception $e) {
        log_index("ERROR al insertar en trabajadores_campanas: " . $e->getMessage());
        echo json_encode(['ok' => false, 'mensaje' => 'Error al insertar trabajador en campaña']);
        exit;
    }
}

// Verificar existencia en tb_jerarquia_trabajadores
log_index("Verificando existencia en tb_jerarquia_trabajadores...");
$sql_check_jerarquia = "SELECT 1 FROM tb_jerarquia_trabajadores WHERE id_trabajador = :trabajador_id AND id_jefe = :jefe_id AND id_campana = :campana_id";
$stmt = $pdo->prepare($sql_check_jerarquia);
$stmt->execute([
    'trabajador_id' => $trabajador_id,
    'jefe_id' => $jefe_id,
    'campana_id' => $campana_id
]);
$existe_jerarquia = $stmt->fetch();
log_index($existe_jerarquia ? "Ya existe en jerarquía." : "No existe en jerarquía, se insertará.");

// Insertar en jerarquía si no existe
if (!$existe_jerarquia) {
    try {
        $sql_insert_jerarquia = "
            INSERT INTO tb_jerarquia_trabajadores (id_trabajador, id_jefe, id_campana, fecha_registro)
            VALUES (:trabajador_id, :jefe_id, :campana_id, NOW())";
        $stmt = $pdo->prepare($sql_insert_jerarquia);
        $stmt->execute([
            'trabajador_id' => $trabajador_id,
            'jefe_id' => $jefe_id,
            'campana_id' => $campana_id
        ]);
        log_index("Insertado correctamente en tb_jerarquia_trabajadores.");
    } catch (Exception $e) {
        log_index("ERROR al insertar en jerarquía: " . $e->getMessage());
        echo json_encode(['ok' => false, 'mensaje' => 'Error al insertar en jerarquía']);
        exit;
    }
}

// Verificar que el trabajador está en la campaña correcta y no modificar otras campañas
log_index("== INICIO Verificación de campaña correcta para evitar modificaciones no deseadas ==");

// Consultar las campañas donde el trabajador está asignado
$sql_check_campanas_trabajador = "SELECT campana_id FROM trabajadores_campanas WHERE trabajador_id = :trabajador_id";
$stmt = $pdo->prepare($sql_check_campanas_trabajador);
$stmt->execute(['trabajador_id' => $trabajador_id]);
$campanas_asociadas = $stmt->fetchAll();

// Verificar si la campaña recibida es una de las campañas a modificar
$es_la_campana_correcta = false;
foreach ($campanas_asociadas as $campana) {
    if ($campana['campana_id'] == $campana_id) {
        $es_la_campana_correcta = true;
        break;
    }
}

// Si la campaña recibida no es una de las campañas asociadas, generar un error
if (!$es_la_campana_correcta) {
    log_index("ERROR: El trabajador no está asignado a la campaña con ID: {$campana_id}. No se puede modificar esta campaña.");
    echo json_encode(['ok' => false, 'mensaje' => 'El trabajador no está asignado a la campaña indicada']);
    exit;
}

log_index("== Fin de la verificación de campaña correcta para evitar modificaciones no deseadas ==");




log_index("== FIN guardar_trabajador.php ==");
echo json_encode(['ok' => true, 'mensaje' => 'Trabajador agregado correctamente']);

?>