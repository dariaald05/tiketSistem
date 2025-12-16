}<?php
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

