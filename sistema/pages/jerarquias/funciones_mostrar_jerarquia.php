<?php
// Función recursiva para mostrar jerarquía de campañas
function mostrarJerarquiaCampana($campana_id, $pdo) 
{
  
    // Obtener jefes directos de la campaña
    $sql = "SELECT c.id as campana_id, c.nombre as campana_nombre, t.id as jefe_id, t.nombre_completo 
            FROM tb_campanas c
            JOIN trabajadores_campanas tc ON c.id = tc.campana_id
            JOIN trabajadores t ON t.id = tc.trabajador_id
            JOIN cargos cg ON tc.cargo_id = cg.id
            WHERE c.id = :campana_id AND cg.nivel_jerarquia = 2";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['campana_id' => $campana_id]);
    $jefes_directos = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
 
    echo "<ul class='list-group list-group-flush'>";
    foreach ($jefes_directos as $jefe) {
        echo "<br>";
        echo "<li class='list-group-item list-group-item-primary' draggable='true' data-tipo='jefe' data-cargo='jefe' data-id='{$jefe['jefe_id']}' data-campana='{$jefe['campana_id']}'>";
        echo "<details class='mb-3' data-id='jefe-{$jefe['jefe_id']}'>";
        echo "<summary><i class='fas fa-briefcase'></i> " . htmlspecialchars($jefe['nombre_completo']) . " (Jefe Directo)</summary>";

        // Botones de acción para el jefe
        echo "<div class='action-buttons my-2'>";
        echo "<button class='edit-btn btn btn-sm btn-warning me-1' data-accion='editar_jefe' data-jefe='{$jefe['jefe_id']}' data-campana='{$jefe['campana_id']}'><i class='fas fa-edit'></i> Editar</button>";
        echo "<button class='delete-btn btn btn-sm btn-danger me-1' data-accion='eliminar_jefe' data-jefe='{$jefe['jefe_id']}' data-campana='{$jefe['campana_id']}'><i class='fas fa-trash-alt'></i> Eliminar</button>";
        echo "<button class='add-btn btn btn-sm btn-success' data-accion='agregar_trabajador' data-jefe='{$jefe['jefe_id']}' data-campana='{$jefe['campana_id']}'><i class='fas fa-user-plus'></i> Agregar Trabajador</button>";
        echo "<button class='add-btn btn btn-sm btn-warning' data-accion='agregar_campana' data-jefe='{$jefe['jefe_id']}' data-campana='{$jefe['campana_id']}'><i class='fas fa-user-plus'></i> Agregar Campaña</button>";
        echo "</div>";

        // Obtener trabajadores del jefe
        $sql_t = "SELECT t.id, t.nombre_completo
                  FROM trabajadores t
                  JOIN tb_jerarquia_trabajadores jt ON t.id = jt.id_trabajador
                  WHERE jt.id_jefe = :id_jefe AND jt.id_campana = :campana_id";
        $stmt_t = $pdo->prepare($sql_t);
        $stmt_t->execute([
            'id_jefe' => $jefe['jefe_id'],
            'campana_id' => $jefe['campana_id']
        ]);
        $trabajadores = $stmt_t->fetchAll(PDO::FETCH_ASSOC);

        if ($trabajadores) {
            echo "<ul class='list-group'>";
            foreach ($trabajadores as $trabajador) {
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                echo "👤 " . htmlspecialchars($trabajador['nombre_completo']);
                echo "<div class='action-buttons'>";
                echo "<button class='delete-btn btn btn-sm btn-outline-danger' data-accion='eliminar_trabajador' data-jefe='{$jefe['jefe_id']}' data-campana='{$jefe['campana_id']}' data-trabajador='{$trabajador['id']}'><i class='fas fa-trash-alt'></i> Eliminar</button>";
                echo "</div>";
                echo "</li>";
            }
            echo "</ul>";
        }


        // Subcampañas donde este jefe es responsable
         $sql_sub = "SELECT id, nombre FROM tb_campanas WHERE id_responsable = :id_jefe AND id_padre = :id_padre";
        $stmt_sub = $pdo->prepare($sql_sub);
        $stmt_sub->execute([
            'id_jefe' => $jefe['jefe_id'],
            'id_padre' => $campana_id
        ]);
        $campanas_sub = $stmt_sub->fetchAll(PDO::FETCH_ASSOC);

        foreach ($campanas_sub as $sub) {
            echo "<li class='list-group-item' data-tipo='subcampana' draggable='true' data-id='{$sub['id']}' data-campana='{$sub['id']}' data-jefe='{$jefe['jefe_id']}'>";
           echo "<details class='ms-3 mt-2' data-id='subcampana-{$sub['id']}'>";
            echo "<summary><i class='fas fa-leaf'></i> " . htmlspecialchars($sub['nombre']) . " (Subcampaña)
                  <button class='add-btn btn btn-sm btn-primary ms-2' data-accion='agregar_jefe'  data-campana='{$sub['id']}'>
                      <i class='fas fa-user-tie'></i> Agregar Jefe Directo
                  </button>
                  <button class='delete-btn btn btn-sm btn-danger me-1' data-accion='eliminar_campana' data-jefe='{$jefe['jefe_id']}' data-campana='{$sub['id']}'><i class='fas fa-trash-alt'></i> Eliminar Campaña</button>
                  </summary>";
            mostrarJerarquiaCampana($sub['id'], $pdo); // llamada recursiva
            echo "</details>";
            echo "</li>";
        }

        echo "</details>";
        echo "</li>";
    }
    echo "</ul>";
    
$sql_trabajadores_sin_jefe = "
    SELECT DISTINCT t.id, t.nombre_completo
    FROM trabajadores t
    INNER JOIN trabajadores_campanas tc ON t.id = tc.trabajador_id
    LEFT JOIN tb_jerarquia_trabajadores jt 
        ON t.id = jt.id_trabajador AND tc.campana_id = jt.id_campana
    WHERE jt.id_jefe IS NULL AND tc.campana_id = :campana_id
";
$stmt = $pdo->prepare($sql_trabajadores_sin_jefe);
$stmt->execute(['campana_id' => $campana_id]);
$trabajadores_sin_jefe = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($trabajadores_sin_jefe) {
    echo "<h5 class='mt-4'>Trabajadores sin jefe:</h5>";
    echo "<ul class='list-group'>";
    foreach ($trabajadores_sin_jefe as $trabajador) {
        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
        echo "👤 " . htmlspecialchars($trabajador['nombre_completo']);
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<div class='mt-4 alert alert-success'>Todos los trabajadores tienen jefe asignado en sus campañas.</div>";
}
}
?>