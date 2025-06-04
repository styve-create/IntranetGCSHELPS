<?php
$log_index = __DIR__ . '/log_index.txt';

include_once(__DIR__ . '/../../analisisRecursos.php');
function log_index($mensaje) {
    global $log_index;
    file_put_contents($log_index, date('Y-m-d H:i:s') . " - $mensaje\n", FILE_APPEND);
}

log_index("Cargado index.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    log_index("POST detectado en index.php");
    log_index("Contenido POST: " . print_r($_POST, true));
    log_index("Archivos: " . print_r($_FILES, true));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    log_index("GET detectado en index.php");
    log_index("Contenido GET: " . print_r($_GET, true));
}
?>
<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    </head>
    <body>
        <form id="formExcel" enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-light">
  <div class="mb-3">
    <label for="archivo_excel" class="form-label fw-bold">Selecciona tu archivo Excel:</label>
    <input type="file" class="form-control" id="archivo_excel" name="archivo_excel" accept=".xls,.xlsx" required>
  </div>
  <button type="submit" class="btn btn-primary">Procesar</button>

  <div id="mensajeCarga" class="alert alert-info mt-3 d-none">Procesando archivo...</div>
  <div id="mensajeExito" class="alert alert-success mt-3 d-none">✅ ¡Archivo procesado y descargado exitosamente!</div>
  <div id="mensajeError" class="alert alert-danger mt-3 d-none">❌ Hubo un problema al procesar el archivo.</div>

</form>

<script>
function logDesdeJS(mensaje) {
    fetch('log_js.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ mensaje })
    });
}

const form = document.getElementById('formExcel');
const mensajeCarga = document.getElementById('mensajeCarga');
const mensajeExito = document.getElementById('mensajeExito');
const mensajeError = document.getElementById('mensajeError');

form.addEventListener('submit', function(e) {
    e.preventDefault();

    mensajeCarga.classList.remove('d-none');
    mensajeExito.classList.add('d-none');
    mensajeError.classList.add('d-none');

    const formData = new FormData(form);
    logDesdeJS("Mostrando mensaje de carga");

    fetch('pages/registrosHuellas/procesar_excel.php', {
        method: 'POST',
        body: formData
    })
    .then(async res => {
    const text = await res.text();
    logDesdeJS("Respuesta del servidor: " + text);

    try {
        const data = JSON.parse(text);
        if (data.success && data.url) {
            // Iniciar descarga
            const link = document.createElement('a');
            link.href = data.url;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Mostrar mensaje de éxito
            mensajeCarga.classList.add('d-none');
            mensajeExito.classList.remove('d-none');
            form.reset();

            setTimeout(() => {
                mensajeExito.classList.add('d-none');
            }, 4000);
        } else {
            throw new Error("Respuesta incorrecta del servidor");
        }
    } catch (e) {
        mensajeCarga.classList.add('d-none');
        mensajeError.classList.remove('d-none');
        logDesdeJS("Error al parsear JSON o contenido inválido: " + e.message);
    }
})
    .catch(err => {
        mensajeCarga.classList.add('d-none');
        mensajeError.classList.remove('d-none');
        logDesdeJS("Error de red o fetch: " + err);
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>


