<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../config.php');

// Función para registrar logs de depuración
function log_debug($message) {
    error_log($message, 3, __DIR__ . "/debug_log.txt");
}

session_name('mi_sesion_personalizada');
session_start();

if (isset($_POST['email']) && isset($_POST['password_user']) && !empty($_POST['email']) && !empty($_POST['password_user'])) {
    $email = $_POST['email'];
    $password_user = $_POST['password_user'];

   

    $sql = "SELECT * FROM tb_usuarios WHERE email = :email";
   

    try {
        $query = $pdo->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        
    } catch (Exception $e) {
        
        exit();
    }

    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        log_debug("Usuario encontrado: email = $email\n");
        $password_user_tabla = $usuario['password_user'];
        $nombres = $usuario['nombres'];

        // Verificar la contraseña
        $password_correct = false;

        if (password_verify($password_user, $password_user_tabla)) {
            $password_correct = true;
            
        } else if ($password_user === $password_user_tabla) {
            $password_correct = true;
            
        } 

     if ($password_correct) {
    // Verificar si ya hay una conexión activa
    $sql_check = "SELECT * FROM usuarios_conectados WHERE id_usuario = :id_usuario AND estado = 'conectado'";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([':id_usuario' => $usuario['id_usuarios']]);
    $conexion_activa = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($conexion_activa) {
        
        $_SESSION['mensaje'] = "Ya tienes una sesión activa en otro dispositivo o navegador.";
        header('Location: ' . $URL . '/index.php');
        exit();
    }
          
// Establecer la zona horaria de Colombia
date_default_timezone_set('America/Bogota');

$_SESSION['usuario_info'] = [
    'id' => $usuario['id_usuarios'],
    'email' => $email,
    'nombre' => $nombres,
    'rol' => !empty($usuario['id_rol']) ? $usuario['id_rol'] : '2',
    
];


function detectar_dispositivo($user_agent) {
    $user_agent = strtolower($user_agent);

    if (strpos($user_agent, 'mobile') !== false || strpos($user_agent, 'android') !== false || strpos($user_agent, 'iphone') !== false) {
        return 'Móvil';
    } elseif (strpos($user_agent, 'tablet') !== false || strpos($user_agent, 'ipad') !== false) {
        return 'Tablet';
    } elseif (strpos($user_agent, 'windows') !== false || strpos($user_agent, 'macintosh') !== false || strpos($user_agent, 'linux') !== false) {
        return 'PC o Laptop';
    } else {
        return 'Otro dispositivo';
    }
}

try {
    $email = $usuario['email'];
    $id_usuario = $usuario['id_usuarios']; 
    $nombre = $usuario['nombres'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $sistema = detectar_dispositivo($user_agent);
    $ip = $_SERVER['REMOTE_ADDR'];
    
$latitud = $_POST['latitud'] ?? null;
$longitud = $_POST['longitud'] ?? null;


$ubicacion = null;



    $id_conexion = uniqid('conn_', true);
    $_SESSION['id_conexion'] = $id_conexion;
    
    // Hora actual en Colombia
    $fecha_ingreso = date('Y-m-d H:i:s');
    $ultima_actividad = date('Y-m-d H:i:s');
    $estado = 'conectado';
    $rango = 'Dentro';
    
    // Para loguear lo que se va a insertar (opcional)
    $datos_insert = [
        ':id_usuario' => $id_usuario,
        ':nombre' => $nombre,
        ':sistema' => $sistema,
        ':ip' => $ip,
        ':fecha_ingreso' => $fecha_ingreso,
        ':ultima_actividad' => $ultima_actividad,
        ':estado' => $estado,
        ':email' => $email,
        ':id_conexion' => $id_conexion ,
        ':ubicacion' => $ubicacion,
        ':latitud' => $latitud,
        ':longitud' => $longitud,
        ':rango' => $rango
    ];

    

$sql_insert = "INSERT INTO usuarios_conectados 
(id_usuario, nombre, sistema, ip, fecha_ingreso, ultima_actividad, estado, email, id_conexion, ubicacion, latitud, longitud, rango) 
VALUES (:id_usuario, :nombre, :sistema, :ip, :fecha_ingreso, :ultima_actividad, :estado, :email, :id_conexion, :ubicacion, :latitud, :longitud, :rango)";
               
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute($datos_insert);
    
    
   

} catch (PDOException $e) {
    
}




header('Location: ' . $URL . '/sistema/index.php');
exit();
} else {
            $_SESSION['mensaje'] = "Datos incorrectos, vuelva a intentarlo.";
           
            header('Location: ' . $URL . '/index.php?error=login');
            exit();
        }
    } else {
        $_SESSION['mensaje'] = "Usuario no encontrado.";
        
        header('Location: ' . $URL . '/index.php?error=nouser');
        exit();
    }
} else {
    $_SESSION['mensaje'] = "Por favor, complete todos los campos del formulario.";
   
    header('Location: ' . $URL . '/index.php?error=incompleto');
    exit();
}
?>