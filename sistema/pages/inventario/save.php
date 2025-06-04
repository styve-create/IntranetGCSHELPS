<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ruta del archivo de log
$log_index = __DIR__ . '/log_index.txt';

function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

log_index("Cargado save.php");

date_default_timezone_set('America/Bogota');

require_once(__DIR__ . '/../../../app/controllers/config.php'); 
include_once(__DIR__ . '/helpers/email.php');
include_once(__DIR__ . '/helpers/enlaces_respuesta.php');
header('Content-Type: application/json');

// Generar número de formulario y token
$numero_formulario = 'EQ-' . date('YmdHis') . '-' . rand(100, 999);
$token = bin2hex(random_bytes(16));

// Recibir datos del formulario
$nombre      = $_POST['nombre'] ?? '';
$documento   = $_POST['documento'] ?? '';
$email       = $_POST['email'] ?? '';
$equipos     = $_POST['equipos'] ?? [];
$seriales    = $_POST['seriales'] ?? [];
$fecha_registro = $_POST['fecha_registro'] ?? null;

if (empty($fecha_registro)) {
    $fecha_registro = date('Y-m-d '); // Usa la fecha actual si no viene ninguna
} else {
    $timestamp = strtotime($fecha_registro);
    if ($timestamp !== false) {
        $fecha_registro = date('Y-m-d ', $timestamp);
    } else {
        $fecha_registro = date('Y-m-d '); // En caso de error al parsear
    }
}
// Guardar equipos y seriales como JSON
$equipos_json = json_encode($equipos);
$seriales_json = json_encode($seriales);

// Preparar tabla HTML para el correo
$tablaEquipos = '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; width:100%">';
$tablaEquipos .= '<thead><tr><th>Equipo</th><th>Serial</th></tr></thead><tbody>';
foreach ($equipos as $equipo) {
    $serial = isset($seriales[$equipo]) ? htmlspecialchars($seriales[$equipo]) : 'N/A';
    $tablaEquipos .= "<tr><td>" . ucfirst(htmlspecialchars($equipo)) . "</td><td>{$serial}</td></tr>";
}
$tablaEquipos .= '</tbody></table>';

// Crear enlaces de aprobación/denegación
$aprobadoUrl = generarEnlaceRespuesta($numero_formulario, 'aprobado', $token);
$denegadoUrl = generarEnlaceRespuesta($numero_formulario, 'rechazado', $token);

// Contenido del correo
$mensaje = "
<div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9;'>
    <h2 style='color: #007BFF;'>Formulario de Asignación de Equipos</h2>
    <p><strong>Número de formulario:</strong> {$numero_formulario}</p>
    <p><strong>Nombre:</strong> {$nombre}</p>
    <p><strong>Documento:</strong> {$documento}</p>
    <p><strong>Email:</strong> {$email}</p>
    <p><strong>Fecha de Registro:</strong> {$fecha_registro}</p>

    <h3 style='margin-top: 30px;'>Equipos asignados</h3>
    {$tablaEquipos}

    <div style='margin-top: 30px; text-align: center;'>
        <a href='{$aprobadoUrl}' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Aprobar</a>
    <a href='{$denegadoUrl}' style='padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Denegar</a>
    </div>
</div>
";

// Insertar en base de datos
try {
    $fecha_trabajador = null;
    

    $stmt = $pdo->prepare("INSERT INTO formularios_asignacion 
        (numero_formulario, nombre, documento, email, equipos, seriales, fecha_registro, estado_trabajador, fecha_trabajador, token, estado_formulario)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $estado_trabajador = 'pendiente';
    $estado_formulario = 'pendiente';

if (!$stmt->execute([
    $numero_formulario,
    $nombre,
    $documento,
    $email,
    $equipos_json,
    $seriales_json,
    $fecha_registro, 
    $estado_trabajador,
    $fecha_trabajador,
    $token,
    $estado_formulario
])) {
    throw new Exception("Error al guardar en la base de datos");
}

    // Enviar correo
    enviarCorreo($email, "Equipos - Formulario #{$numero_formulario}", $mensaje);

    echo json_encode([
        'status' => 'success',
        'numero_formulario' => $numero_formulario
    ]);
    exit;

} catch (Exception $e) {
    log_index("Error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Ocurrió un error al procesar el formulario.'
    ]);
    exit;
}
?>