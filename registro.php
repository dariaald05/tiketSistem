<?php
require_once 'config.php';

$errores = [];
$exito = "";

// Inicializar variables
$nombre = $email = $password = $confirm = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST["nombre"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm_password"] ?? "";

    // Validaciones
    if (empty($nombre) || empty($email) || empty($password) || empty($confirm)) {
        $errores[] = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Correo inválido.";
    } elseif (strlen($password) < 4) { // Mínimo 4 caracteres para pruebas
        $errores[] = "La contraseña es muy corta.";
    } elseif ($password !== $confirm) {
        $errores[] = "Las contraseñas no coinciden.";
    } else {
        // Verificar si existe el email
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errores[] = "Este correo ya está registrado.";
            }
            $stmt->close();
        }

        // Si no hay errores, insertar
        if (empty($errores)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'usuario')";
            
            if ($stmt = $conexion->prepare($sql)) {
                $stmt->bind_param("sss", $nombre, $email, $hash);
                if ($stmt->execute()) {
                    $exito = "¡Cuenta creada! <a href='login.php'>Inicia sesión aquí</a>";
                    $nombre = $email = ""; // Limpiar campos
                } else {
                    $errores[] = "Error SQL: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema Tickets</title>
    <style>
        body { font-family: sans-serif; background: linear-gradient(135deg, #2e003e 0%, #6a0dad 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; color: #333; }
        .container { background: #fff; padding: 30px; border-radius: 10px; width: 100%; max-width: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        h2 { text-align: center; color: #4b0082; margin-top: 0; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #8e44ad; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background: #732d91; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; font-size: 14px; }
        .error { background: #fce4ec; color: #c2185b; border: 1px solid #f8bbd0; }
        .success { background: #f3e5f5; color: #6a1b9a; border: 1px solid #e1bee7; }
        a { color: #8e44ad; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro</h2>
        <?php if ($errores): ?>
            <div class="alert error"><?php echo implode('<br>', $errores); ?></div>
        <?php endif; ?>
        <?php if ($exito): ?>
            <div class="alert success"><?php echo $exito; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="nombre" placeholder="Nombre completo" value="<?php echo htmlspecialchars($nombre); ?>" required>
            <input type="email" name="email" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($email); ?>" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
        <p style="text-align:center; font-size:14px;">¿Ya tienes cuenta? <a href="login.php">Entrar</a></p>
    </div>
</body>
</html>
