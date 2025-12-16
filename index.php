<?php
// index.php
require_once "config.php";

/*
 |-------------------------------------------------
 | Verificar si el usuario ya inició sesión
 |-------------------------------------------------
 | Si está logueado, redirige al sistema de tickets
 */
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: tickets.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Tickets - Inicio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Estilos generales -->
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #ede9fe, #ddd6fe, #c4b5fd);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: #ffffff;
            padding: 3rem;
            border-radius: 18px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 40px rgba(94, 53, 177, 0.25);
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            font-size: 2.6em;
            color: #5E35B1;
            margin-bottom: 15px;
        }

        p {
            font-size: 1.15em;
            color: #7E57C2;
            margin-bottom: 40px;
        }

        .call-to-action {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .action-button {
            width: 220px;
            text-align: center;
            padding: 15px 30px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .login-btn {
            background: linear-gradient(135deg, #7c3aed, #5E35B1);
            color: #ffffff;
        }

        .register-btn {
            background-color: #ede9fe;
            color: #5E35B1;
            border: 2px solid #5E35B1;
        }

        .action-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(124, 58, 237, 0.4);
            opacity: 0.95;
        }

        .footer {
            margin-top: 35px;
            font-size: 0.9em;
            color: #6b7280;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>💜 Sistema de Tickets</h2>

        <p>
            Bienvenido al sistema de gestión de incidencias.
            Inicia sesión o crea una cuenta para continuar.
        </p>

        <div class="call-to-action">
            <a href="login.php" class="action-button login-btn">Iniciar Sesión</a>
            <a href="registro.php" class="action-button register-btn">Registrarse</a>
        </div>

        <div class="footer">
            Proyecto académico · Servidor LAMP
        </div>
    </div>

</body>
</html>

