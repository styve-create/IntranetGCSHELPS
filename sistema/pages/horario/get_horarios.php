<?php
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php');
header('Content-Type: application/json');

$sql = "
SELECT h.*, t.nombre_completo
FROM tb_horario h
JOIN tb_usuarios u ON h.id_usuario = u.id_usuarios
JOIN trabajadores t ON u.trabajador_id = t.id
ORDER BY h.fecha DESC
";

$stmt = $pdo->query($sql);
$horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($horarios);
exit;

?>