
<?php
session_start();

if (!isset($_SESSION['Cedula'])) {
    header("Location: login.php");
    exit();
}

$cedula = $_SESSION['Cedula'];
$mensaje = "";

$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}


$stmtCfg = $conn->prepare("SELECT font_size, theme, icons FROM configuracionUsuario WHERE Cedula = ?");
$stmtCfg->bind_param("i", $cedula);
$stmtCfg->execute();
$config = $stmtCfg->get_result()->fetch_assoc();

$fontSize = isset($config['font_size']) ? (int)$config['font_size'] : 3;
$theme = isset($config['theme']) ? $config['theme'] : 'light'; 
$iconsMode = isset($config['icons']) ? $config['icons'] : 'icons'; 


if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["msg"])) {
    $msg = trim($_POST["msg"]);

    $stmt = $conn->prepare("INSERT INTO Mensajes (Mensaje, Cedula) VALUES (?, ?)");
    $stmt->bind_param("si", $msg, $cedula);

    if ($stmt->execute()) {
        $mensaje = "<p style='color:green;'>✅ Mensaje enviado correctamente.</p>";
    } else {
        $mensaje = "<p style='color:red;'>❌ Error al enviar mensaje: " . $conn->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MyCoop - Enviar Mensaje</title>
<style>
:root {
    --font-size: <?= $fontSize * 4 ?>px;
    --bg-color: #f4f6f9;
    --text-color: #333;
    --nav-bg: #219150;
    --icon-bg: #fff;
    --icon-filter: invert(0);
    --form-bg: #fff;
}

<?php if ($theme === "dark"): ?>
:root {
    --bg-color: #1a1a1a;
    --text-color: #fff;
    --nav-bg: #111;
    --icon-bg: #fff;
    --icon-filter: invert(1);
    --form-bg: #222;
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
    background: var(--nav-bg);
    padding: 10px 0;
    width: 100%;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
    position: sticky;
    top: 0;
    z-index: 100;
}

#Navegador {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
}

#Navegador a {
    text-align: center;
    color: var(--text-color);
    font-weight: bold;
    font-size: 0.9rem;
    text-decoration: none;
}

#Navegador a img {
    height: 70px;
    width: 70px;
    object-fit: cover;
    border-radius: 50%;
    padding: 8px;
    background: var(--icon-bg);
    filter: var(--icon-filter);
    transition: transform 0.3s, filter 0.3s, background 0.3s;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.15);
}

#Navegador a img:hover {
    transform: scale(1.15);
    filter: brightness(1.1) var(--icon-filter);
}

#Logo img {
    margin: 20px 0;
    max-width: 200px;
}

h2 {
    color: var(--text-color);
    margin-bottom: 20px;
}

form {
    background: var(--form-bg);
    border-radius: 10px;
    padding: 20px;
    width: 60%;
    margin: 0 auto;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
    text-align: left;
}

label {
    font-weight: bold;
    color: var(--text-color);
}

input[type="text"] {
    margin-top: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    width: 100%;
    padding: 5px;
    background-color: <?= $theme === 'dark' ? '#333' : '#fff' ?>;
    color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
}

button {
    background-color: #27ae60;
    color: white;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    cursor: pointer;
    transition: background 0.3s;
    margin-top: 10px;
}

button:hover {
    background-color: #219150;
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

    echo menuItem("usuarioTesorero.php", "IconoUsuario.png", "Usuario", $iconsMode);
    echo menuItem("fechasTesorero.php", "iconoCalendario.png", "fechas", $iconsMode);
    echo menuItem("inicioTesorero.php", "anuncios.png", "Inicio", $iconsMode);
    echo menuItem("archivoTesorero.php", "iconoDocumentos.png", "Archivos", $iconsMode);
    echo menuItem("foroTesorero.php", "redes-sociales.png", "Foro", $iconsMode);
    echo menuItem("configuracionTesorero.php", "iconoConfiguracion.png", "Configuración", $iconsMode);
    echo menuItem("notificacionesTesorero.php", "iconoNotificacion.png", "Notificaciones", $iconsMode);
    echo menuItem("seccionTesorero.php", "Tesorero.png", "Tesorero", $iconsMode);
    ?>
</div>
</nav>


<div id="Logo">
    <img src="logoMyCoop.png" alt="Logo MyCoop">
</div>

<h2>Enviar mensaje a un administrador</h2>
<form method="POST">
    <label for="msg">Ingrese su mensaje:</label><br>
    <input type="text" id="msg" name="msg" required><br><br>
    <button type="submit">Enviar</button>
</form>

<?php
if (!empty($mensaje)) echo $mensaje;
?>
</body>
</html>
