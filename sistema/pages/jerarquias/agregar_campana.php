<?php

include_once(__DIR__ . '/../../../app/controllers/config.php');

$id_padre = intval($_POST['id_padre'] ?? 0);
$id_responsable = intval($_POST['id_responsable'] ?? 0);

if ($id_padre <= 0 || $id_responsable <= 0) {
    echo json_encode([
        'ok' => false,
        'mensaje' => 'Faltan datos para crear la nueva campaña.'
    ]);
    exit;
}

// Obtener nombre de campaña padre
$stmt = $pdo->prepare("SELECT nombre FROM tb_campanas WHERE id = ?");
$stmt->execute([$id_padre]);
$nombre_padre = $stmt->fetchColumn() ?: 'Desconocida';

// Obtener nombre del jefe responsable
$stmt = $pdo->prepare("SELECT nombre_completo FROM trabajadores WHERE id = ?");
$stmt->execute([$id_responsable]);
$nombre_responsable = $stmt->fetchColumn() ?: 'Desconocido';

// Generar HTML del formulario
$formularioHTML = '
<form id="formAgregarCampana">
    <input type="hidden" name="id_padre" value="' . $id_padre . '">
    <input type="hidden" name="id_responsable" value="' . $id_responsable . '">

    <div class="mb-6">
        <label class="form-label fw-bold">Campaña padre:</label>
        <div class="form-control-plaintext">' . htmlspecialchars($nombre_padre) . '</div>
         <label class="form-label fw-bold">Responsable:</label>
        <div class="form-control-plaintext">' . htmlspecialchars($nombre_responsable) . '</div>
        <label for="nombre" class="form-label">Nombre de la nueva campaña</label>
        <input type="text" name="nombre" id="nombre" class="form-control" required>
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea name="descripcion" id="descripcion" class="form-control" rows="1" required></textarea>
         <label for="estado" class="form-label">Estado</label>
        <select name="estado" id="estado" class="form-select" required>
            <option value="">-- Seleccionar Estado --</option>
            <option value="activa">Activa</option>
            <option value="inactiva">Inactiva</option>
        </select>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar Campaña</button>
    </div>
</form>';

header('Content-Type: application/json');
echo json_encode([
    'ok' => true,
    'formulario' => $formularioHTML
]);