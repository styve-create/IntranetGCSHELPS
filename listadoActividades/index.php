<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Listado de Actividades</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f2f2f2;
      font-family: 'Roboto', sans-serif;
    }

    .container-box {
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 40px;
      max-width: 700px;
      margin: 60px auto;
      text-align: center;
    }

    .drop-area {
      border: 2px dashed #ccc;
      border-radius: 10px;
      padding: 40px 120px;
      background-color: #f9f9f9;
      cursor: pointer;
      transition: border-color 0.3s ease;
      min-height: 240px;
      position: relative;
    }

    .drop-area:hover {
      border-color: #007bff;
    }

    .drop-area input[type="file"] {
      display: none;
    }

    .drop-button {
      margin-top: 15px;
    }

    .drop-icon {
      font-size: 60px;
      color: #007bff;
      margin-bottom: 10px;
    }

    .note {
      font-size: 0.9rem;
      color: #888;
      margin-top: 15px;
    }
  </style>
</head>
<body>

<div class="container-box">
  <h1 class="mb-4"> Listado de Actividades</h1>
  <h5 class="mb-2 text-secondary">Inserte el Excel</h5>
  <form id="formExcel" enctype="multipart/form-data">
    <label for="archivo_excel" class="drop-area" id="drop-area">
      <div class="drop-icon">📁</div>
      <p class="mb-2">Suelte el documento aquí para cargarlo</p>

      <button type="button" class="btn btn-outline-primary drop-button"
              onclick="document.getElementById('archivo_excel').click();">
        Seleccionar desde la PC
      </button>

      <input type="file" id="archivo_excel" name="archivo_excel" accept=".xls,.xlsx" required>

      <p class="note">Solo se permiten archivos Excel</p>
    </label>
  </form>

  <div id="mensajeCarga" class="alert alert-info mt-3 d-none">Procesando archivo...</div>
  <div id="mensajeExito" class="alert alert-success mt-3 d-none">✅ ¡Archivo procesado y descargado exitosamente!</div>
  <div id="mensajeError" class="alert alert-danger mt-3 d-none">❌ Hubo un problema al procesar el archivo.</div>
</div>


<script>
const form = document.getElementById('formExcel');
const dropArea = document.getElementById('drop-area');
const inputFile = document.getElementById('archivo_excel');
const mensajeCarga = document.getElementById('mensajeCarga');
const mensajeExito = document.getElementById('mensajeExito');
const mensajeError = document.getElementById('mensajeError');

// Drag and drop
dropArea.addEventListener('dragover', (e) => {
  e.preventDefault();
  dropArea.classList.add('border-primary');
});
dropArea.addEventListener('dragleave', () => {
  dropArea.classList.remove('border-primary');
});
dropArea.addEventListener('drop', (e) => {
  e.preventDefault();
  dropArea.classList.remove('border-primary');
  const files = e.dataTransfer.files;
  if (files.length) {
    inputFile.files = files;
    submitForm();
  }
});

// Submit when file selected
inputFile.addEventListener('change', () => {
  if (inputFile.files.length) {
    submitForm();
  }
});

function submitForm() {
  const formData = new FormData(form);
  mensajeCarga.classList.remove('d-none');
  mensajeExito.classList.add('d-none');
  mensajeError.classList.add('d-none');

fetch('procesar_excel.php', {
  method: 'POST',
  body: formData
})
.then(async res => {
  const contentType = res.headers.get('content-type');
  const status = res.status;
  const text = await res.text();

  console.log("🔍 Estado HTTP:", status);
  console.log("🔍 Content-Type:", contentType);
  console.log("🔍 Respuesta completa del servidor:");
  console.log(text);

  try {
    const data = JSON.parse(text);

    if (data.success && data.url) {
      const link = document.createElement('a');
      link.href = data.url;
      link.download = '';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      mensajeCarga.classList.add('d-none');
      mensajeExito.classList.remove('d-none');
      form.reset();

      setTimeout(() => mensajeExito.classList.add('d-none'), 4000);
    } else {
      throw new Error("Respuesta incorrecta del servidor");
    }
  } catch (e) {
    console.error("❌ Error al intentar parsear JSON:", e.message);
    console.warn("⚠️ Respuesta que no se pudo parsear:", text);
    mensajeCarga.classList.add('d-none');
    mensajeError.classList.remove('d-none');
  }
})
.catch(err => {
  console.error("❌ Error en la petición fetch:", err);
  mensajeCarga.classList.add('d-none');
  mensajeError.classList.remove('d-none');
});
}
</script>

</body>
</html>