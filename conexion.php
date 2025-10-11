<?php
$host = "localhost";     
$user = "root";          
$pass = "equipoinfrog"; 
$db   = "Proyect_database_Mycoop6"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>