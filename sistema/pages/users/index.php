<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../app/controllers/config.php');


if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

$id_conexion = $_SESSION['id_conexion'] ?? null;

if ($id_conexion) {
    $pagina = $_SERVER['REQUEST_URI'];
    $fecha_apertura = date("Y-m-d H:i:s");

    // Verificar si ya existe una entrada abierta para esta página y conexión
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM paginas_abiertas WHERE id_conexion = :id_conexion AND pagina = :pagina AND estado = 'abierta'");
    if (!$stmt) {
        die('Error al preparar la consulta: ' . implode(" | ", $pdo->errorInfo()));
    }

    $stmt->execute([
        ':id_conexion' => $id_conexion,
        ':pagina' => $pagina
    ]);

    $total = $stmt->fetchColumn();

    if ($total == 0) {
        // Si no existe, insertar una nueva entrada
        $stmt = $pdo->prepare("INSERT INTO paginas_abiertas (id_conexion, pagina, fecha_apertura, estado) VALUES (:id_conexion, :pagina, :fecha_apertura, 'abierta')");
        if (!$stmt) {
            die('Error al preparar la consulta de inserción: ' . implode(" | ", $pdo->errorInfo()));
        }

        $stmt->execute([
            ':id_conexion' => $id_conexion,
            ':pagina' => $pagina,
            ':fecha_apertura' => $fecha_apertura
        ]);
    }
}


// Consulta de usuarios
$sql = "SELECT u.id_usuarios, u.nombres, u.email, r.rol AS rol
        FROM tb_usuarios u
        LEFT JOIN tb_roles r ON u.id_rol = r.id_rol";
$sentencia = $pdo->query($sql);
$usuarios_datos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
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
  <div class="row">
    <div class="col-12">
      <h1 class="text-center">Listado de Usuarios</h1>
      <div class="table-responsive">
        <table id="tablaUsuarios" class="table table-striped table-bordered w-100">
          <thead class="bg-light text-dark">
            <tr>
              <th>Nro</th>
              <th>Nombres</th>
              <th>Email</th>
              <th>Rol</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php $contador = 0; ?>
            <?php foreach ($usuarios_datos as $usuarios_dato): ?>
                  <tr>
                    <td><?= htmlspecialchars($usuarios_dato['id_usuarios']) ?></td>
                    <td><?= htmlspecialchars($usuarios_dato['nombres']) ?></td>
                    <td><?= htmlspecialchars($usuarios_dato['email']) ?></td>
                    <td><?= htmlspecialchars($usuarios_dato['rol'] ?? 'Sin rol') ?></td>
                    <td>
                         <div class="d-flex gap-2">
                          <a href="#" 
                         class="btn btn-sm btn-success enlaceDinamicos"
                         data-link="update_user"
                         data-id="<?= $usuarios_dato['id_usuarios'] ?>"
                         >
                        Editar
                     </a>
                 
                       <a href="#" class="btn btn-sm btn-danger btn-eliminar"
                           data-id="<?= $usuarios_dato['id_usuarios'] ?>"
                           data-email="<?= $usuarios_dato['email'] ?>">
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
    var tabla = $('#tablaUsuarios').DataTable({
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
    
   document.querySelectorAll('.btn-eliminar').forEach(btn => {
  btn.addEventListener('click', function (e) {
    e.preventDefault();
    const id = this.dataset.id;
    const email = this.dataset.email;

    Swal.fire({
      title: '¿Estás seguro?',
      text: "Esta acción eliminará al usuario.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('<?= $URL ?>/sistema/pages/users/delete.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `id_usuarios=${id}&email=${encodeURIComponent(email)}`
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            Swal.fire('¡Eliminado!', data.message, 'success').then(() => {
              location.reload();
            });
          } else {
            Swal.fire('Error', data.message, 'error');
          }
        })
        .catch(err => {
          Swal.fire('Error', 'Error al conectar con el servidor.', 'error');
        });
      }
    });
  });
});
});

})();

</script>
      



