<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Bogota');

include_once(__DIR__ . '/../app/controllers/config.php');
include_once(__DIR__ . '/helpers/email.php');
include_once(__DIR__ . '/helpers/enlaces_respuesta.php');
header('Content-Type: application/json');

// Función de log personalizada
function logDebug($message) {
    file_put_contents(__DIR__ . '/debug_respuesta.txt', date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

// Log inicial
logDebug("🔄 Iniciando proceso de actualización de ausencia");



try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    logDebug("📥 Datos recibidos por POST: " . print_r($_POST, true));

    $nombre = $_POST['nombre_completo'] ?? '';
    $documento = $_POST['documento'] ?? '';
    $email = $_POST['email'] ?? '';
    $id_campana = $_POST['id_campana'] ?? null;
    $tipo_ausencia = $_POST['tipo_ausencia'] ?? '';
    $rango = isset($_POST['rango_fechas']) ? explode(' - ', $_POST['rango_fechas']) : [];
    $fecha_inicio = $rango[0] ?? '';
    $fecha_fin = $rango[1] ?? $rango[0] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';
    $nombre_jefe = $_POST['nombre_jefe'] ?? '';

    // Validaciones
    if (!$nombre || !$documento || !$email || !$id_campana || !$tipo_ausencia || !$fecha_inicio || !$fecha_fin) {
        throw new Exception("❗ Faltan campos requeridos");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("❗ Correo electrónico no válido: $email");
    }

    logDebug("🔎 Buscando trabajador con documento: $documento");
    $stmt = $pdo->prepare("SELECT id FROM trabajadores WHERE numero_documento = ?");
    $stmt->execute([$documento]);
    $trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trabajador) {
        throw new Exception("❌ Trabajador no encontrado con documento: $documento");
    }

    $id_trabajador = $trabajador['id'];
    logDebug("✅ Trabajador encontrado - ID: $id_trabajador");


       $email_jefe = null;


        logDebug("🔍 Buscando jefe por nombre: $nombre_jefe");

    $stmt = $pdo->prepare("SELECT id, email FROM trabajadores WHERE nombre_completo = :nombre_jefe AND estado = 'activo' LIMIT 1");
$stmt->execute(['nombre_jefe' => $nombre_jefe]);
$jefeFormulario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jefeFormulario) {
    logDebug("❌ Jefe no activo encontrado: $nombre_jefe");
    echo json_encode(['status' => 'error', 'message' => "⚠️ El jefe '$nombre_jefe' no se encuentra registrado como activo."]);
    exit;
}

$id_jefe = $jefeFormulario['id'];
$email_jefe = $jefeFormulario['email'];
logDebug("✅ Jefe encontrado - ID: $id_jefe, Email: $email_jefe");

if (empty($id_jefe)) {
    throw new Exception("❗ id_jefe no puede estar vacío");
}
       
    
    // Número de formulario
    $numero_formulario = uniqid('FORM-');

    // Subir comprobantes
    $carpeta_destino = __DIR__ . '/uploads/ausencias/';
    if (!is_dir($carpeta_destino)) {
        mkdir($carpeta_destino, 0777, true);
    }

    $comprobantes_guardados = [];
    if (!empty($_FILES['comprobantes']['name'][0])) {
        foreach ($_FILES['comprobantes']['tmp_name'] as $index => $tmpName) {
            $nombreArchivo = basename($_FILES['comprobantes']['name'][$index]);
            $ruta = $carpeta_destino . $numero_formulario . '_' . $nombreArchivo;
            if (move_uploaded_file($tmpName, $ruta)) {
                $comprobantes_guardados[] = 'uploads/ausencias/' . $numero_formulario . '_' . $nombreArchivo;
                logDebug("✔️ Comprobante guardado: $nombreArchivo");
            } else {
                logDebug("❌ Error al guardar el comprobante: $nombreArchivo");
            }
        }
    }

    $comprobantes_serializados = implode(',', $comprobantes_guardados);

    // Fecha y hora Colombia para la base de datos
    $fecha_registro = date('Y-m-d H:i:s');

    

    // Formato para el correo
    $fecha_inicio_mostrar = DateTime::createFromFormat('Y-m-d', trim($rango[0]))->format('d/m/Y');
    $fecha_fin_mostrar = isset($rango[1])
        ? DateTime::createFromFormat('Y-m-d', trim($rango[1]))->format('d/m/Y')
        : $fecha_inicio_mostrar;

    // Generar links y adjuntos
    $adjuntos = [];
    $enlacesHTML = '';

    if (!empty($comprobantes_guardados)) {
        $enlacesHTML .= "<p><strong>Comprobantes adjuntos:</strong></p>";

        foreach ($comprobantes_guardados as $comprobante) {
            $nombreArchivo = basename($comprobante);
            $url = "https://gcshelps.com/intranet/Ausencia/$comprobante";
            $rutaLocal = __DIR__ . '/' . $comprobante;
            $adjuntos[] = $rutaLocal;

            $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $enlacesHTML .= "<p><img src='$url' alt='Comprobante' style='max-width:200px; margin: 5px;' /></p>";
            } elseif ($ext === 'pdf') {
                $enlacesHTML .= "<p><a href='$url' target='_blank'>📎 Ver archivo PDF: $nombreArchivo</a></p>";
            } else {
                $enlacesHTML .= "<p><a href='$url' target='_blank'>📁 Ver archivo: $nombreArchivo</a></p>";
            }
        }
    }

    // Enlaces de respuesta para jefe
    list($url_aprobar_jefe, $url_denegar_jefe) = construirEnlacesRespuesta($numero_formulario, 'teamlead');

// Verificar si se ha asignado un jefe correcto

    



        // Armar mensaje HTML para el jefe
        $mensaje_jefe = "
        <div style='font-family: Arial, sans-serif; padding: 20px;'>
            <h2 style='color: #2E4053;'>Detalles de la solicitud de ausencia</h2>
            <p><strong>Nombre del Solicitante:</strong> $nombre</p>
            <p><strong>Documento del Solicitante:</strong> $documento</p>
            <p><strong>Email del Solicitante:</strong> <a href='mailto:$email'>$email</a></p>
            <p><strong>Tipo de solicitud:</strong> $tipo_ausencia</p>
            <p><strong>Fecha de inicio:</strong> $fecha_inicio_mostrar</p>
            <p><strong>Fecha de fin:</strong> $fecha_fin_mostrar</p>
            <p><strong>Comentarios:</strong> $observaciones</p>
            $enlacesHTML
            <div style='margin-top: 30px;'>
                <p style='font-weight: bold; color: #8B0000;'>Acciones:</p>
                <a href='$url_aprobar_jefe' 
                   style='background-color: #28a745; color: white; padding: 12px 25px;
                          text-decoration: none; border-radius: 5px; margin-right: 10px; font-weight: bold; display: inline-block;'>
                   Aprobar
                </a>
                <a href='$url_denegar_jefe' 
                   style='background-color: #dc3545; color: white; padding: 12px 25px;
                          text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>
                   Denegar
                </a>
            </div>
            <hr style='margin-top: 40px; border: none; border-top: 1px solid #ddd;'>
            <p style='font-size: 12px; color: #999;'>Este correo fue generado automáticamente por el sistema de gestión de ausencias.</p>
        </div>
        ";

        // Enviar correo
        enviarCorreo($email_jefe, "Formulario de ausencia: $nombre", $mensaje_jefe, $adjuntos);
      




// Insertar en la base de datos
$stmt = $pdo->prepare("INSERT INTO tb_ausencias
    (numero_formulario, id_trabajador, id_campana, id_jefe, tipo_ausencia, fecha_inicio, fecha_fin, observaciones, comprobantes, fecha_registro, nombre, documento, email)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->execute([
    $numero_formulario,
    $id_trabajador,
    $id_campana,
    $id_jefe, 
    $tipo_ausencia,
    $fecha_inicio,
    $fecha_fin,
    $observaciones,
    $comprobantes_serializados,
    $fecha_registro,
    $nombre,
    $documento,
    $email
]);

logDebug("✔️ Datos insertados en la base de datos");

echo json_encode([ 
    'status' => 'success', 
    'message' => 'Formulario enviado correctamente.', 
    'numero_formulario' => $numero_formulario
]);

} catch (Exception $e) {
    logDebug("❌ Error general en save.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

?>