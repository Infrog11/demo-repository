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
    <h1>Novedades</h1>
 
    
    <?php

    $host = "localhost";
    $user = "root";      
    $pass = "equipoinfrog";           
    $db   = "proyect_database_MyCoop6";

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

 
    $sql = "SELECT idNovedad, Novedad FROM Novedades ORDER BY idNovedad DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li><b>Novedad #" . $row['idNovedad'] . ":</b> " . htmlspecialchars($row['Novedad']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No hay novedades cargadas aún.</p>";
    }

    $conn->close();
    ?>
</body>
</html>
