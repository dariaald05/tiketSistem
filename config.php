<?php
/**
 * ==========================================
 * Configuración Global - Sistema de Tickets
 * ==========================================
 * Este archivo SOLO contiene configuraciones.
 * NO registra usuarios.
 * NO tiene HTML ni diseño.
 */

// -------------------------------
// 1. CONFIGURACIÓN BASE DE DATOS
// -------------------------------
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');        // Usuario MySQL
define('DB_PASSWORD', '123456'); // ⚠️ Cambia por la real
define('DB_NAME', 'sistematickets');

// Conexión MySQL
$conexion = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

// Forzar UTF-8 (buena práctica)
$conexion->set_charset("utf8");


// -------------------------------
// 2. CONFIGURACIÓN reCAPTCHA V2
// -------------------------------
define('RECAPTCHA_SITE_KEY', '6LeILi0sAAAAAFekhG0_MiH8HzgWqOaURRCjccv3');
define('RECAPTCHA_SECRET_KEY', '6LeILi0sAAAAAJT1Isr0NqAgQzdRi9DFjotExrVe');


// -------------------------------
// 3. CONFIGURACIÓN DE SESIONES
// -------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// -------------------------------
// 4. CONFIGURACIÓN DE ERRORES (Desarrollo)
// -------------------------------
// En producción, cambia a false para no mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurar zona horaria
date_default_timezone_set('America/Mexico_City'); // Cambia según tu zona


// -------------------------------
// 5. CONSTANTES DE LA APLICACIÓN
// -------------------------------
define('APP_NAME', 'Sistema de Tickets');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/tiketSistem/'); // Cambia en producción

// Configuración de email (para notificaciones futuras)
define('SMTP_HOST', 'smtp.gmail.com'); // Ejemplo
define('SMTP_PORT', 587);
define('SMTP_USER', 'tuemail@gmail.com');
define('SMTP_PASS', 'tucontraseña');
define('EMAIL_FROM', 'noreply@tusistema.com');

// Configuración de uploads (si se necesitan archivos)
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Crear directorio de uploads si no existe
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}


// -------------------------------
// 6. FUNCIONES DE UTILIDAD
// -------------------------------
/**
 * Función para sanitizar entrada de usuario
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Función para redirigir con mensaje
 */
function redirect_with_message($url, $message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: $url");
    exit;
}

/**
 * Función para obtener mensaje flash
 */
function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Función para verificar si el usuario está logueado
 */
function is_logged_in() {
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}

/**
 * Función para obtener datos del usuario actual
 */
 function obtener_usuario_actual()() {
    if (is_logged_in()) {
        return [
            'id' => $_SESSION["id"],
            'nombre' => $_SESSION["nombre"]
        ];
    }
    return null;
}

?>

