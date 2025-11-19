<?php
session_start();

if (!isset($_SESSION["Cedula"])) {
    header("Location: login.php");
    exit();
}

$cedula = $_SESSION["Cedula"];


$mysqli = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_MyCoop6");
if ($mysqli->connect_errno) {
    die("Error al conectar a la base de datos: " . $mysqli->connect_error);
}

$configRes = $mysqli->query("SELECT * FROM ConfiguracionUsuario WHERE Cedula = $cedula");

if ($configRes->num_rows > 0) {
    $config = $configRes->fetch_assoc();


    if (!isset($config["icons"]) || ($config["icons"] != "icons" && $config["icons"] != "words")) {
        $config["icons"] = "icons";
    }

} else {

    $mysqli->query("INSERT INTO ConfiguracionUsuario (Cedula) VALUES ($cedula)");
    $config = [
        "font_size" => 3,
        "theme" => "light",
        "icons" => "icons"
    ];
}


$fontSize = intval($config["font_size"]) * 4 + 8;

$themeBg = ($config["theme"] == "dark") ? "#1a1f36" : "#f4f6f9";
$themeColor = ($config["theme"] == "dark") ? "#eee" : "#333";

$iconMode = $config["icons"]; 

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop</title>
</head>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: <?= $themeBg ?>;
    color: <?= $themeColor ?>;
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: <?= $fontSize ?>px;
}

nav {
    background: #2c3e50;
    padding: 5px 0;
    width: 100%;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
    position: sticky;
    top: 0;
    z-index: 10;
}

#Navegador {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;   
    gap: 10px;
    color: white;
}

#Navegador a img {
    height: 45px;      
    transition: transform 0.25s ease, filter 0.25s ease;
    border-radius: 50%;
    padding: 3px;
    background: white;
}

#Navegador a img:hover {
    transform: scale(1.15);
    filter: brightness(1.15);
}

a {
    text-decoration: none;
    font-size: <?= $fontSize ?>px;
    font-weight: bold;
    margin: 0 5px;
    color: #d0dfeeff;
    transition: color 0.3s, transform 0.2s;
}

a:hover {
    color: #3498db;
    transform: scale(1.1);
}

#Logo img {
    border-radius: 20px;
    box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
    background: white;
    padding: 15px;
}
</style>

<nav>
    <div id="Navegador">

        <?php if ($iconMode === "icons"): ?>
            <a href="usuarioTesorero.php"><img src="iconoUsuario.png"></a>
            <a href="fechasTesorero.php"><img src="iconoCalendario.png"></a>
            <a href="comunicacionTesorero.php"><img src="iconoComunicacion.png"></a>
            <a href="archivoTesorero.php"><img src="iconoDocumentos.png"></a>
            <a href="foroTesorero.php"><img src="redes-sociales.png"></a>
            <a href="configuracionTesorero.php"><img src="iconoConfiguracion.png"></a>
            <a href="notificacionesTesorero.php"><img src="iconoNotificacion.png"></a>
            <a href="SeccionTesorero.php"><img src="Tesorero.png"></a>

        <?php else: ?>
            <a href="usuarioTesorero.php">Usuario</a>
            <a href="fechasTesorero.php">Calendario</a>
            <a href="comunicacionTesorero.php">Comunicación</a>
            <a href="archivoTesorero.php">Archivos</a>
            <a href="foroTesorero.php">Foro</a>
            <a href="configuracionTesorero.php">Configuración</a>
            <a href="notificacionesTesorero.php">Notificaciones</a>
            <a href="SeccionTesorero.php">Tesorería</a>
        <?php endif; ?>

    </div>
</nav>

<body>

<div id="Logo">
    <img src="logoMyCoop.png" height="200px">
</div>

<h1>Novedades</h1>

<?php

$sql = "SELECT idNovedad, Novedad FROM Novedades ORDER BY idNovedad DESC";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li><b>Novedad #" . $row['idNovedad'] . ":</b> " . htmlspecialchars($row['Novedad']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No hay novedades cargadas aún.</p>";
}

$mysqli->close();
?>

</body>
</html>
