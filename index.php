<?php
require_once 'config.php';
session_start();

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$error = "";

// ---------------------------------------------------
// 1. LÓGICA PARA GUARDAR TICKET
// ---------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_ticket'])) {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $prioridad = $_POST['prioridad'] ?? 'Baja';
    $descripcion = $_POST['descripcion'] ?? '';
    $usuario_id = $_SESSION['usuario_id'];

    if (empty($nombre) || empty($descripcion)) {
        $error = "Nombre y descripción son obligatorios.";
    } else {
        $sql = "INSERT INTO tickets (usuario_id, nombre, email, prioridad, descripcion) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param("issss", $usuario_id, $nombre, $email, $prioridad, $descripcion);
            if ($stmt->execute()) {
                $mensaje = "Ticket guardado correctamente.";
            } else {
                $error = "Error al guardar.";
            }
            $stmt->close();
        }
    }
}

// ---------------------------------------------------
// 2. LÓGICA PARA OBTENER TICKETS Y CONTADORES
// ---------------------------------------------------

// Contar urgentes (Alta prioridad)
$urgentes = 0;
$sql_count = "SELECT COUNT(*) as total FROM tickets WHERE prioridad = 'Alta'";
$res_count = $conexion->query($sql_count);
if ($row = $res_count->fetch_assoc()) { $urgentes = $row['total']; }

// Obtener lista de tickets (Del más reciente al más antiguo)
$tickets = [];
$sql_list = "SELECT * FROM tickets ORDER BY fecha_creacion DESC";
$res_list = $conexion->query($sql_list);
while ($row = $res_list->fetch_assoc()) {
    $tickets[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Tickets</title>
    <style>
        /* ESTILOS GENERALES (Estilo Morado/Azulado Moderno) */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        
        /* HEADER */
        header {
            background: linear-gradient(135deg, #4a148c 0%, #7b1fa2 100%); /* Morado */
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .header-title h1 { margin: 0; font-size: 24px; }
        .header-title span { font-size: 14px; opacity: 0.8; }
        .header-info { display: flex; align-items: center; gap: 15px; }
        .badge-urgent { background-color: #ffca28; color: #333; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 14px; }
        .btn-logout { background: rgba(255,255,255,0.2); color: white; text-decoration: none; padding: 5px 15px; border-radius: 5px; font-size: 14px; transition: 0.3s; }
        .btn-logout:hover { background: rgba(255,255,255,0.4); }

        /* CONTENEDOR PRINCIPAL */
        .container {
            display: flex;
            gap: 30px;
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
            flex-wrap: wrap; /* Para móviles */
        }

        /* COLUMNA IZQUIERDA (FORMULARIO) */
        .col-left { flex: 1; min-width: 300px; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-top: 4px solid #7b1fa2; }
        .card h2 { margin-top: 0; color: #333; font-size: 18px; margin-bottom: 20px; }
        
        label { display: block; margin-bottom: 5px; color: #666; font-weight: 500; font-size: 14px; }
        input, select, textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-family: inherit; }
        input:focus, select:focus, textarea:focus { border-color: #7b1fa2; outline: none; }
        
        .btn-save { width: 100%; background-color: #007bff; /* Azul como la imagen */ color: white; border: none; padding: 12px; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-save:hover { background-color: #0056b3; }

        /* COLUMNA DERECHA (LISTA) */
        .col-right { flex: 2; min-width: 300px; }
        .toolbar { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .search-bar { width: 60%; }
        .filters button { background: white; border: 1px solid #ddd; padding: 8px 15px; cursor: pointer; color: #555; }
        .filters button:first-child { border-radius: 4px 0 0 4px; background: #666; color: white; border-color: #666; }
        .filters button:last-child { border-radius: 0 4px 4px 0; }
        .filters button:hover { background: #f0f0f0; }

        /* LISTA DE TICKETS */
        .ticket-list { background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); overflow: hidden; }
        .empty-state { padding: 40px; text-align: center; color: #999; }
        
        .ticket-item { padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .ticket-item:last-child { border-bottom: none; }
        .ticket-info h3 { margin: 0 0 5px 0; font-size: 16px; color: #333; }
        .ticket-info p { margin: 0; font-size: 13px; color: #777; }
        .priority-badge { font-size: 12px; padding: 3px 8px; border-radius: 10px; color: white; }
        .p-Alta { background-color: #dc3545; }
        .p-Normal { background-color: #28a745; }
        .p-Baja { background-color: #6c757d; }

        /* MENSAJES */
        .msg { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 14px; }
        .msg-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .msg-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        footer { text-align: center; padding: 20px; font-size: 12px; color: #aaa; margin-top: 20px; }
    </style>
</head>
<body>

    <header>
        <div class="header-title">
            <h1>Sistema de Tickets</h1>
            <span>Unidad 3: Programación Cliente-Servidor | Usuario: <?php echo htmlspecialchars($_SESSION['usuario'] ?? 'Invitado'); ?></span>
        </div>
        <div class="header-info">
            <div class="badge-urgent">Urgentes: <?php echo $urgentes; ?></div>
            <a href="logout.php" class="btn-logout">Salir</a>
        </div>
    </header>

    <div class="container">
        <!-- COLUMNA IZQUIERDA: FORMULARIO -->
        <div class="col-left">
            <div class="card">
                <h2>Nuevo Ticket</h2>
                
                <?php if ($mensaje): ?> <div class="msg msg-success"><?php echo $mensaje; ?></div> <?php endif; ?>
                <?php if ($error): ?> <div class="msg msg-error"><?php echo $error; ?></div> <?php endif; ?>

                <form method="POST" action="index.php">
                    <label>Nombre</label>
                    <!-- Pre-llenamos con el nombre de usuario de la sesión pero permitimos editar -->
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($_SESSION['usuario'] ?? ''); ?>" placeholder="Tu nombre" required>

                    <label>Email</label>
                    <input type="email" name="email" placeholder="nombre@correo.com" required>

                    <label>Prioridad</label>
                    <select name="prioridad">
                        <option value="Baja">Baja</option>
                        <option value="Normal">Normal</option>
                        <option value="Alta">Alta</option>
                    </select>

                    <label>Descripción del problema</label>
                    <textarea name="descripcion" rows="5" placeholder="Describe la falla..." required></textarea>

                    <button type="submit" name="guardar_ticket" class="btn-save">Guardar Ticket</button>
                </form>
            </div>
        </div>

        <!-- COLUMNA DERECHA: LISTA -->
        <div class="col-right">
            <div class="toolbar">
                <input type="text" class="search-bar" placeholder="Buscar ticket...">
                <div class="filters">
                    <button>Todas</button>
                    <button>Alta</button>
                    <button>Normal</button>
                </div>
            </div>

            <div class="ticket-list">
                <div style="padding: 10px 20px; background: #f9f9f9; border-bottom: 1px solid #eee; font-weight: bold; color: #555;">
                    Tickets Registrados (<?php echo count($tickets); ?>)
                </div>

                <?php if (empty($tickets)): ?>
                    <div class="empty-state">
                        No hay tickets registrados aún.
                    </div>
                <?php else: ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <div class="ticket-item">
                            <div class="ticket-info">
                                <h3><?php echo htmlspecialchars($ticket['nombre']); ?> <small style="font-weight:normal; color:#999;">(<?php echo htmlspecialchars($ticket['email']); ?>)</small></h3>
                                <p><?php echo htmlspecialchars($ticket['descripcion']); ?></p>
                                <span style="font-size: 11px; color: #aaa;"><?php echo $ticket['fecha_creacion']; ?></span>
                            </div>
                            <div>
                                <span class="priority-badge p-<?php echo $ticket['prioridad']; ?>">
                                    <?php echo $ticket['prioridad']; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        Actividad Práctica - Conexión Base de Datos y Web Service
    </footer>

</body>
</html>
