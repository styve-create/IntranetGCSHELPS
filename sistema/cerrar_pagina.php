<?php

include_once(__DIR__ . '/../app/controllers/config.php');

$log_cerrarpagina = __DIR__ . '/log_cerrarpagina.txt';

function log_cerrarpagina($mensaje) {
    global $log_cerrarpagina;
    file_put_contents($log_cerrarpagina, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

log_cerrarpagina("=== INICIO cerrarpagina.php ===");

$input = file_get_contents('php://input');

if (empty($input)) {
    log_cerrarpagina("Entrada vacía, abortando.");
    http_response_code(400);
    exit;
}

log_cerrarpagina("RAW JSON: " . $input);

$data = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    log_cerrarpagina("ERROR al decodificar JSON: " . json_last_error_msg());
    http_response_code(400);
    exit;
} else {
    log_cerrarpagina("JSON INPUT: " . print_r($data, true));
}

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
    log_cerrarpagina("Sesión iniciada");
} else {
    log_cerrarpagina("Sesión ya activa");
}

$id_usuario    = $_SESSION['usuario_info']['id'] ?? null;
$pagina        = $data['pagina'] ?? null;
$id_conexion   = $data['id_conexion'] ?? null;
$tiempo_inicio = $data['tiempo_inicio'] ?? microtime(true);


log_cerrarpagina("SESSION: " . print_r($_SESSION, true));
log_cerrarpagina("POST: " . print_r($_POST, true));

if ($id_usuario && $pagina && $id_conexion) {
    try {
        $stmt = $pdo->prepare("
            UPDATE paginas_abiertas 
            SET fecha_cierre = NOW(), estado = 'cerrada'
            WHERE id_conexion = :id_conexion AND pagina = :pagina AND estado = 'abierta'
        ");

        $stmt->execute([
          
            ':id_conexion' => $id_conexion,
            ':pagina'      => $pagina
        ]);

        log_cerrarpagina("Actualización exitosa para usuario $id_usuario en página $pagina");
    } catch (PDOException $e) {
        log_cerrarpagina("ERROR DB: " . $e->getMessage());
        http_response_code(500);
    }
} else {
    log_cerrarpagina("Datos incompletos. id_usuario: " . var_export($id_usuario, true) . ", pagina: " . var_export($pagina, true));
    http_response_code(422);
}

log_cerrarpagina("=== FIN cerrarpagina.php ===");

http_response_code(204); // No content

?>