<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Piezas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $role = $_POST['role']; // Obtener el rol del formulario

    // Verificar si el usuario ya existe
    $sql_check = "SELECT * FROM usuarios WHERE username = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $user);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        // Registrar nuevo usuario
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
        $sql_insert = "INSERT INTO usuarios (username, password, rol) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sss", $user, $hashed_password, $role); // Incluir el rol
        $stmt_insert->execute();
        $stmt_insert->close();

        $_SESSION['mensaje'] = "Registro exitoso. Puedes iniciar sesión.";
        header("Location: index.php");
        exit();
    } else {
        $error = "El usuario ya existe.";
    }

    $stmt_check->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>

.form-control {
    display: block;
    width: 100%;
    height: calc(1.5em + .75rem + 2px);
    padding: .500rem .50rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}
.container{
            max-width: 400px;
            margin: auto;
            margin-top: 40px;
            padding: 30px;
            
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
       

        body {
            background-color: #f8f9fa;
            padding: 50px;
            padding-top: 50px;
            padding-left:500; 
            padding-right: 500;
        }
        h2 {
            margin-bottom: 30px;
            color: #343a40;
        }



        .btn-primary  {
              background-color: #00c800;
            border-color: #fffffd;
            align-content: center;
            text-align: center;
           
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Registro de usuario</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Rol:</label>
                <select class="form-control" name="role" required> <!-- Corregido aquí -->
                    <option value="disenador">Diseñador</option>
                    <option value="cortador">Cortador</option>
                    <option value="enchapador">Enchapador</option>
                    <option value="modulador">Modulador</option>
                    <option value="jefe">Jefe</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
        <p class="text-center mt-3">
            <a href="index.php">¿Ya tienes cuenta? Inicia sesión</a>
        </p>
    </div>
</body>
</html>
