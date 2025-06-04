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

// Obtener datos del trabajador, incluyendo puesto y campaña
$stmt = $pdo->prepare("
    SELECT t.*, tc.campana_id AS id_campana, tc.puesto_id 
    FROM trabajadores t
    LEFT JOIN trabajadores_campanas tc ON t.id = tc.trabajador_id
    WHERE t.id = ?
");
$stmt->execute([$id]);
$trabajador = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$trabajador) {
    mostrarSweetAlert('Error', 'Trabajador no encontrado.', 'error');
    exit;
}

// Obtener campañas activas
$stmt_campanas = $pdo->query("SELECT id, nombre FROM tb_campanas WHERE estado = 'Activa'");
$campanas = $stmt_campanas->fetchAll(PDO::FETCH_ASSOC);

// Obtener cargos
$stmt_cargos = $pdo->query("SELECT id, nombre FROM cargos");
$cargos = $stmt_cargos->fetchAll(PDO::FETCH_ASSOC);

// Campos de fecha
$campos_fecha = [
    'fecha_expedicion', 'fecha_nacimiento', 'fecha_ingreso_falcon', 'fecha_retiro_falcon',
    'fecha_ingreso_gcs', 'fecha_retiro_gcs','finalizacion_contractual'
];

function formatearFechaColombia($fecha) {
    if (!$fecha || $fecha === '0000-00-00') return '';
    try {
        $fechaObj = new DateTime($fecha);
        return $fechaObj->format('d/m/Y');
    } catch (Exception $e) {
        return '';
    }
}

function formatearFechaMySQL($fecha) {
    if (!$fecha) return null;
    $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
    return $fechaObj ? $fechaObj->format('Y-m-d') : null;
}

function formatearFechaInputDate($fecha) {
    if (!$fecha || $fecha === '0000-00-00') return '';
    try {
        $fechaObj = new DateTime($fecha);
        return $fechaObj->format('Y-m-d');
    } catch (Exception $e) {
        return '';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = [
        'nombre_completo', 'grupo_sanguineo', 'estado_civil', 'edad', 'tipo_documento',
        'numero_documento', 'fecha_expedicion', 'lugar_expedicion', 'fecha_nacimiento', 'lugar_nacimiento',
        'nivel_estudio', 'profesion', 'domicilio', 'ciudad', 'celular', 'email',
        'cuenta_bancaria', 'banco', 'tipo_cuenta', 'tipo_contrato', 'horas_contratadas',
        'salario_basico', 'auxilio_transporte', 'eps', 'codigo_pension', 'codigo_cesantias',
        'estado', 'fecha_ingreso_falcon', 'fecha_retiro_falcon', 'salario_falcon_pesos', 'salario_falcon_usd',
        'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'finalizacion_contractual','tipo_retiro','cargo_certificado',
        'nombre_contacto_emergencia', 'numero_contacto_emergencia'
    ];

    $valores = [];
    foreach ($campos as $campo) {
        $valor = $_POST[$campo] ?? null;
        if (in_array($campo, $campos_fecha)) {
            $valor = formatearFechaMySQL($valor);
        }
        $valores[$campo] = $valor;
    }

    if (empty($valores['nombre_completo']) || empty($valores['numero_documento']) || empty($_POST['cargo'])) {
        mostrarSweetAlert('Campos incompletos', 'Por favor complete todos los campos obligatorios.', 'warning');
    } else {
        // Obtener id del puesto
        $puesto_id = $_POST['cargo'];
        $id_campana = $trabajador['id_campana']; // ya viene de la consulta inicial

        // Actualizar trabajador
        $set = implode(', ', array_map(fn($campo) => "$campo = ?", array_keys($valores)));
        $sql = "UPDATE trabajadores SET $set WHERE id = ?";
        $stmt_update = $pdo->prepare($sql);
        $stmt_update->execute([...array_values($valores), $id]);

        // Actualizar solo el puesto_id existente en trabajadores_campanas
        $stmt_tc = $pdo->prepare("UPDATE trabajadores_campanas SET puesto_id = ? WHERE trabajador_id = ? AND campana_id = ?");
        $stmt_tc->execute([$puesto_id, $id, $id_campana]);

        mostrarSweetAlert('Éxito', 'Datos del trabajador actualizados correctamente.', 'success');

        // Recargar datos del trabajador actualizado
        $stmt->execute([$id]);
        $trabajador = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>
<html>
    <head>
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> 
    </head>
    <body>
        <!-- HTML -->
<div class="container mt-4">
    <h2 class="mb-4">Editar Trabajador</h2>
    <form method="POST">
        <?php
        $inputs = [
            'nombre_completo', 'grupo_sanguineo', 'estado_civil', 'edad', 'tipo_documento',
            'numero_documento', 'fecha_expedicion', 'lugar_expedicion', 'fecha_nacimiento', 'lugar_nacimiento',
            'nivel_estudio', 'profesion', 'domicilio', 'ciudad', 'celular', 'email',
            'cuenta_bancaria', 'banco', 'tipo_cuenta', 'tipo_contrato', 'horas_contratadas',
            'salario_basico', 'auxilio_transporte', 'eps', 'codigo_pension', 'codigo_cesantias',
            'estado', 'fecha_ingreso_falcon', 'fecha_retiro_falcon', 'salario_falcon_pesos', 'salario_falcon_usd',
            'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'finalizacion_contractual',
            'nombre_contacto_emergencia', 'numero_contacto_emergencia','tipo_retiro','cargo_certificado',
        ];

        $count = 0;
        foreach ($inputs as $index => $input) {
            if ($count % 3 === 0) echo '<div class="row">';
            $label = ucwords(str_replace('_', ' ', $input));
            $value_raw = $trabajador[$input] ?? '';
            $value = in_array($input, $campos_fecha)
                ? formatearFechaInputDate($value_raw)
                : htmlspecialchars($value_raw);

            echo "<div class='col-md-4 mb-3'>
                <label class='form-label'>$label</label>";

            if ($input === 'estado') {
                echo "<select name='estado' class='form-select'>
                        <option value='Activo' " . ($value === 'Activo' ? 'selected' : '') . ">Activo</option>
                        <option value='No Activo' " . ($value === 'No Activo' ? 'selected' : '') . ">No Activo</option>
                        <option value='No Disponible' " . ($value === 'No Disponible' ? 'selected' : '') . ">No Disponible</option>
                      </select>";
            } elseif ($input === 'tipo_retiro') {
                $opciones_finalizacion = [
                    'Retiro voluntario',
                    'Despido sin justa causa',
                    'Despido con justa causa',
                    'Finalización contrato de aprendizaje',
                    'Finalización por mutuo acuerdo'
                ];
                echo "<select name='tipo_retiro' class='form-select'>
                        <option value=''>Seleccione una opción</option>";
                foreach ($opciones_finalizacion as $opcion) {
                    $selected = $value === $opcion ? 'selected' : '';
                    echo "<option value='$opcion' $selected>$opcion</option>";
                }
                echo "</select>";
            } else {
                $inputType = in_array($input, $campos_fecha) ? 'date' : 'text';
                echo "<input type='$inputType' name='$input' class='form-control' value='$value'>";
            }

            echo "</div>";
            $count++;
            if ($count % 3 === 0 || $index === array_key_last($inputs)) echo '</div>';
        }
        ?>

        <div class="mb-3">
            <label class="form-label">Campaña</label>
            <select name="campana" class="form-select">
                <?php
                foreach ($campanas as $campana) {
                    $selected = $campana['id'] == $trabajador['id_campana'] ? 'selected' : '';
                    echo "<option value='{$campana['nombre']}' $selected>{$campana['nombre']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Cargo</label>
            <select name="cargo" class="form-select">
                <?php
                foreach ($cargos as $cargo) {
                    $selected = $cargo['id'] == $trabajador['puesto_id'] ? 'selected' : '';
                    echo "<option value='{$cargo['id']}' $selected>{$cargo['nombre']}</option>";
                }
                ?>
            </select>
        </div>

        
        <br>
                  <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-lg btn-primary">Guardar</button>
                    <a href="<?= $URL ?>/sistema/paginasdinamicas.php?page=trabajadores&ajax=1" class="btn btn-lg btn-secondary">
                      Cancelar
                    </a>
        </div> 
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>

