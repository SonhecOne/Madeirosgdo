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

    if ($action == 'pause') {
        $sql = "UPDATE proyectos SET estado_corte = 'Pausado' WHERE id = ?";
    } elseif ($action == 'resume') {
        $sql = "UPDATE proyectos SET estado_corte = 'Reanudado y listo para cortar'  WHERE id = ?";
    } else {
        $sql = "UPDATE proyectos SET estado_corte = 'Listo para Enchape' WHERE id = ?";
    }

 

    

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
