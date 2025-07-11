<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');

header('Content-Type: application/json');

$sql = "SELECT 
    a.*, 
    t.nombre_completo, 
    t.numero_documento, 
    c.nombre AS nombre_campana, 
    j.nombre_completo AS nombre_jefe
FROM tb_ausencias a
LEFT JOIN trabajadores t ON t.id = a.id_trabajador
LEFT JOIN tb_campanas c ON c.id = a.id_campana
LEFT JOIN trabajadores j ON j.id = a.id_jefe
ORDER BY a.id DESC";

$ausencias = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

foreach ($ausencias as &$a) {
    if (isset($a['tareas']) && is_string($a['tareas'])) {
        $decoded = json_decode($a['tareas'], true);
        $a['tareas'] = is_array($decoded) ? $decoded : [];
    } else {
        $a['tareas'] = [];
    }
}
unset($a);

echo json_encode($ausencias);
?>
