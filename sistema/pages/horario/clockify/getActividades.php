<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../../app/controllers/config.php');

try {
  
    $stmt = $pdo->prepare("SELECT * FROM actividades");
$stmt->execute();
$actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener estructura agrupada por cliente
    $sql = "SELECT c.nombre_cliente, a.nombre_actividad 
            FROM actividades a 
            JOIN clientes c ON a.id_cliente = c.id_cliente
            ORDER BY c.nombre_cliente, a.nombre_actividad";
    $stmt2 = $pdo->prepare($sql);
    $stmt2->execute();
    $result = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $estructura = [];
    foreach ($result as $row) {
        $cliente = $row['nombre_cliente'];
        $actividad = $row['nombre_actividad'];

        if (!isset($estructura[$cliente])) {
            $estructura[$cliente] = [];
        }
        $estructura[$cliente][] = $actividad;
    }

    // Devolver ambas cosas en una sola respuesta JSON
    echo json_encode([
        'actividades' => $actividades,
        'estructura' => $estructura
         
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
?>