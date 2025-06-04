<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');

function mostrarSweetAlert($titulo, $mensaje, $icono) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '$titulo',
                text: '$mensaje',
                icon: '$icono',
                confirmButtonText: 'Aceptar'
            });
        });
    </script>";
}

$id = $_GET['id'] ?? null;
if (!$id) {
    mostrarSweetAlert('Error', 'ID de trabajador no proporcionado.', 'error');
    exit;
}

// Obtener datos del trabajador
$stmt = $pdo->prepare("SELECT * FROM trabajadores WHERE id = ?");
$stmt->execute([$id]);
$trabajador = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$trabajador) {
    mostrarSweetAlert('Error', 'Trabajador no encontrado.', 'error');
    exit;
}

// Obtener campañas activas
$stmt_campanas = $pdo->query("SELECT id, nombre FROM tb_campanas WHERE estado = 'Activa'");
$campanas = $stmt_campanas->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = [
        'nombre_completo', 'grupo_sanguineo', 'estado_civil', 'edad', 'tipo_documento',
        'numero_documento', 'fecha_expedicion', 'lugar_expedicion', 'fecha_nacimiento', 'lugar_nacimiento',
        'nivel_estudio', 'profesion', 'domicilio', 'ciudad', 'celular', 'email',
        'cuenta_bancaria', 'banco', 'tipo_cuenta', 'tipo_contrato', 'horas_contratadas',
        'salario_basico', 'auxilio_transporte', 'eps', 'codigo_pension', 'codigo_cesantias',
        'estado', 'fecha_ingreso_falcon', 'fecha_retiro_falcon', 'salario_falcon_pesos', 'salario_falcon_usd',
        'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'campana', 'cargo', 'finalizacion_contractual', 'cargo_certificado',
        'nombre_contacto_emergencia', 'numero_contacto_emergencia'
    ];

    $valores = [];
    foreach ($campos as $campo) {
        $valores[$campo] = $_POST[$campo] ?? null;
    }

    $nombre_campana = $valores['campana'];
    $stmt_id_campana = $pdo->prepare("SELECT id FROM tb_campanas WHERE nombre = ?");
    $stmt_id_campana->execute([$nombre_campana]);
    $id_campana = $stmt_id_campana->fetchColumn();

    $set = implode(', ', array_map(fn($campo) => "$campo = ?", array_keys($valores)));
    $sql = "UPDATE trabajadores SET $set, id_campana = ? WHERE id = ?";
    $stmt_update = $pdo->prepare($sql);
    $stmt_update->execute([...array_values($valores), $id_campana, $id]);

    mostrarSweetAlert('Éxito', 'Datos del trabajador actualizados correctamente.', 'success');

    $stmt->execute([$id]);
    $trabajador = $stmt->fetch(PDO::FETCH_ASSOC);
}

function formatearFechaColombia($fecha) {
    if (!$fecha || $fecha === '0000-00-00') return '-';
    try {
        setlocale(LC_TIME, 'es_CO.UTF-8');
        $fechaObj = new DateTime($fecha);
        return $fechaObj->format('d/m/Y');
    } catch (Exception $e) {
        return '-';
    }
}
?>
<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    </head>
    <body>
        <div class="container mt-5 mb-5">
    <h2 class="mb-4 text-center">Detalles del Trabajador</h2>
    <div class="bg-white p-4 shadow rounded">
        <?php
        $inputs = [
            'nombre_completo', 'grupo_sanguineo', 'estado_civil', 'edad', 'tipo_documento',
            'numero_documento', 'fecha_expedicion', 'lugar_expedicion', 'fecha_nacimiento', 'lugar_nacimiento',
            'nivel_estudio', 'profesion', 'domicilio', 'ciudad', 'celular', 'email',
            'cuenta_bancaria', 'banco', 'tipo_cuenta', 'tipo_contrato', 'horas_contratadas',
            'salario_basico', 'auxilio_transporte', 'eps', 'codigo_pension', 'codigo_cesantias',
            'estado', 'fecha_ingreso_falcon', 'fecha_retiro_falcon', 'salario_falcon_pesos', 'salario_falcon_usd',
            'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'tipo_retiro', 'cargo', 'finalizacion_contractual','cargo_certificado',
            'nombre_contacto_emergencia', 'numero_contacto_emergencia'
        ];
        $campos_fecha = [
    'fecha_expedicion', 'fecha_nacimiento', 'fecha_ingreso_falcon', 'fecha_retiro_falcon',
    'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'finalizacion_contractual',
];

        $count = 0;
        foreach ($inputs as $index => $input) {
            if ($count % 3 === 0) echo '<div class="row">';

            $label = ucwords(str_replace('_', ' ', $input));
            $value_raw = $trabajador[$input] ?? '';
            $value = in_array($input, $campos_fecha) 
                ? formatearFechaColombia($value_raw) 
                : htmlspecialchars($value_raw);

            echo "<div class='col-md-4 mb-3'>
                    <label class='form-label fw-bold'>$label</label>
                    <div class='form-control bg-light'>" . ($value ?: '-') . "</div>
                  </div>";

            $count++;
            if ($count % 3 === 0 || $index === array_key_last($inputs)) echo '</div>';
        }
        ?>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Campaña</label>
                <div class="form-control bg-light">
                    <?= htmlspecialchars($trabajador['campana'] ?? '-') ?>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex">
           <div class="d-flex gap-2">
                    <a href="<?= $URL ?>/sistema/paginasdinamicas.php?page=update_trabajadores&id=<?= $trabajador['id'] ?>&ajax=1" class="btn btn-lg btn-primary">
                      Editar
                    </a>
                    <a href="<?= $URL ?>/sistema/paginasdinamicas.php?page=trabajadores&ajax=1" class="btn btn-lg btn-secondary ">
                      Cancelar
                    </a>
        </div> 
    </div>
    
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>


