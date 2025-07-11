<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../../analisisRecursos.php');

include_once(__DIR__ . '/../../../app/controllers/config.php');

// Consulta de trabajadores
$sql = "SELECT 
    t.id, 
    t.nombre_completo, 
    t.numero_documento, 
    t.email, 
    c.nombre AS cargo, 
    cp.nombre AS campana, 
    t.estado
FROM trabajadores t
LEFT JOIN trabajadores_campanas tc ON t.id = tc.trabajador_id
LEFT JOIN cargos c ON tc.puesto_id = c.id
LEFT JOIN tb_campanas cp ON tc.campana_id = cp.id";
$sentencia = $pdo->query($sql);
$trabajadores = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Agrupar campañas por trabajador
$trabajadores_agrupados = [];
foreach ($trabajadores as $trabajador) {
    if (isset($trabajadores_agrupados[$trabajador['id']])) {
        $trabajadores_agrupados[$trabajador['id']]['campanas'][] = $trabajador['campana'];
    } else {
        $trabajadores_agrupados[$trabajador['id']] = [
            'id' => $trabajador['id'], 
            'nombre_completo' => $trabajador['nombre_completo'],
            'numero_documento' => $trabajador['numero_documento'],
            'email' => $trabajador['email'],
            'estado' => $trabajador['estado'],
            'cargo' => $trabajador['cargo'],
            'campanas' => [$trabajador['campana']],
        ];
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
        <div class="row">
            <div class="col-12">
                <div class="card-custom">
                  <h1 class="text-center">Listado de Trabajadores</h1>
                   <!-- Filtros de fechas -->

                <div class="table-responsive">
                    <br>
                    <table id="tablaTrabajadores" class="table table-striped table-bordered">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th>Nombre completo</th>
                                <th>Documento</th>
                                <th>Email</th>
                                <th>Campaña</th>
                                <th>Cargo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trabajadores_agrupados as $trabajador): ?>
                                <tr>
                                    <td><?= htmlspecialchars($trabajador['nombre_completo'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($trabajador['numero_documento'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($trabajador['email'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars(implode(", ", $trabajador['campanas'])) ?></td>
                                    <td><?= htmlspecialchars($trabajador['cargo'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($trabajador['estado'] ?? '-') ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="#" 
                                               class="btn  btn-primary enlaceDinamicos"
                                               data-link="ver_trabajadores"
                                               data-id="<?= $trabajador['id'] ?>">
                                              Ver
                                            </a>
                                            
                                            <a href="#" class="btn  btn-danger btn-eliminar"
                                               data-id="<?= $trabajador['id'] ?>"
                                               data-nombre="<?= htmlspecialchars($trabajador['nombre_completo']) ?>">
                                                Eliminar
                                            </a>
                                        </div>
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

        var tabla = $('#tablaTrabajadores').DataTable({
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

        // Script de eliminación con confirmación
        console.log('Script de eliminación cargado');
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.dataset.id;
                const nombre = this.dataset.nombre;

                console.log(`Eliminar trabajador con ID: ${id} y Nombre: ${nombre}`);

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `Esta acción eliminará al trabajador "${nombre}".`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log('Confirmación recibida, eliminando trabajador...');
                        fetch('<?= $URL ?>/sistema/pages/trabajadores/deleteTrabajador.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id=${encodeURIComponent(id)}`
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                console.log('Eliminación exitosa');
                                Swal.fire('¡Eliminado!', data.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                console.log('Error en la eliminación:', data.message);
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(() => {
                            console.log('Error al conectar con el servidor.');
                            Swal.fire('Error', 'Error al conectar con el servidor.', 'error');
                        });
                    }
                });
            });
        });

    });  
    
})();
</script>





