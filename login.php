<?php
require_once "config.php";

/*
 |-------------------------------------------------
 | Si ya está logueado, redirigir
 |-------------------------------------------------
 */
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: tickets.php");
    exit;
}

$error = "";

/*
 |-------------------------------------------------
 | Procesar login
 |-------------------------------------------------
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } else {

        $sql = "SELECT id, nombre, password FROM usuarios WHERE email = ?";
        $stmt = $conexion->prepare($sql);

        if (!$stmt) {
            $error = "Error del sistema.";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows === 1) {
                $usuario = $resultado->fetch_assoc();

                if (password_verify($password, $usuario["password"])) {

                    // Crear sesión
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $usuario["id"];
                    $_SESSION["nombre"] = $usuario["nombre"];

                    header("Location: tickets.php");
                    exit;

                } else {
                    $error = "Contraseña incorrecta.";
                }

            } else {
                $error = "El usuario no existe.";
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Sistema de Tickets</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #ede9fe, #ddd6fe, #c4b5fd);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(94, 53, 177, 0.25);
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            color: #5E35B1;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            color: #5E35B1;
        }

        input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #c4b5fd;
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #7c3aed, #5E35B1);
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #4527a0;
            transform: translateY(-2px);
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }

        .extra {
            text-align: center;
            margin-top: 15px;
            font-size: 0.9em;
        }

        .extra a {
            color: #5E35B1;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>🔐 Iniciar Sesión</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Correo electrónico</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" required>
        </div>

        <input type="submit" value="Entrar" class="btn-primary">
    </form>

    <div class="extra">
        ¿No tienes cuenta?
        <a href="registro.php">Regístrate</a>
    </div>
</div>

</body>
</html>
