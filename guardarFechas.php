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
$db   = "proyect_database_mycoop6";

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
    <title>Agregar Evento - MyCoop</title>

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

        .evento-box {
            background: <?= $boxBg ?>;
            color: <?= $textColor ?>;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 6px 15px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 600px;
            margin-top: 40px;
        }

        .evento-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .evento-box input[type="text"],
        .evento-box input[type="date"],
        .evento-box textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            font-size: <?= $realFontSize ?>px;
            margin-bottom: 15px;
            background: <?= $inputBg ?>;
            color: <?= $textColor ?>;
            box-shadow: inset 0px 2px 6px rgba(0,0,0,0.2);
            font-family: inherit;
        }

        .evento-box textarea {
            resize: vertical;
            min-height: 100px;
        }

        .evento-box button {
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
            margin-bottom: 10px;
        }

        .evento-box button:hover {
            background: <?= $buttonHover ?>;
            transform: translateY(-2px);
        }

        .evento-box a {
            display: inline-block;
            text-align: center;
            text-decoration: none;
            background: <?= $buttonBg ?>;
            color: #fff;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
            width: 100%;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
            font-size: <?= $realFontSize ?>px;
        }

        .evento-box a:hover {
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

<div class="evento-box">
    <h2>Agregar Evento</h2>
    <form method="post">
        <input type="date" name="fecha" required>
        <input type="text" name="titulo" maxlength="150" placeholder="Título del evento" required>
        <textarea name="descripcion" placeholder="Descripción del evento (opcional)"></textarea>
        <button type="submit">💾 Guardar</button>
    </form>

    <a href="fechas.php">⬅ Volver</a>

    <?php
    // -------------------------------------------------------
    // Guardar evento
    // -------------------------------------------------------

    // Nota: ya estamos conectados arriba
    $conn->set_charset("utf8mb4");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fecha = $_POST['fecha'];
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'] ?? "";

        if (!empty($fecha) && !empty($titulo)) {
            $sql = "INSERT INTO Eventos (NombreEvento, FechaEvento, DescripcionEvento)
                    VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sss", $titulo, $fecha, $descripcion);

                if ($stmt->execute()) {
                    echo "<p class='mensaje exito'>✅ Evento guardado correctamente</p>";
                } else {
                    echo "<p class='mensaje error'>❌ Error al guardar: " . $stmt->error . "</p>";
                }

                $stmt->close();
            } else {
                echo "<p class='mensaje error'>❌ Error en la consulta: " . $conn->error . "</p>";
            }
        } else {
            echo "<p class='mensaje error'>⚠ Completa los campos obligatorios.</p>";
        }
    }

    $conn->close();
    ?>
</div>

</body>
</html>
