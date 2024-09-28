<?php
// Asignar nombre del módulo
$modulo_nombre = "Revisión";

session_start(); // Iniciar la sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user'])) {
    die("No estás autorizado para ver esta página.");
}

$usuario_entregador = $_SESSION['user']; // Obtener el nombre del usuario que inició sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Revisión</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
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

        .mensaje {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
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
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            margin-right: 5px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .estado-pausado {
            color: red;
        }

        .estado-reanudado {
            color: green;
        }

        form {
            display: inline;
        }
    </style>
</head>
<body>
    <h1>Módulo de Revisión</h1>

    <div class="alert alert-info" role="alert">
        Usuario logueado: <strong><?php echo htmlspecialchars($usuario_entregador); ?></strong>
    </div>

    <div class="mb-3">
        <button onclick="location.reload();" class="btn btn-primary">Actualizar Página</button>
        <button onclick="window.location.href='main.php';" class="btn btn-secondary">Volver a Menu Inicio</button>
    </div>

    <?php
    if (isset($_SESSION['mensaje'])) {
        echo "<div class='mensaje'>" . $_SESSION['mensaje'] . "</div>";
        unset($_SESSION['mensaje']);
    }
    ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Cliente</th>
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
            $sql = "SELECT * FROM proyectos WHERE estado_corte IN ('Listo para Revisión', 'Pausado en el modulo de revision', 'Reanudado para revision') ORDER BY id ASC";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nombre_pieza']) . "</td>";
                echo "<td><a href='" . htmlspecialchars($row['pdf_url']) . "' target='_blank'>Ver PDF</a></td>";
                echo "<td>" . htmlspecialchars($row['fecha_envio']) . "</td>";
                
                // Determinar la clase CSS basada en el estado
                $estado_clase = '';
                if ($row['estado_corte'] === 'Pausado en el modulo de revision') {
                    $estado_clase = 'estado-pausado';
                } elseif ($row['estado_corte'] === 'Reanudado para revision') {
                    $estado_clase = 'estado-reanudado';
                }

                echo "<td class='$estado_clase'>" . htmlspecialchars($row['estado_corte']) . "</td>";
                echo "<td>
                          <form action='process_revision.php' method='post' style='display:inline;'>
                              <input type='hidden' name='id' value='" . $row['id'] . "'>
                              <button type='submit' name='accion' value='aprobar' class='btn btn-success'>Aprobar</button>
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
