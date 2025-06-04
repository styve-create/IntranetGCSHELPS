<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php');

$sql = "
SELECT h.*, t.nombre_completo
FROM tb_horario h
JOIN tb_usuarios u ON h.id_usuario = u.id_usuarios
JOIN trabajadores t ON u.trabajador_id = t.id
ORDER BY h.fecha DESC
";
$sentencia = $pdo->query($sql);
$horarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>


    <link href="<?php echo $URL; ?>/librerias/DataTables/datatables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
    <div class="card-custom">
        <h2 class="text-center">Registros de Horarios -Para la Administrativos</h2>
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
           
                 <table class="table table-bordered table-striped" id="tablaHorariosAdministrativo" style="width:100%">
                <thead>
                    <tr>
                        <th>Trabajador</th>
                        <th>Inicio Turno</th>
                        <th>Fecha</th>
                        <th>Break 1 Inicio</th>
                        <th>Break 1 Fin</th>
                        <th>Break 2 Inicio</th>
                        <th>Break 2 Fin</th>
                        <th>Break 3 Inicio</th>
                        <th>Break 3 Fin</th>
                        <th>Fin Turno</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($horarios as $h): ?>
                        <tr>
                           <td><?= htmlspecialchars($h['nombre_completo']) ?></td>
                            <td><?= $h['fecha'] ?></td>
                            <td><?= $h['hora_inicio_turno'] ?></td>
                            <td><?= $h['hora_fin_turno'] ?></td>
                            <td><?= $h['hora_inicio_break1'] ?></td>
                            <td><?= $h['hora_fin_break1'] ?></td>
                            <td><?= $h['hora_inicio_break2'] ?></td>
                            <td><?= $h['hora_fin_break2'] ?></td>
                            <td><?= $h['hora_inicio_break3'] ?></td>
                            <td><?= $h['hora_fin_break3'] ?></td>
                            <td><?= $h['fyh_creacion'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>


<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/DataTables/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (function(){
$(document).ready(function () {
    var tabla = $('#tablaHorariosAdministrativo').DataTable({
        scrollX: true,
        responsive: true,
        pageLength: 10,
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>