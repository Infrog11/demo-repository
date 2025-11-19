<?php
session_start();

if (!isset($_SESSION["Cedula"])) {
    header("Location: login.php");
    exit();
}

$cedula = $_SESSION["Cedula"];


$mysqli = new mysqli("localhost", "root", "equipoinfrog", "PROYECT_DataBase_MyCoop6");

if ($mysqli->connect_errno) {
    die("Error al conectar a la base de datos: " . $mysqli->connect_error);
}

$configResult = $mysqli->query("SELECT * FROM ConfiguracionUsuario WHERE Cedula = $cedula");

if ($configResult->num_rows > 0) {
    $config = $configResult->fetch_assoc();
} else {

    $mysqli->query("INSERT INTO ConfiguracionUsuario (Cedula) VALUES ($cedula)");

    $config = [
        "font_size" => 3,
        "theme" => "light",
        "icons" => "icons"
    ];
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $font_size = intval($_POST["font_size"]);
    $theme = $mysqli->real_escape_string($_POST["theme"]);
    $icons = $mysqli->real_escape_string($_POST["icons"]);

    $update = $mysqli->query("
        UPDATE ConfiguracionUsuario
        SET font_size = $font_size,
            theme = '$theme',
            icons = '$icons'
        WHERE Cedula = $cedula
    ");

    if ($update) {
        $mensajeGuardado = "<div class='alert success'>Configuración guardada correctamente.</div>";
        $config["font_size"] = $font_size;
        $config["theme"] = $theme;
        $config["icons"] = $icons;
    } else {
        $mensajeGuardado = "<div class='alert error'>Error al guardar configuración.</div>";
    }
}


$fontSize = intval($config["font_size"]) * 4 + 8;
$themeBg = ($config["theme"] == "dark") ? "#1a1f36" : "#f4f6f9";
$themeColor = ($config["theme"] == "dark") ? "#eee" : "#000";
$icons = $config["icons"];  
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Configuración</title>
<style>
    body {
        background-color: <?= $themeBg ?>;
        color: <?= $themeColor ?>;
        font-size: <?= $fontSize ?>px;
        font-family: Arial, sans-serif;
        padding: 20px;
    }

    .container {
        max-width: 600px;
        margin: auto;
        background: rgba(255,255,255,0.1);
        padding: 20px;
        border-radius: 10px;
        backdrop-filter: blur(5px);
        box-shadow: 0 0 12px #0003;
    }

    h2 {
        text-align: center;
    }

    label {
        font-weight: bold;
    }

    select, input[type="number"] {
        width: 100%;
        padding: 8px;
        margin: 8px 0 20px;
        border-radius: 5px;
    }

    button {
        width: 100%;
        padding: 10px;
        font-size: 18px;
        background: #4a8ef0;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    button:hover {
        background: #1d6fe0;
    }

    .alert {
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 15px;
        text-align: center;
    }

    .success {
        background: #4caf50;
        color: white;
    }

    .error {
        background: #e53935;
        color: white;
    }
    nav {
    background-color: <?= $themeBg ?>;
    padding: 10px 0;
    width: 100%;
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
</style>
</head>
<nav>
    <div id="Navegador">

        <?php if ($config["icons"] === "icons"): ?>
            <a href="usuarioTesorero.php"><img src="iconoUsuario.png"height="70px"></a>
            <a href="fechasTesorero.php"><img src="iconoCalendario.png"height="70px"></a>
            <a href="comunicacionTesorero.php"><img src="iconoComunicacion.png"height="70px"></a>
            <a href="archivoTesorero.php"><img src="iconoDocumentos.png"height="70px"></a>
            <a href="foroTesorero.php"><img src="redes-sociales.png"height="70px"></a>
            <a href="inicioTesorero.php"><img src="anuncios.png" height="70px"></a>
            <a href="notificacionesTesorero.php"><img src="iconoNotificacion.png"height="70px"></a>
            <a href="seccionTesorero.php"><img src="Tesorero.png"height="70px"></a>

        <?php else: ?>
            <a href="usuarioTesorero.php">Usuario</a>
            <a href="fechasTesorero.php">Calendario</a>
            <a href="comunicacionTesorero.php">Comunicación</a>
            <a href="archivoTesorero.php">Archivos</a>
            <a href="foroTesorero.php">Foro</a>
            <a href="inicioTesorero.php">Novedades</a>
            <a href="notificacionesTesorero.php">Notificaciones</a>
            <a href="seccionTesorero.php">Tesorería</a>
        <?php endif; ?>

    </div>
</nav>
<body>

<div class="container">
    <h2>Configuración Personal</h2>

    <?= isset($mensajeGuardado) ? $mensajeGuardado : '' ?>

    <form method="POST">

        <!-- Tamaño de fuente -->
        <label>Tamaño de fuente:</label>
        <input type="number" name="font_size" min="1" max="10" value="<?= $config["font_size"] ?>">

        <!-- Tema -->
        <label>Tema:</label>
        <select name="theme">
            <option value="light" <?= $config["theme"] == "light" ? "selected" : "" ?>>Claro</option>
            <option value="dark" <?= $config["theme"] == "dark" ? "selected" : "" ?>>Oscuro</option>
        </select>

        <!-- Iconos -->
        <label>Modo de iconos:</label>
        <select name="icons">
            <option value="icons" <?= $config["icons"] == "icons" ? "selected" : "" ?>>Iconos</option>
            <option value="words" <?= $config["icons"] == "words" ? "selected" : "" ?>>Palabras</option>
        </select>

        <button type="submit">Guardar cambios</button>
    </form>
</div>
</body>
</html>
