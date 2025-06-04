<?php
include_once(__DIR__ . '/../app/controllers/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_name('mi_sesion_personalizada');
    session_start();
}

$id_conexion = $_SESSION['id_conexion'] ?? null;

if ($id_conexion) {
    // Establecer la zona horaria de Colombia
    date_default_timezone_set('America/Bogota');

    $pagina = $_SERVER['REQUEST_URI'];
    $fecha_apertura = date("Y-m-d H:i:s");
    
 
    
    // Verificar si ya existe una entrada para esta conexión
    $stmt = $pdo->prepare("SELECT * FROM paginas_abiertas WHERE id_conexion = :id_conexion");
    if (!$stmt) {
        die('Error al preparar la consulta: ' . implode(" | ", $pdo->errorInfo()));
    }

    $stmt->execute([ ':id_conexion' => $id_conexion ]);
    $pagina_abierta = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no existe, inicializamos un arreglo para las páginas abiertas
    if (!$pagina_abierta) {
        $paginas_visitadas = [];
    } else {
        // Si ya existe, decodificamos el arreglo JSON de las páginas visitadas
        $paginas_visitadas = json_decode($pagina_abierta['pagina'], true);
    }

    // Agregar la nueva página al arreglo
    $paginas_visitadas[] = [
        'pagina' => $pagina,
        'fecha_apertura' => $fecha_apertura,
        'estado' => 'abierta',  // Estado de la página
       
    ];

    // Convertir el arreglo de páginas visitadas a JSON
    $paginas_json = json_encode($paginas_visitadas);

    // Insertar o actualizar la entrada en la tabla paginas_abiertas
    if ($pagina_abierta) {
        // Si ya existe un registro, lo actualizamos con la nueva lista de páginas visitadas
        $stmt = $pdo->prepare("UPDATE paginas_abiertas SET pagina = :pagina WHERE id_conexion = :id_conexion");
        $stmt->execute([
            ':pagina' => $paginas_json,
            ':id_conexion' => $id_conexion
        ]);
    } else {
        // Si no existe, lo insertamos en la base de datos
       $stmt = $pdo->prepare("INSERT INTO paginas_abiertas (id_conexion, pagina, fecha_apertura, estado) 
                       VALUES (:id_conexion, :pagina, :fecha_apertura, 'abierta')");
$stmt->execute([
    ':id_conexion' => $id_conexion,
    ':pagina' => $paginas_json,
    ':fecha_apertura' => $fecha_apertura
]);
    }
}
?>