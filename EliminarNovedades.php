<?php
session_start();

// -------------------------------------------------------
// 1. Verificar sesión
// -------------------------------------------------------
if (!isset($_SESSION["Cedula"])) {
    header("Location: login.php");
    exit();
}

$cedula = $_SESSION["Cedula"];

// -------------------------------------------------------
// 2. Conexión BD
// -------------------------------------------------------
$host = "localhost";
$user = "root";
$pass = "equipoinfrog";
$db   = "proyect_database_MyCoop6";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// -------------------------------------------------------
// 3. Obtener configuración del usuario
// -------------------------------------------------------
$sql = "SELECT font_size, theme FROM ConfiguracionUsuario WHERE Cedula = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cedula);
$stmt->execute();
$result = $stmt->get_result();

$font_size = 3;
$theme = "light";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $font_size = intval($row["font_size"]);
    $theme = $row["theme"];
}

$stmt->close();
$realFontSize = $font_size * 4;

// -------------------------------------------------------
// 4. Colores por tema
// -------------------------------------------------------
if ($theme === "dark") {
    $bodyBg        = "#0d1117";
    $boxBg         = "#1a1f36";
    $textColor     = "#ffffff";
    $inputBg       = "#0f1629";
    $buttonBg      = "#4a5675";
    $buttonHover   = "#2f3b57";
} else {
    $bodyBg        = "#f4f6f9";
    $boxBg         = "#ffffff";
    $textColor     = "#000000";
    $inputBg       = "#f0f0f0";
    $buttonBg      = "#4a5675";
    $buttonHover   = "#16275c";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Eliminar Novedad - MyCoop</title>

    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: <?= $bodyBg ?>;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            color: <?= $textColor ?>;
            font-size: <?= $realFontSize ?>px;
        }

        .eliminar-box {
            background: <?= $boxBg ?>;
            color: <?= $textColor ?>;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 6px 15px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 500px;
            margin-top: 40px;
        }

        .eliminar-box h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .eliminar-box input[type="number"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            font-size: <?= $realFontSize ?>px;
            margin-bottom: 15px;
            background: <?= $inputBg ?>;
            color: <?= $textColor ?>;
            box-shadow: inset 0px 2px 6px rgba(0,0,0,0.2);
        }

        .eliminar-box button {
            width: 100%;
            background: <?= $buttonBg ?>;
            color: #ffffff;
            padding: 12px;
            font-size: <?= $realFontSize ?>px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
        }

        .eliminar-box button:hover {
            background: <?= $buttonHover ?>;
            transform: translateY(-2px);
        }

        .mensaje {
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
        }

        .mensaje.exito { color: #2ecc71; }
        .mensaje.error { color: #e74c3c; }
    </style>
</head>
<body>

<div class="eliminar-box">
    <button onclick="history.back(); return false;">⬅ Volver</button>
    <h1>Eliminar Novedad</h1>

    <form method="POST">
        <input type="number" id="idNovedad" name="idNovedad" placeholder="ID de la novedad" required>
        <button type="submit">Eliminar</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $id = intval($_POST["idNovedad"]);

        $check = $conn->prepare("SELECT * FROM Novedades WHERE idNovedad = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("DELETE FROM Novedades WHERE idNovedad = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo "<p class='mensaje exito'>✔ Novedad con ID $id eliminada con éxito.</p>";
            } else {
                echo "<p class='mensaje error'>❌ Error al eliminar: " . $conn->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p class='mensaje error'>⚠ No existe ninguna novedad con el ID $id.</p>";
        }

        $check->close();
        $conn->close();
    }
    ?>
</div>

</body>
</html>
