<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');

header('Content-Type: application/json');

// === LISTAR DATOS PARA DATATABLE ===
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['listar'])) {
    $items = $pdo->query("SELECT * FROM tb_carrusel ORDER BY orden ASC")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($items);
    exit;
}

try {
    $id = $_POST['id'] ?? null;
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? null;
    $imagen_url = '';

    // Validar campos requeridos
    if (empty($titulo) || empty($descripcion) || empty($fecha_inicio) || empty($fecha_fin)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
        exit;
    }

    // Validar imagen si se ha subido
    if (!empty($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $permitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $tipoMime = mime_content_type($_FILES['imagen']['tmp_name']) ?: '';
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($tipoMime, $permitidos)) {
            echo json_encode(['success' => false, 'message' => 'Tipo de imagen no permitido.']);
            exit;
        }

        if ($_FILES['imagen']['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'La imagen excede el tamaño máximo permitido (2MB).']);
            exit;
        }

        // Ruta de guardado
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $nombreSeguro = uniqid('img_', true) . '.' . $ext;

        $carpeta_destino = __DIR__ . '/uploads/carrusel/';
        if (!is_dir($carpeta_destino)) {
            mkdir($carpeta_destino, 0777, true);
        }

        $ruta_absoluta = $carpeta_destino . $nombreSeguro;
        $ruta_relativa = '/sistema/pages/informacion/uploads/carrusel/' . $nombreSeguro;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_absoluta)) {
            echo json_encode(['success' => false, 'message' => 'No se pudo guardar la imagen.']);
            exit;
        }

        $imagen_url = $ruta_relativa;
    }

    // === Actualizar o insertar ===
    if ($id) {
        $sql = "UPDATE tb_carrusel 
                SET titulo = ?, descripcion = ?, fecha_inicio = ?, fecha_fin = ?, 
                    imagen_url = IF(? != '', ?, imagen_url)
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $titulo,
            $descripcion,
            $fecha_inicio,
            $fecha_fin,
            $imagen_url,
            $imagen_url,
            $id
        ]);
    } else {
        $sql = "INSERT INTO tb_carrusel (titulo, descripcion, imagen_url, estado, fecha_inicio, fecha_fin) 
                VALUES (?, ?, ?, 'activo', ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $titulo,
            $descripcion,
            $imagen_url,
            $fecha_inicio,
            $fecha_fin
        ]);
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar: ' . $e->getMessage()
    ]);
}