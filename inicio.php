<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop</title>
    <link rel="stylesheet" href="Style.css" />
</head>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: #f4f6f9;
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
}


nav {
    background: #2c3e50;
    padding: 10px 0;
    width: 100%;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
    position: sticky;
    top: 0;
    z-index: 10;
}

#Navegador {
    display: flex;
    justify-content: center;
    gap: 20px;
}

#Navegador a img {
    transition: transform 0.3s, filter 0.3s;
    border-radius: 50%;
    padding: 5px;
    background: #fff;
}

#Navegador a img:hover {
    transform: scale(1.15);
    filter: brightness(1.1);
}
#Logo {
    margin: 30px 0;
}

#Logo img {
    border-radius: 20px;
    box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
    background: white;
    padding: 15px;
}

h1 {
    margin-top: 10px;
    color: #2c3e50;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.2);
}

a {
    text-decoration: none;
    font-size: 28px;
    font-weight: bold;
    margin: 0 10px;
    color: #2c3e50;
    transition: color 0.3s, transform 0.2s;
}

a:hover {
    color: #3498db;
    transform: scale(1.2);
}
</style>
<nav>
    <div id="Navegador">
        <a href="aprobarUsuarios.php"><img src="iconoAdministracion.png" height="70px"></a>
        <a href="usuario.php"><img src="iconoUsuario.png" height="70px"></a>
        <a href="fechas.php"><img src="iconoCalendario.png" height="70px"></a>
        <a href="comunicacion.php"><img src="iconoComunicacion.png" height="70px"></a>
        <a href="archivo.php"><img src="iconoDocumentos.png" height="70px"></a>
        <a href="Construccion.php"><img src="iconoConstruccion.png" height="70px"></a>
        <a href="configuracion.php"><img src="iconoConfiguracion.png" height="70px"></a>
        <a href="notificaciones.php"><img src="iconoNotificacion.png" height="70px"></a>
        <a href="TesoreroAdmin.php"><img src="Tesorero.png" height="70px"></a>    
    </div>
</nav>
<body>
    <div id="Logo">
        <img src="logoMyCoop.png" height="200px">
    </div>
    <h1>Novedades</h1>
    <a href="añadirNovedades.php">+</a>
    <a href="eliminarNovedades.php">-</a>
    
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
