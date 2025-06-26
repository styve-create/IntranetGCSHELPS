<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../../app/controllers/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

// Parámetros
$idCliente = $_GET['cliente'] ?? null;
$inicio    = $_GET['inicio']  ?? null;
$fin       = $_GET['fin']     ?? null;
if (!$idCliente || !$inicio || !$fin) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan parámetros cliente, inicio o fin']);
    exit;
}

try {
    // 1) Usuarios del cliente
    $sqlUsers = "
    SELECT u.id_usuarios AS uid, u.nombres AS usuario
    FROM cliente_trabajador ct
    JOIN tb_usuarios u 
      ON ct.id_trabajador = u.trabajador_id
    WHERE ct.id_cliente = :cliente
      AND ct.estado     = 1
    ";
    $stmt = $pdo->prepare($sqlUsers);
    $stmt->execute([':cliente' => $idCliente]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Estructuras
    $summaryMap = [];
    $detailsMap = [];
    foreach ($users as $u) {
        $summaryMap[$u['usuario']] = 0;
        $detailsMap[$u['usuario']] = [];
    }

    // Helper timestamp
  function toTs($datetime) {
  return $datetime
    ? strtotime($datetime)
    : null;
}

    // 2) Registros de horario en rango
    $sql = "
    SELECT h.*, u.nombres AS usuario
    FROM tb_horario h
    JOIN tb_usuarios u 
      ON h.id_usuario = u.id_usuarios
    WHERE u.trabajador_id IN (
        SELECT id_trabajador
        FROM cliente_trabajador 
        WHERE id_cliente = :cliente
          AND estado      = 1
          AND fecha_asignacion <= :fin
    )
      AND h.fecha BETWEEN :inicio AND :fin
    ORDER BY h.fecha DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':cliente' => $idCliente,
      ':inicio'  => $inicio,
      ':fin'     => $fin
    ]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar filas
    $horasExtraMap = [];

foreach ($rows as $r) {
    $usr = $r['usuario'];

    // calcular duración neta del turno
    $tIni = toTs($r['hora_inicio_turno']);
    $tFin = toTs($r['hora_fin_turno']);
    $durSec = null;
    if ($tIni && $tFin) {
        $durSec = $tFin - $tIni;
        for ($i = 1; $i <= 3; $i++) {
            $bIni = toTs($r["hora_inicio_break{$i}"]);
            $bFin = toTs($r["hora_fin_break{$i}"]);
            if ($bIni && $bFin) {
                $durSec -= ($bFin - $bIni);
            }
        }
        $summaryMap[$usr] += $durSec;
    }

    // calcular duración de horas extra usando columnas específicas
    $extraIni = toTs($r['hora_inicio_extra']);
    $extraFin = toTs($r['hora_fin_extra']);
    $extraSeg = 0;
    if ($extraIni && $extraFin && $extraFin > $extraIni) {
        $extraSeg = $extraFin - $extraIni;
        if (!isset($horasExtraMap[$usr])) $horasExtraMap[$usr] = 0;
        $horasExtraMap[$usr] += $extraSeg;
    }

    $durTxt = $durSec !== null ? sprintf("%02d:%02d", floor($durSec/3600), floor(($durSec%3600)/60)) : null;

   $detailsMap[$usr][] = [
    'id'              => (int)$r['id'],
    'fecha'           => $r['fecha'],
    'start_turno'     => $r['hora_inicio_turno'],
    'end_turno'       => $r['hora_fin_turno'],
    'inicio_extra'    => $r['hora_inicio_extra'],
    'fin_extra'       => $r['hora_fin_extra'],
    'break1_start'    => $r['hora_inicio_break1'],
    'break1_end'      => $r['hora_fin_break1'],
    'break2_start'    => $r['hora_inicio_break2'],
    'break2_end'      => $r['hora_fin_break2'],
    'break3_start'    => $r['hora_inicio_break3'],
    'break3_end'      => $r['hora_fin_break3'],
    'duracion'        => $durTxt
];
}

// construir el resumen
$summary = [];
foreach ($summaryMap as $usuario => $segTotal) {
    $h = floor($segTotal / 3600);
    $m = floor(($segTotal % 3600) / 60);

    $extraSeg = $horasExtraMap[$usuario] ?? 0;
    $eh = floor($extraSeg / 3600);
    $em = floor(($extraSeg % 3600) / 60);

    $summary[] = [
        'usuario'     => $usuario,
        'tiempo'      => sprintf("%02d:%02d", $h, $m),
        'horas_extra' => sprintf("%02d:%02d", $eh, $em)
    ];
}

    // respuesta
    echo json_encode([
      'summary' => $summary,
      'details' => $detailsMap
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
?>