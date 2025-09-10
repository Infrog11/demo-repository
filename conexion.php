<?php
$host = "localhost";     // igual que en Workbench
$user = "root";          // usuario de MySQL
$pass = "equipoinfrog"; // la misma que usás en Workbench
$db   = "Proyecto_database2"; // nombre de la base creada

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>