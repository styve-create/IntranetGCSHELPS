<?php

include_once(__DIR__ . '/../../../app/controllers/config.php');

$id_campana = intval($_POST['campana_id'] ?? 0);

if ($id_campana <= 0) {
    log_index("ID de campaña no proporcionado.");
    echo json_encode([
        'ok' => false,
        'mensaje' => 'ID de campaña no proporcionado.'
    ]);
    exit;
}

// Obtener cargos disponibles para jefes directos (nivel_jerarquia = 2)
$sql_cargos = "SELECT id, nombre FROM cargos WHERE nivel_jerarquia = 2";
$stmt = $pdo->query($sql_cargos);
$cargos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener posibles trabajadores para ser asignados como jefes
$sql_trabajadores = "
        SELECT t.id, t.nombre_completo
    FROM trabajadores t
    LEFT JOIN trabajadores_campanas tc ON t.id = tc.trabajador_id AND tc.campana_id = :campana_id
    LEFT JOIN tb_jerarquia_trabajadores tj ON t.id = tj.id_trabajador AND tj.id_campana = :campana_id
    WHERE tc.campana_id IS NULL OR tj.id_trabajador IS NULL
";
$stmt = $pdo->prepare($sql_trabajadores);
$stmt->execute(['campana_id' => $id_campana]);
$trabajadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generar el HTML del formulario dinámicamente
$formularioHTML = '
<form id="formAgregarJefe">
    <input type="hidden" name="campana_id" value="' . htmlspecialchars($id_campana) . '">

    <div class="mb-3">
        <label for="trabajador_id" class="form-label">Seleccionar Trabajador</label>
        <select name="trabajador_id" id="trabajador_id" class="form-select" required>
            <option value="">-- Seleccionar --</option>';

            foreach ($trabajadores as $trabajador) {
                $formularioHTML .= '<option value="' . $trabajador['id'] . '">' . htmlspecialchars($trabajador['nombre_completo']) . '</option>';
            }

$formularioHTML .= '
        </select>
    </div>
        <label for="cargo_id" class="form-label">Cargo</label>
        <select name="cargo_id" id="cargo_id" class="form-select" required>
            <option value="">-- Seleccionar Cargo --</option>';

            foreach ($cargos as $cargo) {
                $formularioHTML .= '<option value="' . $cargo['id'] . '">' . htmlspecialchars($cargo['nombre']) . '</option>';
            }

$formularioHTML .= '
        </select>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</form>';

header('Content-Type: application/json');
echo json_encode([
    'ok' => true,
    'formulario' => $formularioHTML
]);
exit;
?>
