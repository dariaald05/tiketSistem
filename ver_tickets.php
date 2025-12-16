<?php
require_once "config.php";

/*
 |-------------------------------------------------
 | Proteger la página (solo usuarios logueados)
 |-------------------------------------------------
 */
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php");
    exit;
}

/*
 |-------------------------------------------------
 | Obtener tickets con nombre de usuario
 |-------------------------------------------------
 */
$sql = "
    SELECT usuarios.nombre, tickets.descripcion, tickets.fecha
    FROM tickets
    INNER JOIN usuarios ON tickets.usuario_id = usuarios.id
    ORDER BY tickets.fecha DESC
";

$resultado = $conexion->query($sql);

if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tickets Registrados</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #ede9fe, #ddd6fe, #c4b5fd);
            font-family: 'Segoe UI', Tahoma, sans-serif;
            padding: 40px;
        }

        .container {
            background: #ffffff;
            max-width: 900px;
            margin: auto;
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
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
        }

        thead {
            background: linear-gradient(135deg, #7c3aed, #5E35B1);
            color: white;
        }

        th, td {
            padding: 14px;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f5f3ff;
        }

        tr:hover {
            background-color: #ede9fe;
        }

        .btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            background: #5E35B1;
            color: white;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #4527a0;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="c

