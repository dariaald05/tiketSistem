<?php
require_once 'config.php';

// Usamos la función corregida para ver si hay sesión
$usuario_actual = obtener_usuario_actual();

// Si no hay usuario, mandar al login
if (!$usuario_actual) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 50px; text-align: center; }
        h1 { color: #6a0dad; }
        .btn { display: inline-block; padding: 10px 20px; background: #c2185b; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>¡Hola, <?php echo htmlspecialchars($usuario_actual); ?>!</h1>
    <p>Has iniciado sesión correctamente en el Sistema de Tickets.</p>
    <a href="logout.php" class="btn">Cerrar Sesión</a>
</body>
</html>
