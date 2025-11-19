<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "equipoinfrog";
$db   = "proyect_database_mycoop6";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

if (!isset($_SESSION['Cedula'])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

$ced = $_SESSION['Cedula'];

$stmt = $conn->prepare("
    SELECT Nombre, Apellido, edad AS Edad,
           COALESCE(Pronombres, '') AS Pronombres,
           COALESCE(FotoPerfil, 'DefaultPerfile.png') AS FotoPerfil
    FROM Persona
    WHERE Cedula = ?
");
$stmt->bind_param("i", $ced);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$nombreCompleto = $usuario['Nombre'] . ' ' . $usuario['Apellido'];

$stmtCfg = $conn->prepare("
    SELECT font_size, theme, icons
    FROM configuracionUsuario
    WHERE Cedula = ?
");
$stmtCfg->bind_param("i", $ced);
$stmtCfg->execute();
$config = $stmtCfg->get_result()->fetch_assoc();

$fontSize = isset($config["font_size"]) ? intval($config["font_size"]) : 3;
$theme = $config["theme"] ?? "light";
$iconsMode = $config["icons"] ?? "icons";

if (isset($_POST['borrar'])) {
    $id = intval($_POST['idforo']);
    $conn->query("DELETE FROM Foros WHERE IdForo = $id");
}

if (isset($_POST['crear'])) {
    $titulo = $_POST['titulo'];
    $autor = $nombreCompleto;

    if (!empty($titulo)) {
        $stmtNew = $conn->prepare("INSERT INTO Foros (Titulo, Autor) VALUES (?, ?)");
        $stmtNew->bind_param("ss", $titulo, $autor);
        $stmtNew->execute();
    }
}

$foros = $conn->query("SELECT * FROM Foros ORDER BY Fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Foro de la Cooperativa</title>

<style>
:root {
    --font-size: <?= $fontSize * 4 ?>px;
    --bg-color: <?= $theme === "dark" ? "#1a1a1a" : "#f0f2f5" ?>;
    --box-bg: <?= $theme === "dark" ? "#1f232fff" : "#ffffff" ?>;
    --text-color: <?= $theme === "dark" ? "#ffffff" : "#2c3e50" ?>;
    --button-bg: <?= $theme === "dark" ? "#4a76d4" : "#4a5675" ?>;
    --button-hover: <?= $theme === "dark" ? "#365bb0" : "#16275c" ?>;
    --icon-bg: <?= $theme === "dark" ? "#d5d5d5ff" : "#171717ff" ?>;

}

body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: var(--bg-color);
    margin: 0;
    padding: 20px;
    display: flex;
    justify-content: center;
    color: var(--text-color);
    font-size: var(--font-size);
}

.foro-box {
    background: var(--box-bg);
    padding: 25px;
    border-radius: 12px;
    width: 90%;
    max-width: 700px;
}

h2, h3 {
    text-align: center;
}

input[type="text"] {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: none;
    margin-bottom: 15px;
    font-size: 16px;
}

button {
    width: 100%;
    background: var(--button-bg);
    color: #fff;
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-bottom: 20px;
    transition: 0.3s;
}

button:hover {
    background: var(--button-hover);
    transform: translateY(-2px);
}

.foro-item {
    background: var(--nav-bg);
    color: white;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.foro-info a {
    color: #a8c0ff;
    text-decoration: none;
}

button.borrar {
    background-color: #e74c3c;
}

button.borrar:hover {
    background-color: #c0392b;
}


.navbar {
    background: var(--nav-bg);
    padding: 8px 0;
    width: 100%;
    position: sticky;
    bottom: 0;
    z-index: 10;
    margin-top: 20px;
}

#Navegador {
    display: flex;
    justify-content: center;
    gap: 10px;
}

#Navegador a img {
    height: 50px;
    border-radius: 50%;
    background: var(--icon-bg);
    padding: 4px;
    transition: 0.3s;
}

#Navegador a img:hover {
    transform: scale(1.1);
}


#Navegador a.text-link {
    color: white;
    background: var(--icon-bg);
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    font-size: 16px;
}
#Navegador a.text-link:hover {
    background: #555;
}
</style>
</head>

<body>
<div class="foro-box">
    <h2>Foro de la Cooperativa</h2>
    <p>Sesión iniciada como <strong><?= htmlspecialchars($nombreCompleto) ?></strong></p>

    <h3>Crear nuevo hilo</h3>
    <form method="POST">
        <input type="text" name="titulo" placeholder="Título del hilo" required>
        <button type="submit" name="crear">Crear hilo</button>
    </form>

    <a href="administrarCuentas.php" class="link">Administrar Cuentas</a>

    <h3>Hilos existentes</h3>

    <?php while ($fila = $foros->fetch_assoc()): ?>
        <div class="foro-item">
            <div class="foro-info">
                <a href="verforo.php?id=<?= $fila['IdForo'] ?>">
                    <?= htmlspecialchars($fila['Titulo']) ?>
                </a>
                — por <?= htmlspecialchars($fila['Autor']) ?>
                (<?= $fila['Fecha'] ?>)
            </div>

            <form method="POST" class="borrar-form" onsubmit="return confirm('¿Seguro que deseas borrar este foro?');">
                <input type="hidden" name="idforo" value="<?= $fila['IdForo'] ?>">
                <button type="submit" name="borrar" class="borrar">Borrar</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

<div class="navbar">
    <div id="Navegador">

        <?php
        function navItem($url, $icon, $text, $mode) {
            if ($mode === "icons") {
                return "<a href='$url'><img src='$icon'></a>";
            } else {
                return "<a href='$url' class='text-link'>$text</a>";
            }
        }

        echo navItem("aprobarUsuarios.php", "iconoAdministracion.png", "Administración", $iconsMode);
        echo navItem("usuario.php", "iconoUsuario.png", "Usuario", $iconsMode);
        echo navItem("fechas.php", "iconoCalendario.png", "Fechas", $iconsMode);
        echo navItem("comunicacion.php", "iconoComunicacion.png", "Comunicación", $iconsMode);
        echo navItem("archivo.php", "iconoDocumentos.png", "Archivos", $iconsMode);
        echo navItem("Construccion.php", "iconoConstruccion.png", "Construcción", $iconsMode);
        echo navItem("inicio.php", "anuncios.png", "Inicio", $iconsMode);
        echo navItem("configuracion.php", "iconoConfiguracion.png", "Configuración", $iconsMode);
        echo navItem("notificaciones.php", "iconoNotificacion.png", "Notificaciones", $iconsMode);
        echo navItem("TesoreroAdmin.php", "Tesorero.png", "Tesorero", $iconsMode);
        ?>
    </div>
</div>

</body>
</html>

