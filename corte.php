<?php
// Iniciar la sesión si no está ya activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Asignar nombre del módulo
$modulo_nombre = "Corte";

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
    <title>Módulo de Corte</title>
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
        .estado-pausado {
            color: red;
            font-weight: bold;
        }
        table {
            margin-top: 20px;
            background-color: #fff;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #e0f7fa;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
    <h1>Módulo de Corte</h1>
    <div>
        <h5>Usuario: <?php echo htmlspecialchars($_SESSION['user']); ?></h5>
    </div>

    <div class="mb-3">
        <button onclick="location.reload();" class="btn btn-primary">Actualizar Página</button>
        <button onclick="window.location.href='main.php';" class="btn btn-secondary">Volver a Menu inicio</button>
    </div>

    <form method="GET" action="corte.php" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" placeholder="Buscar por cliente" class="form-control">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-striped" id="proyectos-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre de cliente</th>
                <th>PDF Enlace</th>
                <th>Fecha de Envío</th>
                <th>Estado de proyecto</th>
                <th>Tiempo de Corte</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Los datos se cargarán aquí -->
        </tbody>
    </table>

    <script>
        function cargarProyectos() {
            $.ajax({
                url: 'get_proyectos.php',
                type: 'GET',
                data: {
                    search: '<?php echo isset($_GET["search"]) ? $_GET["search"] : ""; ?>'
                },
                success: function(data) {
                    let tableBody = $('#proyectos-table tbody');
                    tableBody.empty(); // Limpiar la tabla antes de agregar nuevos datos
                    data.forEach(function(row) {
                        let estadoClass = row.estado_corte === 'Pausado' ? 'estado-pausado' : '';
                        let tiempoCorte = row.fin_corte && row.inicio_corte ? 
                            Math.floor((new Date(row.fin_corte) - new Date(row.inicio_corte)) / 1000) : 
                            'N/A';
                        
                        let minutos = Math.floor(tiempoCorte / 60);
                        let segundos = tiempoCorte % 60;

                        tableBody.append(`
                            <tr>
                                <td>${row.id}</td>
                                <td>${row.nombre_pieza}</td>
                                <td><a href="${row.pdf_url}" target="_blank">Ver PDF</a></td>
                                <td>${row.fecha_envio}</td>
                                <td class="${estadoClass}">${row.estado_corte}</td>
                                <td>${row.estado_corte === 'Corte Finalizado' ? `${minutos} min ${segundos} seg` : 'N/A'}</td>
                                <td>
                                    <form action='process_corte.php' method='post' style='display:inline;'>
                                        <input type='hidden' name='id' value='${row.id}'>
                                        <button type='submit' name='action' value='listo' class='btn btn-success btn-sm' ${row.estado_corte !== 'Corte Finalizado' ? 'disabled' : ''}>Marcar como Listo</button>
                                    </form>
                                    <form action='process_corte.php' method='post' style='display:inline;'>
                                        <input type='hidden' name='id' value='${row.id}'>
                                        <button type='submit' name='action' value='inicio_corte' class='btn btn-primary btn-sm'>INICIO CORTE</button>
                                    </form>
                                    <form action='process_corte.php' method='post' style='display:inline;'>
                                        <input type='hidden' name='id' value='${row.id}'>
                                        <button type='submit' name='action' value='corte_terminado' class='btn btn-danger btn-sm'>CORTE TERMINADO</button>
                                    </form>
                                    <form action='pause_resume.php' method='post' style='display:inline;'>
                                        <input type='hidden' name='id' value='${row.id}'>
                                        <input type='hidden' name='action' value='${row.estado_corte === 'Pausado' ? 'resume' : 'pause'}'>
                                        <button type='submit' class='btn ${row.estado_corte === 'Pausado' ? 'btn-info' : 'btn-warning'} btn-sm'>${row.estado_corte === 'Pausado' ? 'Reanudar' : 'Pausar'}</button>
                                    </form>
                                    <form action='process_module.php' method='post' style='display:inline;'>
                                        <input type='hidden' name='id' value='${row.id}'>
                                        <input type='hidden' name='modulo' value='corte'>
                                        <button type='submit' name='action' value='enviara' class='btn btn-info btn-sm'>Servicios Especiales</button>
                                    </form>
                                    <form action='process_module.php' method='post' style='display:inline;'>
                                        <input type='hidden' name='id' value='${row.id}'>
                                        <input type='hidden' name='modulo' value='corte'>
                                        <button type='submit' name='action' value='enviara22' class='btn btn-info btn-sm'>Enviar Directo Revisión</button>
                                    </form>
                                </td>
                            </tr>
                        `);
                    });
                }
            });
        }

        // Cargar proyectos al inicio
        $(document).ready(function() {
            cargarProyectos();
            setInterval(cargarProyectos, 5000); // Actualizar cada 5 segundos
        });
    </script>
</body>
</html>
