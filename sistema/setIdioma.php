<?php
session_name('mi_sesion_personalizada');
session_start();
$log_index1 = __DIR__ . "/log_index1.txt";
function log_index1($mensaje) {
    global $log_index1;
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, $log_index1);
}

log_index1(">>> Inicio del script setIdioma.php");

$input_raw = file_get_contents('php://input');
log_index1("Datos crudos recibidos: " . $input_raw);

$input = json_decode($input_raw, true);

if ($input === null) {
    log_index1("Error: No se pudo decodificar JSON o está vacío.");
    echo json_encode(['success' => false, 'message' => 'JSON inválido']);
    exit;
}

log_index1("JSON decodificado: " . var_export($input, true));

if (!isset($input['idioma']) || !in_array($input['idioma'], ['EN', 'ES'])) {
    log_index1("Idioma inválido recibido: " . ($input['idioma'] ?? 'NULL'));
    echo json_encode(['success' => false, 'message' => 'Idioma inválido']);
    exit;
}

$_SESSION['idioma'] = $input['idioma'];
log_index1("Idioma establecido en la sesión: " . $_SESSION['idioma']);

echo json_encode(['success' => true]);
log_index1("Respuesta enviada: success => true");
log_index1("Contenido de la sesión: " . var_export($_SESSION, true));