<?php
session_start();

if (!isset($_SESSION['Cedula'])) {
    header("Location: login.php");
    exit();
}

$cedula = $_SESSION['Cedula'];

// Conexión
$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6");
if ($conn->connect_error) {
    die("<p style='color:red;'>Error de conexión: " . $conn->connect_error . "</p>");
}
$conn->set_charset("utf8mb4");

// Obtener configuración del usuario
$stmtCfg = $conn->prepare("SELECT font_size, theme, icons FROM configuracionUsuario WHERE Cedula = ?");
$stmtCfg->bind_param("i", $cedula);
$stmtCfg->execute();
$config = $stmtCfg->get_result()->fetch_assoc();

$fontSize = isset($config['font_size']) ? (int)$config['font_size'] : 3;
$theme = isset($config['theme']) ? $config['theme'] : 'light'; // light / dark
$iconsMode = isset($config['icons']) ? $config['icons'] : 'icons'; // icons / words
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>MyCoop</title>
<style>
:root {
    --font-size: <?= $fontSize * 4 ?>px;
    --bg-color: #f4f6f9;
    --text-color: #333;
    --nav-bg: #2c3e50;
    --icon-bg: #fff;
    --icon-filter: invert(0);
    --link-bg: #27ae60;
    --link-hover-bg: #219150;
    --table-bg: #fff;
    --table-text: #333;
}

<?php if($theme === "dark"): ?>
:root {
    --bg-color: #1a1a1a;
    --text-color: #fff;
    --nav-bg: #111;
    --icon-bg: #fff;
    --icon-filter: invert(1);
    --link-bg: #219150;
    --link-hover-bg: #27ae60;
    --table-bg: #222;
    --table-text: #fff;
}
<?php endif; ?>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body {
    background-color: var(--bg-color);
    color: var(--text-color);
    font-size: var(--font-size);
    text-align: center;
    padding: 20px;
}

nav {
    background-color: var(--nav-bg);
    padding: 10px;
    margin-top: 150px;
}

#Navegador {
    display: flex;
    justify-content: center;
    gap: 15px;
}

#Navegador a {
    text-align: center;
    color: var(--text-color);
    font-weight: bold;
    text-decoration: none;
}

#Navegador a img {
    transition: transform 0.2s;
    height: 70px;
    width: 70px;
    border-radius: 50%;
    background: var(--icon-bg);
    filter: var(--icon-filter);
}

#Navegador a img:hover {
    transform: scale(1.1);
}

#Logo img {
    margin: 20px 0;
    max-width: 200px;
}

h1 {
    color: var(--link-bg);
    margin-bottom: 30px;
}

a.button {
    display: inline-block;
    background-color: var(--link-bg);
    color: white;
    text-decoration: none;
    font-size: 18px;
    font-weight: bold;
    padding: 12px 24px;
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
    transition: background 0.3s, transform 0.2s;
}

a.button:hover {
    background-color: var(--link-hover-bg);
    transform: translateY(-2px);
}

table {
    width: 90%;
    margin: 20px auto;
    border-collapse: collapse;
    background-color: var(--table-bg);
    color: var(--table-text);
}

table th, table td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: left;
}

table th {
    background-color: var(--nav-bg);
    color: var(--text-color);
}
</style>
</head>
<body>

<nav>
<div id="Navegador">
    <?php
    function menuItem($url, $img, $text, $iconsMode) {
        if ($iconsMode === "icons") {
            return "<a href='$url'><img src='$img' height='70'></a>";
        } else {
            return "<a href='$url'>$text</a>";
        }
    }

    echo menuItem("usuarioUsuario.php", "IconoUsuario.png", "Usuario", $iconsMode);
    echo menuItem("fechasUsuarios.php", "iconoCalendario.png", "fechas", $iconsMode);
    echo menuItem("comunicacionUsuarios.php", "iconoComunicacion.png", "Comunicacion", $iconsMode);
    echo menuItem("inicioUsuario.php", "anuncios.png", "Inicio", $iconsMode);
    echo menuItem("foroUsuarios.php", "redes-sociales.png", "Foro", $iconsMode);
    echo menuItem("configuracionUsuarios.php", "iconoConfiguracion.png", "Configuración", $iconsMode);
    echo menuItem("notificacionesUsuario.php", "iconoNotificacion.png", "Notificaciones", $iconsMode);
    echo menuItem("TesoreroUsuario.php", "Tesorero.png", "Tesorero", $iconsMode);
    ?>
</div>
</nav>


<div id="Logo">
    <img src="logoMyCoop.png" height="200px" alt="Logo MyCoop">
</div>

<h1>ARCHIVOS DE LA COOPERATIVA</h1>
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
    echo "<table>";
    echo "<tr><th>Nombre del archivo</th><th>Fecha</th><th>Descripción</th><th>Acción</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['NombreArchivo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Fecha']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DescripcionArch']) . "</td>";
        echo "<td><a class='button' href='uploads/" . htmlspecialchars($row['NombreArchivo']) . "' download>Descargar</a></td>";
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
