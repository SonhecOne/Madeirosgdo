<?php
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
    $action = $_POST['action'];
    $modulo = $_POST['modulo']; // Captura el módulo

    switch ($action) {
        case 'pause':
            $sql = "UPDATE proyectos SET estado_corte = 'Pausado en el modulo de $modulo' WHERE id = ?";
            break;

        case 'resume':
            $sql = "UPDATE proyectos SET estado_corte = 'Reanudado para $modulo' WHERE id = ?";
            break;

        case 'enviara':
            $sql = "UPDATE proyectos SET estado_corte = 'Listo para Servicios Especiales' WHERE id = ?";
            break;

            case 'enviara22':
                $sql = "UPDATE proyectos SET estado_corte = 'Listo para Revisión' WHERE id = ?";
                break;
    
        default:
            // Si no hay una acción válida, redirigir sin hacer nada
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
    }

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Error en la preparación de la declaración: " . $conn->error);
    }
}

$conn->close();
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
