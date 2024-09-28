<?php
session_start(); // Asegúrate de iniciar la sesión
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
    $action = $_POST['action'];

    if ($action === 'inicio_corte') {
        // Marcar el inicio del corte
        $sql = "UPDATE proyectos SET estado_corte = 'Corte en Progreso', inicio_corte = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo "El corte ha comenzado.";
        } else {
            echo "Error al iniciar el corte: " . $stmt->error;
        }
    } elseif ($action === 'corte_terminado') {
        // Marcar el corte como terminado
        $sql = "UPDATE proyectos SET estado_corte = 'Corte Finalizado', fin_corte = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Calcular el tiempo de corte
            $time_sql = "SELECT TIMESTAMPDIFF(SECOND, inicio_corte, fin_corte) AS tiempo_corte FROM proyectos WHERE id = ?";
            $time_stmt = $conn->prepare($time_sql);
            $time_stmt->bind_param("i", $id);
            $time_stmt->execute();
            $time_result = $time_stmt->get_result();
            $time_row = $time_result->fetch_assoc();
            
            $tiempo_corte = $time_row['tiempo_corte'];
            echo "El corte ha terminado. Tiempo de corte: $tiempo_corte segundos.";
        } else {
            echo "Error al finalizar el corte: " . $stmt->error;
        }
    } elseif ($action === 'listo') {
        // Marcar como listo para revisión
        $sql = "UPDATE proyectos SET estado_corte = 'Listo para Revisión' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Cambiar a "Listo para Enchape" después de "Listo para Revisión"
            $sql = "UPDATE proyectos SET estado_corte = 'Listo para Enchape' WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo "El proyecto ha sido marcado como listo para enchapar.";
            } else {
                echo "Error al actualizar el estado a listo para enchapar: " . $stmt->error;
            }
        } else {
            echo "Error al actualizar el estado: " . $stmt->error;
        }
    } else {
        // Manejar otras acciones
        $check_sql = "SELECT estado_corte FROM proyectos WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $row = $check_result->fetch_assoc();

        if ($row['estado_corte'] == 'Listo para Revisión') {
            $sql = "UPDATE proyectos SET estado_corte = 'Listo para Enchape' WHERE id = ?";
        } else {
            $sql = "UPDATE proyectos SET estado_corte = 'Listo para Enchape' WHERE id = ?";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo "El proyecto ha sido actualizado correctamente.";
        } else {
            echo "Error al actualizar el estado: " . $stmt->error;
        }
    }

    $stmt->close();
}

$conn->close();
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
