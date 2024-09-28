<?php
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "Piezas";         

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Eliminar todos los proyectos
$sql = "DELETE FROM proyectos";

if ($conn->query($sql) === TRUE) {
    echo "Todos los proyectos han sido eliminados.";
} else {
    echo "Error al eliminar proyectos: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>

<a href="revision.php">Volver al Módulo de Revisión</a>
