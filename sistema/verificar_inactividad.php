<?php
// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar configuración
include_once(__DIR__ . '/../app/controllers/config.php');

// Logs
$log_cron = __DIR__ . "/debug_cron.txt";
$log_verificar = __DIR__ . "/log_verificarInactividad.txt";

function log_debug($mensaje) {
    global $log_cron;
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, $log_cron);
}

function log_verificar($mensaje) {
    global $log_verificar;
    file_put_contents($log_verificar, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

// Inicio
echo "Script iniciado\n";
log_debug(">>> verificar_inactividad.php se ejecutó");
log_verificar("== INICIO DE VERIFICACIÓN ==");

date_default_timezone_set('America/Bogota');
$limiteMinutos = 3;

try {
    // Hora actual y límite de inactividad
    $ahora = new DateTime();
    $limite = clone $ahora;
    $limite->modify("-$limiteMinutos minutes");

    $horaActual = $ahora->format('Y-m-d H:i:s');
    $horaLimite = $limite->format('Y-m-d H:i:s');

    log_debug("Hora actual del servidor: $horaActual");
    log_debug("Límite de inactividad (marca de corte): $horaLimite");
    log_debug("Límite de inactividad definido: $limiteMinutos minutos");

    // 1. Obtener usuarios inactivos con comparación en PHP (más controlada)
    $sql = "SELECT uc.id_usuario, uc.id_conexion, uc.ultima_actividad, uc.fecha_ingreso
            FROM usuarios_conectados uc
            WHERE uc.estado = 'conectado' 
            AND uc.ultima_actividad < :limite";
    log_debug("Ejecutando consulta para usuarios inactivos:\n$sql");

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':limite' => $horaLimite]);
    $usuarios_inactivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    log_debug("Total de usuarios inactivos encontrados: " . count($usuarios_inactivos));

    if (!empty($usuarios_inactivos)) {
        foreach ($usuarios_inactivos as $usuario) {
            $id_usuario = $usuario['id_usuario'];
            $id_conexion = $usuario['id_conexion'];
            $ultima_actividad = $usuario['ultima_actividad'];
            $fecha_ingreso = $usuario['fecha_ingreso'];

            $dtUltima = new DateTime($ultima_actividad);
            $diffSegundos = $ahora->getTimestamp() - $dtUltima->getTimestamp();
            $diffMinutos = floor($diffSegundos / 60);

            log_debug("Usuario ID $id_usuario (conexion ID $id_conexion) considerado inactivo.");
            log_debug("    Última actividad: $ultima_actividad");
            log_debug("    Fecha de ingreso: $fecha_ingreso");
            log_debug("    Diferencia real en minutos: $diffMinutos");
            log_debug("    Hora actual: $horaActual");

            // 2. Obtener páginas abiertas
            $sql_paginas = "SELECT COUNT(*) AS total_paginas
                            FROM paginas_abiertas
                            WHERE id_conexion = :id_conexion";
            $stmtPaginas = $pdo->prepare($sql_paginas);
            $stmtPaginas->execute([':id_conexion' => $id_conexion]);
            $total_paginas = $stmtPaginas->fetch(PDO::FETCH_ASSOC)['total_paginas'] ?? 0;

            // 3. Duración de conexión
            $inicio = new DateTime($fecha_ingreso);
            $duracion = $inicio->diff($ahora)->format('%H:%I:%S');

            // 4. Insertar actividad diaria (si no existe)
            $sql_check = "SELECT COUNT(*) FROM tb_actividad_diaria 
                          WHERE id_usuario = :id_usuario AND fecha = CURDATE()";
            $stmtCheck = $pdo->prepare($sql_check);
            $stmtCheck->execute([':id_usuario' => $id_usuario]);
            $existe = $stmtCheck->fetchColumn();

            if ($existe == 0) {
                $sql_insert = "INSERT INTO tb_actividad_diaria 
                               (id_usuario, fecha, tiempo_conectado, total_paginas) 
                               VALUES (:id_usuario, CURDATE(), :tiempo, :total_paginas)
                               ON DUPLICATE KEY UPDATE 
                               tiempo_conectado = VALUES(tiempo_conectado), 
                               total_paginas = VALUES(total_paginas)";
                $stmtInsert = $pdo->prepare($sql_insert);
                $stmtInsert->execute([
                    ':id_usuario' => $id_usuario,
                    ':tiempo' => $duracion,
                    ':total_paginas' => $total_paginas
                ]);
                log_verificar("Actividad registrada para usuario ID $id_usuario - Tiempo: $duracion - Recursos: $total_paginas");
            } else {
                log_verificar("Ya existe un registro para el usuario ID $id_usuario el día de hoy.");
            }

            // 5. Eliminar páginas abiertas
            $sql_delete_paginas = "DELETE FROM paginas_abiertas WHERE id_conexion = :id_conexion";
            $pdo->prepare($sql_delete_paginas)->execute([':id_conexion' => $id_conexion]);

            // 6. Actualizar estado
            $sql_update_estado = "UPDATE usuarios_conectados 
                                  SET estado = 'desconectado' 
                                  WHERE id_conexion = :id_conexion";
            $pdo->prepare($sql_update_estado)->execute([':id_conexion' => $id_conexion]);

            // 7. Eliminar conexión
            $sql_delete_conexion = "DELETE FROM usuarios_conectados WHERE id_conexion = :id_conexion";
            $pdo->prepare($sql_delete_conexion)->execute([':id_conexion' => $id_conexion]);

            log_debug("Usuario ID $id_usuario desconectado y limpiado.");
        }
    } else {
        log_debug("No se encontraron usuarios inactivos que cumplan la condición.");
        log_verificar("No hay usuarios inactivos por más de $limiteMinutos minutos.");
    }

} catch (PDOException $e) {
    $error = "ERROR: " . $e->getMessage();
    log_debug($error);
    log_verificar($error);
    echo $error;
}

log_verificar("== FIN DE VERIFICACIÓN ==\n");
?>