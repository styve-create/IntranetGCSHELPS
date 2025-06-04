<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');

// Ruta del archivo de log
$log_index = __DIR__ . '/log_index.txt';

// Función para escribir en el log
function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

log_index("Inicio de eliminar_trabajador.php");

$trabajador_id = intval($_POST['trabajador_id'] ?? 0);
$campana_id = intval($_POST['campana_id'] ?? 0);
$jefe_id = intval($_POST['jefe_id'] ?? 0);

log_index("Datos recibidos: trabajador_id=$trabajador_id, campana_id=$campana_id, jefe_id=$jefe_id");

// Validar que los datos no estén vacíos
if ($trabajador_id <= 0 || $campana_id <= 0 || $jefe_id <= 0) {
    log_index("Datos incompletos para la eliminación.");
    echo json_encode(['ok' => false, 'mensaje' => 'Datos incompletos.']);
    exit;
}

try {
    $pdo->beginTransaction();
    log_index("Transacción iniciada.");

    // Validar que el trabajador exista en la campaña
    $sql_validar_trabajador = "
        SELECT COUNT(*) AS total
        FROM trabajadores t
        INNER JOIN trabajadores_campanas tc ON t.id = tc.trabajador_id
        WHERE t.id = :trabajador_id AND tc.campana_id = :campana_id
    ";
    $stmt = $pdo->prepare($sql_validar_trabajador);
    $stmt->execute(['trabajador_id' => $trabajador_id, 'campana_id' => $campana_id]);
    $trabajador_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    log_index("Validación existencia del trabajador: " . json_encode($trabajador_existente));

    if ($trabajador_existente['total'] == 0) {
        $pdo->rollBack();
        log_index("El trabajador no está asociado a esta campaña.");
        echo json_encode(['ok' => false, 'mensaje' => 'El trabajador no está asociado a esta campaña.']);
        exit;
    }

    // Validar que el trabajador esté vinculado al jefe adecuado
    $sql_validar_jefe = "
        SELECT COUNT(*) AS total
        FROM tb_jerarquia_trabajadores jt
        WHERE jt.id_trabajador = :trabajador_id AND jt.id_campana = :campana_id AND jt.id_jefe = :jefe_id
    ";
    $stmt = $pdo->prepare($sql_validar_jefe);
    $stmt->execute([
        'trabajador_id' => $trabajador_id,
        'campana_id' => $campana_id,
        'jefe_id' => $jefe_id
    ]);
    $jefe_vinculado = $stmt->fetch(PDO::FETCH_ASSOC);

    log_index("Validación jefe vinculado: " . json_encode($jefe_vinculado));

    if ($jefe_vinculado['total'] == 0) {
        $pdo->rollBack();
        log_index("El trabajador no está vinculado al jefe especificado.");
        echo json_encode(['ok' => false, 'mensaje' => 'El trabajador no está vinculado al jefe especificado en esta campaña.']);
        exit;
    }

    // Eliminar de la jerarquía de trabajadores
    $stmt1 = $pdo->prepare("
        DELETE FROM tb_jerarquia_trabajadores 
        WHERE id_trabajador = :trabajador_id AND id_campana = :campana_id
    ");
    $stmt1->execute(['trabajador_id' => $trabajador_id, 'campana_id' => $campana_id]);
    log_index("Eliminación de jerarquía ejecutada.");

    // Eliminar de trabajadores_campanas
    $stmt2 = $pdo->prepare("
        DELETE FROM trabajadores_campanas 
        WHERE trabajador_id = :trabajador_id AND campana_id = :campana_id
    ");
    $stmt2->execute(['trabajador_id' => $trabajador_id, 'campana_id' => $campana_id]);
    log_index("Eliminación de trabajador en campaña ejecutada.");

    $pdo->commit();
    log_index("Transacción completada con éxito.");

    echo json_encode(['ok' => true, 'mensaje' => 'Trabajador eliminado correctamente.']);
} catch (Exception $e) {
    $pdo->rollBack();
    log_index("Error en la transacción: " . $e->getMessage());
    echo json_encode([
        'ok' => false,
        'mensaje' => 'Error al eliminar trabajador: ' . $e->getMessage()
    ]);
}
?>