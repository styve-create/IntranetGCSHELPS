<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php');

// Definir el archivo de log
$log_index = __DIR__ . '/log_index.txt';

function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

header('Content-Type: application/json');

// Validar campos requeridos
$campos_requeridos = ['trabajador_id', 'cargo_id', 'campana_id'];

foreach ($campos_requeridos as $campo) {
    if (empty($_POST[$campo])) {
        log_index("ERROR: El campo '$campo' es obligatorio.");
        echo json_encode([
            'ok' => false,
            'mensaje' => "El campo '$campo' es obligatorio."
        ]);
        exit;
    }
}

$trabajador_id = intval($_POST['trabajador_id']);
$cargo_id = intval($_POST['cargo_id']);
$campana_id = intval($_POST['campana_id']);

try {
    // Iniciar transacción
    $pdo->beginTransaction();

    // Verificar si el trabajador ya está asignado a la campaña especificada
    log_index("Verificando si el trabajador está en la campaña especificada...");
    $sql_check = "SELECT COUNT(*) FROM trabajadores_campanas WHERE trabajador_id = ? AND campana_id = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$trabajador_id, $campana_id]);

    if ($stmt_check->fetchColumn() == 0) {
        // Si no está asignado, insertar en la campaña
        log_index("El trabajador no está asignado, procediendo a insertarlo.");
        $sql_insert = "INSERT INTO trabajadores_campanas (trabajador_id, campana_id, cargo_id) VALUES (?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert);
        if (!$stmt_insert->execute([$trabajador_id, $campana_id, $cargo_id])) {
            throw new Exception("Error al asignar el trabajador a la campaña.");
        }
    } else {
        // Si ya está asignado, solo actualizar el cargo
        log_index("El trabajador ya está asignado a la campaña, actualizando el cargo.");
        $sql_update = "UPDATE trabajadores_campanas SET cargo_id = ? WHERE trabajador_id = ? AND campana_id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        if (!$stmt_update->execute([$cargo_id, $trabajador_id, $campana_id])) {
            throw new Exception("Error al actualizar el cargo del trabajador.");
        }
    }

    // Commit de la transacción
    $pdo->commit();
    log_index("Transacción completada exitosamente.");

    echo json_encode([
        'ok' => true,
        'mensaje' => 'Jefe agregado correctamente.'
    ]);
} catch (Exception $e) {
    // Rollback en caso de error
    $pdo->rollBack();
    log_index("ERROR: " . $e->getMessage());

    echo json_encode([
        'ok' => false,
        'mensaje' => 'Error: ' . $e->getMessage()
    ]);
}
?>