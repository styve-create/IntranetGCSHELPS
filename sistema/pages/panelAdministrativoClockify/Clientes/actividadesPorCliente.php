<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../../app/controllers/config.php');

try {
    // Recibir el ID del cliente
    $id_cliente = isset($_GET['id_cliente']) ? intval($_GET['id_cliente']) : (
        isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0
    );

    if ($id_cliente <= 0) {
        echo json_encode(['error' => 'ID de cliente no válido']);
        exit;
    }

    // Consulta SQL con JOIN para obtener moneda y actividades
    $sql = "
        SELECT 
            pa.id,
            pa.nombre_actividad,
            pa.descripcion,
            pa.precio,
            pa.duracion,
            c.moneda
        FROM proyecto_actividad pa
        JOIN clientes c ON pa.id_cliente = c.id_cliente
        WHERE pa.id_cliente = :id_cliente
        ORDER BY pa.nombre_actividad ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_cliente' => $id_cliente]);

    $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($actividades ?: []);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de servidor: ' . $e->getMessage()]);
}
?>