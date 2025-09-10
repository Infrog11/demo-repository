<?php
$configFile = "config.json";
$config = [
    "font_size" => 3,
    "theme" => "light",
    "language" => "es",
    "icons" => "icons"
];
if (file_exists($configFile)) {
    $config = json_decode(file_get_contents($configFile), true);
}

// aplicar configuración
$fontSize = intval($config["font_size"]) * 4 + 8; // escala 1–5 → px
$themeBg = ($config["theme"] == "dark") ? "#222" : "#fff";
$themeColor = ($config["theme"] == "dark") ? "#eee" : "#000";
?>
<html>
<head>
<style>
    body {
        font-size: <?= $fontSize ?>px;
        background: <?= $themeBg ?>;
        color: <?= $themeColor ?>;
        font-family: Arial, sans-serif;
    }
    table {
        background: <?= ($config["theme"] == "dark") ? "#333" : "#f9f9f9" ?>;
        color: <?= $themeColor ?>;
    }
</style>
</head>
<body>
    
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
    <!--<label for="direccion">Tema de la pagina</label>
        <select id="tema" name="tema">
        <option value="claro">Modo claro</option>
        <option value="oscuro">Modo Oscuro</option>
    </select>
    <label for="letra">Tamaño de la letra</label>
        <select id="letra" name="letra">
        <option value="uno">1</option>
        <option value="dos">2</option>
        <option value="tres">3</option>
        <option value="cuatro">4</option>
        <option value="cinco">5</option>
    </select>
    <label for="daltonismo">Activar el modo daltonismo</label>
        <select id="dalt" name="dalt">
        <option value="No">No</option>
        <option value="Si">Si</option>
    </select>
    <label for="Idioma">Idioma</label>
        <select id="idioma" name="idioma">
        <option value="esp">Español</option>
        <option value="ing">Ingles</option>
    </select>
     <label for="simp">Simplificar pagina</label>
        <select id="simp" name="simp">
        <option value="NoSimp">No</option>
        <option value="SiSimp">Si</option>
    </select> -->

    <?php
$configFile = "config.json";

// cargar configuración actual o valores por defecto
$config = [
    "font_size" => 3,
    "theme" => "light",
    "language" => "es",
    "icons" => "icons" // "icons" o "words"
];

if (file_exists($configFile)) {
    $config = json_decode(file_get_contents($configFile), true);
}

// guardar cambios
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $config["font_size"] = $_POST["font_size"];
    $config["theme"] = $_POST["theme"];
    $config["language"] = $_POST["language"];
    $config["icons"] = $_POST["icons"];

    file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
    echo "<p style='color:green;'>Configuración guardada</p>";
}
?>

<h2>Configuración</h2>
<form method="post">
    <label>Tamaño de letra:</label>
    <select name="font_size">
        <?php
        for ($i = 1; $i <= 5; $i++) {
            $sel = ($config["font_size"] == $i) ? "selected" : "";
            echo "<option value='$i' $sel>$i</option>";
        }
        ?>
    </select>
    <br><br>

    <label>Tema:</label>
    <select name="theme">
        <option value="light" <?= $config["theme"] == "light" ? "selected" : "" ?>>Claro</option>
        <option value="dark" <?= $config["theme"] == "dark" ? "selected" : "" ?>>Oscuro</option>
    </select>
    <br><br>

    <label>Idioma:</label>
    <select name="language">
        <option value="es" <?= $config["language"] == "es" ? "selected" : "" ?>>Español</option>
        <option value="en" <?= $config["language"] == "en" ? "selected" : "" ?>>English</option>
    </select>
    <br><br>

    <label>Mostrar:</label>
    <select name="icons">
        <option value="icons" <?= $config["icons"] == "icons" ? "selected" : "" ?>>Iconos</option>
        <option value="words" <?= $config["icons"] == "words" ? "selected" : "" ?>>Palabras</option>
    </select>
    <br><br>

    <button type="submit">Guardar</button>
</form>


</body>
</html>