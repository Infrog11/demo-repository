<?php
session_start();

if (!isset($_SESSION['Cedula'])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

$ced = $_SESSION['Cedula'];
$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);


$stmt = $conn->prepare("
    SELECT Nombre, Apellido, COALESCE(Pronombres,'') AS Pronombres, COALESCE(FotoPerfil,'DefaultPerfile.png') AS FotoPerfil
    FROM Persona WHERE Cedula = ?
");
$stmt->bind_param("i", $ced);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$nombreCompleto = $usuario['Nombre'] . ' ' . $usuario['Apellido'];


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

if ($theme === "dark") {
    $bodyBg = "#1a1f36";
    $textColor = "#ffffff";
    $boxBg = "#2c3e50";
    $linkColor = "#3498db";
    $btnBg = "#f39c12";
    $btnHover = "#e67e22";
    $inputBg = "#34495e";
    $inputColor = "#fff";
} else {
    $bodyBg = "#f4f6f9";
    $textColor = "#000000";
    $boxBg = "#ffffff";
    $linkColor = "#0077cc";
    $btnBg = "#f39c12";
    $btnHover = "#e67e22";
    $inputBg = "#fff";
    $inputColor = "#000";
}

$idForo = intval($_GET['id'] ?? 0);
$foro = $conn->query("SELECT * FROM Foros WHERE IdForo = $idForo")->fetch_assoc();

if (isset($_POST['responder'])) {
    $mensaje = $_POST['mensaje'];
    if (!empty($mensaje)) {
        $stmt = $conn->prepare("INSERT INTO Respuestas (IdForo, Autor, Mensaje) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $idForo, $nombreCompleto, $mensaje);
        $stmt->execute();
    }
}

if (isset($_POST['reportar_respuesta'])) {
    $idRespuesta = intval($_POST['id_respuesta']);
    $autorRespuesta = $_POST['autor_respuesta'];
    $mensajeNotif = "⚠️ El usuario $nombreCompleto ha reportado una respuesta de $autorRespuesta en el foro '{$foro['Titulo']}'.";
    $stmtNotif = $conn->prepare("INSERT INTO Notificaciones (Tipo, Mensaje, Fecha, Estado) VALUES ('Reporte', ?, NOW(), 'No leído')");
    $stmtNotif->bind_param("s", $mensajeNotif);
    $stmtNotif->execute();
}

$respuestas = $conn->query("SELECT * FROM Respuestas WHERE IdForo = $idForo ORDER BY Fecha ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($foro['Titulo']) ?></title>
<style>
body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: <?= $bodyBg ?>;
    color: <?= $textColor ?>;
    font-size: <?= $fontSize ?>px;
    margin: 20px;
}
a { color: <?= $linkColor ?>; text-decoration: none; }
a:hover { text-decoration: underline; }

.respuesta {
    background: <?= $boxBg ?>;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 10px;
    max-width: 700px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}
.respuesta p { margin: 5px 0; }

textarea {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: none;
    background: <?= $inputBg ?>;
    color: <?= $inputColor ?>;
    resize: vertical;
    font-family: inherit;
    font-size: inherit;
}

button.reportar, button[name="responder"] {
    background-color: <?= $btnBg ?>;
    color: #fff;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: inherit;
}
button.reportar:hover, button[name="responder"]:hover {
    background-color: <?= $btnHover ?>;
    transform: translateY(-2px);
}

a.volver {
    display: inline-block;
    margin-bottom: 20px;
    text-decoration: none;
    background: <?= $btnBg ?>;
    color: #fff;
    padding: 10px;
    border-radius: 6px;
    font-weight: bold;
}
a.volver:hover { background: <?= $btnHover ?>; transform: translateY(-2px); }

form.reportar-form { display: inline; margin-left: 5px; }
</style>
</head>
<body>
<a href="foroUsuarios.php" class="volver"><?= ($icons === "on" ? "⬅ " : "") ?>Volver al foro</a>

<h2><?= htmlspecialchars($foro['Titulo']) ?></h2>
<p><strong>Autor:</strong> <?= htmlspecialchars($foro['Autor']) ?> — <?= $foro['Fecha'] ?></p>
<hr>

<h3>Respuestas</h3>
<?php while ($r = $respuestas->fetch_assoc()) { ?>
    <div class="respuesta">
        <p><strong><?= htmlspecialchars($r['Autor']) ?></strong> (<?= $r['Fecha'] ?>)</p>
        <p><?= nl2br(htmlspecialchars($r['Mensaje'])) ?></p>

        <!-- Botón de Reportar -->
        <form method="POST" class="reportar-form" onsubmit="return confirm('¿Deseas reportar esta respuesta?');">
            <input type="hidden" name="id_respuesta" value="<?= $r['IdRespuesta'] ?>">
            <input type="hidden" name="autor_respuesta" value="<?= htmlspecialchars($r['Autor']) ?>">
            <button type="submit" name="reportar_respuesta" class="reportar">
                <?= ($icons === "on" ? "🚩 " : "") ?>Reportar
            </button>
        </form>
    </div>
<?php } ?>

<h3>Agregar respuesta</h3>
<form method="POST">
    <p><strong>Autor:</strong> <?= htmlspecialchars($nombreCompleto) ?></p>
    <label>Mensaje:</label><br>
    <textarea name="mensaje" required></textarea><br><br>
    <button type="submit" name="responder"><?= ($icons === "on" ? "💬 " : "") ?>Responder</button>
</form>
</body>
</html>
