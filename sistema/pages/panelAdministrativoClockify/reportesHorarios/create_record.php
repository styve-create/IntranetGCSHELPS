<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include_once(__DIR__ . '/../../../../app/controllers/config.php');

$in = json_decode(file_get_contents('php://input'), true);

// Validación básica
if (empty($in['fecha']) || empty($in['start']) || empty($in['end'])) {
    echo json_encode(['success' => false, 'msg' => 'Faltan datos obligatorios (fecha, inicio o fin).']);
    exit;
}

$nombreUsuario = trim($in['usuario'] ?? '');
if (empty($nombreUsuario)) {
    echo json_encode(['success' => false, 'msg' => 'No se proporcionó el nombre del usuario.']);
    exit;
}

// Obtener ID de usuario
$stmtUser = $pdo->prepare("SELECT id_usuarios FROM tb_usuarios WHERE nombres = :nombre LIMIT 1");
$stmtUser->execute([':nombre' => $nombreUsuario]);
$usuarioDB = $stmtUser->fetch(PDO::FETCH_ASSOC);
if (!$usuarioDB) {
    echo json_encode(['success' => false, 'msg' => "No se encontró el usuario: $nombreUsuario"]);
    exit;
}
$idUsuario = $usuarioDB['id_usuarios'];

// Helpers
function getBreak($breaks, $index, $key) {
    return isset($breaks[$index][$key]) && $breaks[$index][$key] !== ''
        ? $breaks[$index][$key]
        : '00:00:00';
}
function combinarFechaHora($fecha, $hora) {
    if (empty($fecha) || empty($hora)) return null;
    return "$fecha $hora:00";
}

// INSERT
$sql = "INSERT INTO tb_horario (
    id_usuario,
    fecha,
    hora_inicio_turno,
    hora_fin_turno,
    hora_inicio_extra,
    hora_fin_extra,
    hora_inicio_break1,
    hora_fin_break1,
    hora_inicio_break2,
    hora_fin_break2,
    hora_inicio_break3,
    hora_fin_break3
) VALUES (
    :id_usuario,
    :fecha,
    :start,
    :end,
    :inicioextra,
    :finextra,
    :b1i,
    :b1f,
    :b2i,
    :b2f,
    :b3i,
    :b3f
)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id_usuario'  => $idUsuario,
    ':fecha'       => $in['fecha'],
    ':start'       => combinarFechaHora($in['fecha'], $in['start']),
    ':end'         => combinarFechaHora($in['fecha'], $in['end']),
    ':inicioextra' => combinarFechaHora($in['fecha'], $in['inicioextra'] ?? ''),
    ':finextra'    => combinarFechaHora($in['fecha'], $in['finextra'] ?? ''),
    ':b1i'         => combinarFechaHora($in['fecha'], getBreak($in['breaks'], 0, 'start')),
    ':b1f'         => combinarFechaHora($in['fecha'], getBreak($in['breaks'], 0, 'end')),
    ':b2i'         => combinarFechaHora($in['fecha'], getBreak($in['breaks'], 1, 'start')),
    ':b2f'         => combinarFechaHora($in['fecha'], getBreak($in['breaks'], 1, 'end')),
    ':b3i'         => combinarFechaHora($in['fecha'], getBreak($in['breaks'], 2, 'start')),
    ':b3f'         => combinarFechaHora($in['fecha'], getBreak($in['breaks'], 2, 'end')),
]);

$newId = $pdo->lastInsertId();
echo json_encode(['success' => true, 'id' => $newId]);
exit;
?>
