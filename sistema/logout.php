<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('America/Bogota');

// Cargar configuración de BD
include_once(__DIR__ . '/../app/controllers/config.php');

// Establecer cabecera antes de cualquier salida
header('Content-Type: application/json');

// Manejo de errores no capturados (avisos, warnings, fatales)
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => "Error en $errfile:$errline - $errstr"
    ]);
    exit;
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => "Shutdown error: " . $error['message']
        ]);
        exit;
    }
});

// Archivo de log
$log_index = __DIR__ . '/log_index.txt';
function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}
log_index("== Inicio de logout ==");

// Iniciar sesión
session_name('mi_sesion_personalizada');
session_start();
log_index("Contenido de \$_SESSION: " . var_export($_SESSION, true));



// Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Validar sesión activa
if (!isset($_SESSION['usuario_info']['id'])) {
    log_index("Sesión inválida o ya destruida.");
    session_unset();
    session_destroy();
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Sesión inválida']);
    exit;
}

$id_usuario = $_SESSION['usuario_info']['id'];
log_index("Procesando logout para usuario ID $id_usuario");



// Obtener datos de conexión
$sql = "SELECT fecha_ingreso, id_conexion, CONVERT_TZ(NOW(), @@session.time_zone, '-05:00') AS salida 
        FROM usuarios_conectados 
        WHERE id_usuario = :id_usuario";
$stmt = $pdo->prepare($sql);
if (!$stmt->execute([':id_usuario' => $id_usuario])) {
    log_index("Error al obtener datos de conexión: " . print_r($stmt->errorInfo(), true));
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error al consultar conexión']);
    exit;
}
$datos = $stmt->fetch(PDO::FETCH_ASSOC);
log_index("Datos obtenidos: " . var_export($datos, true));

if (!empty($datos['fecha_ingreso'])) {
    $inicio = new DateTime($datos['fecha_ingreso'], new DateTimeZone('America/Bogota'));
    $salida = new DateTime($datos['salida'], new DateTimeZone('America/Bogota'));
    $duracion = $inicio->diff($salida);
    $tiempo_conectado = $duracion->format('%H:%I:%S');
    log_index("Tiempo conectado: $tiempo_conectado");

    $id_conexion = $datos['id_conexion'] ?? null;

    // Obtener resumen de actividad
    $sqlStats = "SELECT 
                    COUNT(*) AS total_paginas,
                    SUM(tiempo_cpu) AS total_cpu,
                    SUM(uso_memoria) AS total_ram
                 FROM paginas_abiertas 
                 WHERE id_conexion = :id_conexion";
    $stmtStats = $pdo->prepare($sqlStats);
    $stmtStats->execute([':id_conexion' => $id_conexion]);
    $stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

    $total_paginas = $stats['total_paginas'] ?? 0;
    $total_cpu = $stats['total_cpu'] ?? 0;
    $total_ram = $stats['total_ram'] ?? 0;

    log_index("Resumen de uso: páginas=$total_paginas, cpu=$total_cpu, ram=$total_ram");

    // Acumular en tabla diaria
    $fecha_actual = date('Y-m-d');
    $sqlAcumulado = "
        INSERT INTO tb_actividad_diaria (
            id_usuario, fecha, total_paginas, total_cpu, total_ram, tiempo_conectado, cantidad_conexiones
        ) VALUES (
            :id_usuario, :fecha, :total_paginas, :total_cpu, :total_ram, :tiempo_conectado, 1
        ) ON DUPLICATE KEY UPDATE
            total_paginas = total_paginas + VALUES(total_paginas),
            total_cpu = total_cpu + VALUES(total_cpu),
            total_ram = total_ram + VALUES(total_ram),
            tiempo_conectado = ADDTIME(tiempo_conectado, VALUES(tiempo_conectado)),
            cantidad_conexiones = cantidad_conexiones + 1;
    ";
    $stmtAcumulado = $pdo->prepare($sqlAcumulado);
    $stmtAcumulado->execute([
        ':id_usuario' => $id_usuario,
        ':fecha' => $fecha_actual,
        ':total_paginas' => $total_paginas,
        ':total_cpu' => $total_cpu,
        ':total_ram' => $total_ram,
        ':tiempo_conectado' => $tiempo_conectado
    ]);
    log_index("Actividad acumulada correctamente.");

    // Eliminar de paginas_abiertas
    if (!empty($id_conexion)) {
        $sql_delete_paginas = "DELETE FROM paginas_abiertas WHERE id_conexion = :id_conexion";
        $pdo->prepare($sql_delete_paginas)->execute([':id_conexion' => $id_conexion]);
        log_index("Registros eliminados de paginas_abiertas para id_conexion $id_conexion.");
    }
}

// Actualizar estado y eliminar conexión
$sql_update_estado = "UPDATE usuarios_conectados 
                      SET estado = 'desconectado', ultima_actividad = CONVERT_TZ(NOW(), @@session.time_zone, '-05:00') 
                      WHERE id_usuario = :id_usuario";
$pdo->prepare($sql_update_estado)->execute([':id_usuario' => $id_usuario]);
log_index("Estado actualizado a 'desconectado'");

$sql_delete_conexion = "DELETE FROM usuarios_conectados WHERE id_usuario = :id_usuario";
$pdo->prepare($sql_delete_conexion)->execute([':id_usuario' => $id_usuario]);
log_index("Conexión eliminada de usuarios_conectados.");

// Finalizar sesión
session_unset();
session_destroy();

log_index("Logout finalizado.");

// Enviar respuesta JSON válida

echo json_encode(['success' => true]);
exit;
?>