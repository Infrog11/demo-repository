<?php
session_start();

if (!isset($_SESSION['Cedula'])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

$ced = $_SESSION['Cedula'];
$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

// --- Obtener información del usuario ---
$stmt = $conn->prepare("
    SELECT Nombre, Apellido, COALESCE(Pronombres,'') AS Pronombres, COALESCE(FotoPerfil,'DefaultPerfile.png') AS FotoPerfil
    FROM Persona WHERE Cedula = ?
");
$stmt->bind_param("i", $ced);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$nombreCompleto = $usuario['Nombre'] . ' ' . $usuario['Apellido'];

// --- Obtener configuración de usuario ---
$stmtCfg = $conn->prepare("SELECT font_size, theme, icons FROM ConfiguracionUsuario WHERE Cedula = ?");
$stmtCfg->bind_param("i", $ced);
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

// --- Tema ---
if ($theme === "dark") {
    $bodyBg = "#1a1f36";
    $textColor = "#ffffff";
    $boxBg = "#2c3e50";
    $linkColor = "#3498db";
    $btnBg = "#e67e22";
    $btnHover = "#d35400";
    $inputBg = "#34495e";
    $inputColor = "#fff";
} else {
    $bodyBg = "#f4f6f9";
    $textColor = "#000000";
    $boxBg = "#ffffff";
    $linkColor = "#0077cc";
    $btnBg = "#e67e22";
    $btnHover = "#d35400";
    $inputBg = "#fff";
    $inputColor = "#000";
}

// --- Crear nuevo hilo ---
if (isset($_POST['crear'])) {
    $titulo = trim($_POST['titulo']);
    if (!empty($titulo)) {
        $sql = $conn->prepare("INSERT INTO Foros (Titulo, Autor) VALUES (?, ?)");
        $sql->bind_param("ss", $titulo, $nombreCompleto);
        $sql->execute();
        $sql->close();
    }
}

// --- Reportar foro ---
if (isset($_POST['reportar'])) {
    $idForo = intval($_POST['idforo']);
    $motivo = trim($_POST['motivo']);
    $stmt = $conn->prepare("INSERT INTO ReportesForo (IdForo, CedulaReportante, Motivo) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $idForo, $ced, $motivo);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Foro reportado correctamente.');</script>";
}

// --- Consultar hilos ---
$foros = $conn->query("SELECT * FROM Foros ORDER BY Fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Foro de la Cooperativa</title>
<style>
body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: <?= $bodyBg ?>;
    color: <?= $textColor ?>;
    font-size: <?= $fontSize ?>px;
    margin: 20px;
}
.foro-item {
    margin-bottom: 10px;
    padding: 10px;
    background: <?= $boxBg ?>;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 700px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}
.foro-info a {
    text-decoration: none;
    color: <?= $linkColor ?>;
    font-weight: bold;
}
.foro-info a:hover { text-decoration: underline; }
form { display: inline; margin-left: 10px; }
input[type="text"] {
    padding: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
    background: <?= $inputBg ?>;
    color: <?= $inputColor ?>;
}
button.reportar {
    background-color: <?= $btnBg ?>;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
}
button.reportar:hover { background-color: <?= $btnHover ?>; }

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

</style>
<nav>
    <div id="Navegador">

        <?php if ( $cfg["icons"] === "icons"): ?>
            <a href="usuarioUsuario.php"><img src="iconoUsuario.png"height="70px"></a>
            <a href="fechasUsuarios.php"><img src="iconoCalendario.png"height="70px"></a>
            <a href="comunicacionUsuarios.php"><img src="iconoComunicacion.png"height="70px"></a>
            <a href="archivoUsuarios.php"><img src="iconoDocumentos.png"height="70px"></a>
            <a href="inicioUsuario.php"><img src="anuncios.png" height="70px"></a>
            <a href="configuracionUsuarios.php"><img src="iconoConfiguracion.png"height="70px"></a>
            <a href="notificacionesUsuario.php"><img src="iconoNotificacion.png"height="70px"></a>
            <a href="TesoreroUsuario.php"><img src="Tesorero.png"height="70px"></a>

        <?php else: ?>
            <a href="usuarioUsuario.php">usuario</a>
            <a href="fechasUsuarios.php">Calendario</a>
            <a href="comunicacionUsuarios.php">Comunicación</a>
            <a href="archivoUsuarios.php">Archivos</a>
            <a href="inicioUsuario.php">Novedades</a>
            <a href="configuracionUsuarios.php">Configuración</a>
            <a href="notificacionesUsuario.php">Notificaciones</a>
            <a href="TesoreroUsuario.php">Tesorería</a>
        <?php endif; ?>

    </div>
</nav>

</head>
<body>
<h2>Foro de la Cooperativa</h2>
<p>Sesión iniciada como <strong><?= htmlspecialchars($nombreCompleto) ?></strong></p>

<h3>Crear nuevo hilo</h3>
<form method="POST">
    <label>Título:</label><br>
    <input type="text" name="titulo" required><br><br>
    <button type="submit" name="crear">
        <?= ($icons === "on" ? "📝 " : "") ?>Crear hilo
    </button>
</form>

<h3>Hilos existentes</h3>
<?php while ($fila = $foros->fetch_assoc()) { ?>
    <div class="foro-item">
        <div class="foro-info">
            <a href="verforoUsuario.php?id=<?= $fila['IdForo'] ?>">
                <?= htmlspecialchars($fila['Titulo']) ?>
            </a>
            — por <?= htmlspecialchars($fila['Autor']) ?> (<?= $fila['Fecha'] ?>)
        </div>

        <form method="POST" onsubmit="return confirm('¿Deseas reportar este foro?');">
            <input type="hidden" name="idforo" value="<?= $fila['IdForo'] ?>">
            <input type="text" name="motivo" placeholder="Motivo (opcional)" style="width:140px;">
            <button type="submit" name="reportar" class="reportar">
                <?= ($icons === "on" ? "⚠️ " : "") ?>Reportar
            </button>
        </form>
    </div>
<?php } ?>
</body>
</html>
