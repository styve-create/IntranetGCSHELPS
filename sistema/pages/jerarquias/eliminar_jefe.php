<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');

$jefe_id = intval($_POST['jefe_id'] ?? 0);
$campana_id = intval($_POST['campana_id'] ?? 0);

if ($jefe_id <= 0 || $campana_id <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'Datos incompletos.']);
    exit;
}

$formularioHTML = '
<form id="formEliminarJefe" method="POST">
    <input type="hidden" name="jefe_id" value="' . $jefe_id . '">
    <input type="hidden" name="campana_id" value="' . $campana_id . '">

    <div class="mb-4">
        <h5 class="fw-bold text-danger">Eliminar Jefe</h5>
        <p class="text-muted">
            ¿Estás seguro de que deseas eliminar este jefe de la campaña? 
            <span class="text-danger fw-semibold">Esta acción eliminará su jerarquía y desvinculará a todos los trabajadores asociados.</span>
        </p>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <button type="submit" class="btn btn-danger">
            Sí, eliminar
        </button>
    </div>
</form>';

echo json_encode([
    'ok' => true,
    'formulario' => $formularioHTML
]);

?>