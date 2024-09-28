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
    <title>Módulo de Entrega</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            padding: 20px;
            font-family: 'Roboto', sans-serif;
        }
        h1 {
            text-align: center;
            color: #fff;
            background-color: #4CAF50;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .btn-delivered {
            background-color: #007BFF; /* Color azul */
            color: white;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h1>Módulo de Entrega</h1>

    <div class="alert alert-info" role="alert">
        Usuario logueado: <strong><?php echo htmlspecialchars($usuario_entregador); ?></strong>
    </div>

    <div class="mb-3">
        <button onclick="location.reload();" class="btn btn-primary"><i class="fas fa-sync"></i> Actualizar Página</button>
        <button onclick="window.location.href='main.php';" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver a Menu Inicio</button>
    </div>

    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre de cliente...">
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Cliente</th>
                <th>Estado de Proyecto</th>
                <th>Acciones</th>
                <th>Herrajería</th>
            </tr>
        </thead>
        <tbody id="dataTableBody">
            <?php
            $servername = "localhost";  
            $username = "root";         
            $password = "";             
            $dbname = "Piezas";         

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }

            // Obtener proyectos que están "Listo para Entregar"
            $sql = "SELECT id, nombre_pieza, estado_corte, lleva_hherrajeria, estado_herramienta FROM proyectos WHERE estado_corte = 'Listo para entregar' ORDER BY id ASC";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['nombre_pieza']) . "</td>";
                echo "<td>" . htmlspecialchars($row['estado_corte']) . "</td>";
                
                // Aquí definimos el estado del botón de entrega
                $disabledEntrega = ($row['lleva_hherrajeria'] && $row['estado_herramienta'] !== 'Entregada') ? 'disabled' : '';

                echo "<td>
                          <form action='process_entrega.php' method='post' style='display:inline;' class='entrega-form'>
                              <input type='hidden' name='id' value='" . $row['id'] . "'>
                              <input type='hidden' name='entregador' value='" . htmlspecialchars($usuario_entregador) . "'>
                              <button type='submit' class='btn btn-success entrega-btn' $disabledEntrega>Marcar como Entregado</button>
                          </form>
                      </td>";
                
                // Mostrar botón de entrega de herrajería si aplica
                if ($row['lleva_hherrajeria']) {
                    if ($row['estado_herramienta'] == 'Entregada') {
                        echo "<td>
                                  <button class='btn btn-delivered' disabled>HERRAJERIA ENTREGADA</button>
                              </td>";
                    } else {
                        echo "<td>
                                  <form action='process_herramienta.php' method='post' style='display:inline;' class='herramienta-form'>
                                      <input type='hidden' name='proyecto_id' value='" . $row['id'] . "'>
                                      <button type='submit' class='btn btn-warning entregar-herramienta'>Entregar Herrajería</button>
                                  </form>
                              </td>";
                    }
                } else {
                    echo "<td>No aplica</td>"; // O cualquier otro mensaje que prefieras
                }

                echo "</tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>

    <script>
        // Función para habilitar el botón de entrega cuando se presiona el botón de entregar herrajería
        document.querySelectorAll('.entregar-herramienta').forEach(button => {
            button.addEventListener('click', function(event) {
                // Evitar que se envíe el formulario inmediatamente
                event.preventDefault();
                
                const form = button.closest('.herramienta-form');
                const entregaForm = form.closest('tr').querySelector('.entrega-form');
                const entregaButton = entregaForm.querySelector('.entrega-btn');
                
                // Aquí puedes agregar la lógica para procesar la entrega de herrajería
                form.submit(); // Enviar el formulario de herrajería

                // Habilitar el botón de entregar
                entregaButton.disabled = false;
            });
        });

        document.getElementById('searchInput').addEventListener('keyup', function() {
            var input = this.value.toLowerCase();
            var rows = document.querySelectorAll('#dataTableBody tr');

            rows.forEach(function(row) {
                var cells = row.getElementsByTagName('td');
                var match = false;

                // Filtrar por nombre de pieza (segunda columna)
                if (cells[1].innerText.toLowerCase().includes(input)) {
                    match = true;
                }

                row.style.display = match ? '' : 'none';
            });
        });
    </script>
</body>
</html>
