<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
include_once(__DIR__ . '/../../analisisRecursos.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios Conectados</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
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
      <!-- Se llena con JS -->
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


<script>
  async function cargarUsuarios() {
    try {
      const res = await fetch('<?= $URL ?>/sistema/pages/controlusers/obtener_usuarios.php');
      const datos = await res.json();

      const tbodyUsuarios = document.getElementById('tabla-usuarios');
      tbodyUsuarios.innerHTML = '';

      for (const usuario of datos) {
        tbodyUsuarios.innerHTML += `
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
    } catch (error) {
      console.error("Error al cargar usuarios:", error);
    }
  }

  async function cargarPaginasAbiertas() {
    try {
      const res = await fetch('<?= $URL ?>/sistema/pages/controlusers/obtenerPaginasAbiertas.php');
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

  // Carga inicial y actualización periódica cada 5 segundos
  cargarUsuarios();
  cargarPaginasAbiertas();
  setInterval(cargarUsuarios, 5000);
  setInterval(cargarPaginasAbiertas, 5000);
</script>

<!-- Registro de cierre de página -->
<script>
  let beaconSent = false;
  const tiempoInicio = performance.now();

  window.addEventListener("beforeunload", function () {
    if (beaconSent) return;

    let ramUsageMb = null;
    try {
      if (performance.memory) {
        ramUsageMb = Math.round(performance.memory.usedJSHeapSize / 1024 / 1024);
      }
    } catch (e) {
      console.warn("No se pudo obtener RAM:", e);
    }

    const tiempoCPU = Math.round(performance.now() / 1000);
    const payload = {
      id_conexion: '<?= $_SESSION['id_conexion'] ?? '' ?>',
      pagina: '<?= $_SERVER['REQUEST_URI'] ?>',
      tiempo_inicio: '<?= $_SESSION['tiempo_inicio'] ?? microtime(true) ?>',
      ram_usage_mb: ramUsageMb,
      tiempo_cpu: tiempoCPU
    };

    const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
    navigator.sendBeacon('<?= $URL ?>/sistema/cerrar_pagina.php', blob);
    beaconSent = true;
  });
</script>
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>