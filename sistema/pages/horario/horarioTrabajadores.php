<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');
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
    <h2 class="text-center "><p class="traducible">Registros de Horarios -Para Jefes</p></h2>
 <br>
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="start_date"><p class="traducible">Fecha Inicio</p></label>
                <input type="date" id="start_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="end_date"><p class="traducible">Fecha Fin</p></label>
                <input type="date" id="end_date" class="form-control">
            </div>
            <div class="col-md-3 d-flex gap-2 align-items-end">
                <button id="applyFilter" class="btn btn-primary "><p class="traducible">Filtrar</p></button>
                <button id="resetFilter" class="btn btn-secondary ml-2 "><p class="traducible">Restablecer</p></button>
            </div>
        </div>
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="tablaHorariosTrabajadores" style="width:100%">
            <thead>
                <tr>
                    <th><p class="traducible">Campaña</p></th>
                    <th><p class="traducible">Trabajador</p></th>
                    <th><p class="traducible">Fecha</p></th>
                    <th><p class="traducible">Inicio Turno</p></th>
                    <th><p class="traducible">Fin Turno</p></th>
                    <th><p class="traducible">Break 1 Inicio</p></th>
                    <th><p class="traducible">Break 1 Fin</p></th>
                    <th><p class="traducible">Break 2 Inicio</p></th>
                    <th><p class="traducible">Break 2 Fin</p></th>
                    <th><p class="traducible">Break 3 Inicio</p></th>
                    <th><p class="traducible">Break 3 Fin</p></th>
                    <th><p class="traducible">Fecha Registro</p></th>
                </tr>
            </thead>
            <tbody>
        
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
function cargarHorariosJefe() {
    fetch(`/intranet/sistema/pages/horario/get_horarios_jefe.php?t=${Date.now()}`)
        .then(res => res.json())
        .then(json => {
            if (!json.success) {
                Swal.fire('Error', json.msg || 'No se pudieron cargar los datos', 'error');
                return;
            }

            const data = json.data.map(r => [
                r.campana,
                r.trabajador,
                r.fecha,
                r.inicio_turno,
                r.fin_turno,
                r.break1_inicio,
                r.break1_fin,
                r.break2_inicio,
                r.break2_fin,
                r.break3_inicio,
                r.break3_fin,
                r.registro
            ]);

            const tabla = $('#tablaHorariosTrabajadores').DataTable();
            tabla.clear();
            tabla.rows.add(data);
            tabla.draw();
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Ocurrió un error al cargar los datos', 'error');
        });
}
cargarHorariosJefe();
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

})();
</script>

