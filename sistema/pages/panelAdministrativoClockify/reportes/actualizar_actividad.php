<?php
include_once(__DIR__ . '/../../../../app/controllers/config.php');
header('Content-Type: application/json');
ini_set('display_errors',1);
error_reporting(E_ALL);

$input = json_decode(file_get_contents('php://input'), true);

// 1) Validar que llegue el ID de la actividad
if (empty($input['id'])) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>"Falta el campo 'id'"]);
    exit;
}

// 2) Obtener datos actuales de la actividad
$stmtOld = $pdo->prepare("
    SELECT cliente, actividad, hora_inicio, hora_fin, fecha,
           cobrado, cobrado_general, descripcion, duracion
    FROM tb_actividades
    WHERE id = ?
");
$stmtOld->execute([$input['id']]);
$old = $stmtOld->fetch(PDO::FETCH_ASSOC);

if (!$old) {
    http_response_code(404);
    echo json_encode(['success'=>false,'message'=>"Actividad con ID {$input['id']} no existe"]);
    exit;
}

// 3) Resolver el nombre del cliente
if (isset($input['cliente']) && $input['cliente'] !== '') {
    // Llegó un ID de cliente: buscamos su nombre
    $stmtC = $pdo->prepare("SELECT nombre_cliente FROM clientes WHERE id_cliente = ?");
    $stmtC->execute([$input['cliente']]);
    $nombreC = $stmtC->fetchColumn();
    if (!$nombreC) {
        http_response_code(400);
        echo json_encode(['success'=>false,'message'=>"Cliente con ID {$input['cliente']} no encontrado"]);
        exit;
    }
    $cliente = $nombreC;
} else {
    // No llegó o está vacío: conservar el valor actual en tb_actividades
    $cliente = $old['cliente'];
}

// 4) Para cada campo, si viene vacío lo mantenemos
$actividad      = (isset($input['actividad']) && $input['actividad'] !== '') 
                   ? $input['actividad'] : $old['actividad'];
$hora_inicio    = (isset($input['hora_inicio']) && $input['hora_inicio'] !== '') 
                   ? $input['hora_inicio']  : $old['hora_inicio'];
$hora_fin       = (isset($input['hora_fin'])    && $input['hora_fin']    !== '') 
                   ? $input['hora_fin']     : $old['hora_fin'];
$fecha          = (isset($input['fecha'])       && $input['fecha']       !== '') 
                   ? $input['fecha']        : $old['fecha'];
$cobrado        = isset($input['cobrado'])        
                   ? $input['cobrado']      : $old['cobrado'];
$cobradoGeneral = isset($input['cobradoGeneral']) 
                   ? $input['cobradoGeneral'] : $old['cobrado_general'];
$descripcion    = (isset($input['descripcion']) && $input['descripcion'] !== '') 
                   ? $input['descripcion']   : $old['descripcion'];

// 5) Recalcular duración solo si cambian horas
if ($hora_inicio !== $old['hora_inicio'] || $hora_fin !== $old['hora_fin']) {
    $t1 = strtotime($hora_inicio);
    $t2 = strtotime($hora_fin);
    if ($t2 < $t1) {
        http_response_code(400);
        echo json_encode(['success'=>false,'message'=>"Hora fin no puede ser anterior a hora inicio"]);
        exit;
    }
    $duracion = $t2 - $t1;
} else {
    $duracion = $old['duracion'];
}

// 6) Preparar UPDATE
$sql = "UPDATE tb_actividades SET
            cliente         = ?,
            actividad       = ?,
            hora_inicio     = ?,
            hora_fin        = ?,
            fecha           = ?,
            cobrado         = ?,
            cobrado_general = ?,
            descripcion     = ?,
            duracion        = ?
        WHERE id = ?";
$params = [
    $cliente,
    $actividad,
    $hora_inicio,
    $hora_fin,
    $fecha,
    $cobrado,
    $cobradoGeneral,
    $descripcion,
    $duracion,
    $input['id']
];

// 7) Ejecutar
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>"Error al actualizar: ".$e->getMessage()]);
}