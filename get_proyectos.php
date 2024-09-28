<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user'])) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

// Conexión a la base de datos
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "Piezas";         

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT *, 
               TIMESTAMPDIFF(SECOND, inicio_corte, fin_corte) AS tiempo_corte 
        FROM proyectos 
        WHERE (nombre_pieza LIKE ? AND 
               (estado_corte IN ('Pendiente para corte', 'Remitido a Corte', 'Pausado', 'Reanudado y listo para cortar', 'Corte en Progreso', 'Corte Finalizado'))) 
        ORDER BY id ASC"; // Cambiado a ORDER BY id ASC

$stmt = $conn->prepare($sql);
$like = "%$search%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

$proyectos = [];
while ($row = $result->fetch_assoc()) {
    // Calcular tiempo en minutos y segundos
    if ($row['tiempo_corte'] !== null) {
        $minutos = floor($row['tiempo_corte'] / 60);
        $segundos = $row['tiempo_corte'] % 60;
        $row['tiempo_corte_formateado'] = "{$minutos} min {$segundos} seg";
    } else {
        $row['tiempo_corte_formateado'] = 'N/A';
    }
    $proyectos[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($proyectos);
