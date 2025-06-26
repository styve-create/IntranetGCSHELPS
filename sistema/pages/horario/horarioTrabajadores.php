<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../app/controllers/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

$id_usuario_jefe = $_SESSION['usuario_info']['id'] ?? null;
if (!$id_usuario_jefe) {
    http_response_code(401);
    echo json_encode(['success' => false, 'msg' => 'No autenticado']);
    exit;
}

// Obtener el trabajador_id
$sql = "SELECT trabajador_id FROM tb_usuarios WHERE id_usuarios = :id_usuario";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id_usuario' => $id_usuario_jefe]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$trabajador_id_jefe = $row['trabajador_id'] ?? null;

if (!$trabajador_id_jefe) {
    die("No se encontró el trabajador asociado al usuario.");
}

// Verificar si es jefe (cargo_id = 2)
$sql = "SELECT * FROM trabajadores_campanas WHERE trabajador_id = :trabajador_id AND cargo_id = 2";
$stmt = $pdo->prepare($sql);
$stmt->execute(['trabajador_id' => $trabajador_id_jefe]);
$es_jefe = $stmt->rowCount() > 0;

// Unificar todos los subordinados aquí
$registros = [];

// Lógica 1: si es jefe, buscar en tb_jerarquia_trabajadores
if ($es_jefe) {
    $sql = "SELECT jt.id_trabajador, jt.id_campana, t.nombre_completo, u.id_usuarios, c.nombre
            FROM tb_jerarquia_trabajadores jt
            JOIN trabajadores t ON jt.id_trabajador = t.id
            JOIN tb_usuarios u ON u.trabajador_id = t.id
            JOIN tb_campanas c ON c.id = jt.id_campana
            WHERE jt.id_jefe = :id_jefe";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_jefe' => $trabajador_id_jefe]);
    $subordinados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($subordinados as $sub) {
        $sql = "SELECT * FROM tb_horario WHERE id_usuario = :id_usuario ORDER BY fecha DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_usuario' => $sub['id_usuarios']]);
        $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($horarios as $h) {
            $registros[] = [
                'campana' => $sub['nombre'],
                'trabajador' => $sub['nombre_completo'],
                'fecha' => $h['fecha'],
                'inicio_turno' => $h['hora_inicio_turno'],
                'fin_turno' => $h['hora_fin_turno'],
                'break1_inicio' => $h['hora_inicio_break1'],
                'break1_fin' => $h['hora_fin_break1'],
                'break2_inicio' => $h['hora_inicio_break2'],
                'break2_fin' => $h['hora_fin_break2'],
                'break3_inicio' => $h['hora_inicio_break3'],
                'break3_fin' => $h['hora_fin_break3'],
                'registro' => $h['fyh_creacion']
            ];
        }
    }
}

// Lógica 2: campañas donde es responsable
$sql = "SELECT * FROM tb_campanas WHERE id_responsable = :trabajador_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['trabajador_id' => $trabajador_id_jefe]);
$campanas_responsable = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($campanas_responsable as $campana) {
    $campana_id = $campana['id'];
    $campana_nombre = $campana['nombre'];

    $sql = "SELECT t.id AS trabajador_id, t.nombre_completo, u.id_usuarios
            FROM trabajadores_campanas tc
            JOIN trabajadores t ON tc.trabajador_id = t.id
            JOIN tb_usuarios u ON u.trabajador_id = t.id
            WHERE tc.campana_id = :campana_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['campana_id' => $campana_id]);
    $subordinados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($subordinados as $sub) {
        $sql = "SELECT * FROM tb_horario WHERE id_usuario = :id_usuario ORDER BY fecha DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_usuario' => $sub['id_usuarios']]);
        $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($horarios as $h) {
            $registros[] = [
                'campana' => $campana_nombre,
                'trabajador' => $sub['nombre_completo'],
                'fecha' => $h['fecha'],
                'inicio_turno' => $h['hora_inicio_turno'],
                'fin_turno' => $h['hora_fin_turno'],
                'break1_inicio' => $h['hora_inicio_break1'],
                'break1_fin' => $h['hora_fin_break1'],
                'break2_inicio' => $h['hora_inicio_break2'],
                'break2_fin' => $h['hora_fin_break2'],
                'break3_inicio' => $h['hora_inicio_break3'],
                'break3_fin' => $h['hora_fin_break3'],
                'registro' => $h['fyh_creacion']
            ];
        }
    }
}
?>

<!-- 1) Bootstrap CSS -->
<link
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
  rel="stylesheet"
/>

<!-- 2) DataTables Bootstrap5 CSS -->
<link
  href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css"
  rel="stylesheet"
/>

<!-- 3) Buttons Bootstrap5 CSS -->
<link
  href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css"
  rel="stylesheet"
/>

<!-- 4) FontAwesome (para el icono Excel) -->
<link
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  rel="stylesheet"
/>
    <style>
        .table-responsive { overflow-x: auto; margin-top: 20px; }
        .card-custom {
            background: #fff; border-radius: 10px; padding: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .custom-excel-btn {
            background-color: #217346 !important;
            color: white !important;
            border-radius: 6px;
            padding: 6px 12px;
            font-weight: bold;
        }
        .custom-excel-btn i { margin-right: 5px; }
    </style>

<div id="contenido-principal">
  <div class="container-fluid mt-4">
    <h2 class="text-center">Registros de Horarios -Para Jefes</h2>
 <br>
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="start_date">Fecha Inicio</label>
                <input type="date" id="start_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="end_date">Fecha Fin</label>
                <input type="date" id="end_date" class="form-control">
            </div>
            <div class="col-md-3 d-flex gap-2 align-items-end">
                <button id="applyFilter" class="btn btn-primary">Filtrar</button>
                <button id="resetFilter" class="btn btn-secondary ml-2">Restablecer</button>
            </div>
        </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="tablaHorariosTrabajadores" style="width:100%">
            <thead>
                <tr>
                    <th>Campaña</th>
                    <th>Trabajador</th>
                    <th>Fecha</th>
                    <th>Inicio Turno</th>
                    <th>Fin Turno</th>
                    <th>Break 1 Inicio</th>
                    <th>Break 1 Fin</th>
                    <th>Break 2 Inicio</th>
                    <th>Break 2 Fin</th>
                    <th>Break 3 Inicio</th>
                    <th>Break 3 Fin</th>
                    <th>Fecha Registro</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['campana']) ?></td>
                    <td><?= htmlspecialchars($r['trabajador']) ?></td>
                    <td><?= $r['fecha']?? '' ?></td>
                    <td><?= $r['inicio_turno']?? '' ?></td>
                    <td><?= $r['fin_turno']?? '' ?></td>
                    <td><?= $r['inicio_break1']?? '' ?></td>
                    <td><?= $r['fin_break1']?? '' ?></td>
                    <td><?= $r['inicio_break2']?? '' ?></td>
                    <td><?= $r['fin_break2']?? '' ?></td>
                    <td><?= $r['inicio_break3']?? '' ?></td>
                    <td><?= $r['fin_break3']?? '' ?></td>
                    <td><?= $r['creado']?? '' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>  
</div>



<!-- 5) jQuery (requerido por DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- 6) Bootstrap Bundle JS (incluye Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- 7) DataTables core JS -->
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<!-- 8) DataTables Bootstrap5 integration -->
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

<!-- 9) Buttons core JS -->
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>

<!-- 10) Buttons Bootstrap5 integration -->
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>

<!-- 11) JSZip (necesario para excelHtml5) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- 12) HTML5 export (Excel) -->
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

<!-- 13) SweetAlert2 (si usas alertas) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    
(function(){    
$(document).ready(function () {
    var tabla = $('#tablaHorariosTrabajadores').DataTable({
        scrollX: true,
        responsive: true,
        pageLength: 5,
        lengthChange: false,
        autoWidth: false,
        layout: {
            topStart: {
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success custom-excel-btn'
                    }
                ]
            }
        },
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
        }
    });

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var start = $('#start_date').val();
        var end = $('#end_date').val();

        if (!start || !end) return true;

        var date = new Date(data[2]); // Columna 2 = fecha
        return date >= new Date(start) && date <= new Date(end);
    });

    $('#applyFilter').on('click', function () {
        if (!$('#start_date').val() || !$('#end_date').val()) {
            Swal.fire('Campos incompletos', 'Por favor selecciona ambas fechas', 'warning');
            return;
        }
        tabla.draw();
    });

    $('#resetFilter').on('click', function () {
        $('#start_date').val('');
        $('#end_date').val('');
        tabla.search('').columns().search('').draw();
    });
});
})();
</script>

