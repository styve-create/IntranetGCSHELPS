<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');
?>

<link rel="stylesheet" href="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?php echo $URL; ?>/librerias/vendor/npm-asset/bootstrap/dist/css/bootstrap.min.css">

<div id="contenido-principal">
   <div class="container mt-5">
  <h3>Usuarios Conectados en Tiempo Real</h3>
  <table class="table table-bordered table-hover mt-3">
    <thead class="table">
      <tr>
        <th>Nombre</th>
        <th>Sistema</th>
        <th>IP</th>
        <th>Fecha de Ingreso</th>
        <th>Fecha Ultima Actividad</th>
        <th>Ubicación</th>
        <th>Rango</th>
      </tr>
    </thead>
    <tbody id="tabla-usuarios">
      
    </tbody>
  </table>
</div>

<div class="container mt-5">
  <h3>Actividad diaria por Usuarios</h3>
  <table class="table table-bordered table-hover mt-3">
    <thead class="table">
      <tr>
        <th>Nombre</th>
        <th>Fecha</th>
        <th>Total de Páginas</th>
        <th>Cantidad Conexiones</th>
      </tr>
    </thead>
    <tbody id="estado-servidor">
      <!-- Se llena con JS -->
    </tbody>
  </table>
</div> 
</div>



<script src="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function() {
       
async function cargarPaginasAbiertas() {
    try {
      const res = await fetch(`/intranet/sistema/pages/controlusers/obtenerPaginasAbiertas.php?t=${Date.now()}`);
      const datos = await res.json();

      const tbody = document.getElementById('estado-servidor');
      tbody.innerHTML = '';

      datos.forEach(pagina => {
        tbody.innerHTML += `
          <tr>
            <td>${pagina.nombre}</td>
            <td>${pagina.fecha}</td>
            <td>${pagina.total_paginas}</td>
            <td>${pagina.conexiones}</td>
          </tr>`;
      });
    } catch (error) {
      console.error("Error al cargar páginas abiertas:", error);
    }
  }
  cargarPaginasAbiertas();
  setInterval(cargarPaginasAbiertas, 5000);
        })();
 
</script>


<script>
(function () {
  const tablaUsuarios = document.getElementById('tabla-usuarios');
  let ultimaRespuestaJSON = '';

  async function cargarUsuariosConectados() {
    try {
      const res = await fetch(`/intranet/sistema/pages/controlusers/obtener_usuarios.php?t=${Date.now()}`, {
  credentials: 'include'
});
      if (!res.ok) throw new Error("Error en la respuesta del servidor");

      const datos = await res.json();
      const jsonActual = JSON.stringify(datos);

      // Solo actualizar el DOM si hubo cambios
      if (jsonActual !== ultimaRespuestaJSON) {
        ultimaRespuestaJSON = jsonActual;

        tablaUsuarios.innerHTML = ''; // Limpiar tabla

        for (const usuario of datos) {
          tablaUsuarios.innerHTML += `
            <tr>
              <td>${usuario.nombre}</td>
              <td>${usuario.sistema}</td>
              <td>${usuario.ip}</td>
              <td>${usuario.fecha_ingreso}</td>
              <td>${usuario.ultima_actividad}</td>
              <td>${usuario.ubicacion}</td>
              <td>${usuario.rango}</td>
            </tr>`;
        }
      }
    } catch (error) {
      console.warn('Error al consultar usuarios conectados:', error);
    }
  }

  // Ejecutar inmediatamente y luego cada 5 segundos
  cargarUsuariosConectados();
  setInterval(cargarUsuariosConectados, 5000);
})();
</script>



