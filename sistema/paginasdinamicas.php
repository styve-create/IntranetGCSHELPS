<?php
$pagina = $_GET['page'] ?? null;

  $paginas_validas = [
          'home' => 'pages/home/index.php',
          'users' => 'pages/users/index.php',
          'edit_user' => 'pages/users/create.php',
          'controlusers' => 'pages/controlusers/index.php',
          'update_user' => 'pages/users/update.php',
          'formularioAusencias' => 'pages/formularioAusencias/index.php',
          'trabajadores' => 'pages/trabajadores/index.php',
          'update_trabajadores' => 'pages/trabajadores/updateTrabajadores.php',
          'ver_trabajadores' => 'pages/trabajadores/verTrabajadores.php',
          'new_trabajadores' => 'pages/trabajadores/createTrabajadores.php',
          'registros_huellas' => 'pages/registrosHuellas/index.php',
          'registros_excel' => 'pages/registrosHuellas/procesar_excel.php',
          'roles' => 'pages/roles/index.php',
          'jerarquias' => 'pages/jerarquias/index.php',
          'jerarquiasGuardarJefe' => 'pages/jerarquias/guardar_jefe.php',
          'inventarioRegistro' => 'pages/inventario/index.php',
          'listadoInventario' => 'pages/inventario/creacionInventario.php',
          'horario' => 'pages/horario/index.php',
          'horarioTrabajadores' => 'pages/horario/horarioTrabajadores.php',
          'horarioAdministrativo' => 'pages/horario/horarioAdministrativo.php',
          'horarioclockify' => 'pages/horario/clockify/index.php',
          'informacionTrabajador' => 'pages/informacion/index.php',
          'panelAdministrativoAnuncios' => 'pages/informacion/panelAdministrativoAnuncios.php',
          'Projectos' => 'pages/panelAdministrativoClockify/Projectos/index.php',
          
      ];
      
      if (!isset($pagina) || !array_key_exists($pagina, $paginas_validas)) {
    http_response_code(404);
    echo "❌ Página no válida.";
    exit;
}

include $paginas_validas[$pagina];


      
