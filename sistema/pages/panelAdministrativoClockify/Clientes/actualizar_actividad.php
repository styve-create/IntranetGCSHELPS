<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include_once(__DIR__ . '/../../../../app/controllers/config.php');



// 1. Recibir y validar datos
$rawData = file_get_contents('php://input');

$data = json_decode($rawData, true);


if (!isset($data['id'], $data['nombre_actividad'], $data['descripcion'], $data['duracion'], $data['precio'])) {
    log_index1("❌ Datos incompletos");
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

try {
    // 2. Convertir duración (horas a segundos)
    $duracion_horas = is_numeric($data['duracion']) ? floatval($data['duracion']) : 0;
    $duracion_segundos = $duracion_horas * 3600;
   
    // 3. Obtener id_actividad desde proyecto_actividad
    $stmtBuscar = $pdo->prepare("SELECT id_actividad FROM proyecto_actividad WHERE id = ?");
    $stmtBuscar->execute([$data['id']]);
    $fila = $stmtBuscar->fetch(PDO::FETCH_ASSOC);

    if (!$fila) {
        log_index1("❌ No se encontró el registro en proyecto_actividad con id=" . $data['id']);
        echo json_encode(['success' => false, 'message' => 'No se encontró la actividad.']);
        exit;
    }

    $id_actividad = $fila['id_actividad'];
    

    // 4. UPDATE en proyecto_actividad
    $stmt1 = $pdo->prepare("UPDATE proyecto_actividad SET 
        nombre_actividad = :nombre,
        descripcion = :descripcion,
        duracion = :duracion,
        precio = :precio
        WHERE id = :id
    ");
    $stmt1->execute([
        'nombre' => $data['nombre_actividad'],
        'descripcion' => $data['descripcion'],
        'duracion' => $duracion_segundos,
        'precio' => $data['precio'],
        'id' => $data['id']
    ]);
    

    // 5. UPDATE en actividades (misma lógica, usando duración en horas)
    $stmt2 = $pdo->prepare("UPDATE actividades SET 
        nombre_actividad = :nombre,
        descripcion = :descripcion,
        duracion = :duracion,
        precio = :precio
        WHERE id_actividad = :id_actividad
    ");
    $stmt2->execute([
        'nombre' => $data['nombre_actividad'],
        'descripcion' => $data['descripcion'],
        'duracion' => $duracion_horas,
        'precio' => $data['precio'],
        'id_actividad' => $id_actividad
    ]);
   
 // … haces tus SELECT/UPDATE …
    echo json_encode(['success'=>true]);
    exit;
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    exit;
}

?>