<?php
// Iniciar la sesión si no está ya activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Asignar nombre del módulo
$modulo_nombre = "Servicios Especiales";

// Verificar si el usuario está logueado
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirigir al login si no está logueado
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Servicios Especiales</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        h1 {
            color: #fff;
            background-color: #4CAF50;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .estado-pausado {
            color: red; /* Color para el estado "Pausado" */
        }
        table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        button {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <h1>Módulo de Servicios Especiales</h1>
    <div>
        <h5>Usuario: <?php echo htmlspecialchars($_SESSION['user']); ?></h5>
    </div>
    <div class="mb-3">
        <button onclick="location.reload();" class="btn btn-primary">Actualizar Página</button>
        <button onclick="window.location.href='main.php';" class="btn btn-secondary">Volver a Menu Inicio</button>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre de cliente</th>
                <th>PDF Enlace</th>
                <th>Fecha de Envío</th>
                <th>Estado de Proyecto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $servername = "localhost";  
            $username = "root";         
            $password = "";             
            $dbname = "Piezas";         

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }

            // Modificar la consulta para ordenar por ID de menor a mayor
            $sql = "SELECT * FROM proyectos WHERE estado_corte IN ('Listo para Servicios Especiales', 'Remitido a Servicios Especiales', 'Pendiente', 'Pausado en el modulo de Servicios Especiales', 'Reanudado para Servicios Especiales') ORDER BY id ASC";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nombre_pieza']) . "</td>";
                echo "<td><a href='" . htmlspecialchars($row['pdf_url']) . "' target='_blank'>Ver PDF</a></td>";
                echo "<td>" . htmlspecialchars($row['fecha_envio']) . "</td>";
                echo "<td class='" . ($row['estado_corte'] == 'Pausado en el modulo de Servicios Especiales' ? 'estado-pausado' : '') . "'>" . htmlspecialchars($row['estado_corte']) . "</td>";
                echo "<td>
                          <form action='process_servicios.php' method='post' style='display:inline;'>
                              <input type='hidden' name='id' value='" . $row['id'] . "'>
                              <button type='submit' class='btn btn-success'>Marcar como Listo para Revisión</button>
                          </form>
                          <form action='process_module.php' method='post' style='display:inline;'>
                              <input type='hidden' name='id' value='" . $row['id'] . "'>
                              <input type='hidden' name='modulo' value='" . htmlspecialchars($modulo_nombre) . "'>
                              <button type='submit' name='action' value='pause' class='btn btn-warning'>Pausar</button>
                          </form>
                          <form action='process_module.php' method='post' style='display:inline;'>
                              <input type='hidden' name='id' value='" . $row['id'] . "'>
                              <input type='hidden' name='modulo' value='" . htmlspecialchars($modulo_nombre) . "'>
                              <button type='submit' name='action' value='resume' class='btn btn-info'>Reanudar</button>
                          </form>
                      </td>";
                echo "</tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
