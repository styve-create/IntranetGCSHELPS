<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
header('Content-Type: application/json');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$accion = $data['accion'] ?? null;

try {
    if ($accion === 'crear') {
        $rol = trim($data['rol'] ?? '');
        if ($rol === '') throw new Exception('El nombre del rol no puede estar vacío.');

        $stmt = $pdo->prepare("INSERT INTO tb_roles (rol, fyh_creacion, fyh_actualizacion) VALUES (?, NOW(), NOW())");
        $stmt->execute([$rol]);

        echo json_encode(['status' => 'success', 'message' => 'Rol creado exitosamente.']);
    }

    elseif ($accion === 'editar') {
        $id = $data['id_rol'] ?? null;
        $rol = trim($data['rol'] ?? '');
        if (!$id || $rol === '') throw new Exception('Faltan datos para editar.');

        $stmt = $pdo->prepare("UPDATE tb_roles SET rol = ?, fyh_actualizacion = NOW() WHERE id_rol = ?");
        $stmt->execute([$rol, $id]);

        echo json_encode(['status' => 'success', 'message' => 'Rol actualizado correctamente.']);
    }

    elseif ($accion === 'eliminar') {
        $id = $data['id_rol'] ?? null;
        if (!$id) throw new Exception('ID no proporcionado.');

        $stmt = $pdo->prepare("DELETE FROM tb_roles WHERE id_rol = ?");
        $stmt->execute([$id]);

        echo json_encode(['status' => 'success', 'message' => 'Rol eliminado correctamente.']);
    }

    else {
        throw new Exception('Acción no válida.');
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;
?>