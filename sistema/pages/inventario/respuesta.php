<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../../../app/controllers/config.php'); 
$id_formulario = $_GET['formulario'] ?? null;
$accion = $_GET['accion'] ?? null;

if (!$id_formulario || !$accion || !in_array($accion, ['aprobado', 'rechazado'])) {
    exit("Parámetros inválidos.");
}

$estado = ($accion === 'aprobado') ? 'aprobado' : 'rechazado';

// Mostrar formulario para comentario si es GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>' . ucfirst($estado) . ' Formulario</title>
        <link rel="stylesheet" href="https://gcshelps.com/intranet/librerias/vendor/npm-asset/bootstrap-icons/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://gcshelps.com/intranet/librerias/vendor/npm-asset/bootstrap/dist/css/bootstrap.min.css">
       
    </head>
    <body>
        <div class="container mt-5">
            <h3>' . ucfirst($estado) . ' Formulario</h3>
            <form method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="requestId" value="' . htmlspecialchars($id_formulario) . '">
                <input type="hidden" name="response" value="' . $estado . '">
                
                <div class="mb-3">
                    <label for="comentarios" class="form-label">Comentario:</label>
                    <textarea name="comentarios" class="form-control" ' . ($estado === 'aprobado' ? '' : 'required') . '></textarea>
                    <div class="invalid-feedback">Este campo es obligatorio si el formulario es rechazado.</div>
                </div>
                
                <button type="submit" class="btn btn-' . ($estado === 'aprobado' ? 'success' : 'danger') . '" id="submitBtn">Enviar</button>
            </form>
        </div>
        <script>
        (() => {
            "use strict";
            const forms = document.querySelectorAll(".needs-validation");
            Array.from(forms).forEach((form) => {
                form.addEventListener("submit", (event) => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add("was-validated");

                    // Bloquear el botón después de enviar el formulario para evitar múltiples envíos
                    const submitButton = document.getElementById("submitBtn");
                    submitButton.disabled = true;
                    submitButton.innerHTML = "Enviando...";
                }, false);
            });
        })();
        </script>
    </body>
    </html>';
    exit;
}

// Comentario
$comentario = $_POST['comentarios'] ?? null;

try {
    // Verificar si ya fue procesado
    $stmt = $pdo->prepare("SELECT estado_trabajador FROM formularios_asignacion WHERE numero_formulario = ?");
    $stmt->execute([$id_formulario]);
    $estadoActual = $stmt->fetchColumn();

    if ($estadoActual === 'aprobado' || $estadoActual === 'rechazado') { 
        exit("Este formulario ya fue respondido.");
    }


    // Actualizar formulario
    $sql = "UPDATE formularios_asignacion SET estado_trabajador = ?, fecha_trabajador = NOW()";
    $params = [$estado];

    if ($comentario) {
        $sql .= ", comentarios_trabajador = ?";
        $params[] = $comentario;
    }

    $sql .= " WHERE numero_formulario = ?";
    $params[] = $id_formulario;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

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
";

if ($comentario) {
    echo "<p class='mb-2'><strong>Comentarios:</strong> " . htmlspecialchars($comentario) . "</p>";
}

echo "
    </div>";

} catch (Exception $e) {
    echo "Error al procesar: " . $e->getMessage();
}
?>

