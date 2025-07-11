<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once(__DIR__ . '/../../app/controllers/config.php');

require_once(__DIR__ . '/../../librerias/PHPMailer-master/PHPMailer-master/src/Exception.php');
require_once(__DIR__ . '/../../librerias/PHPMailer-master/PHPMailer-master/src/PHPMailer.php');
require_once(__DIR__ . '/../../librerias/PHPMailer-master/PHPMailer-master/src/SMTP.php');

function enviarCorreo($destinatario, $asunto, $mensajeHTML, $adjuntos = [])
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
$mail->Host       = SMTP_HOST;
$mail->SMTPAuth   = true;
$mail->Username   = SMTP_USER;
$mail->Password   = SMTP_PASS;
$mail->SMTPSecure = SMTP_SECURE;
$mail->Port       = SMTP_PORT;

$mail->setFrom(SMTP_USER, 'Formulario de Ausencias');

       
        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensajeHTML;

        // Adjuntar archivos si existen
        if (!empty($adjuntos)) {
            foreach ($adjuntos as $archivo) {
                if (file_exists($archivo)) {
                    $mail->addAttachment($archivo);
                }
            }
        }

        $mail->send();
    } catch (Exception $e) {
        error_log("❌ Error al enviar correo: " . $mail->ErrorInfo);
    }
}