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
    // Verifica que se haya cargado un archivo
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $nombre_cliente = $_POST['nombre'];
        $responsable = $_POST['responsable']; // Captura el responsable
        $lleva_hherrajeria = isset($_POST['herrajeria']) ? 1 : 0; // 1 si está marcado, 0 si no
        $numero_factura = $_POST['factura']; // Captura el número de factura

        // Procesa el archivo PDF
        $file_tmp_path = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $file_path = 'uploads/' . $file_name; // Asegúrate de que la carpeta uploads exista

        // Mueve el archivo a la carpeta de destino
        if (move_uploaded_file($file_tmp_path, $file_path)) {
            // Inserta el nuevo proyecto en la base de datos
            $sql_insert = "INSERT INTO proyectos (nombre_pieza, pdf_url, fecha_envio, estado_corte, modulo, lleva_hherrajeria, responsable, numero_factura) VALUES (?, ?, NOW(), 'Pendiente para corte', 'Corte', ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssiss", $nombre_cliente, $file_path, $lleva_hherrajeria, $responsable, $numero_factura); // Incluye el número de factura

            if ($stmt_insert->execute()) {
                $_SESSION['mensaje'] = "Proyecto cargado exitosamente.";
            } else {
                $_SESSION['mensaje'] = "Error al cargar el proyecto: " . $stmt_insert->error;
            }

            $stmt_insert->close();
        } else {
            $_SESSION['mensaje'] = "Error al mover el archivo. Asegúrate de que la carpeta de destino tenga permisos de escritura.";
        }
    } else {
        $_SESSION['mensaje'] = "Error al cargar el archivo. Asegúrate de que el archivo sea un PDF.";
    }
}

$conn->close();
header("Location: diseno.php");
exit();
