<?php
session_start();
if (!isset($_SESSION['Cedula'])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

$ced = $_SESSION['Cedula'];
$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

$stmtCfg = $conn->prepare("SELECT font_size, theme FROM ConfiguracionUsuario WHERE Cedula = ?");
$stmtCfg->bind_param("i", $ced);
$stmtCfg->execute();
$resCfg = $stmtCfg->get_result();

if ($resCfg->num_rows > 0) {
    $cfg = $resCfg->fetch_assoc();
} else {
    $cfg = ["font_size" => 3, "theme" => "light"];
}

$fontSize = intval($cfg["font_size"]) * 4 + 12;
$theme = $cfg["theme"];

if ($theme === "dark") {
    $bodyBg = "#1a1f36";
    $textColor = "#ffffff";
    $boxBg = "#2c3e50";
    $respuestaBg = "#34495e";
    $btnBg = "#64348bff";
    $btnHover = "#16275c";
    $volverBg = "#674a75ff";
} else {
    $bodyBg = "#f4f6f9";
    $textColor = "#000000";
    $boxBg = "#ffffff";
    $respuestaBg = "#eaf2f8";
    $btnBg = "#8e44ad";
    $btnHover = "#34495e";
    $volverBg = "#9b59b6";
}

$stmt = $conn->prepare("
    SELECT Nombre, Apellido, COALESCE(Pronombres, '') AS Pronombres
    FROM Persona WHERE Cedula = ?
");
$stmt->bind_param("i", $ced);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$nombreCompleto = $usuario['Nombre'] . ' ' . $usuario['Apellido'];

$idForo = intval($_GET['id'] ?? 0);
$foro = $conn->query("SELECT * FROM Foros WHERE IdForo = $idForo")->fetch_assoc();

if (isset($_POST['borrar_respuesta'])) {
    $idRespuesta = intval($_POST['id_respuesta']);
    $conn->query("DELETE FROM Respuestas WHERE IdRespuesta = $idRespuesta AND IdForo = $idForo");
}

if (isset($_POST['responder'])) {
    $mensaje = $_POST['mensaje'];
    if (!empty($mensaje)) {
        $stmt = $conn->prepare("INSERT INTO Respuestas (IdForo, Autor, Mensaje) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $idForo, $nombreCompleto, $mensaje);
        $stmt->execute();
    }
}

$respuestas = $conn->query("SELECT * FROM Respuestas WHERE IdForo = $idForo ORDER BY Fecha ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($foro['Titulo']); ?></title>
<style>
body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: <?= $bodyBg ?>;
    color: <?= $textColor ?>;
    font-size: <?= $fontSize ?>px;
    margin: 20px;
    display: flex;
    justify-content: center;
}
.foro-box {
    background: <?= $boxBg ?>;
    color: <?= $textColor ?>;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0px 6px 15px rgba(0,0,0,0.2);
    width: 90%;
    max-width: 700px;
}
h2, h3 { text-align: center; text-shadow: 1px 1px 4px rgba(0,0,0,0.3); }
.respuesta {
    background: <?= $respuestaBg ?>;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 10px;
    box-shadow: inset 0px 2px 4px rgba(0,0,0,0.2);
}
.respuesta p { margin: 5px 0; }
form.borrar-form { display: inline; }
button.borrar {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}
button.borrar:hover { background-color: #c0392b; transform: translateY(-2px); }

form.responder textarea {
    width: 100%;
    min-height: 100px;
    padding: 12px;
    border-radius: 8px;
    border: none;
    font-size: <?= $fontSize ?>px;
    font-family: inherit;
    margin-bottom: 15px;
    box-shadow: inset 0px 2px 6px rgba(0,0,0,0.2);
    resize: vertical;
}

form.responder button {
    width: 100%;
    background: <?= $btnBg ?>;
    color: #fff;
    padding: 12px;
    font-size: <?= $fontSize + 2 ?>px;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
}
form.responder button:hover {
    background: <?= $btnHover ?>;
    transform: translateY(-2px);
}

.volver {
    display: inline-block;
    margin-bottom: 20px;
    text-decoration: none;
    background: <?= $volverBg ?>;
    color: #fff;
    padding: 12px;
    border-radius: 8px;
    font-weight: bold;
    width: 100%;
    text-align: center;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}
.volver:hover {
    background: <?= $btnHover ?>;
    transform: translateY(-2px);
}
</style>
</head>
<body>

<div class="foro-box">
    <a href="foro.php" class="volver">⬅ Volver al foro</a>

    <h2><?php echo htmlspecialchars($foro['Titulo']); ?></h2>
    <p style="text-align:center;"><strong>Autor:</strong> <?php echo htmlspecialchars($foro['Autor']); ?> — <?php echo $foro['Fecha']; ?></p>
    <hr>

    <h3>Respuestas</h3>
    <?php while ($r = $respuestas->fetch_assoc()) { ?>
        <div class="respuesta">
            <p><strong><?php echo htmlspecialchars($r['Autor']); ?></strong> (<?php echo $r['Fecha']; ?>)</p>
            <p><?php echo nl2br(htmlspecialchars($r['Mensaje'])); ?></p>
            <form method="POST" class="borrar-form" onsubmit="return confirm('¿Seguro que deseas eliminar esta respuesta?');">
                <input type="hidden" name="id_respuesta" value="<?php echo $r['IdRespuesta']; ?>">
                <button type="submit" name="borrar_respuesta" class="borrar">Eliminar</button>
            </form>
        </div>
    <?php } ?>

    <h3>Agregar respuesta</h3>
    <form method="POST" class="responder">
        <p><strong>Autor:</strong> <?php echo htmlspecialchars($nombreCompleto); ?></p>
        <textarea name="mensaje" placeholder="Escribe tu respuesta aquí..." required></textarea>
        <button type="submit" name="responder">Responder</button>
    </form>
</div>

</body>
</html>
