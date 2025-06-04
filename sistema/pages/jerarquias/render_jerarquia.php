<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);


include_once(__DIR__ . '/../../analisisRecursos.php');
require_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/funciones_mostrar_jerarquia.php');


try {
    $sql = "SELECT * FROM tb_campanas WHERE id_responsable IS NULL";
    $campanas_raiz = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    

    ob_start(); // Captura el HTML generado
    foreach ($campanas_raiz as $campana) {
        echo "<ul class='list-group list-group-flush'>";
        echo "<li class='list-group-item list-group-item-info' draggable='true' data-tipo='campana' data-jerarquia='1' data-campana='{$campana['id']}'>";
        echo "<details>";
        echo "<summary><i class='fas fa-globe-americas'></i> " . htmlspecialchars($campana['nombre']) . " (Campaña Principal)";
        echo "<button class='add-btn btn btn-sm btn-primary ms-2' data-accion='agregar_jefe' data-jefe='' data-campana='{$campana['id']}'><i class='fas fa-user-tie'></i> Agregar Jefe Directo</button>";
        echo "</summary>";
        mostrarJerarquiaCampana($campana['id'], $pdo);
        echo "</details>";
        echo "</li>";
        echo "</ul>";
    }
    $html = ob_get_clean();

    echo json_encode([
        'ok' => true,
        'html' => $html
    ]);
} catch (Exception $e) {
    echo json_encode([
        'ok' => false,
        'mensaje' => $e->getMessage()
    ]);
}
?>