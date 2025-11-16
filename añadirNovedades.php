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
// 2. Conexión a la BD
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

$font_size = 3; // por defecto
$theme = "light";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $font_size = intval($row["font_size"]);
    $theme = $row["theme"];
}

$stmt->close();

// convertir font_size (escala x4 como tu sistema)
$realFontSize = $font_size * 4;

// -------------------------------------------------------
// 4. Colores según el theme
// -------------------------------------------------------
if ($theme === "dark") {
    $bodyBg        = "#0d1117";
    $boxBg         = "#1a1f36";
    $textColor     = "#ffffff";
    $textareaBg    = "#0f1629";
    $buttonBg      = "#4a5675";
    $buttonHover   = "#2f3b57";
} else {
    $bodyBg        = "#f4f6f9";
    $boxBg         = "#ffffff";
    $textColor     = "#000000";
    $textareaBg    = "#f0f0f0";
    $buttonBg      = "#4a5675";
    $buttonHover   = "#16275c";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop - Novedades</title>

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

        .novedades-box {
            background: <?= $boxBg ?>;
            color: <?= $textColor ?>;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 6px 15px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 600px;
            margin-top: 40px;
        }

        .novedades-box h2 {
            text-align: center;
            margin-bottom: 15px;
        }

        .novedades-box textarea {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            border-radius: 8px;
            border: none;
            resize: vertical;
            font-size: <?= $realFontSize ?>px;
            background: <?= $textareaBg ?>;
            color: <?= $textColor ?>;
            font-family: inherit;
        }

        .novedades-box textarea::placeholder {
            color: #c0c0c0;
            opacity: 1;
        }

        .novedades-box button {
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
            margin-bottom: 10px;
        }

        .novedades-box button:hover {
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

<div class="novedades-box">
    <button onclick="history.back(); return false;">⬅ Volver</button>
    <h2>Ingresa las novedades</h2>

    <form method="POST">
        <textarea name="Nov" placeholder="Escribe tu novedad aquí..." required></textarea>
        <button type="submit">💾 Guardar</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $Novedad = trim($_POST["Nov"]);

        if (!empty($Novedad)) {

            $stmt = $conn->prepare("INSERT INTO Novedades (Novedad) VALUES (?)");
            $stmt->bind_param("s", $Novedad);

            if ($stmt->execute()) {
                echo "<p class='mensaje exito'>✔ Novedad guardada con éxito</p>";
            } else {
                echo "<p class='mensaje error'>❌ Error al guardar: " . $conn->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p class='mensaje error'>⚠ Debes ingresar una novedad</p>";
        }
    }

    $conn->close();
    ?>
</div>

</body>
</html>
