<?php
// send_invoice.php

// Ruta al log
$log_index1 = __DIR__ . "/log_index1.txt";
function log_index1($mensaje) {
    global $log_index1;
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, $log_index1);
}

// Inicio
log_index1(">>> Inicio send_invoice.php");

// Desactivar errores en pantalla
ini_set('display_errors',0);
ini_set('log_errors',1);
header('Content-Type: application/json');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once(__DIR__ . '/../../../../app/controllers/config.php');

require_once(__DIR__ . '/../../../../librerias/PHPMailer-master/PHPMailer-master/src/Exception.php');
require_once(__DIR__ . '/../../../../librerias/PHPMailer-master/PHPMailer-master/src/PHPMailer.php');
require_once(__DIR__ . '/../../../../librerias/PHPMailer-master/PHPMailer-master/src/SMTP.php');

try {
    // 1) dump de lo que llega
    log_index1("RAW \$_POST: " . print_r($_POST, true));
    log_index1("RAW \$_FILES: " . print_r($_FILES, true));

    // 2) Validar campos POST y FILES
    if (empty($_POST['invoiceNumber'])) {
        throw new Exception('invoiceNumber vacio');
    }
    if (empty($_POST['email'])) {
        throw new Exception('email vacio');
    }
    if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('PDF no subido correctamente');
    }

    $invoiceNum = $_POST['invoiceNumber'];
    $emailDest  = $_POST['email'];
    $pdfFile    = $_FILES['pdf'];
    
     // 2) Guardar el PDF en disco
    $uploadDir = __DIR__ . '/uploads/facturas/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $dstPath = $uploadDir . $invoiceNum . '.pdf';

    if (!move_uploaded_file($pdfFile['tmp_name'], $dstPath)) {
        throw new Exception("No se pudo guardar el PDF en $dstPath");
    }
    log_index1("PDF guardado en: $dstPath");

    log_index1("invoiceNum = $invoiceNum");
    log_index1("emailDest = $emailDest");
    

    // 3) Configurar PHPMailer
    
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'jenni@gcshelps.com';
    $mail->Password = 'yydy oahd xtdn kiju'; // ⚠️ RECUERDA: Usar variable segura en producción
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('jenni@gcshelps.com','Global Connect Solutions');
    $mail->addAddress($emailDest);

    $mail->Subject = "Factura $invoiceNum";
    $mail->Body    = "Adjunto  la factura $invoiceNum.\n\nSaludos.";


    
     // Adjunta el PDF desde la carpeta donde lo guardaste
    $mail->addAttachment($dstPath, "$invoiceNum.pdf");
    log_index1("Adjuntado PDF a PHPMailer");

    $mail->send();
    log_index1("Mail enviado con exito");

    $response= ['success'=>true];

} catch (Exception $e) {
    // Capturar cualquier excepción
    log_index1("ERROR: " . $e->getMessage());
    http_response_code(500);
    $response = ['success' => false, 'message' => $e->getMessage()];
}

// 7) Limpiar buffers y devolver JSON
while (ob_get_level()) { ob_end_clean(); }
echo json_encode($response);
log_index1(">>> Fin send_invoice.php\n");