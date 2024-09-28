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
    
    // Actualizar el estado de servicios a "Listo para Revisión"
    $sql = "UPDATE proyectos SET estado_corte = 'Listo para Revisión' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Registrar en el historial
        $historial_sql = "INSERT INTO historial (proyecto_id, accion) VALUES (?, 'Remitido a Revisión')";
        $historial_stmt = $conn->prepare($historial_sql);
        $historial_stmt->bind_param("i", $id);
        $historial_stmt->execute();
        $historial_stmt->close();
        
        echo "El proyecto ha sido marcado como listo para revisión.";
    } else {
        echo "Error al actualizar el estado: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
