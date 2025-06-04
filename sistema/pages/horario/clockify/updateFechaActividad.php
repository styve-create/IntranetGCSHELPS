<?php 
header('Content-Type: application/json');
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// Incluir conexión a la base de datos
include_once(__DIR__ . '/../../../../app/controllers/config.php');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'], $data['nueva_fecha'])) {
        $id = $data['id'];
        $nuevaFecha = $data['nueva_fecha'];

        $stmt = $pdo->prepare("UPDATE tb_actividades SET fecha = ? WHERE id = ?");
        $success = $stmt->execute([$nuevaFecha . ' 00:00:00', $id]);

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Fecha actualizada' : 'Error al actualizar fecha'
        ]);
        exit;
    }
}

?>