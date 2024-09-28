<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user'])) {
    die("No estás autorizado para ver esta página.");
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

// Consulta para obtener los datos
$sql = "SELECT id, nombre_pieza, numero_factura, estado_corte, estado_herramienta, lleva_hherrajeria, responsable, entregador FROM proyectos";
$result = $conn->query($sql);

// Crear el archivo Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="historial_proyectos.xls"');

// Generar el contenido del archivo
echo "<table border='1'>";
echo "<tr>
        <th>ID</th>
        <th>Nombre de Cliente</th>
        <th>Número de Factura</th>
        <th>Estado General</th>
        <th>Estado de Herrajería</th>
        <th>Responsable</th>
        <th>Entregador</th>
      </tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre_pieza']) . "</td>";
        echo "<td>" . htmlspecialchars($row['numero_factura']) . "</td>";
        echo "<td>" . htmlspecialchars($row['estado_corte']) . "</td>";
        echo "<td>" . htmlspecialchars($row['estado_herramienta']) . "</td>";
        echo "<td>" . htmlspecialchars($row['responsable']) . "</td>";
        echo "<td>" . htmlspecialchars($row['entregador']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>No hay registros</td></tr>";
}

echo "</table>";

$conn->close();
exit();
?>
