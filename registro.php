<?php
// Asegúrate de incluir tu conexión a la BD
require_once 'config.php';

$errores = [];
$exito = "";

// Inicializar variables para mantener los datos en el formulario si hay error
$nombre   = "";
$email    = "";
$password = "";
$confirm  = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre   = trim($_POST["nombre"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm_password"] ?? "";

    // Validaciones básicas
    if ($nombre === "" || $email === "" || $password === "" || $confirm === "") {
        $errores[] = "Todos los campos son obligatorios.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }

    if (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }

    if ($password !== $confirm) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    /*
     |-------------------------------------------------
     | Verificar si el email ya existe
     |-------------------------------------------------
     */
    if (empty($errores)) {
        // Asegúrate de que $conexion venga de config.php
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $conexion->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errores[] = "Este correo ya está registrado.";
            }
            $stmt->close();
        } else {
            $errores[] = "Error al verificar el usuario (SQL).";
        }
    }

    /*
     |-------------------------------------------------
     | Insertar usuario
     |-------------------------------------------------
     */
    if (empty($errores)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // NOTA IMPORTANTE: Revisa en tu base de datos si la columna se llama
        // 'password' o 'contrasena'. Aquí estoy usando 'password'.
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sss", $nombre, $email, $hash);

            if ($stmt->execute()) {
                // AQUÍ ES DONDE SE CORTABA TU CÓDIGO ANTERIORMENTE
                $exito = "Registro completado con éxito. <a href='login.php'>Inicia sesión aquí</a>.";
                // Limpiar campos
                $nombre = $email = $password = $confirm = "";
            } else {
                $errores[] = "Error al registrar en base de datos: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errores[] = "Error en la preparación de la inserción.";
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
        /* ESTILO MORADO CLARO */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #9c27b0; /* Morado medio */
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #7b1fa2; /* Morado oscuro */
            font-weight: 600;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1bee7; /* Morado muy claro */
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #ba68c8; /* Morado claro */
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #ba68c8; /* Morado claro */
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #9c27b0; /* Morado medio */
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #fce4ec;
            color: #c2185b;
            border: 1px solid #f8bbd0;
        }

        .alert-success {
            background-color: #f3e5f5;
            color: #6a1b9a;
            border: 1px solid #e1bee7;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .login-link a {
            color: #ba68c8;
            text-decoration: none;
            font-weight: bold;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Crear Cuenta</h2>

        <!-- Mostrar Errores -->
        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errores as $error): ?>
                    <p style="margin: 0;">• <?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Mostrar Éxito -->
        <?php if (!empty($exito)): ?>
            <div class="alert alert-success">
                <?php echo $exito; ?>
            </div>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit">Registrarse</button>
        </form>

        <div class="login-link">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
        </div>
    </div>

</body>
</html>
