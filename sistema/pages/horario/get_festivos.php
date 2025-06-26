<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT fecha FROM tb_festivos ORDER BY fecha ASC");
echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));