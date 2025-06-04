
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$log_pingUsuario = __DIR__ . "/pingUsuario.txt";

function log_pingUsuario($mensaje) {
    global $log_pingUsuario;
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, $log_pingUsuario);
}

log_pingUsuario("========== INICIO ping_usuario ==========");


include_once(__DIR__ . '/../app/controllers/config.php');

$raw_input = file_get_contents('php://input');
$input = json_decode($raw_input, true);
// Registrar el cuerpo completo recibido
log_pingUsuario("Datos recibidos (raw): $raw_input");
// Registrar el array ya decodificado
log_pingUsuario("Datos decodificados: " . json_encode($input));

$id_conexion = $input['id_conexion'] ?? ''; 
$stmt = $pdo->prepare("SELECT * FROM usuarios_conectados WHERE id_conexion = ?");
$stmt->execute([$id_conexion]);
$conexion = $stmt->fetch();

if (!$conexion) {
    log_pingUsuario("ID conexión NO encontrado en BD: $id_conexion — destruyendo sesión.");

    // Iniciar sesión si no está iniciada (por si acaso)
    if (session_status() === PHP_SESSION_NONE) {
        session_name('mi_sesion_personalizada');
        session_start();
    }

    // Destruir sesión
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    
    // Redirigir al login
   
    exit;
}
$lat_actual = $input['latitud'] ?? ''; 
$lon_actual = $input['longitud'] ?? ''; 
$hora_ping = $input['hora_ping'] ?? null;


log_pingUsuario("ID conexión: $id_conexion | Lat: $lat_actual | Lon: $lon_actual | Hora ping: $hora_ping");

date_default_timezone_set('America/Bogota');

// Obtener ubicación actual
try {
    $sql_select = "SELECT latitud, longitud FROM usuarios_conectados WHERE id_conexion = :id_conexion";
    $stmt = $pdo->prepare($sql_select);
    $stmt->execute([':id_conexion' => $id_conexion]);
    $ubicacion = $stmt->fetch(PDO::FETCH_ASSOC);
    log_pingUsuario("Ubicación BD: " . json_encode($ubicacion));
} catch (Exception $e) {
    log_pingUsuario("Error al consultar ubicación: " . $e->getMessage());
}

$distancia_metros = 0;
if ($lat_actual && $lon_actual && $ubicacion) {
    $lat_bd = floatval($ubicacion['latitud']);
    $lon_bd = floatval($ubicacion['longitud']);

    function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
        $radio_tierra = 6371000; // metros
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $radio_tierra * $c;
    }

    $distancia_metros = calcularDistancia($lat_bd, $lon_bd, $lat_actual, $lon_actual);
    log_pingUsuario("Distancia calculada: $distancia_metros metros");

    $nuevo_rango = $distancia_metros > 500 ? 'Fuera' : 'Dentro';
    log_pingUsuario("Nuevo rango: $nuevo_rango");

    try {
        $sql_rango = "UPDATE usuarios_conectados SET rango = :rango WHERE id_conexion = :id_conexion";
        $stmt_rango = $pdo->prepare($sql_rango);
        $stmt_rango->execute([':rango' => $nuevo_rango, ':id_conexion' => $id_conexion]);
        log_pingUsuario("Rango actualizado correctamente");
    } catch (Exception $e) {
        log_pingUsuario("Error al actualizar rango: " . $e->getMessage());
    }
} else {
    log_pingUsuario("No se calculó distancia (lat/lon no válidos o ubicación no encontrada)");
}

try {
    if ($hora_ping) {
        $fecha = new DateTime($hora_ping, new DateTimeZone('UTC'));
        $fecha->setTimezone(new DateTimeZone('America/Bogota'));
        $fecha_formato_mysql = $fecha->format('Y-m-d H:i:s');
        log_pingUsuario("Fecha formateada: $fecha_formato_mysql");

        $sql = "UPDATE usuarios_conectados SET ultima_actividad = :hora_ping WHERE id_conexion = :id_conexion";
        $stmt = $pdo->prepare($sql);
        $res = $stmt->execute([':hora_ping' => $fecha_formato_mysql, ':id_conexion' => $id_conexion]);
    } else {
        $sql = "UPDATE usuarios_conectados SET ultima_actividad = NOW() WHERE id_conexion = :id_conexion";
        $stmt = $pdo->prepare($sql);
        $res = $stmt->execute([':id_conexion' => $id_conexion]);
    }

    if ($res) {
        log_pingUsuario("Ultima actividad actualizada correctamente");
        echo json_encode([
            'success' => true,
            'distancia' => round($distancia_metros, 2),
            'mensaje' => $distancia_metros > 0 ? "Ubicacion evaluada" : "Sin cambio de ubicacion"
        ]);
    } else {
        log_pingUsuario("Error al actualizar ultima actividad");
        echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
    }
} catch (Exception $e) {
    log_pingUsuario("Excepción al actualizar actividad: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Excepción: ' . $e->getMessage()]);
}

log_pingUsuario("========== FIN ping_usuario ==========");
?>