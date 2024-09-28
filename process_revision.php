<?php
session_start(); // Asegúrate de iniciar la sesión

$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "Piezas";         

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $accion = $_POST['accion'];
    
    // Definir nuevo estado según la acción
    switch ($accion) {
        case 'aprobar':
            $nuevo_estado = 'Listo para entregar'; // Cambiado a "Listo para entregar"
            break;
        case 'rechazar':
            $nuevo_estado = 'Rechazado';
            break;
        case 'remitir_enchape':
            $nuevo_estado = 'Remitido a Enchape';
            break;
        case 'remitir_corte':
            $nuevo_estado = 'Remitido a Corte';
            break;
        default:
            die("Acción no válida.");
    }

    // Actualizar el estado en la base de datos
    $sql = "UPDATE proyectos SET estado_corte = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nuevo_estado, $id);
    $stmt->execute();
    $stmt->close();

    // Registrar en el historial
    $historial_sql = "INSERT INTO historial (proyecto_id, accion) VALUES (?, ?)";
    $historial_stmt = $conn->prepare($historial_sql);
    $historial_stmt->bind_param("is", $id, $nuevo_estado);
    $historial_stmt->execute();
    $historial_stmt->close();

    $_SESSION['mensaje'] = "Proyecto " . strtolower($nuevo_estado) . ".";
    header("Location: revision.php");
    exit();
}

$conn->close();
