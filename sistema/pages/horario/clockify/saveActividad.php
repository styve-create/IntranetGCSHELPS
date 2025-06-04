<?php 
header('Content-Type: application/json');
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ruta del log
$log_file = __DIR__ . '/log_fecha.txt';
function log_debug($mensaje) {
    global $log_file;
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, $log_file);
}



// Incluir conexión a la base de datos
include_once(__DIR__ . '/../../../../app/controllers/config.php');


$data = json_decode(file_get_contents("php://input"), true);

// Validación de datos
if (
    empty($data['cliente']) || empty($data['actividad']) ||
    empty($data['descripcion']) || empty($data['duracion']) || empty($data['fecha']) || empty($data['id_usuario'])
) {
    log_debug("Faltan datos: " . print_r($data, true));
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    exit;
}

log_debug("Fecha recibida: " . print_r($data['fecha'], true));

// Convertir la fecha a DateTime
try {
    $datetime = new DateTime($data['fecha'], new DateTimeZone('America/Bogota'));
} catch (Exception $e) {
    $error_msg = "Error al convertir fecha: " . $e->getMessage();
    log_debug($error_msg);
    echo json_encode(['success' => false, 'message' => 'Fecha inválida', 'error' => $e->getMessage()]);
    exit;
}

$fecha = $datetime->format('Y-m-d H:i:s');

// Calcular horas
$hora_fin = $datetime->format('H:i:s');
$datetime_inicio = clone $datetime;
$datetime_inicio->modify("-{$data['duracion']} seconds");
$hora_inicio = $datetime_inicio->format('H:i:s');

log_debug("Hora inicio calculada: $hora_inicio, Hora fin: $hora_fin");

// Insertar en la base de datos
try {
    $stmt = $pdo->prepare("INSERT INTO tb_actividades (cliente, actividad, descripcion, duracion, fecha, hora_inicio, hora_fin, id_usuario)  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $ok = $stmt->execute([
        $data['cliente'],
        $data['actividad'],
        $data['descripcion'],
        $data['duracion'],
        $fecha,
        $hora_inicio,
        $hora_fin,
        $data['id_usuario']
    ]);
} catch (PDOException $e) {
    $error_msg = "Error al ejecutar el INSERT: " . $e->getMessage();
    log_debug($error_msg);
    echo json_encode(['success' => false, 'message' => 'Error en base de datos', 'error' => $e->getMessage()]);
    exit;
}

if ($ok) {
    log_debug("Insert exitoso: " . print_r($data, true));
    echo json_encode([
        'success' => true,
        'actividad' => [
            'cliente' => $data['cliente'],
            'actividad' => $data['actividad'],
            'descripcion' => $data['descripcion'],
            'duracion' => $data['duracion'],
            'hora_inicio' => $hora_inicio,
            'hora_fin' => $hora_fin,
            'fecha' => $fecha,
            'id_usuario' => $data['id_usuario']
        ]
    ]);
} else {
    log_debug("Fallo al insertar datos. ErrorInfo: " . print_r($stmt->errorInfo(), true));
    echo json_encode(['success' => false, 'message' => 'Error al guardar en base de datos']);
}
?>