<?php
require_once "config.php";

/*
 |-------------------------------------------------
 | Protección: solo usuarios logueados
 |-------------------------------------------------
 */
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";
$usuario_id = $_SESSION["id"];

/*
 |-------------------------------------------------
 | Crear nuevo ticket
 |-------------------------------------------------
 */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["crear_ticket"])) {
    $prioridad = $_POST["prioridad"] ?? "Normal";
    $descripcion = trim($_POST["descripcion"] ?? "");

    if (empty($descripcion)) {
        $error = "La descripción del problema es obligatoria.";
    } else {
        $sql = "INSERT INTO tickets (usuario_id, prioridad, descripcion)
                VALUES (?, ?, ?)";

        $stmt = $conexion->prepare($sql);

        if (!$stmt) {
            $error = "Error en la consulta: " . $conexion->error;
        } else {
            $stmt->bind_param("iss", $usuario_id, $prioridad, $descripcion);

            if ($stmt->execute()) {
                $success = "✅ Ticket registrado exitosamente.";
            } else {
                $error = "❌ Error al registrar el ticket.";
            }
            $stmt->close();
        }
    }
}

/*
 |-------------------------------------------------
 | Obtener tickets del usuario
 |-------------------------------------------------
 */
$tickets_del_usuario = [];

$sql_tickets = "SELECT id, prioridad, descripcion, estado, fecha_creacion
                FROM tickets
                WHERE usuario_id = ?
                ORDER BY fecha_creacion DESC";

$stmt_tickets = $conexion->prepare($sql_tickets);
$stmt_tickets->bind_param("i", $usuario_id);
$stmt_tickets->execute();
$resultado = $stmt_tickets->get_result();

while ($row = $resultado->fetch_assoc()) {
    $tickets_del_usuario[] = $row;
}

$stmt_tickets->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Tickets - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #ede9fe, #ddd6fe, #c4b5fd);
            font-family: 'Segoe UI', Tahoma, sans-serif;
            padding: 40px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(94, 53, 177, 0.25);
        }

        h2 {
            color: #5E35B1;
            margin-bottom: 25px;
        }

        .tickets-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 30px;
        }

        .ticket-form-container,
        .ticket-list-container {
            background: #f5f3ff;
            padding: 1.8rem;
            border-radius: 15px;
        }

        h3 {
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

        select, textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #c4b5fd;
        }

        .btn-primary {
            background: linear-gradient(135deg, #7c3aed, #5E35B1);
            color: white;
            padding: 12px;
            border-radius: 10px;
            border: none;
            width: 100%;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #4527a0;
            transform: translateY(-2px);
        }

        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            background: #d1fae5;
            color: #065f46;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .ticket-card {
            background: #ffffff;
            padding: 1.2rem;
            border-radius: 12px;
            margin-bottom: 15px;
            border-left: 6px solid #7c3aed;
        }

        .priority-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            color: white;
        }

        .priority-Baja { background: #10b981; }
        .priority-Normal { background: #6366f1; }
        .priority-Alta { background: #f59e0b; }
        .priority-Urgente { background: #ef4444; }

        .status-badge {
            background: #ede9fe;
            color: #5E35B1;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            margin-left: 8px;
        }

        .top-actions {
            text-align: right;
            margin-bottom: 20px;
        }

        .logout {
            text-decoration: none;
            color: white;
            background: #5E35B1;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="top-actions">
        <a href="logout.php" class="logout">Cerrar sesión</a>
    </div>

    <h2>📝 Bienvenido, <?= htmlspecialchars($_SESSION["nombre"] ?? "Usuario") ?></h2>

    <?php if ($success): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <div class="tickets-grid">

        <!-- Formulario -->
        <div class="ticket-form-container">
            <h3>Nuevo Ticket</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Prioridad</label>
                    <select name="prioridad">
                        <option>Baja</option>
                        <option selected>Normal</option>
                        <option>Alta</option>
                        <option>Urgente</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Descripción del problema</label>
                    <textarea name="descripcion" rows="5" required></textarea>
                </div>

                <input type="submit" name="crear_ticket" value="Crear Ticket" class="btn-primary">
            </form>
        </div>

        <!-- Lista -->
        <div class="ticket-list-container">
            <h3>Mis Tickets (<?= count($tickets_del_usuario) ?>)</h3>

            <?php if ($tickets_del_usuario): ?>
                <?php foreach ($tickets_del_usuario as $ticket): ?>
                    <div class="ticket-card">
                        <p>
                            <span class="priority-badge priority-<?= $ticket['prioridad'] ?>">
                                <?= $ticket['prioridad'] ?>
                            </span>
                            <span class="status-badge"><?= $ticket['estado'] ?></span>
                        </p>
                        <p><strong>Fecha:</strong> <?= date("d/m/Y H:i", strtotime($ticket['fecha_creacion'])) ?></p>
                        <p><?= htmlspecialchars($ticket['descripcion']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center;color:#6b7280;">No has registrado tickets aún.</p>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>
