<?php
// crear_actividad.php
include_once(__DIR__ . '/../../../../app/controllers/config.php');
header('Content-Type: application/json');
ini_set('display_errors',1);
error_reporting(E_ALL);

// 1) Leer y decodificar el JSON de entrada
$input = json_decode(file_get_contents('php://input'), true);

// 2) Validar campos obligatorios
foreach (['usuario_id','cliente_id','actividad','hora_inicio','hora_fin','fecha'] as $f) {
    if (empty($input[$f]) && $input[$f] !== '0') {
        http_response_code(400);
        echo json_encode(['success'=>false, 'message'=>"Falta el campo '$f'"]);
        exit;
    }
}

// 3) Resolver nombre del cliente
$stmtC = $pdo->prepare("SELECT nombre_cliente FROM clientes WHERE id_cliente = ?");
$stmtC->execute([$input['cliente_id']]);
$nombreC = $stmtC->fetchColumn();
if (!$nombreC) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>"Cliente con ID {$input['cliente_id']} no encontrado"]);
    exit;
}

// 4) Resolver nombre del usuario
$stmtU = $pdo->prepare("SELECT nombres FROM tb_usuarios WHERE id_usuarios = ?");
$stmtU->execute([$input['usuario_id']]);
$nombreU = $stmtU->fetchColumn();
if (!$nombreU) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>"Usuario con ID {$input['usuario_id']} no encontrado"]);
    exit;
}

// 5) Calcular duración (en segundos) y validar horas
$t1 = strtotime($input['hora_inicio']);
$t2 = strtotime($input['hora_fin']);
if ($t1 === false || $t2 === false) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>"Formato de hora inválido"]);
    exit;
}
if ($t2 < $t1) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>"Hora fin no puede ser anterior a hora inicio"]);
    exit;
}
$duracion = $t2 - $t1;

// 6) Insertar nueva actividad
$sql = "INSERT INTO tb_actividades
          (id_usuario, cliente, actividad,
           hora_inicio, hora_fin, fecha,
           duracion, cobrado, cobrado_general, descripcion)
        VALUES
          (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$params = [
    $input['usuario_id'],       // FK usuario_id
    $nombreC,                   // nombre de cliente
    $input['actividad'],        // descripción de la actividad
    $input['hora_inicio'],
    $input['hora_fin'],
    $input['fecha'],
    $duracion,
    $input['cobrado']   ?? 0,
    $input['cobradoGeneral'] ?? 0,
    $input['descripcion'] ?? ''
];

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $newId = $pdo->lastInsertId();
    echo json_encode(['success'=>true,'id'=>$newId]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>"Error al crear actividad: ".$e->getMessage()]);
}