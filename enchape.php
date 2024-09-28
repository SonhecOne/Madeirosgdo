<?php
// Iniciar la sesión si no está ya activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Asignar nombre del módulo
$modulo_nombre = "Enchape";

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
    <title>Módulo de Enchape</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            padding: 20px;
            font-family: 'Roboto', sans-serif;
        }
        h1 {
            color: #fff;
            background-color: #4CAF50;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        table {
            margin-top: 20px;
            background-color: #fff;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            text-align: left;
            padding: 12px;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        .estado-pausado {
            color: red; /* Color para el estado "Pausado" */
            font-weight: bold;
        }
        .mb-3 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Módulo de Enchape</h1>
    <div>
        <h5>Usuario: <?php echo htmlspecialchars($_SESSION['user']); ?></h5> <!-- Muestra el usuario logueado -->
    </div>

    <div class="mb-3">
        <button onclick="location.reload();" class="btn btn-primary">Actualizar Página</button>
        <button onclick="window.location.href='main.php';" class="btn btn-secondary">Volver a Menu Inicio</button>
    </div>
    
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre de cliente</th>
                <th>PDF Enlace</th>
                <th>Fecha de Envío</th>
                <th>Estado de proyecto</th>
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

            // Cambié la consulta para ordenar por ID de menor a mayor
            $sql = "SELECT * FROM proyectos WHERE estado_corte IN ('Listo para Enchape', 'Remitido a Enchape', 'Pendiente', 'Pausado en el modulo de enchape', 'Reanudado para enchape') ORDER BY id ASC";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['nombre_pieza'] . "</td>";
                echo "<td><a href='" . $row['pdf_url'] . "' target='_blank'>Ver PDF</a></td>";
                echo "<td>" . $row['fecha_envio'] . "</td>";
                echo "<td class='" . ($row['estado_corte'] == 'Pausado' ? 'estado-pausado' : '') . "'>" . $row['estado_corte'] . "</td>";
                echo "<td>
                          <form action='process_enchape.php' method='post' style='display:inline;'>
                              <input type='hidden' name='id' value='" . $row['id'] . "'>
                              <button type='submit' class='btn btn-success btn-sm'>Marcar como Listo para Revisión</button>
                          </form>
                          <form action='process_module.php' method='post' style='display:inline;'>
                              <input type='hidden' name='id' value='" . $row['id'] . "'>
                              <input type='hidden' name='modulo' value='Enchape'>
                              <button type='submit' name='action' value='pause' class='btn btn-warning btn-sm'>Pausar</button>
                          </form>
                          <form action='process_module.php' method='post' style='display:inline;'>
                              <input type='hidden' name='id' value='" . $row['id'] . "'>
                              <input type='hidden' name='modulo' value='Enchape'>
                              <button type='submit' name='action' value='resume' class='btn btn-info btn-sm'>Reanudar</button>
                          </form>
                          <form action='process_module.php' method='post' style='display:inline;'>
                              <input type='hidden' name='id' value='" . $row['id'] . "'>
                              <input type='hidden' name='modulo' value='Enchape'>
                              <button type='submit' name='action' value='enviara' class='btn btn-info btn-sm'>Servicios especiales</button>
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
