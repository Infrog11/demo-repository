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
}

body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: #f5f7fa;
    color: #2c3e50;
    text-align: center;
    padding: 20px;
}

#Navegador {
    display: flex;
    justify-content: center;
    gap: 25px;
    background: #2c3e50;
    padding: 10px 0;
    border-bottom: 4px solid #27ae60;
}

#Navegador a {
    transition: transform 0.2s ease-in-out;
}

#Navegador a:hover {
    transform: scale(1.15);
}

#Logo {
    margin: 30px 0;
}

h1 {
    font-size: 2rem;
    color: #27ae60;
    margin-top: 15px;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
}

</style>
<nav>
    <div id="Navegador">
        <a href="usuarioTesorero.php"><img src="iconoUsuario.png" height="70px"></a>
        <a href="fechasTesorero.php"><img src="iconoCalendario.png" height="70px"></a>
        <a href="comunicacionTesorero.php"><img src="iconoComunicacion.png" height="70px"></a>
        <a href="archivoTesorero.php"><img src="iconoDocumentos.png" height="70px"></a>
        <a href="configuracionTesorero.php"><img src="iconoConfiguracion.png" height="70px"></a>
        <a href="notificacionesTesorero.php"><img src="iconoNotificacion.png" height="70px"></a>
        <a href="SeccionTesorero.php"><img src="Tesorero.png" height="70px"></a>
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
