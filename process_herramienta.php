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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['proyecto_id'])) {
        $proyecto_id = $_POST['proyecto_id'];

        // Actualiza el estado del proyecto para indicar que la herrajería ha sido entregada
        $sql_update = "UPDATE proyectos SET estado_herramienta = 'Entregada' WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $proyecto_id);

        if ($stmt_update->execute()) {
            $_SESSION['mensaje'] = "Herrajería entregada correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al entregar la herrajería: " . $stmt_update->error;
        }

        $stmt_update->close();
    } else {
        $_SESSION['mensaje'] = "No se ha especificado un proyecto.";
    }
}

$conn->close();
header("Location: entrega.php"); // Redirige a la página de entrega
exit();
