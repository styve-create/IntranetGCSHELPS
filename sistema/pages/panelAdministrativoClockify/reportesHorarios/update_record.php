<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include_once(__DIR__ . '/../../../../app/controllers/config.php');



$in = json_decode(file_get_contents('php://input'), true);

$log_index1 = __DIR__ . "/log_index1.txt";
function log_index1($mensaje) {
    global $log_index1;
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, $log_index1);
}

log_index1(">>> Inicio del script update_record.php");

// Validación básica de datos obligatorios
if (empty($in['fecha']) || empty($in['start']) || empty($in['end']) || empty($in['id'])) {
    echo json_encode(['success' => false, 'msg' => 'Faltan datos obligatorios (fecha, inicio, fin o id).']);
    exit;
}

// Función auxiliar para obtener break seguro
function getBreak($breaks, $index, $key) {
    return isset($breaks[$index][$key]) && $breaks[$index][$key] !== ''
        ? $breaks[$index][$key]
        : '00:00:00';
}

function combinarFechaHora($fecha, $hora) {
    if (empty($fecha) || empty($hora)) return null;
    return "$fecha $hora:00";
}

// Intentar ejecutar el update
try {
    $sql = "UPDATE tb_horario SET
        fecha=:fecha,
        hora_inicio_turno=:start,
        hora_fin_turno=:end,
         hora_inicio_extra=:inicioextra,
        hora_fin_extra=:finextra,
        hora_inicio_break1=:b1i,
        hora_fin_break1=:b1f,
        hora_inicio_break2=:b2i,
        hora_fin_break2=:b2f,
        hora_inicio_break3=:b3i,
        hora_fin_break3=:b3f
        WHERE id=:id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
    ':fecha' => $in['fecha'],
    ':start' => combinarFechaHora($in['fecha'], $in['start']),
    ':end'   => combinarFechaHora($in['fecha'], $in['end']),
    ':inicioextra' => combinarFechaHora($in['fecha'], $in['inicioextra']),
    ':finextra'   => combinarFechaHora($in['fecha'], $in['finextra']),
    ':b1i'   => combinarFechaHora($in['fecha'], getBreak($in['breaks'], 0, 'start')),
    ':b1f'   => combinarFechaHora($in['fecha'], getBreak($in['breaks'], 0, 'end')),
    ':b2i'   => combinarFechaHora($in['fecha'], getBreak($in['breaks'], 1, 'start')),
    ':b2f'   => combinarFechaHora($in['fecha'], getBreak($in['breaks'], 1, 'end')),
    ':b3i'   => combinarFechaHora($in['fecha'], getBreak($in['breaks'], 2, 'start')),
    ':b3f'   => combinarFechaHora($in['fecha'], getBreak($in['breaks'], 2, 'end')),
    ':id'    => $in['id']
]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'msg' => 'Error al actualizar: ' . $e->getMessage()]);
}
exit;
?>