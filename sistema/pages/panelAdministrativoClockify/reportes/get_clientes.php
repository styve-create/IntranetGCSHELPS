<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../../app/controllers/config.php');

// 1) Arrancar sesión
session_name('mi_sesion_personalizada');
session_start();

header('Content-Type: application/json');

$log_index1 = __DIR__ . "/log_index1.txt";
function log_index1($mensaje) {
    global $log_index1;
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, $log_index1);
}

log_index1(">>> Inicio del script get_clientes.php");

try {
    // 2) Obtener el user_id (desde GET o SESSION)
    $userId = $_GET['user_id'] 
            ?? $_SESSION['usuario_info']['id'] 
            ?? null;
    log_index1("Parámetro user_id recibido: " . var_export($userId, true));
    if (!$userId) {
        throw new Exception("Falta el parámetro user_id o no hay sesión válida");
    }

    // 3) Mapear user_id → trabajador_id
    $stmt = $pdo->prepare("SELECT trabajador_id FROM tb_usuarios WHERE id_usuarios = :uid");
    $stmt->execute([':uid' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    log_index1("tb_usuarios row: " . var_export($row, true));
    if (!$row || empty($row['trabajador_id'])) {
        throw new Exception("No se encontró trabajador asociado al usuario $userId");
    }
    $trabajadorId = $row['trabajador_id'];
    log_index1("Trabajador ID: $trabajadorId");

    // 4) Obtener clientes con nombre y moneda
    $sql = "
        SELECT
          ct.id_cliente,
          c.nombre_cliente,
          c.moneda
        FROM cliente_trabajador ct
        JOIN clientes c
          ON c.id_cliente = ct.id_cliente
        WHERE ct.id_trabajador = :tid
          AND ct.estado      = 'activo'
        ORDER BY c.moneda ASC
    ";
    $stmt2 = $pdo->prepare($sql);
    $stmt2->execute([':tid' => $trabajadorId]);
    $clientes = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    log_index1("Clientes obtenidos: " . json_encode($clientes));

    // 5) Devolver JSON
    echo json_encode([
        'clientes' => $clientes
    ]);
    log_index1("JSON enviado correctamente");
} catch (Exception $e) {
    log_index1("ERROR: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
?>

 


