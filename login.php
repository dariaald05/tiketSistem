<?php
require_once 'config.php';
session_start();

// Si ya está logueado, enviar al index
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        $error = "Por favor ingresa correo y contraseña.";
    } else {
        $sql = "SELECT id, nombre, password, rol FROM usuarios WHERE email = ?";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $nombre, $hashed_password, $rol);
                $stmt->fetch();
                
                // Verificar contraseña
                if (password_verify($password, $hashed_password)) {
                    // ¡Login Exitoso! Guardamos datos en sesión
                    $_SESSION['usuario_id'] = $id;
                    $_SESSION['usuario'] = $nombre; // Usado por obtener_usuario_actual
                    $_SESSION['rol'] = $rol;
                    
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "La contraseña es incorrecta.";
                }
            } else {
                $error = "No existe una cuenta con ese correo.";
            }
            $stmt->close();
        } else {
            $error = "Error del sistema.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Tickets</title>
    <style>
        body { font-family: sans-serif; background: linear-gradient(135deg, #2e003e 0%, #6a0dad 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; color: #333; }
        .container { background: #fff; padding: 40px; border-radius: 10px; width: 100%; max-width: 350px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        h2 { text-align: center; color: #4b0082; margin-top: 0; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #8e44ad; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background: #732d91; }
        .alert { background: #fce4ec; color: #c2185b; padding: 10px; margin-bottom: 15px; border-radius: 5px; font-size: 14px; text-align: center; border: 1px solid #f8bbd0; }
        a { color: #8e44ad; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Iniciar Sesión</h2>
        <?php if ($error): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
        <p style="text-align:center; font-size:14px; margin-top: 20px;">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
    </div>
</body>
</html>
