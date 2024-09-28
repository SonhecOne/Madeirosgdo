<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MADEIROS L&D GESTOR DE PRODUCCION</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            background-image: url('imagenes/top-view-desk-concept-with-copy-space.jpg');
            background-size: cover;
            background-position: center;
            color: #333;
            font-family: 'Roboto', sans-serif;
        }
        .header {
            background-color: rgba(40, 167, 69, 0.9);
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 2px solid #218838;
        }
        h1 {
            margin: 0;
            font-size: 2.5em;
        }
        .container {
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
        }
        .user-info {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .user-info strong {
            display: block;
            margin-top: 10px;
            margin-bottom: 15px;
            font-size: 1.2em;
        }
        .module-link {
            margin: 15px 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        .module-button {
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 1.1em;
            width: 150px;
            margin: 10px;
            transition: background-color 0.3s, transform 0.3s;
            text-align: center;
        }
        .module-link a {
            text-decoration: none;
        }
        .module-link a:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MADEIROS L&D GESTOR DE PRODUCCION</h1>
    </div>
    <div class="container">
        <div class="user-info">
            <i class="fas fa-user-circle fa-4x mb-2"></i>
            <strong>Bienvenido, <?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']) : 'Invitado'; ?></strong>
            <?php if (isset($_SESSION['user'])): ?>
                <div class="d-flex justify-content-center">
                    <a href="logout.php" class="btn btn-danger btn-sm mt-2 mr-2">Cerrar Sesión</a>
                    <?php if ($_SESSION['role'] == 'jefe'): ?>
                        <a href="register.php" class="btn btn-secondary btn-sm mt-2">Registrar nuevo usuario</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['role'])): ?>
            <div class="module-link text-center">
                <?php if ($_SESSION['role'] == 'jefe'): ?>
                    <a href="diseno.php" class="btn btn-warning module-button">Subir proyecto</a>
                    <a href="corte.php" class="btn btn-success module-button">Cortes</a>
                    <a href="enchape.php" class="btn btn-danger module-button">Enchapes</a>
                    <a href="servicios.php" class="btn btn-success module-button">Servicios especiales</a>
                    <a href="revision.php" class="btn btn-info module-button">Revisión</a>
                    <a href="entrega.php" class="btn btn-primary module-button">Entrega</a>
                    <a href="estado_general.php" class="btn btn-info module-button">Observar estado general</a>
                <?php elseif ($_SESSION['role'] == 'disenador'): ?>
                    <a href="diseno.php" class="btn btn-warning module-button">Subir proyecto</a>
                    <a href="entrega.php" class="btn btn-primary module-button">Entrega</a>
                <?php elseif ($_SESSION['role'] == 'cortador'): ?>
                    <a href="corte.php" class="btn btn-success module-button">Cortes</a>
                    <a href="estado_general.php" class="btn btn-info module-button">Observar estado general</a>
                <?php elseif ($_SESSION['role'] == 'enchapador'): ?>
                    <a href="enchape.php" class="btn btn-danger module-button">Enchapes</a>
                    <a href="servicios.php" class="btn btn-success module-button">Servicios especiales</a>
                <?php elseif ($_SESSION['role'] == 'modulador'): ?>
                    <a href="estado_general.php" class="btn btn-info module-button">Observar estado general</a>
                    <a href="revision.php" class="btn btn-info module-button">Revisión de materiales</a>
                    <a href="entrega.php" class="btn btn-primary module-button">Entrega</a>
                <?php else: ?>
                    <p class="text-muted">No tienes acceso a ningún módulo.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="module-link text-center">
                <p class="text-muted">No has iniciado sesión.</p>
            </div>
        <?php endif; ?>
    </div>
    <div class="footer">
        <p>&copy; 2024 Madeiros L&D Optimización de Trabajo. Todos los derechos reservados.</p>
        <p>&copy; Desarrollado por Hector JOE.</p>
    </div>
</body>
</html>
