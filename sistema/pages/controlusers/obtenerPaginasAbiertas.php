<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php'); 
header('Content-Type: application/json');

$fecha_hoy = date('Y-m-d');

// SOLO PARA DEBUG (puedes borrar después)
$debug_sql = "SELECT id_usuario, fecha FROM tb_actividad_diaria WHERE DATE(fecha) = :fecha_hoy";
$debug_stmt = $pdo->prepare($debug_sql);
$debug_stmt->execute([':fecha_hoy' => $fecha_hoy]);
$debug_resultado = $debug_stmt->fetchAll(PDO::FETCH_ASSOC);


// CONSULTA PRINCIPAL
$sql = "SELECT 
            u.nombres AS nombre,
            DATE(p.fecha) AS fecha,
            SUM(p.total_paginas) AS total_paginas,
            SUM(p.total_cpu) AS total_cpu,
            SUM(p.total_ram) AS total_ram,
            SUM(p.cantidad_conexiones) AS conexiones
        FROM tb_actividad_diaria p
        INNER JOIN tb_usuarios u ON p.id_usuario = u.id_usuarios
        WHERE DATE(p.fecha) = :fecha_hoy
        GROUP BY u.id_usuarios, DATE(p.fecha)
        ORDER BY u.nombres ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':fecha_hoy' => $fecha_hoy]);

$paginas = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($paginas);
?>