<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop</title>
    <link rel="stylesheet" href="Style.css" />
</head>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body {
    background-color: #f4f6f9;
    text-align: center;
    padding: 20px;
}

nav {
    background-color: #2c3e50;
    padding: 10px 0;
    margin-bottom: 20px;
}

#Navegador {
    display: flex;
    justify-content: center;
    gap: 15px;
}

#Navegador a img {
    transition: transform 0.2s;
}

#Navegador a img:hover {
    transform: scale(1.1);
}

#Logo img {
    margin: 20px 0;
}

h1 {
    color: #2c3e50;
    margin-bottom: 30px;
}

a {
    display: inline-block;
    background-color: #27ae60;
    color: white;
    text-decoration: none;
    font-size: 18px;
    font-weight: bold;
    padding: 12px 24px;
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
    transition: background 0.3s, transform 0.2s;
}

a:hover {
    background-color: #219150;
    transform: translateY(-2px);
}</style>
<nav>
    <div id="Navegador">
        <a href="http://localhost/PROYECTOUTU/usuario.php"><img src="iconoUsuario.png" height="70px"></a>
        <a href="http://localhost/PROYECTOUTU/fechas.php"><img src="iconoCalendario.png" height="70px"></a>
        <a href="http://localhost/PROYECTOUTU/comunicacion.php"><img src="iconoComunicacion.png" height="70px"></a>
        <a href="http://localhost/PROYECTOUTU/archivo.php"><img src="iconoDocumentos.png" height="70px"></a>
        <a href="http://localhost/PROYECTOUTU/configuracion.php"><img src="iconoConfiguracion.png" height="70px"></a>
        <a href="http://localhost/PROYECTOUTU/notificaciones.php"><img src="iconoNotificacion.png" height="70px"></a>
        <a href="TesoreroAdmin.php"><img src="Tesorero.png" height="70px"></a>
    </div>
</nav>
<body>
    <div id="Logo">
        <img src="logoMyCoop.png" height="200px">
    </div>
    <h1>ARCHIVOS DE LA COOPERATIVA</h1>
    <a href="subircomprobante.php">Subir Archivo</a>  

<?php

$servername = "localhost";
$username   = "root";      
$password   = "equipoinfrog";         
$database   = "proyect_database_mycoop6"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("<p style='color:red;'>Error de conexión: " . $conn->connect_error . "</p>");
}
$conn->set_charset("utf8mb4");

$sql = "SELECT IdArchivo, NombreArchivo, Fecha, DescripcionArch FROM Archivos ORDER BY Fecha DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Nombre del archivo</th><th>Fecha</th><th>Descripción</th><th>Acción</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['NombreArchivo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Fecha']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DescripcionArch']) . "</td>";
        echo "<td><a href='uploads/" . htmlspecialchars($row['NombreArchivo']) . "' download>Descargar</a></td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No hay archivos registrados en la base de datos.</p>";
}

$conn->close();
?>
</body>
</html>
