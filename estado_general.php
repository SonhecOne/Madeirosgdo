<?php
session_start();

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
    <title>Estado General de Módulos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }
        h1 {
            text-align: center;
            color: #fff;
            background-color: #4CAF50;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .estado-pausado {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Estado General de Módulos</h1>

    <div class="alert alert-info" role="alert">
        Usuario: <strong><?php echo htmlspecialchars($usuario_entregador); ?></strong>
    </div>

    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre de cliente, número de factura o entrega...">
    </div>
    <div class="mb-3">
        <button onclick="location.reload();" class="btn btn-primary">Actualizar Página</button>
        <button onclick="window.location.href='main.php';" class="btn btn-secondary">Volver a Menu inicio</button>
        <a href="descargar_excel.php" class="btn btn-success">Descargar Historial Excel</a> <!-- Botón para descargar -->
    </div>

    <table class="table table-bordered table-striped" id="dataTable">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Nombre de Cliente</th>
                <th scope="col">Número de Factura</th>
                <th scope="col">Estado General</th>
                <th scope="col">Estado de Herrajería</th>
                <th scope="col">Responsable</th>
                <th scope="col">Entregado por</th>
                <th scope="col">Tiempo de Corte</th> <!-- Nueva columna para el tiempo de corte -->
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
            $sql = "SELECT id, nombre_pieza, numero_factura, estado_corte, estado_herramienta, lleva_hherrajeria, responsable, entregador, 
                           TIMESTAMPDIFF(SECOND, inicio_corte, fin_corte) AS tiempo_corte 
                    FROM proyectos 
                    ORDER BY id DESC"; // Cambiado a ASC
            $result = $conn->query($sql);

            if (!$result) {
                die("Error en la consulta: " . $conn->error);
            }

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nombre_pieza']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['numero_factura']) . "</td>";
                    echo "<td class='" . ($row['estado_corte'] == 'Pausado' ? 'estado-pausado' : '') . "'>" . htmlspecialchars($row['estado_corte']) . "</td>";

                    // Mostrar el estado de herrajería
                    if ($row['estado_herramienta'] == 'Entregada') {
                        echo "<td>Herrajería Entregada</td>";
                    } elseif (isset($row['lleva_hherrajeria']) && $row['lleva_hherrajeria']) {
                        echo "<td>No se ha entregado herrajería</td>";
                    } else {
                        echo "<td>No aplica</td>";
                    }

                    // Mostrar el responsable
                    echo "<td>" . htmlspecialchars($row['responsable']) . "</td>";
                    // Mostrar el entregador
                    echo "<td>" . htmlspecialchars($row['entregador']) . "</td>";

                    // Calcular tiempo de corte
                    if ($row['tiempo_corte'] !== null) {
                        $minutos = floor($row['tiempo_corte'] / 60);
                        $segundos = $row['tiempo_corte'] % 60;
                        echo "<td>{$minutos} min {$segundos} seg</td>";
                    } else {
                        echo "<td>No Aplica</td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No hay registros</td></tr>"; // Cambiar a 8 columnas
            }

            $conn->close();
            ?>
        </tbody>
    </table>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var input = this.value.toLowerCase();
            var rows = document.querySelectorAll('#dataTable tbody tr');

            rows.forEach(function(row) {
                var cells = row.getElementsByTagName('td');
                var match = false;

                for (var i = 0; i < cells.length; i++) {
                    if (cells[i].innerText.toLowerCase().includes(input)) {
                        match = true;
                        break;
                    }
                }

                row.style.display = match ? '' : 'none';
            });
        });
    </script>
</body>
</html>
