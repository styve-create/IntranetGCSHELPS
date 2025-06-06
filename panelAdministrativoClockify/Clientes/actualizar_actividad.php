<?php
header('Content-Type: application/json');
include_once(__DIR__ . '/../../../../app/controllers/config.php');

// LOG
$log_index1 = __DIR__ . "/log_index1.txt";
function log_index1($mensaje) {
    global $log_index1;
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, $log_index1);
}

log_index1(">>> Inicio del script actualizar_actividad.php");

// 1. Recibir y validar datos
$rawData = file_get_contents('php://input');
log_index1("📥 Raw JSON recibido: " . $rawData);

$data = json_decode($rawData, true);
log_index1("✅ JSON decodificado: " . print_r($data, true));

if (!isset($data['id'], $data['nombre_actividad'], $data['descripcion'], $data['duracion'], $data['precio'])) {
    log_index1("❌ Datos incompletos");
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

try {
    // 2. Convertir duración (horas a segundos)
    $duracion_horas = is_numeric($data['duracion']) ? floatval($data['duracion']) : 0;
    $duracion_segundos = $duracion_horas * 3600;
    log_index1("⏱️ Duración en horas: $duracion_horas => en segundos: $duracion_segundos");

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
    log_index1("🔗 id_actividad relacionado: $id_actividad");

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
    log_index1("✅ UPDATE realizado en proyecto_actividad");

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
        'duracion' => $duracion_horas, // Aquí sí se guarda como horas
        'precio' => $data['precio'],
        'id_actividad' => $id_actividad
    ]);
    log_index1("✅ UPDATE realizado en actividades");

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    log_index1("❌ Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

?>