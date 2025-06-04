<?php
// Definir las nuevas credenciales de la base de datos
define('SERVIDOR', 'localhost'); // El servidor de base de datos sigue siendo localhost
define('USUARIO', 'u589041854_SuperAdmin'); // Nuevo usuario
define('PASSWORD', 'Matematicas1@2345'); // Nueva contraseña
define('BD', 'u589041854_IntranetGlobal'); // Nuevo nombre de la base de datos

// Crear la conexión PDO con los nuevos parámetros
$servidor = "mysql:dbname=" . BD . ";host=" . SERVIDOR;

try {
    // Intentar establecer la conexión con PDO
    $pdo = new PDO($servidor, USUARIO, PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    // echo "Conexión exitosa"; // Puedes habilitar para depuración
} catch (PDOException $e) {
    // Si hay un error en la conexión, crea el directorio de logs si no existe
    $logDir = __DIR__ . '/logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true); // Crea el directorio con permisos adecuados
    }
    
    // Registrar el error en el archivo de log
    error_log("Error de conexión: " . $e->getMessage(), 3, $logDir . '/mi_sistema.log');
    
    // Mensaje amigable para el usuario
    echo "Error al conectar la base de datos";  
    exit();  // Detenemos la ejecución en caso de error
}

// Configuración de la URL base para el sistema
$URL = "https://gcshelps.com/intranet";  // Actualiza con la URL correcta si es necesario
date_default_timezone_set("America/Bogota"); // Establecer la zona horaria
$fechaHora = date('Y-m-d H:i:s'); // Obtener la fecha y hora actual
?>