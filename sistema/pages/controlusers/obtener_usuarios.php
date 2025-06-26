<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_info']['id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

include_once(__DIR__ . '/../../../app/controllers/config.php');

if (!isset($pdo)) {
    die(json_encode(["error" => "No se pudo conectar a la base de datos. \$pdo no está definido."]));
}

try {
    // Consulta: obtener todos los usuarios conectados
   $stmt = $pdo->prepare("
    SELECT nombre, sistema, ip, fecha_ingreso, ultima_actividad, ubicacion, rango, latitud, longitud
    FROM usuarios_conectados
    WHERE estado = 'conectado'
    ORDER BY ultima_actividad DESC
");
    $stmt->execute();
   $usuariosBrutos = $stmt->fetchAll(PDO::FETCH_ASSOC);
   $usuarios = [];

foreach ($usuariosBrutos as $usuario) {
    $lat = $usuario['latitud'];
    $lon = $usuario['longitud'];
    $ubicacion = $usuario['ubicacion']; // ← importante: usar el valor actual

    // Si ya existe una ubicación, usarla sin llamar a la API
    if (!$ubicacion && $lat && $lon) {
        $apiKey = 'cb48b12ae6cb4d67af2073dbd42e6496';
        $url = "https://api.geoapify.com/v1/geocode/reverse?lat=$lat&lon=$lon&apiKey=$apiKey";

        $response = @file_get_contents($url);
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['features'][0]['properties']['formatted'])) {
                $ubicacion = $data['features'][0]['properties']['formatted'];

                // Guardar la dirección en la base de datos solo si se obtuvo correctamente
                $updateStmt = $pdo->prepare("
                    UPDATE usuarios_conectados
                    SET ubicacion = :ubicacion
                    WHERE ip = :ip AND fecha_ingreso = :fecha_ingreso
                ");
                $updateStmt->execute([
                    ':ubicacion' => $ubicacion,
                    ':ip' => $usuario['ip'],
                    ':fecha_ingreso' => $usuario['fecha_ingreso']
                ]);
            }
        }
    }

    $usuarios[] = [
        'nombre'           => $usuario['nombre'],
        'sistema'          => $usuario['sistema'],
        'ip'               => $usuario['ip'],
        'fecha_ingreso'    => $usuario['fecha_ingreso'],
        'ultima_actividad' => $usuario['ultima_actividad'],
        'ubicacion'        => $ubicacion ?: 'No disponible',
        'rango'            => $usuario['rango']
    ];
}
    // Verificar si se obtuvieron usuarios
    if ($usuarios) {
        echo json_encode($usuarios);
    } else {
        echo json_encode([]); // Si no hay usuarios, devolver un array vacío
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
}
?>