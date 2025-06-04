<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require_once(__DIR__ . '/../../../../app/controllers/config.php'); 

require_once(__DIR__ . '/../../../../librerias/PHPMailer-master/PHPMailer-master/src/Exception.php');
require_once(__DIR__ . '/../../../../librerias/PHPMailer-master/PHPMailer-master/src/PHPMailer.php');
require_once(__DIR__ . '/../../../../librerias/PHPMailer-master/PHPMailer-master/src/SMTP.php');

function enviarCorreo($destinatario, $asunto, $mensajeHTML, $adjuntos = [])
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jenni@gcshelps.com';
        $mail->Password = 'yydy oahd xtdn kiju'; // ⚠️ RECUERDA: Usar variable segura en producción
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('jenni@gcshelps.com', 'Formulario de Asignaciones');
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
?>