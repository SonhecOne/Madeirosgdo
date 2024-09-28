<?php
// Iniciar la sesión si no está ya activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Piezas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener todos los usuarios registrados para el menú desplegable
$sql_users = "SELECT username FROM usuarios";
$result_users = $conn->query($sql_users);
$usernames = [];

if ($result_users->num_rows > 0) {
    while ($row = $result_users->fetch_assoc()) {
        $usernames[] = $row['username'];
    }
}

// Manejar el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Consulta para verificar el usuario
    $sql = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verificar la contraseña
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user'] = $row['username']; // Guardar el nombre de usuario
            $_SESSION['role'] = $row['rol']; // Guardar el rol del usuario
            header("Location: main.php");
            exit();
        } else {
            $error_message = "Contraseña incorrecta.";
        }
    } else {
        $error_message = "Usuario no encontrado.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #e9ecef;
        }
        .login-container {
            max-width: 400px;
            margin: auto;
            margin-top: 40px;
            padding: 30px;
            
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #00c800;
        }
        .btn-primary {
            background-color: #00c800;
            border-color: #fffffd;
        }
        .btn-primary:hover {
            background-color: #165eab;
            border-color: #165eab;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <h2>Iniciar Sesión</h2>
        <form method="POST">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <select class="form-control" name="username" required>
                    <option value="">Seleccione un usuario</option>
                    <?php foreach ($usernames as $username): ?>
                        <option value="<?php echo $username; ?>"><?php echo $username; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
        </form>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        
    </div>
</body>
</html>
