<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../../app/controllers/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

// 1) Leer y validar payload
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!isset($data['actividades']) || !is_array($data['actividades'])) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Falta array actividades']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 2) Preparar statements
    // 2.a) Buscar id_usuario por nombre
    $stmtUser    = $pdo->prepare("
        SELECT id_usuarios 
          FROM tb_usuarios 
         WHERE nombres = :nombre 
         LIMIT 1
    ");
    // 2.b) Buscar nombre_cliente
    $stmtCliente = $pdo->prepare("
        SELECT nombre_cliente 
          FROM clientes 
         WHERE id_cliente = :id 
         LIMIT 1
    ");
    // 2.c) Insertar en tb_actividades
    $stmtIns     = $pdo->prepare("
        INSERT INTO tb_actividades
  (cliente, actividad, descripcion, duracion, cobrado, cobrado_general,
   fecha, hora_inicio, hora_fin, id_usuario)
VALUES
  (:cliente, :actividad, :descripcion, :duracion, :cobrado, :cobrado_general,
   :fecha, :hora_inicio, :hora_fin, :id_usuario)
    ");

    foreach ($data['actividades'] as $act) {
        //
        // 3) Resuelvo id_usuario
        //
        $stmtUser->execute([':nombre'=>$act['usuario_id']]);
        $u = $stmtUser->fetch(PDO::FETCH_ASSOC);
        if (!$u) {
            throw new Exception("Usuario no encontrado: {$act['usuario_id']}");
        }
        $idUsuario = $u['id_usuarios'];

        //
        // 4) Resuelvo nombre de cliente
        //
        $stmtCliente->execute([':id'=>$act['cliente_id']]);
        $c = $stmtCliente->fetch(PDO::FETCH_ASSOC);
        if (!$c) {
            throw new Exception("Cliente no encontrado: {$act['cliente_id']}");
        }
        $nombreCliente = $c['nombre_cliente'];

        //
        // 5) Calculo duración neta del turno (en segundos)
        //
        $turnoIni = strtotime("{$act['fecha']} {$act['hora_inicio_turno']}");
        $turnoFin = strtotime("{$act['fecha']} {$act['hora_fin_turno']}");
        if (!$turnoIni || !$turnoFin) {
            throw new Exception("Turno inválido para horario_id {$act['horario_id']}");
        }
        $durSeg = $turnoFin - $turnoIni;

        // 6) Descuento cada uno de los 3 breaks
        for ($i=1; $i<=3; $i++) {
            $bIni = $act["break{$i}_inicio"];
            $bFin = $act["break{$i}_fin"];
            if ($bIni && $bFin) {
                $tsIni = strtotime("{$act['fecha']} $bIni");
                $tsFin = strtotime("{$act['fecha']} $bFin");
                if ($tsIni && $tsFin && $tsFin > $tsIni) {
                    $durSeg -= ($tsFin - $tsIni);
                }
            }
        }
        if ($durSeg < 0) {
            $durSeg = 0;
        }

        // 7.a) Inserto el registro principal

        $stmtIns->execute([
            ':cliente'         => $nombreCliente,
            ':actividad'       => $act['actividad'],
            ':descripcion'     => $act['descripcion'] ?? '',
            ':duracion'        => $durSeg,
            ':cobrado'         => $act['cobrado'] ?? 0,
            ':cobrado_general' => 1,
            ':fecha'           => $act['fecha'],
            ':hora_inicio'     => $act['hora_inicio_turno'],
            ':hora_fin'        => $act['hora_fin_turno'],
            ':id_usuario'      => $idUsuario
        ]);
        //
        // 7.b) Si hay horas extra, inserto un segundo registro
        //
        if (!empty($act['hora_inicio_extra']) && !empty($act['hora_fin_extra'])) {
            $iniX = strtotime("{$act['fecha']} {$act['hora_inicio_extra']}");
            $finX = strtotime("{$act['fecha']} {$act['hora_fin_extra']}");
            if ($iniX && $finX && $finX > $iniX) {
                $extraSeg = $finX - $iniX;
                // descripción indicando que es horas extra
                $descExtra = trim(($act['descripcion'] ?? '') . ' (Horas extras)');

                $stmtIns->execute([
                ':cliente'         => $nombreCliente,
                ':actividad'       => $act['actividad'],
                ':descripcion'     => $descExtra,
                ':duracion'        => $extraSeg,
                ':cobrado'         => $act['cobrado'] ?? 0,
                ':cobrado_general' => 1,
                ':fecha'           => $act['fecha'],
                ':hora_inicio'     => $act['hora_inicio_extra'],
                ':hora_fin'        => $act['hora_fin_extra'],
                ':id_usuario'      => $idUsuario
            ]);
            }
        }
    }

    $pdo->commit();
    echo json_encode(['success'=>true]);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
exit;
?>