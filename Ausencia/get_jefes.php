<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../app/controllers/config.php');

if (!empty($_POST['id_campana']) && !empty($_POST['documento'])) {
    $id_campana = $_POST['id_campana'];
    $documento = $_POST['documento'];

    // 1. Buscar ID del trabajador
    $stmt = $pdo->prepare("SELECT id FROM trabajadores WHERE numero_documento = ? AND estado = 'Activo'");
    $stmt->execute([$documento]);
    $trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trabajador) {
        echo '<option value="">Trabajador no encontrado o inactivo</option>';
        exit;
    }

    $trabajador_id = $trabajador['id'];

    // 2. Verificar si el trabajador está directamente asignado a la campaña
    $stmt = $pdo->prepare("
        SELECT tc.cargo_id
        FROM trabajadores_campanas tc
        WHERE tc.trabajador_id = ? AND tc.campana_id = ?
    ");
    $stmt->execute([$trabajador_id, $id_campana]);
    $relacion = $stmt->fetch(PDO::FETCH_ASSOC);

    $asignado_directamente = $relacion ? true : false;
    $es_jefe = $relacion && in_array($relacion['cargo_id'], [1, 2]);

    // ✅ NUEVA VERIFICACIÓN: si no está asignado directamente, verificar si pertenece al responsable de esta campaña
    if (!$asignado_directamente) {
        // Buscar el responsable de la campaña
        $stmt = $pdo->prepare("SELECT id_responsable FROM tb_campanas WHERE id = ?");
        $stmt->execute([$id_campana]);
        $campana_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$campana_info || empty($campana_info['id_responsable'])) {
            echo '<option value="">El responsable de esta campaña no está definido</option>';
            exit;
        }

        $id_responsable = $campana_info['id_responsable'];

        // Verificar si el trabajador depende del responsable (mismo id_campana)
        $stmt = $pdo->prepare("
            SELECT 1
            FROM trabajadores_campanas tc
            WHERE tc.trabajador_id = ? AND tc.campana_id = ?
        ");
        $stmt->execute([$trabajador_id, $id_campana]);
        $subordinado = $stmt->fetchColumn();

        if (!$subordinado) {
            echo '<option value="">No estás asignado a esta campaña ni perteneces a su responsable</option>';
            exit;
        }
    }

    // 3. Si NO es jefe, buscar jefes directos en la misma campaña
    if (!$es_jefe) {
        $stmt = $pdo->prepare("
            SELECT t.id, t.nombre_completo, c.nombre AS nombre_cargo
            FROM trabajadores_campanas tc
            INNER JOIN trabajadores t ON tc.trabajador_id = t.id
            INNER JOIN cargos c ON tc.cargo_id = c.id
            WHERE tc.campana_id = ?
              AND tc.cargo_id IN (1, 2)
              AND t.estado = 'Activo'
        ");
        $stmt->execute([$id_campana]);
        $jefes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($jefes)) {
            echo '<option value="">Selecciona un jefe</option>';
            foreach ($jefes as $jefe) {
                echo '<option value="' . htmlspecialchars($jefe['nombre_completo']) . '">' .
                     htmlspecialchars($jefe['nombre_completo']) . 
                     '</option>';
            }
            exit;
        }
    }

    // 4. Si es jefe, o no se encontraron jefes directos → buscar jefe superior jerárquico
    $campana_actual = $id_campana;
    while ($campana_actual) {
        $stmt = $pdo->prepare("SELECT id_padre, id_responsable FROM tb_campanas WHERE id = ?");
        $stmt->execute([$campana_actual]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['id_responsable'])) {
            $id_responsable = $row['id_responsable'];

            // Obtener jefe jerárquico
            $stmt = $pdo->prepare("SELECT id, nombre_completo FROM trabajadores WHERE id = ? AND estado = 'Activo'");
            $stmt->execute([$id_responsable]);
            $jefe_superior = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($jefe_superior) {
                echo '<option value="">Jefe jerárquico superior</option>';
                echo '<option value="' . htmlspecialchars($jefe_superior['nombre_completo']) . '">' .
                     htmlspecialchars($jefe_superior['nombre_completo']) . 
                     '</option>';
                exit;
            }
        }

        if (empty($row['id_padre'])) {
            break;
        }

        $campana_actual = $row['id_padre'];
    }

    echo '<option value="">No se encontró ningún jefe en esta campaña ni en jerarquías superiores</option>';
} else {
    echo '<option value="">Error: faltan datos</option>';
}
?>