<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php'); // Aquí asegúrate que $pdo esté definido si cambiaste

header('Content-Type: application/json');

$sql = "SELECT u.nombres AS nombre, p.fecha, p.total_paginas, p.total_cpu, p.total_ram, p.cantidad_conexiones AS conexiones
        FROM tb_actividad_diaria p
        INNER JOIN tb_usuarios u ON p.id_usuario = u.id_usuarios
        ORDER BY p.fecha DESC";

$stmt = $pdo->query($sql);
$paginas = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($paginas);

?>