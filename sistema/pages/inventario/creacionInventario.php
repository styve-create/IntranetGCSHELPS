<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');
// Obtener todos los formularios
$sql = "SELECT * FROM formularios_asignacion ORDER BY fecha_registro DESC";
$sentencia = $pdo->query($sql);
$formularios = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Determinar cuántos equipos máximos hay en un formulario
$maxEquipos = 0;
foreach ($formularios as $formulario) {
    $equipos = !empty($formulario['equipos']) ? array_map('trim', explode(',', $formulario['equipos'])) : [];
    $maxEquipos = max($maxEquipos, count($equipos));
}

function parseList($string) {
    $decoded = json_decode($string, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        return $decoded; // no usar array_map aquí si es asociativo
    } else {
        return array_map('trim', explode(',', $string));
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
.table-responsive {
            overflow-x: auto;
            margin-top: 20px;
        }


.card-custom {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.custom-excel-btn {
    background-color: #217346 !important;  /* Verde estilo Excel */
    color: white !important;
    border-radius: 6px;
    padding: 6px 12px;
    border: none;
    font-weight: bold;
}

.custom-excel-btn i {
    margin-right: 5px;
}
    </style>
<div id="contenido-principal">
    <div class="container-fluid mt-4">
    <div class="card-custom">
         <h2 class="text-center">Formulario de Asignaciones</h2>
       <br>  
         <!-- Filtros de fechas -->
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
        <table id="tablaFormularios" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>N° Formulario</th>
                            <th>Fecha Registro</th>
                            <th>Nombre</th>
                            <th>Documento</th>
                            <th>Email</th>
                            <?php for ($i = 1; $i <= $maxEquipos; $i++): ?>
                                <th>Equipo <?= $i ?></th>
                                <th>Serial <?= $i ?></th>
                            <?php endfor; ?>
                            
                            <th>Estado Trabajador</th>
                            <th>Fecha Trabajador</th>
                            <th>Comentarios</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($formularios as $formulario): 
                            $equipos = parseList($formulario['equipos'] ?? '');
                            $seriales = parseList($formulario['seriales'] ?? '');
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($formulario['numero_formulario']?? '') ?></td>
                            <td><?= date('Y-m-d', strtotime($formulario['fecha_registro'])) ?></td>
                            <td><?= htmlspecialchars($formulario['nombre']?? '') ?></td>
                            <td><?= htmlspecialchars($formulario['documento']?? '') ?></td>
                            <td><?= htmlspecialchars($formulario['email']?? '') ?></td>
                    
                            <?php for ($i = 0; $i < $maxEquipos; $i++): 
                                $equipo = $equipos[$i] ?? '-';
                                $serial = is_array($seriales) 
                                    ? ($seriales[$equipo] ?? '-') 
                                    : '-';
                            ?>
                                <td><?= htmlspecialchars($equipo) ?></td>
                                <td><?= htmlspecialchars($serial) ?></td>
                            <?php endfor; ?>
                    
                            <td><?= htmlspecialchars($formulario['estado_trabajador']?? '') ?></td>
                            <td><?= htmlspecialchars($formulario['fecha_trabajador']?? '') ?></td>
                            <td><?= nl2br(htmlspecialchars($formulario['comentarios_trabajador']?? '')) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
        </table>
    </div>
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
    (function() {
        $(document).ready(function () {
    console.log('Document ready, initializing DataTable...');

    var tabla = $('#tablaFormularios').DataTable({
        scrollX: true,
        responsive: true,
        pageLength: 5,
        lengthChange: false,
        autoWidth: false,
        paging: true,
        info: false,
        searching: true,
         dom: 'Bfrtip',

        buttons: [
            {
                extend: 'excelHtml5',     // para exportar a Excel
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success custom-excel-btn'
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json',
            paginate: {
                previous: '‹',
                next: '›',
                first: '«',
                last: '»'
            }
        }
    });


   // Filtro personalizado de fechas
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var start = $('#start_date').val();
            var end = $('#end_date').val();

            if (!start || !end) return true;

            var min = new Date(start);
            var max = new Date(end);

            var dateStr = data[1]; // Asumiendo que columna 1 tiene fecha 'YYYY-MM-DD'
            var date = new Date(dateStr);

            return date >= min && date <= max;
        }
    );

    // Botón FILTRAR
    $('#applyFilter').on('click', function () {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        if (!startDate || !endDate) {
            Swal.fire('Campos incompletos', 'Por favor selecciona ambas fechas', 'warning');
            return;
        }

        tabla.draw();
    });

    // Botón RESTABLECER
    $('#resetFilter').on('click', function () {
        $('#start_date').val('');
        $('#end_date').val('');
        tabla.search('').columns().search('').draw(); // <- Esto reinicia todo el filtrado
    });
});

})();

</script>

