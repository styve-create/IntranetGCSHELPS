<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../app/controllers/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

$id_usuario_jefe = $_SESSION['usuario_info']['id'] ?? null;
if (!$id_usuario_jefe) {
    http_response_code(401);
    echo json_encode(['success' => false, 'msg' => 'No autenticado']);
    exit;
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener el trabajador_id
$sql = "SELECT trabajador_id FROM tb_usuarios WHERE id_usuarios = :id_usuario";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id_usuario' => $id_usuario_jefe]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$trabajador_id_jefe = $row['trabajador_id'] ?? null;

if (!$trabajador_id_jefe) {
    die("No se encontró el trabajador asociado al usuario.");
}

// Verificar si es jefe (cargo_id = 2)
$sql = "SELECT * FROM trabajadores_campanas WHERE trabajador_id = :trabajador_id AND cargo_id = 2";
$stmt = $pdo->prepare($sql);
$stmt->execute(['trabajador_id' => $trabajador_id_jefe]);
$es_jefe = $stmt->rowCount() > 0;

// Unificar todos los subordinados aquí
$registros = [];

// Lógica 1: si es jefe, buscar en tb_jerarquia_trabajadores
if ($es_jefe) {
    $sql = "SELECT jt.id_trabajador, jt.id_campana, t.nombre_completo, u.id_usuarios, c.nombre
            FROM tb_jerarquia_trabajadores jt
            JOIN trabajadores t ON jt.id_trabajador = t.id
            JOIN tb_usuarios u ON u.trabajador_id = t.id
            JOIN tb_campanas c ON c.id = jt.id_campana
            WHERE jt.id_jefe = :id_jefe";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_jefe' => $trabajador_id_jefe]);
    $subordinados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($subordinados as $sub) {
        $sql = "SELECT * FROM tb_horario WHERE id_usuario = :id_usuario ORDER BY fecha DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_usuario' => $sub['id_usuarios']]);
        $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($horarios as $h) {
            $registros[] = [
                'campana' => $sub['nombre'],
                'trabajador' => $sub['nombre_completo'],
                'fecha' => $h['fecha'],
                'inicio_turno' => $h['hora_inicio_turno'],
                'fin_turno' => $h['hora_fin_turno'],
                'break1_inicio' => $h['hora_inicio_break1'],
                'break1_fin' => $h['hora_fin_break1'],
                'break2_inicio' => $h['hora_inicio_break2'],
                'break2_fin' => $h['hora_fin_break2'],
                'break3_inicio' => $h['hora_inicio_break3'],
                'break3_fin' => $h['hora_fin_break3'],
                'registro' => $h['fyh_creacion']
            ];
        }
    }
}

// Lógica 2: campañas donde es responsable
$sql = "SELECT * FROM tb_campanas WHERE id_responsable = :trabajador_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['trabajador_id' => $trabajador_id_jefe]);
$campanas_responsable = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($campanas_responsable as $campana) {
    $campana_id = $campana['id'];
    $campana_nombre = $campana['nombre'];

    $sql = "SELECT t.id AS trabajador_id, t.nombre_completo, u.id_usuarios
            FROM trabajadores_campanas tc
            JOIN trabajadores t ON tc.trabajador_id = t.id
            JOIN tb_usuarios u ON u.trabajador_id = t.id
            WHERE tc.campana_id = :campana_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['campana_id' => $campana_id]);
    $subordinados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($subordinados as $sub) {
        $sql = "SELECT * FROM tb_horario WHERE id_usuario = :id_usuario ORDER BY fecha DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_usuario' => $sub['id_usuarios']]);
        $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($horarios as $h) {
            $registros[] = [
                'campana' => $campana_nombre,
                'trabajador' => $sub['nombre_completo'],
                'fecha' => $h['fecha'],
                'inicio_turno' => $h['hora_inicio_turno'],
                'fin_turno' => $h['hora_fin_turno'],
                'break1_inicio' => $h['hora_inicio_break1'],
                'break1_fin' => $h['hora_fin_break1'],
                'break2_inicio' => $h['hora_inicio_break2'],
                'break2_fin' => $h['hora_fin_break2'],
                'break3_inicio' => $h['hora_inicio_break3'],
                'break3_fin' => $h['hora_fin_break3'],
                'registro' => $h['fyh_creacion']
            ];
        }
    }
}

echo json_encode(['success' => true, 'data' => $registros]);