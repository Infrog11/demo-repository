<?php
session_start();

if (!isset($_SESSION['Cedula'])) {
    header("Location: inSesion.php");
    exit();
}

$cedula = $_SESSION['Cedula'];

$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);


$stmtCfg = $conn->prepare("SELECT font_size, theme, icons FROM ConfiguracionUsuario WHERE Cedula = ?");
$stmtCfg->bind_param("i", $cedula);
$stmtCfg->execute();
$resCfg = $stmtCfg->get_result();
if ($resCfg->num_rows > 0) {
    $cfg = $resCfg->fetch_assoc();
} else {
    $cfg = ["font_size" => 3, "theme" => "light", "icons" => "on"];
}
$fontSize = intval($cfg["font_size"]) * 4 + 12;
$theme = $cfg["theme"];
$icons = $cfg["icons"];


if ($theme === "dark") {
    $bodyBg = "#1a1f36";
    $textColor = "#ffffff";
    $tableBg = "#2c3e50";
    $thBg = "#34495e";
    $archivadoBg = "#34495e";
    $inputBg = "#34495e";
    $inputColor = "#fff";
    $btnBg = "#f39c12";
    $btnHover = "#e67e22";
} else {
    $bodyBg = "#f4f6f9";
    $textColor = "#000000";
    $tableBg = "#ffffff";
    $thBg = "#eee";
    $archivadoBg = "#f9f9f9";
    $inputBg = "#fff";
    $inputColor = "#000";
    $btnBg = "#f39c12";
    $btnHover = "#e67e22";
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['mensaje'])) {
    $mensaje = trim($_POST['mensaje']);
    if ($mensaje !== "") {
        $stmt = $conn->prepare("INSERT INTO Mensajes (Mensaje, Cedula) VALUES (?, ?)");
        $stmt->bind_param("si", $mensaje, $cedula);
        $stmt->execute();
        $stmt->close();
    }
}


$stmt = $conn->prepare("SELECT Mensaje, Respuesta, Archivado FROM Mensajes WHERE Cedula = ? ORDER BY idMensaje DESC");
$stmt->bind_param("i", $cedula);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Mensajes - MyCoop</title>
<style>
body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: <?= $bodyBg ?>;
    color: <?= $textColor ?>;
    font-size: <?= $fontSize ?>px;
    margin: 20px;
}

table { border-collapse: collapse; width: 90%; margin-bottom: 20px; background: <?= $tableBg ?>; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background-color: <?= $thBg ?>; }
.archivado { background-color: <?= $archivadoBg ?>; }

textarea {
    width: 100%;
    height: 60px;
    padding: 10px;
    border-radius: 6px;
    border: none;
    background: <?= $inputBg ?>;
    color: <?= $inputColor ?>;
    font-family: inherit;
    font-size: inherit;
    resize: vertical;
}

button {
    padding: 5px 10px;
    margin-top: 5px;
    background: <?= $btnBg ?>;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: inherit;
    transition: all 0.3s ease;
}
button:hover { background: <?= $btnHover ?>; transform: translateY(-2px); }
nav {
    <?php if ($theme === "dark"): ?>
        background: #111;
    <?php else: ?>
        background: #2c3e50;
    <?php endif; ?>
    padding: 10px 0;
    width: 100%;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
}

#Navegador {
    display: flex;
    justify-content: center;
    gap: 20px;
}

#Navegador a {
    text-decoration: none;
    color: inherit;
    font-weight: bold;
}

#Navegador a img {
    transition: transform 0.3s, filter 0.3s;
    border-radius: 50%;
    padding: 5px;
    background: #fff;
}

#Logo img { height: 200px; margin-bottom: 20px; }
</style>
</head>
<body>
<nav>
    <div id="Navegador">

        <?php if ($cfg["icons"] === "icons"): ?>
            <a href="usuarioTesorero.php"><img src="iconoUsuario.png" height="70px"></a>
            <a href="fechasTesorero.php"><img src="iconoCalendario.png"height="70px"></a>
            <a href="comunicacionTesorero.php"><img src="iconoComunicacion.png"height="70px"></a>
            <a href="archivoTesorero.php"><img src="iconoDocumentos.png"height="70px"></a>
            <a href="foroTesorero.php"><img src="redes-sociales.png"height="70px"></a>
            <a href="configuracionTesorero.php"><img src="iconoConfiguracion.png"height="70px"></a>
            <a href="inicioTesorero.php"><img src="anuncios.png" height="70px"></a>
            <a href="seccionTesorero.php"><img src="Tesorero.png"height="70px"></a>

        <?php else: ?>
            <a href="usuarioTesorero.php">Novedades</a>
            <a href="fechasTesorero.php">Calendario</a>
            <a href="comunicacionTesorero.php">Comunicación</a>
            <a href="archivoTesorero.php">Archivos</a>
            <a href="foroTesorero.php">Foro</a>
            <a href="configuracionTesorero.php">Configuración</a>
            <a href="inicioTesorero.php">Notificaciones</a>
            <a href="seccionTesorero.php">Tesorería</a>
        <?php endif; ?>

    </div>
</nav>

<div id="Logo">
    <img src="logoMyCoop.png" alt="Logo MyCoop">
</div>

<h1>Mis Mensajes</h1>

<h2><?= ($icons==="on" ? "💬 " : "") ?>Enviar un nuevo mensaje</h2>
<form method="POST">
    <textarea name="mensaje" placeholder="Escribe tu mensaje aquí..." required></textarea><br>
    <button type="submit"><?= ($icons==="on" ? "📤 " : "") ?>Enviar</button>
</form>

<h2><?= ($icons==="on" ? "📝 " : "") ?>Historial de mensajes</h2>
<?php if ($result->num_rows > 0): ?>
<table>
<tr>
    <th>Mensaje</th>
    <th>Respuesta del Administrador</th>
    <th>Estado</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr class="<?= $row['Archivado'] ? 'archivado' : '' ?>">
    <td><?= htmlspecialchars($row['Mensaje']) ?></td>
    <td><?= $row['Respuesta'] !== null ? htmlspecialchars($row['Respuesta']) : "Sin respuesta aún" ?></td>
    <td><?= $row['Archivado'] ? 'Archivado' : 'Pendiente' ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p>No has enviado mensajes aún.</p>
<?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
