<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php');
$log_index = __DIR__ . '/log_index.txt';

function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

// === INICIO ===
log_index("==== INICIO DE PROCESO DE ACTUALIZACIÓN DE JEFE ====");
log_index("Contenido completo de \$_POST: " . json_encode($_POST));

header('Content-Type: application/json');
log_index("Cabecera Content-Type: application/json establecida.");

// === VALIDACIÓN DE CAMPOS REQUERIDOS ===
$campos_requeridos = ['id', 'nombre', 'campana_id', 'cargo_id'];
log_index("Validación de campos requeridos: " . implode(", ", $campos_requeridos));

foreach ($campos_requeridos as $campo) {
    if (empty($_POST[$campo])) {
        log_index("Campo '$campo' está vacío.");
        echo json_encode([
            'ok' => false,
            'mensaje' => "El campo '$campo' es obligatorio."
        ]);
        exit;
    }
}

// === ASIGNACIÓN DE VARIABLES ===
$id = intval($_POST['id']);
$nombre = trim($_POST['nombre']);
$campana_id = intval($_POST['campana_id']);
$cargo_id = intval($_POST['cargo_id']);
$nuevo_jefe_id = !empty($_POST['nuevo_jefe_id']) ? intval($_POST['nuevo_jefe_id']) : null;

log_index("Datos recibidos - ID: $id, Nombre: $nombre, Campaña ID: $campana_id, Cargo ID: $cargo_id, Nuevo Jefe ID: $nuevo_jefe_id");

// === INICIO TRANSACCIÓN ===
try {
    log_index("Iniciando transacción...");
    $pdo->beginTransaction();

    // === VERIFICACIÓN DE LA CAMPAÑA ===
    $sql_check_campanas_trabajador = "SELECT campana_id FROM trabajadores_campanas WHERE trabajador_id = :trabajador_id";
    $stmt_check = $pdo->prepare($sql_check_campanas_trabajador);
    $stmt_check->execute(['trabajador_id' => $id]);
    $campanas_asociadas = $stmt_check->fetchAll(PDO::FETCH_ASSOC);

    $es_la_campana_correcta = false;
    foreach ($campanas_asociadas as $campana) {
        if ($campana['campana_id'] == $campana_id) {
            $es_la_campana_correcta = true;
            break;
        }
    }

    if (!$es_la_campana_correcta) {
        log_index("ERROR: El trabajador no está asignado a la campaña con ID: {$campana_id}.");
        echo json_encode(['ok' => false, 'mensaje' => 'El trabajador no está asignado a la campaña indicada']);
        exit;
    }

    log_index("✅ El trabajador está asignado a la campaña correcta.");

    // === ACTUALIZAR NOMBRE ===
    $sqlTrabajador = "UPDATE trabajadores SET nombre_completo = ? WHERE id = ?";
    $stmt1 = $pdo->prepare($sqlTrabajador);
    if (!$stmt1->execute([$nombre, $id])) {
        throw new Exception('Error al actualizar el nombre del trabajador.');
    }

    // === ACTUALIZAR CARGO ===
    $sqlUpdateCampana = "UPDATE trabajadores_campanas SET cargo_id = ? WHERE trabajador_id = ? AND campana_id = ?";
    $stmtUpdate = $pdo->prepare($sqlUpdateCampana);
    if (!$stmtUpdate->execute([$cargo_id, $id, $campana_id])) {
        throw new Exception('Error al actualizar trabajadores_campanas');
    }

   // === ACTUALIZAR JERARQUÍA SI SE PROPORCIONÓ NUEVO JEFE ===
if ($nuevo_jefe_id) {
    if ($nuevo_jefe_id == $id) {
        throw new Exception("Un trabajador no puede ser su propio jefe.");
    }

    // Validar que el nuevo jefe pertenezca a la misma campaña
    $stmtJefeCampana = $pdo->prepare("SELECT COUNT(*) FROM trabajadores_campanas WHERE trabajador_id = ? AND campana_id = ?");
    $stmtJefeCampana->execute([$nuevo_jefe_id, $campana_id]);
    if (!$stmtJefeCampana->fetchColumn()) {
        throw new Exception("El nuevo jefe no está asignado a la misma campaña.");
    }

    // Buscar subordinados del jefe actual
    $sql_subordinados = "SELECT id_trabajador FROM tb_jerarquia_trabajadores WHERE id_jefe = ? AND id_campana = ?";
    $stmt_sub = $pdo->prepare($sql_subordinados);
    $stmt_sub->execute([$id, $campana_id]);
    $subordinados = $stmt_sub->fetchAll(PDO::FETCH_COLUMN);

    if (count($subordinados) > 0) {
        log_index("Se encontraron subordinados del jefe $id: " . implode(", ", $subordinados));

        foreach ($subordinados as $sub_id) {
            if ($nuevo_jefe_id == $sub_id) {
                throw new Exception("Un subordinado no puede ser su propio jefe.");
            }

            $sql_update_sub = "UPDATE tb_jerarquia_trabajadores SET id_jefe = ? WHERE id_trabajador = ? AND id_campana = ?";
            $stmt_update_sub = $pdo->prepare($sql_update_sub);
            if (!$stmt_update_sub->execute([$nuevo_jefe_id, $sub_id, $campana_id])) {
                throw new Exception("Error al actualizar jerarquía del trabajador $sub_id");
            }

            log_index("Jerarquía actualizada para el subordinado $sub_id");
        }
    } else {
        log_index("No se encontraron subordinados. Procesando como trabajador individual.");

        // Actualizar jerarquía del trabajador directamente
        $sqlCheck = "SELECT COUNT(*) FROM tb_jerarquia_trabajadores WHERE id_trabajador = ? AND id_campana = ?";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([$id, $campana_id]);
        $existe = $stmtCheck->fetchColumn();

        if ($existe) {
            $sqlJerarquia = "UPDATE tb_jerarquia_trabajadores SET id_jefe = ? WHERE id_trabajador = ? AND id_campana = ?";
            $stmtJerarquia = $pdo->prepare($sqlJerarquia);
            if (!$stmtJerarquia->execute([$nuevo_jefe_id, $id, $campana_id])) {
                throw new Exception('Error al actualizar la jerarquía.');
            }
            log_index("Jerarquía actualizada para el trabajador $id.");
        } else {
            log_index("No se encontró jerarquía previa para el trabajador $id.");
        }
    }

    // Cambiar cargo de jefe (2) a trabajador (3)
    $sqlCheckCargo = "SELECT cargo_id FROM trabajadores_campanas WHERE trabajador_id = ? AND campana_id = ?";
    $stmtCargo = $pdo->prepare($sqlCheckCargo);
    $stmtCargo->execute([$id, $campana_id]);
    $cargo_actual = $stmtCargo->fetchColumn();

    if ($cargo_actual == 2) {
        $sqlUpdateCargo = "UPDATE trabajadores_campanas SET cargo_id = 3 WHERE trabajador_id = ? AND campana_id = ?";
        $stmtUpdateCargo = $pdo->prepare($sqlUpdateCargo);
        if (!$stmtUpdateCargo->execute([$id, $campana_id])) {
            throw new Exception('Error al actualizar el cargo del antiguo jefe.');
        }
        log_index("⚠️ Cargo del trabajador $id cambiado de jefe (2) a trabajador (3).");
    }
    
    // Verificar si el nuevo jefe tiene el cargo de jefe (por ejemplo, cargo_id = 2)
$sqlCheckCargoJefe = "SELECT cargo_id FROM trabajadores_campanas WHERE trabajador_id = ? AND campana_id = ?";
$stmtCheckCargoJefe = $pdo->prepare($sqlCheckCargoJefe);
$stmtCheckCargoJefe->execute([$nuevo_jefe_id, $campana_id]);
$cargo_jefe = $stmtCheckCargoJefe->fetchColumn();

if ($cargo_jefe != 2) {
    // Si no es jefe, actualizar su cargo a jefe (cargo_id = 2)
    $sqlUpdateCargoJefe = "UPDATE trabajadores_campanas SET cargo_id = 2 WHERE trabajador_id = ? AND campana_id = ?";
    $stmtUpdateCargoJefe = $pdo->prepare($sqlUpdateCargoJefe);
    if (!$stmtUpdateCargoJefe->execute([$nuevo_jefe_id, $campana_id])) {
        throw new Exception('Error al asignar cargo de jefe al nuevo jefe.');
    }
    log_index("⚠️ Cargo del nuevo jefe actualizado a jefe (cargo_id = 2).");
}

} else {
    log_index("No se seleccionó nuevo jefe.");
}
    // === COMMIT ===
    $pdo->commit();
    log_index("✅ Transacción completada con éxito.");
    echo json_encode([
        'ok' => true,
        'mensaje' => 'Los cambios se guardaron correctamente.'
    ]);
    log_index("==== FIN EXITOSO ====");

} catch (Exception $e) {
    $pdo->rollBack();
    log_index("❌ ERROR: " . $e->getMessage());
    log_index("==== TRANSACCIÓN CANCELADA ====");
    echo json_encode([
        'ok' => false,
        'mensaje' => 'Error al guardar los datos: ' . $e->getMessage()
    ]);
}
?>