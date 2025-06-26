<?php
header('Content-Type: application/json');
session_start();
date_default_timezone_set('America/Bogota');
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../../app/controllers/config.php');

try {
    // 1) Obtener el user_id (desde GET o SESSION)
    $userId = $_GET['user_id'] 
            ?? $_SESSION['id_usuario'] 
            ?? null;
    if (!$userId) {
        throw new Exception("Falta el parámetro user_id o no hay sesión válida");
    }

    // 2) Mapear user_id → trabajador_id
    $stmt = $pdo->prepare("SELECT trabajador_id FROM tb_usuarios WHERE id_usuarios = :uid");
    $stmt->execute([':uid' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row || empty($row['trabajador_id'])) {
        throw new Exception("No se encontró trabajador asociado al usuario $userId");
    }
    $trabajadorId = $row['trabajador_id'];

    // 3) Consultar todas las actividades (sin filtrar)
    $stmtAll = $pdo->query("SELECT * FROM actividades");
    $actividades = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

    // 4) Obtener clientes activos para ese trabajador
    $stmt = $pdo->prepare("
        SELECT ct.id_cliente
        FROM cliente_trabajador ct
        WHERE ct.id_trabajador = :tid
          AND ct.estado      = 'activo'
    ");
    $stmt->execute([':tid' => $trabajadorId]);
    $idsAsignados = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_cliente');

    // 5) Si no tiene clientes, devolvemos la estructura vacía
    if (empty($idsAsignados)) {
        echo json_encode([
          'actividades' => $actividades,
          'estructura'   => []
        ]);
        exit;
    }

    // 6) Preparamos el IN(...) para filtrar
    $placeholders = implode(',', array_fill(0, count($idsAsignados), '?'));

    // 7) Consulta de estructura filtrada
    $sql = "
      SELECT c.nombre_cliente, a.nombre_actividad
      FROM actividades a
      JOIN clientes c ON a.id_cliente = c.id_cliente
      WHERE a.id_cliente IN ($placeholders)
      ORDER BY c.nombre_cliente, a.nombre_actividad
    ";
    $stmt2 = $pdo->prepare($sql);
    $stmt2->execute($idsAsignados);
    $result = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // 8) Agrupar en cliente → [actividades]
    $estructura = [];
    foreach ($result as $r) {
        $estructura[$r['nombre_cliente']][] = $r['nombre_actividad'];
    }

    // 9) Devolver ambas cosas
    echo json_encode([
        'actividades' => $actividades,
        'estructura'   => $estructura
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
?>