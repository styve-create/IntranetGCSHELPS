<?php

require_once(__DIR__ . '/../../../app/controllers/config.php');

$response = ['ok' => false, 'mensaje' => ''];

try {
    log_index("Iniciando mover elemento");

    $tipo = $_POST['tipo']; // 'subcampana' o 'jefe'
    $id = intval($_POST['id']); // ID del elemento a mover
    $origen = intval($_POST['origen_campana']); // ID de la campaña actual
    $destino = intval($_POST['destino_campana']); // ID de la campaña destino
    $destino_jefe = isset($_POST['destino_jefe']) ? intval($_POST['destino_jefe']) : null; // ID del nuevo jefe directo

    

    if ($tipo === 'subcampana') {
        log_index("Procesando movimiento de subcampaña");

        if (!$destino_jefe) {
            $response['mensaje'] = 'Debes seleccionar un jefe directo válido para mover una subcampaña.';
            log_index("Error: No se envió un destino_jefe.");
        } else {
            // Validar que el jefe pertenezca a la campaña destino
            log_index("Validando si el jefe $destino_jefe pertenece a la campaña $destino...");
            $sql_validar_jefe = "SELECT COUNT(*) FROM trabajadores_campanas WHERE trabajador_id = :jefe_id AND campana_id = :campana_id";
            $stmt_validar_jefe = $pdo->prepare($sql_validar_jefe);
            $stmt_validar_jefe->execute([
                'jefe_id' => $destino_jefe,
                'campana_id' => $destino
            ]);
            $jefe_valido = $stmt_validar_jefe->fetchColumn();

            if (!$jefe_valido) {
                $response['mensaje'] = 'El jefe seleccionado no pertenece a la campaña destino.';
                log_index("Error: El jefe $destino_jefe no pertenece a la campaña $destino.");
            } else {
                // Verificar que la campaña destino exista
                log_index("Verificando existencia de la campaña destino $destino...");
                $sql_check_destino = "SELECT id FROM tb_campanas WHERE id = :destino";
                $stmt_check_destino = $pdo->prepare($sql_check_destino);
                $stmt_check_destino->execute(['destino' => $destino]);
                $campana_destino = $stmt_check_destino->fetch(PDO::FETCH_ASSOC);

            if (!$campana_destino) {
    $response['mensaje'] = 'La campaña destino no existe.';
    log_index("Error: La campaña destino $destino no existe.");
} elseif ($id == $destino) {
    $response['mensaje'] = 'Una subcampaña no puede ser su propia campaña padre.';
    log_index("Error: La subcampaña ID $id no puede ser su propio padre.");
} else {
    log_index("Asignando subcampaña ID $id al nuevo jefe ID $destino_jefe y nueva campaña padre ID $destino...");

    // Actualizar tanto el responsable como la relación de jerarquía
    $sql_update_subcampana = "UPDATE tb_campanas 
                              SET id_responsable = :nuevo_responsable, id_padre = :nueva_campana_padre 
                              WHERE id = :subcampana_id";
    $stmt_update_subcampana = $pdo->prepare($sql_update_subcampana);
    $stmt_update_subcampana->execute([
        'nuevo_responsable' => $destino_jefe,
        'nueva_campana_padre' => $destino,
        'subcampana_id' => $id
    ]);

    log_index("Subcampaña ID $id ahora pertenece al jefe ID $destino_jefe y está bajo la campaña ID $destino.");

    $response['ok'] = true;
    $response['mensaje'] = 'Subcampaña movida correctamente.';
}
            }
        }
}elseif ($tipo === 'jefe') {
    log_index("Procesando movimiento de jefe");

    // Verificar si el jefe está siendo asignado como subordinado de otro jefe directo
    if ($destino_jefe) {
        // Verificar que el jefe no esté siendo asignado a otro jefe directo
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM tb_jerarquia_trabajadores WHERE id_trabajador = :jefe AND id_jefe = :nuevo_jefe");
        $stmt_check->execute(['jefe' => $id, 'nuevo_jefe' => $destino_jefe]);
        if ($stmt_check->fetchColumn() > 0) {
            $response['mensaje'] = 'Error: Un jefe directo no puede estar bajo otro jefe directo.';
            log_index("Error: Jefe ID $id no puede estar bajo jefe directo ID $destino_jefe.");
            echo json_encode($response);
            exit;
        }
    }

    // Mover al jefe a la nueva campaña en la tabla trabajadores_campanas
    $sql = "UPDATE trabajadores_campanas SET campana_id = :nueva_campana WHERE trabajador_id = :jefe_id AND campana_id = :origen";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nueva_campana' => $destino, 'jefe_id' => $id, 'origen' => $origen]);
    log_index("Jefe ID $id movido de campaña $origen a campaña $destino en trabajadores_campanas.");

    // Mover al jefe a la nueva jerarquía en la tabla tb_jerarquia_trabajadores
    $sql_jerarquia = "UPDATE tb_jerarquia_trabajadores SET id_campana = :nueva_campana WHERE id_trabajador = :jefe_id AND id_campana = :origen";
    $stmt_jerarquia = $pdo->prepare($sql_jerarquia);
    $stmt_jerarquia->execute(['nueva_campana' => $destino, 'jefe_id' => $id, 'origen' => $origen]);
    log_index("Jefe ID $id movido a la jerarquía de campaña $destino en tb_jerarquia_trabajadores.");

    // ✅ CORREGIDO: Actualizar el id_padre de las subcampañas asociadas al jefe
    $sql_update_subcampanas = "UPDATE tb_campanas SET id_padre = :nueva_campana WHERE id_responsable = :jefe_id AND id_padre = :campana_origen";
    $stmt_update_subcampanas = $pdo->prepare($sql_update_subcampanas);
    $stmt_update_subcampanas->execute([
        'nueva_campana' => $destino,        // campaña válida
        'jefe_id' => $id,                   // jefe que tiene esas subcampañas
        'campana_origen' => $origen        // campaña actual de origen
    ]);
    log_index("Subcampañas con jefe ID $id actualizadas a nueva campaña padre ID $destino.");

    // Mover también a los subordinados en la jerarquía
    $sql_subordinados = "SELECT id_trabajador FROM tb_jerarquia_trabajadores WHERE id_jefe = :jefe_id AND id_campana = :origen";
    $stmt = $pdo->prepare($sql_subordinados);
    $stmt->execute(['jefe_id' => $id, 'origen' => $origen]);
    $subordinados = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($subordinados as $sub_id) {
        // Mover al subordinado a la nueva campaña en trabajadores_campanas
        $sql_update_sub = "UPDATE trabajadores_campanas SET campana_id = :nueva_campana WHERE trabajador_id = :trabajador_id AND campana_id = :origen";
        $stmt_update = $pdo->prepare($sql_update_sub);
        $stmt_update->execute([
            'nueva_campana' => $destino,
            'trabajador_id' => $sub_id,
            'origen' => $origen
        ]);
        log_index("Subordinado $sub_id movido a campaña $destino en trabajadores_campanas.");

        // Mover al subordinado en la jerarquía en tb_jerarquia_trabajadores
        $sql_update_sub_jerarquia = "UPDATE tb_jerarquia_trabajadores SET id_campana = :nueva_campana WHERE id_trabajador = :trabajador_id AND id_campana = :origen";
        $stmt_update_jerarquia = $pdo->prepare($sql_update_sub_jerarquia);
        $stmt_update_jerarquia->execute([
            'nueva_campana' => $destino,
            'trabajador_id' => $sub_id,
            'origen' => $origen
        ]);
        log_index("Subordinado $sub_id movido a jerarquía de campaña $destino en tb_jerarquia_trabajadores.");
    }

    $response['ok'] = true;
    $response['mensaje'] = 'Jefe y su jerarquía movidos correctamente.';
} else {
    $response['mensaje'] = 'Tipo de movimiento no válido.';
    log_index("Error: Tipo de movimiento no válido.");
}
} catch (Exception $e) {
    $response['mensaje'] = 'Error: ' . $e->getMessage();
    log_index("Error: " . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>