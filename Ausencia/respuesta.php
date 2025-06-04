<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../app/controllers/config.php');
include_once(__DIR__ . '/helpers/email.php');
include_once(__DIR__ . '/helpers/enlaces_respuesta.php');

// Validar parámetros
$id_formulario = $_GET['form'] ?? null;
$accion = $_GET['accion'] ?? null;
$etapa = $_GET['etapa'] ?? null;

if (!$id_formulario || !$accion || !$etapa || !in_array($etapa, ['teamlead', 'rrhh'])) {
    exit("Parámetros inválidos.");
}

$estado = ($accion === 'aprobar') ? 'aprobado' : 'rechazado';
$mapa_campos = [
    'teamlead' => ['estado' => 'estado_team_lead', 'fecha' => 'fecha_team_lead'],
    'rrhh' => ['estado' => 'estado_rrhh', 'fecha' => 'fecha_rrhh']
];

if (!isset($mapa_campos[$etapa])) {
    exit("Etapa inválida.");
}

$campo_estado = $mapa_campos[$etapa]['estado'];
$campo_fecha = $mapa_campos[$etapa]['fecha'];

try {
    $stmt = $pdo->prepare("SELECT $campo_estado FROM tb_ausencias WHERE numero_formulario = ?");
    $stmt->execute([$id_formulario]);
    $estadoActual = $stmt->fetchColumn();

    if ($estadoActual === 'aprobado' || $estadoActual === 'rechazado') {
        exit("Esta solicitud ya fue respondida.");
    }

    if ($accion === 'rechazar' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Razón de Rechazo</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <h3>Razón de Rechazo</h3>
            <form class="needs-validation" method="POST" novalidate>
                <input type="hidden" name="requestId" value="' . htmlspecialchars($id_formulario) . '">
                <input type="hidden" name="response" value="rechazado">

                <div class="mb-3">
                    <label for="razonRechazo" class="form-label">Escriba la razón de rechazo:</label>
                    <textarea class="form-control" id="razonRechazo" name="razonRechazo" placeholder="Campo requerido" required></textarea>
                    <div class="invalid-feedback">
                        Este dato es obligatorio.
                    </div>
                </div>

                <div class="mb-3">
                    <button type="submit" id="submitButton" class="btn btn-danger">
                        Enviar
                    </button>
                    <span id="enviandoTexto" style="display:none; margin-left: 15px; color: #555;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Enviando...
                    </span>
                </div>
            </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            (() => {
                "use strict";
                const forms = document.querySelectorAll(".needs-validation");

                Array.from(forms).forEach((form) => {
                    form.addEventListener("submit", (event) => {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        } else {
                            const btn = document.getElementById("submitButton");
                            btn.disabled = true;
                            document.getElementById("enviandoTexto").style.display = "inline-block";
                        }
                        form.classList.add("was-validated");
                    }, false);
                });
            })();
        </script>
    </body>
    </html>';
    exit;
}

    $razon = $_POST['razonRechazo'] ?? null;

    $sql = "UPDATE tb_ausencias SET $campo_estado = ?, $campo_fecha = NOW()";
    $params = [$estado];

    if ($estado === 'rechazado') {
        $sql .= ", razon_rechazo = ?";
        $params[] = $razon;
    }

    $sql .= " WHERE numero_formulario = ?";
    $params[] = $id_formulario;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Log de columnas actualizadas
    file_put_contents(__DIR__ . '/debug_respuesta.txt', "🔄 Actualizando: $campo_estado y $campo_fecha
", FILE_APPEND);

    $stmt = $pdo->prepare("SELECT 
    a.tipo_ausencia, 
    a.fecha_inicio, 
    a.fecha_fin, 
    a.observaciones,
    a.nombre AS nombre_solicitante,
    a.documento AS documento_solicitante,
    a.email AS email_solicitante,
    j.email AS email_jefe
    FROM tb_ausencias a
    JOIN trabajadores j ON a.id_jefe = j.id
    WHERE a.numero_formulario = ?");
    $stmt->execute([$id_formulario]);
    $info = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$info) exit("Formulario no encontrado.");

    $email_solicitante = $info['email_solicitante'];
    $email_jefe = $info['email_jefe'];
    $nombre = $info['nombre_solicitante'];
    $documento_solicitante = $info['documento_solicitante'];
    $tipo = $info['tipo_ausencia'];
    $f1 = $info['fecha_inicio'];
    $f2 = $info['fecha_fin'];
    $obs = $info['observaciones'];
    
    setlocale(LC_TIME, 'es_CO.UTF-8'); // Intenta establecer localización en español colombiano
    date_default_timezone_set('America/Bogota'); // Asegura zona horaria de Colombia
    
    

    if ($estado === 'aprobado') {
        if ($etapa === 'teamlead') {
            enviarCorreo($email_solicitante, "Tu solicitud fue aprobada", "Tu jefe aprobó tu solicitud.<br>Ahora será revisada por RRHH.");
            
                       // Buscar correo de RRHH
                        $stmt_rrhh = $pdo->prepare("
                          SELECT t.email
                          FROM trabajadores_campanas tc
                          INNER JOIN cargos c ON tc.puesto_id = c.id
                          INNER JOIN trabajadores t ON tc.trabajador_id = t.id
                          WHERE LOWER(REPLACE(REPLACE(REPLACE(c.nombre, 'ó', 'o'), 'é', 'e'), 'í', 'i')) LIKE '%gestion%'
                            AND LOWER(REPLACE(REPLACE(REPLACE(c.nombre, 'ó', 'o'), 'é', 'e'), 'í', 'i')) LIKE '%humana%'
                          LIMIT 1
                        ");
                        $stmt_rrhh->execute();
                        $rrhh = $stmt_rrhh->fetch(PDO::FETCH_ASSOC);
                        
                        if (!$rrhh || empty($rrhh['email'])) {
                            echo json_encode(['status' => 'error', 'message' => 'No se encontró un trabajador de Gestión Humana.']);
                            error_log("⚠️ No se encontró RRHH o correo vacío");
                            exit;
                        }
                        
                        $email_rrhh = $rrhh['email'];

            list($url_aprobar_rrhh, $url_rechazar_rrhh) = construirEnlacesRespuesta($id_formulario, 'rrhh');
            $stmt = $pdo->prepare("SELECT comprobantes FROM tb_ausencias WHERE numero_formulario = ?");
            $stmt = $pdo->prepare("SELECT comprobantes FROM tb_ausencias WHERE numero_formulario = ?");
            $stmt->execute([$id_formulario]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $comprobantes = explode(',', $datos['comprobantes']);
            $enlacesHTML = '';
            $adjuntos = [];
            $tieneComprobantes = false;
            
          foreach ($comprobantes as $archivoRelativo) {
    $archivoRelativo = trim($archivoRelativo);
    if (!empty($archivoRelativo)) {
        $nombreArchivo = basename($archivoRelativo);
        
        // Ruta local del archivo
        $rutaLocal = __DIR__ . "/../../intranet/Ausencia/uploads/ausencias/" . $nombreArchivo;

        // Verificar que exista
        if (file_exists($rutaLocal)) {
            $tieneComprobantes = true;
            $url = "https://gcshelps.com/intranet/Ausencia/uploads/ausencias/" . $nombreArchivo;
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
}

// Solo si hay comprobantes, mostramos el bloque
$bloqueComprobantes = '';
if ($tieneComprobantes) {
    $bloqueComprobantes = "
        <p><strong>Comprobante(s):</strong></p>
        $enlacesHTML
    ";
}
            
            $mensajeRRHH = "
            <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                <h2 style='color: #333;'>Revisión de solicitud de ausencia</h2>
            
                <p><strong>Nombre del solicitante:</strong> $nombre</p>
                 <p><strong>Documento:</strong> $documento_solicitante</p>
                <p><strong>Correo:</strong> $email_solicitante</p>
                <p><strong>Tipo de ausencia:</strong> $tipo</p>
                <p><strong>Desde:</strong>$f1</p>
                <p><strong>Hasta:</strong>$f2</p>
                <p><strong>Observaciones:</strong> $obs</p>
                $bloqueComprobantes
               
                <div style='margin-top: 30px;'>
                    <p style='font-weight: bold; color: #8B0000;'>Acciones:</p>
                    <a href='$url_aprobar_rrhh' 
                       style='background-color: #28a745; color: white; padding: 12px 25px;
                              text-decoration: none; border-radius: 5px; margin-right: 10px; font-weight: bold; display: inline-block;'>
                       Aprobar
                    </a>
                    <a href='$url_rechazar_rrhh' 
                       style='background-color: #dc3545; color: white; padding: 12px 25px;
                              text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>
                       Denegar
                    </a>
                </div>
            
                <hr style='margin-top: 40px; border: none; border-top: 1px solid #ddd;'>
                <p style='font-size: 12px; color: #999;'>Este correo fue generado automáticamente por el sistema de gestión de ausencias.</p>
            </div>
            ";
            
            enviarCorreo( $email_rrhh, "Formulario de ausencia: $nombre", $mensajeRRHH,$adjuntos);



        } elseif ($etapa === 'rrhh') {
            $mensaje = "Tu solicitud fue aprobada por RRHH.";
            enviarCorreo($email_solicitante, "Solicitud aprobada por RRHH", $mensaje);
            enviarCorreo($email_jefe, "RRHH aprueba la solicitud de ausencia de $nombre", $mensaje);
        }
    } else {
        $razonTexto = "<p>Razón: $razon</p>";
        $mensaje = ($etapa === 'teamlead')
            ? "Tu jefe no aprueba tu solicitud de ausencia $nombre ." . $razonTexto
            : "RRHH no aprueba la solicitud de ausencia de $nombre." . $razonTexto;

        enviarCorreo($email_solicitante, "Solicitud de ausencia fue rechazada", $mensaje);

        if ($etapa === 'rrhh') {
            enviarCorreo($email_jefe, "RRHH no aprueba la solicitud de $nombre", $mensaje);
        }
    }

      echo "<div style='
        padding: 2rem; 
        font-family: Roboto, sans-serif; 
        max-width: 600px; 
        margin: 3rem auto; 
        background-color: #f8f9fa; 
        border-radius: 1rem; 
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        text-align: center;'>
       <h2 class='mb-3' style='color: #008000'>
            ✅ Formulario actualizado exitosamente
        </h2>
</div>
";

} catch (Exception $e) {
    file_put_contents(__DIR__ . '/debug_respuesta.txt', "❌ Error: " . $e->getMessage(), FILE_APPEND);
    echo "Error al procesar la solicitud.";
}
?>
