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

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Actualizar el estado a "Pausado"
    $sql = "UPDATE proyectos SET estado_corte = 'Pausado' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "El proyecto ha sido pausado.";
    } else {
        echo "Error al pausar el proyecto: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<a href="corte.php">Volver al Módulo de Corte</a>
