<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../../app/controllers/config.php');


   session_name('mi_sesion_personalizada');
    session_start();
header('Content-Type: application/json');



try {
    // 2) Obtener el user_id (desde GET o SESSION)
    $userId = $_GET['user_id'] 
            ?? $_SESSION['usuario_info']['id'] 
            ?? null;
   
    if (!$userId) {
        throw new Exception("Falta el parámetro user_id o no hay sesión válida");
    }

    // 3) Mapear user_id → trabajador_id
    $stmt = $pdo->prepare("SELECT trabajador_id FROM tb_usuarios WHERE id_usuarios = :uid");
    $stmt->execute([':uid' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$row || empty($row['trabajador_id'])) {
        throw new Exception("No se encontró trabajador asociado al usuario $userId");
    }
    $trabajadorId = $row['trabajador_id'];
    

    // 4) Obtener clientes + nombre del cliente
    $sql = "
        SELECT
          ct.id_cliente,
          c.nombre_cliente
        FROM cliente_trabajador ct
        JOIN clientes c
          ON c.id_cliente = ct.id_cliente
        WHERE ct.id_trabajador = :tid
          AND ct.estado      = 'activo'
    ";
    $stmt2 = $pdo->prepare($sql);
    $stmt2->execute([':tid' => $trabajadorId]);
    $clientes = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    

    // 5) Devolver JSON
    echo json_encode([
        'clientes' => $clientes
    ]);
    
} catch (Exception $e) {
    
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
?>

 


