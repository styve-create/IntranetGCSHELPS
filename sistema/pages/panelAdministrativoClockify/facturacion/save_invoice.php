<?php
// save_invoice.php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../../app/controllers/config.php');
header('Content-Type: application/json');

try {
    // 1) Leer JSON del body
    $payload = json_decode(file_get_contents('php://input'), true);
    if (!$payload) {
        throw new Exception('JSON inválido');
    }

    // 2) Validar campos requeridos
    foreach (['invoiceNumber','fechaInicio','fechaFin','billFrom','billTo','discountPct','items'] as $f) {
        if (!isset($payload[$f])) {
            throw new Exception("Falta el campo $f");
        }
    }

    // 3) Insertar en facturas
    $stmt = $pdo->prepare("
        INSERT INTO facturas
          (invoice_number, fecha_inicio, fecha_fin, bill_from, bill_to, discount_pct, notes)
        VALUES
          (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $payload['invoiceNumber'],
        date('Y-m-d', strtotime($payload['fechaInicio'])),
        date('Y-m-d', strtotime($payload['fechaFin'])),
        $payload['billFrom'],
        $payload['billTo'],
        $payload['discountPct'],
        $payload['notes'] ?? ''
    ]);
    $facturaId = $pdo->lastInsertId();

    // 4) Insertar cada ítem
    $stmtItem = $pdo->prepare("
        INSERT INTO factura_items
          (factura_id, actividad, descripcion, duracion, precio_unitario, subtotal)
        VALUES
          (?, ?, ?, ?, ?, ?)
    ");
    foreach ($payload['items'] as $item) {
        $stmtItem->execute([
            $facturaId,
            $item['actividad'],
            $item['descripcion'],
            $item['duracion'],
            $item['precioUnitario'],
            $item['subtotal']
        ]);
    }

    // 5) Responder éxito
    echo json_encode(['success' => true, 'factura_id' => $facturaId]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}