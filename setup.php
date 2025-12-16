<?php
/**
 * Script de configuración de la base de datos para el Sistema de Tickets
 * Ejecuta este archivo una vez para crear la base de datos y las tablas necesarias.
 */

// Configuración de la base de datos (ajusta si es necesario)
$servername = "localhost";
$username = "root";
$password = "123456"; // Cambia por tu contraseña real
$dbname = "sistematickets";

// Crear conexión sin especificar base de datos
$conn = new mysqli($servername, $username, $password);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Crear base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8 COLLATE utf8_general_ci";
if ($conn->query($sql) === TRUE) {
    echo "Base de datos '$dbname' creada o ya existe.<br>";
} else {
    echo "Error creando base de datos: " . $conn->error . "<br>";
}

// Seleccionar la base de datos
$conn->select_db($dbname);

// Crear tabla de usuarios
$sql_usuarios = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_usuarios) === TRUE) {
    echo "Tabla 'usuarios' creada o ya existe.<br>";
} else {
    echo "Error creando tabla usuarios: " . $conn->error . "<br>";
}

// Crear tabla de tickets (para el sistema completo)
$sql_tickets = "CREATE TABLE IF NOT EXISTS tickets (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT(11) NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    estado ENUM('abierto', 'en_progreso', 'cerrado') DEFAULT 'abierto',
    prioridad ENUM('baja', 'media', 'alta') DEFAULT 'media',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
)";

if ($conn->query($sql_tickets) === TRUE) {
    echo "Tabla 'tickets' creada o ya existe.<br>";
} else {
    echo "Error creando tabla tickets: " . $conn->error . "<br>";
}

$conn->close();

echo "<br>Configuración completada. Puedes eliminar este archivo después de ejecutarlo.";
?></content>
<parameter name="filePath">c:\Users\Dariana Ruiz\Desktop\tiketSistem\setup.php