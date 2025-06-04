<?php

include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');

$sql = "SELECT 
    a.*, 
    t.nombre_completo, 
    t.numero_documento, 
    c.nombre AS nombre_campana, 
    j.nombre_completo AS nombre_jefe
FROM tb_ausencias a
LEFT JOIN trabajadores t ON t.id = a.id_trabajador
LEFT JOIN tb_campanas c ON c.id = a.id_campana
LEFT JOIN trabajadores j ON j.id = a.id_jefe
ORDER BY a.id DESC";

$ausencias = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
   <link href="<?php echo $URL; ?>/librerias/DataTables/datatables.min.css" rel="stylesheet">
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
</head>
<body>
  <!-- HTML -->
<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-12">
        <div class="card-custom">
            <h1 class="text-center">Listado de Ausencias</h1>
            
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
        <table id="tablaAusencias" class="table table-striped table-bordered w-100">
          <thead class="bg-light text-dark">
            <tr>
              <th>#</th>
              <th>Nro Formulario</th>
              <th>Fecha Registro</th>
              <th>Trabajador</th>
              <th>Documento</th>
              <th>Campaña</th>
              <th>Jefe</th>
              <th>Tipo</th>
              <th>Inicio</th>
              <th>Fin</th>
              <th>Estado Team Lead</th>
              <th>Fecha TL</th>
              <th>Estado RRHH</th>
              <th>Fecha RRHH</th>
              <th>Razón Rechazo</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php $contador = 0; ?>
            <?php foreach ($ausencias as $a): ?>
              <tr>
                  <td><?= ++$contador ?></td>
                  <td><?= htmlspecialchars($a['numero_formulario'] ?? '') ?></td>
                  <td><?= !empty($a['fecha_registro']) ? date('d/m/Y H:i', strtotime($a['fecha_registro'])) : '' ?></td>
                  <td><?= htmlspecialchars($a['nombre_completo'] ?? '') ?></td>
                  <td><?= htmlspecialchars($a['numero_documento'] ?? '') ?></td>
                  <td><?= htmlspecialchars($a['nombre_campana'] ?? '') ?></td>
                  <td><?= htmlspecialchars($a['nombre_jefe'] ?? '') ?></td>
                  <td><?= htmlspecialchars($a['tipo_ausencia'] ?? '') ?></td>
                  <td><?= !empty($a['fecha_inicio']) ? date('d/m/Y', strtotime($a['fecha_inicio'])) : '' ?></td>
                  <td><?= !empty($a['fecha_fin']) ? date('d/m/Y', strtotime($a['fecha_fin'])) : '' ?></td>
                  <td><?= htmlspecialchars($a['estado_team_lead'] ?? '') ?></td>
                  <td><?= !empty($a['fecha_team_lead']) ? date('d/m/Y', strtotime($a['fecha_team_lead'])) : '' ?></td>
                  <td><?= htmlspecialchars($a['estado_rrhh'] ?? '') ?></td>
                  <td><?= !empty($a['fecha_rrhh']) ? date('d/m/Y', strtotime($a['fecha_rrhh'])) : '' ?></td>
                  <td><?= htmlspecialchars($a['razon_rechazo'] ?? '') ?></td>
                 <td>
<?php if (!empty($a['comprobantes'])): ?>
    <?php $final_url = $URL . '/Ausencia/' . $a['comprobantes']; ?>
    <a href="<?= htmlspecialchars($final_url) ?>" class="btn btn-sm btn-primary" download target="_blank">
        Descargar Foto
    </a>
  
<?php else: ?>
    <span class="text-muted">Sin foto</span>
<?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
</div>
      
    </div>
  </div>
</div>

<!-- Scripts al final para evitar errores -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/DataTables/datatables.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    console.log('Document ready, initializing DataTable...');

    var tabla = $('#tablaAusencias').DataTable({
        scrollX: true,
        responsive: true,
        pageLength: 5,
        lengthChange: false,
        autoWidth: false,
        paging: true,
        info: false,
        searching: true,

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
</script>

<script>
let beaconSent = false;

// Guardar el tiempo de inicio real cuando se abre la página
const tiempoInicio = performance.now();

window.addEventListener("beforeunload", function () {
    if (beaconSent) return;

    // Obtener uso de RAM si está disponible
    let ramUsageMb = null;
    try {
        if (performance.memory) {
            ramUsageMb = Math.round(performance.memory.usedJSHeapSize / 1024 / 1024); // en MB
        }
    } catch (e) {
        console.warn("No se pudo obtener uso de RAM:", e);
    }

    // Calcular tiempo de uso de la página (aproximado, en segundos)
    const tiempoCPU = Math.round(performance.now() / 1000);

    // Preparar datos para enviar al servidor
    const payload = {
        id_conexion: '<?php echo $_SESSION['id_conexion'] ?? ''; ?>',
        pagina: '<?php echo $_SERVER['REQUEST_URI']; ?>',
        tiempo_inicio: '<?php echo $_SESSION['tiempo_inicio'] ?? microtime(true); ?>',
        ram_usage_mb: ramUsageMb,
        tiempo_cpu: tiempoCPU
    };

    // Enviar con sendBeacon
    const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
    navigator.sendBeacon('<?php echo $URL; ?>/sistema/cerrar_pagina.php', blob);

    beaconSent = true;
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>



