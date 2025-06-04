<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../app/controllers/config.php');

// Obtener campañas desde la base de datos
$stmt = $pdo->query("SELECT id, nombre FROM tb_campanas");
$campanas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Ausencias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    body {
        background: #f5f5f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 16px;
        margin: 0;
        padding: 0;
    }

    .form-container {
        max-width: 700px;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        margin: 50px auto;
    }

    .form-title {
        font-weight: bold;
        font-size: 2rem;
        margin-bottom: 1.5rem;
        color: #333;
        text-align: center;
    }

    label {
        font-weight: 600;
        font-size: 1rem;
        display: block;
        margin-bottom: 5px;
    }

    input, select, textarea {
        font-size: 1rem;
        width: 100%;
        padding: 14px;
        margin-bottom: 1.2rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
    }

    input[type="file"] {
        padding: 10px;
    }

    button[type="submit"] {
        background-color: #1063f3;
        color: white;
        font-weight: bold;
        padding: 14px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        width: 100%;
        margin-top: 10px;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
        background-color: #0d52cc;
    }

    /* Responsive para tablets y móviles */
    @media (max-width: 768px) {
        .form-container {
            padding: 25px;
            margin: 30px 15px;
        }

        .form-title {
            font-size: 1.6rem;
        }

        input, select, textarea, button[type="submit"] {
            font-size: 1.1rem;
            padding: 16px;
        }
    }

    @media (max-width: 480px) {
        .form-container {
            padding: 20px;
            margin: 20px 10px;
        }

        .form-title {
            font-size: 1.4rem;
        }

        input, select, textarea, button[type="submit"] {
            font-size: 1.05rem;
            padding: 14px;
        }
    }
</style>
</head>
<body>
<div class="container-fluid d-flex justify-content-center px-2">
    <div class="form-container">
        <div class="form-title">Formulario Ausencias - (Absence Form)</div>
        <form id="formAusencia" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nombre Completo- (Full name)</label>
                <input type="text" name="nombre_completo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Documento - (ID)</label>
                <input type="text" name="documento" class="form-control" required pattern="\d+">
            </div>
            <div class="mb-3">
                <label class="form-label">Correo electrónico (Email)</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Campaña - (Account)</label>
                <select name="id_campana" id="campana" class="form-select" required>
                    <option value="">Selecciona una campaña</option>
                    <?php foreach ($campanas as $campana): ?>
                        <option value="<?= htmlspecialchars($campana['id']) ?>">
                            <?= htmlspecialchars($campana['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Jefes - (Leads)</label>
                <select name="nombre_jefe" id="jefe" class="form-select">
                    <option value="">Selecciona una campaña primero</option>
                </select>
            </div>
           <div class="mb-3">
    <label class="form-label">Tipo de Ausencia - (Request type)</label>
    <select name="tipo_ausencia" class="form-select" required>
        <option value="">Selecciona una opción</option>
        <option value="Citas medicas">Citas médicas</option>
        <option value="Licencia no remunerada - Permiso personal">Licencia no remunerada - Permiso personal</option>
        <option value="Incapacidad medica">Incapacidad médica</option>
        <option value="Festivo libre">Festivo libre</option>
        <option value="Vacaciones">Vacaciones</option>
        <option value="Calamidad doméstica">Calamidad doméstica</option>
        <option value="Licencia de luto">Licencia de luto</option>
        <option value="Licencia matrimonio">Licencia matrimonio</option>
        <option value="Dia de la familia">Día de la familia</option>
        <option value="Trabajo desde casa">Solicitud de trabajo desde casa</option>
    </select>
</div>
            <div class="row g-3">
                <div class="mb-3">
                    <label class="form-label">Rango de Fechas - (Select a date or date range)</label>
                    <input type="text" id="rangoFechas" class="form-control" name="rango_fechas" required placeholder="Selecciona fecha o rango de fechas">
                </div>
            </div>
            <div class="mb-3 mt-3">
                <label class="form-label">Observaciones - (Comments)</label>
                <textarea name="observaciones" class="form-control" rows="3" placeholder="Especifica cualquier detalle relevante..."></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Comprobantes - (Proofs)</label>
                <input type="file" name="comprobantes[]" class="form-control" multiple accept="image/*,.pdf">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg" id="btnEnviar">
                    <i class="bi bi-send-fill me-1"></i>Enviar Formulario
                </button>
            </div>
        </form>
        <div id="mensaje" class="mt-4"></div>
    </div>
</div>

<script>
$(document).ready(function () {
 $('#campana').on('change', function () {
    let id_campana = $(this).val();
    let documento = $('input[name="documento"]').val(); // obtener el documento ingresado

    if (id_campana && documento) {
        $.post('get_jefes.php', {
            id_campana: id_campana,
            documento: documento
        }, function (data) {
            $('#jefe').html(data);
        });
    } else {
        $('#jefe').html('<option value="">Completa primero documento y campaña</option>');
    }
});

$('input[name="documento"]').on('blur', function () {
    $('#campana').trigger('change'); 
});


    $('#formAusencia').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        for (var pair of formData.entries()) {
        console.log(pair[0]+ ': '+ pair[1]); 
    }
    
        $('#btnEnviar').prop('disabled', true).text('Enviando...');

        $.ajax({
            url: 'save.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                console.log(res);
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Formulario Enviado',
                        html: 'Número de formulario: <strong>' + res.numero_formulario + '</strong>'
                    });
                    $('#formAusencia')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message,
                    });
                }
                $('#btnEnviar').prop('disabled', false).text('Enviar Formulario');
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error inesperado',
                    text: 'No se pudo enviar el formulario.'
                });
                $('#btnEnviar').prop('disabled', false).text('Enviar Formulario');
            }
        });
    });
});

</script>
<script>
    new Litepicker({
    element: document.getElementById('rangoFechas'),
    singleMode: false, // Permite seleccionar rango
    format: 'YYYY-MM-DD',
    allowRepick: true,
    tooltipText: {
        one: 'Día',
        other: 'días'
    },
    setup: (picker) => {
        picker.on('selected', (start, end) => {
            console.log('Fecha inicio:', start.format('YYYY-MM-DD'));
            console.log('Fecha fin:', end.format('YYYY-MM-DD'));
        });
    }
});
</script>
</body>
</html>
