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

// Verificar si se recibió un ID
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Obtener el nombre del usuario que inició sesión
    $usuario_entregador = $_SESSION['user']; 

    // Actualizar el estado y el entregador en la base de datos
    $sql = "UPDATE proyectos SET estado_corte = 'Entregado', entregador = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $usuario_entregador, $id);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Proyecto marcado como Entregado.";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar el estado: " . $stmt->error;
    }

    $stmt->close();
} else {
    $_SESSION['mensaje'] = "No se recibió el ID del proyecto.";
}

$conn->close();
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
