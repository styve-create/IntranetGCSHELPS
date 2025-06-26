<?php
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}
$id_usuarios = $_SESSION['usuario_info']['id'] ?? null;
if (!$id_usuarios) {
    http_response_code(401);
    echo json_encode(['success' => false, 'msg' => 'No autenticado']);
    exit;
}

$hoy   = date('Y-m-d');
$ahora = date('Y-m-d H:i:s'); // Timestamp actual

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input  = json_decode(file_get_contents('php://input'), true);
    $accion = $input['accion'] ?? null;

    // 1) Asegurar que existe registro de hoy
    $stmt = $pdo->prepare("SELECT * FROM tb_horario WHERE id_usuario = :uid AND fecha = :fecha LIMIT 1");
    $stmt->execute([':uid' => $id_usuarios, ':fecha' => $hoy]);
    $asistencia = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$asistencia) {
        // Insertar fila vacía
        $stmtInsert = $pdo->prepare("INSERT INTO tb_horario (id_usuario, fecha) VALUES (:uid, :fecha)");
        $stmtInsert->execute([':uid' => $id_usuarios, ':fecha' => $hoy]);
        $asistencia_id = $pdo->lastInsertId();
    } else {
        $asistencia_id = $asistencia['id'];
    }

    try {
        switch ($accion) {
            case 'inicio_turno':
                // proteger doble inicio
                if (!empty($asistencia['hora_inicio_turno'])) {
                    echo json_encode(['success'=>false,'msg'=>'Ya iniciaste turno hoy']);
                    exit;
                }
                $sql = "UPDATE tb_horario
                        SET hora_inicio_turno = :hora,
                            hora_fin_turno    = NULL
                        WHERE id = :id";
                $pdo->prepare($sql)
                    ->execute([':hora'=>$ahora, ':id'=>$asistencia_id]);
                echo json_encode(['success'=>true]);
                break;

            case 'fin_turno':
                $sql = "UPDATE tb_horario
                        SET hora_fin_turno = :hora
                        WHERE id = :id";
                $pdo->prepare($sql)
                    ->execute([':hora'=>$ahora, ':id'=>$asistencia_id]);
                echo json_encode(['success'=>true]);
                break;

            case 'inicio_break':
                $n = (int)($input['break_num'] ?? 0);
                if ($n<1||$n>3) {
                    echo json_encode(['success'=>false,'msg'=>'Break inválido']);
                    exit;
                }
                $f1 = "hora_inicio_break{$n}";
                $f2 = "hora_fin_break{$n}";
                $sql = "UPDATE tb_horario
                        SET $f1 = :hora,
                            $f2 = NULL
                        WHERE id = :id";
                $pdo->prepare($sql)
                    ->execute([':hora'=>$ahora,':id'=>$asistencia_id]);
                echo json_encode(['success'=>true]);
                break;

            case 'fin_break':
                $n = (int)($input['break_num'] ?? 0);
                if ($n<1||$n>3) {
                    echo json_encode(['success'=>false,'msg'=>'Break inválido']);
                    exit;
                }
                $f = "hora_fin_break{$n}";
                $sql = "UPDATE tb_horario
                        SET $f = :hora
                        WHERE id = :id";
                $pdo->prepare($sql)
                    ->execute([':hora'=>$ahora,':id'=>$asistencia_id]);
                echo json_encode(['success'=>true]);
                break;

            case 'inicio_extra':
                // arrancar tiempo extra
                if (!empty($asistencia['hora_inicio_extra'])) {
                    echo json_encode(['success'=>false,'msg'=>'Ya iniciaste tiempo extra']);
                    exit;
                }
                $sql = "UPDATE tb_horario
                        SET hora_inicio_extra = :hora,
                            hora_fin_extra    = NULL
                        WHERE id = :id";
                $pdo->prepare($sql)
                    ->execute([':hora'=>$ahora,':id'=>$asistencia_id]);
                echo json_encode(['success'=>true]);
                break;

            case 'fin_extra':
                // terminar tiempo extra
                $sql = "UPDATE tb_horario
                        SET hora_fin_extra = :hora
                        WHERE id = :id";
                $pdo->prepare($sql)
                    ->execute([':hora'=>$ahora,':id'=>$asistencia_id]);
                echo json_encode(['success'=>true]);
                break;

            default:
                echo json_encode(['success'=>false,'msg'=>'Acción no válida']);
                break;
        }
    } catch (Throwable $e) {
        echo json_encode(['success'=>false,'msg'=>$e->getMessage()]);
    }
    exit;
}
?>