<?php
$envPath = __DIR__ . '/../../config/config.env.php';  // Ruta relativa

if (!file_exists($envPath)) {
    die('Archivo de configuración no encontrado.');
}

$config = include($envPath);

define('SERVIDOR', $config['host']);
define('BD', $config['database']);
define('USUARIO', $config['username']);
define('PASSWORD', $config['password']);
define('SMTP_HOST',     $config['smtp_host']);
define('SMTP_USER',     $config['smtp_user']);
define('SMTP_PASS',     $config['smtp_pass']);
define('SMTP_PORT',     $config['smtp_port']);
define('SMTP_SECURE',   $config['smtp_secure']);
$servidor = "mysql:dbname=" . BD . ";host=" . SERVIDOR;

try {
    $pdo = new PDO($servidor, USUARIO, PASSWORD, [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ]);
} catch (PDOException $e) {
    $logDir = __DIR__ . '/../../storage/logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }

    error_log("[" . date('Y-m-d H:i:s') . "] Error de conexión: " . $e->getMessage() . PHP_EOL, 3, $logDir . '/mi_sistema.log');
    echo "Error al conectar con la base de datos.";
    exit();
}

// Configuración de la URL base para el sistema
$URL = "https://gcshelps.com/intranet";  // Actualiza con la URL correcta si es necesario
date_default_timezone_set("America/Bogota"); // Establecer la zona horaria
$fechaHora = date('Y-m-d H:i:s'); // Obtener la fecha y hora actual
?>