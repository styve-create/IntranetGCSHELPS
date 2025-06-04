<?php
$log_index = __DIR__ . '/log_index.txt';

function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - [JS] $mensaje\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['mensaje'])) {
        log_index($data['mensaje']);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se recibió mensaje']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}