<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop</title>
    <link rel="stylesheet" href="Style.css" />
</head>
<nav>
    <div id="Navegador">
        <a href="usuarioUsuario.php"><img src="iconoUsuario.png" height="70px"></a>
        <a href="fechasUsuarios.php"><img src="iconoCalendario.png" height="70px"></a>
        <a href="comunicacionUsuarios.php"><img src="iconoComunicacion.png" height="70px"></a>
        <a href="archivoUsuarios.php"><img src="iconoDocumentos.png" height="70px"></a>
        <a href="configuracionUsuarios.php"><img src="iconoConfiguracion.png" height="70px"></a>
        <a href="notificacionesUsuario.php"><img src="iconoNotificacion.png" height="70px"></a>
        <a href="TesoreroUsuario.php"><img src="Tesorero.png" height="70px"></a>
    </div>
</nav>
<body>
    <div id="Logo">
        <img src="logoMyCoop.png" height="200px">
    </div>
    <h1>ARCHIVOS DE LA COOPERATIVA</h1>
    <!--<a href="subircomprobante.php">Subir Archivo</a>  -->

<?php
// --- CONEXIÓN A LA BD ---
$servername = "localhost";
$username   = "root";       // cámbialo si usas otro usuario
$password   = "equipoinfrog";           // tu contraseña de MySQL si tienes
$database   = "proyecto_database2"; // cambia por el nombre de tu BD real

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("<p style='color:red;'>Error de conexión: " . $conn->connect_error . "</p>");
}
$conn->set_charset("utf8mb4");

// --- CONSULTA A LA TABLA ---
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