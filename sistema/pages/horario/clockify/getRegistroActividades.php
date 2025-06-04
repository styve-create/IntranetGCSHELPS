<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../../app/controllers/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

$id_usuario = $_SESSION['usuario_info']['id'] ?? null;

$fechaHoy = date('Y-m-d');
$inicioDia = $fechaHoy . ' 00:00:00';
$finDia = $fechaHoy . ' 23:59:59';
$today = new DateTime();
$today->setTime(0, 0);

// Establecer inicio de semana (lunes)
$startOfWeek = clone $today;
$startOfWeek->modify('monday this week');

// Establecer fin de semana (domingo)
$endOfWeek = clone $startOfWeek;
$endOfWeek->modify('+6 days');
$endOfWeek->setTime(23, 59, 59);

$inicioSemana = $startOfWeek->format('Y-m-d H:i:s');
$finSemana = $endOfWeek->format('Y-m-d H:i:s');

try {
    
   $stmtHoy = $pdo->prepare("SELECT * FROM tb_actividades WHERE id_usuario = ? AND fecha BETWEEN ? AND ? ORDER BY fecha DESC, hora_inicio DESC");
    $stmtHoy->execute([$id_usuario, $inicioDia, $finDia]);
    $actividadesHoy = $stmtHoy->fetchAll(PDO::FETCH_ASSOC);

    // Actividades de la semana
   $stmtSemana = $pdo->prepare("SELECT * FROM tb_actividades WHERE id_usuario = ? AND fecha BETWEEN ? AND ? ORDER BY fecha DESC");
    $stmtSemana->execute([$id_usuario, $inicioSemana, $finSemana]);
    $actividadesSemana = $stmtSemana->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'hoy' => $actividadesHoy,
        'semana' => $actividadesSemana
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

?>