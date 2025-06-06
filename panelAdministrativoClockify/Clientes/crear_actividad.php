<?php
header('Content-Type: application/json');
include_once(__DIR__ . '/../../../../app/controllers/config.php');

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (
        !isset($data['id_cliente']) ||
        !isset($data['nombre_actividad']) ||
        !isset($data['precio'])
    ) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
        exit;
    }

    $id_cliente = intval($data['id_cliente']);
    $nombre_actividad = trim($data['nombre_actividad']);
    $descripcion = trim($data['descripcion'] ?? '');
    $duracion_horas = is_numeric($data['duracion']) ? floatval($data['duracion']) : 0;
    $duracion_segundos = $duracion_horas * 3600; // ✅ Conversión horas → segundos
    $precio = floatval($data['precio']);

    // Obtener moneda del cliente
    $stmtMoneda = $pdo->prepare("SELECT moneda FROM clientes WHERE id_cliente = ?");
    $stmtMoneda->execute([$id_cliente]);
    $cliente = $stmtMoneda->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        echo json_encode(['success' => false, 'message' => 'Cliente no encontrado.']);
        exit;
    }

    $moneda = $cliente['moneda'];

    // Insertar en tabla `actividades`
    $stmt = $pdo->prepare("
        INSERT INTO actividades 
        (id_cliente, nombre_actividad, descripcion, duracion, precio, moneda, fyh_creacion)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $id_cliente,
        $nombre_actividad,
        $descripcion,
        $duracion_segundos,
        $precio,
        $moneda
    ]);

    // Obtener el ID de la actividad recién insertada
    $id_actividad = $pdo->lastInsertId();

    // Insertar también en tabla `proyecto_actividad`
    $stmt2 = $pdo->prepare("
        INSERT INTO proyecto_actividad 
        (id_actividad, id_cliente, nombre_actividad, descripcion, precio, fecha_creacion, duracion)
        VALUES (?, ?, ?, ?, ?, NOW(), ?)
    ");
    $stmt2->execute([
        $id_actividad,
        $id_cliente,
        $nombre_actividad,
        $descripcion,
        $precio,
        $duracion_segundos
    ]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al insertar actividad: ' . $e->getMessage()
    ]);
}