<?php
// Configuración de base de datos
$db_host = "localhost";
$db_user = "root";  // CAMBIA ESTO
$db_password = "123456"; // CAMBIA ESTO
$db_name = "sistematickets"; // CAMBIA ESTO SI ES DIFERENTE

// Conexión
$conexion = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Función corregida para evitar el error "Cannot redeclare"
if (!function_exists('obtener_usuario_actual')) {
    function obtener_usuario_actual() {
        if (isset($_SESSION['usuario'])) {
            return $_SESSION['usuario'];
        }
        return null;
    }
}
?>
