<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php');

if (isset($_GET['query'])) {
    $query = '%' . $_GET['query'] . '%';  // Buscar por cualquier parte del nombre o número de documento
    $sql = "SELECT id, nombre_completo, numero_documento FROM trabajadores WHERE nombre_completo LIKE :query OR numero_documento LIKE :query";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['query' => $query]);

    $trabajadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($trabajadores);
}
?>