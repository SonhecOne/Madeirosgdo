<?php
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
    <title>Módulo de Diseño y Optimización de Corte</title>
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
        .mensaje {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
        .checkbox-container {
            margin: 10px 0;
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
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Cargar Especificaciones de Corte</h1>

    <div class="alert-info">
        Usuario logueado: <strong><?php echo htmlspecialchars($usuario_entregador); ?></strong>
    </div>

    <?php
    if (isset($_SESSION['mensaje'])) {
        echo "<div class='mensaje'>" . $_SESSION['mensaje'] . "</div>";
        unset($_SESSION['mensaje']); // Limpiar el mensaje
    }
    ?>

    <form action="process_design.php" method="post" enctype="multipart/form-data">
        <label for="file">Cargar archivo:</label>
        <input type="file" name="file" id="file" required>
        
        <label for="nombre">Nombre del cliente:</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="responsable">Responsable:</label>
        <input type="text" name="responsable" id="responsable" required>

        <label for="factura">Número de factura:</label>
        <input type="text" name="factura" id="factura" required>

        <div class="checkbox-container">
            <label>
                <input type="checkbox" name="herrajeria" id="herrajeria" value="1"> Lleva herrajería
            </label>
        </div>
        
        <button type="submit">Enviar</button>
    </form>
    
    <p class="back-link">
        <a href="main.php">Volver a inicio</a>
    </p>
</body>
</html>
