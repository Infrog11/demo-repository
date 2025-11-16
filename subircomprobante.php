<?php
session_start();
if (!isset($_SESSION["Cedula"])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

$cedula = $_SESSION["Cedula"];

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener configuración del usuario
$stmt = $conn->prepare("SELECT font_size, theme FROM ConfiguracionUsuario WHERE Cedula = ?");
$stmt->bind_param("i", $cedula);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $cfg = $res->fetch_assoc();
} else {
    $cfg = [
        "font_size" => 3,
        "theme" => "light"
    ];
}

// Aplicar configuración
$fontSize = intval($cfg["font_size"]) * 4 + 12;
$theme = $cfg["theme"];

if ($theme === "dark") {
    $bgColor = "#1a1f36";
    $textColor = "#ffffff";
    $inputBg = "#333";
    $inputColor = "#fff";
} else {
    $bgColor = "#f4f6f9";
    $textColor = "#000";
    $inputBg = "#fff";
    $inputColor = "#000";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Subir archivos - MyCoop</title>
<style>
body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: <?= $bgColor ?>;
    color: <?= $textColor ?>;
    font-size: <?= $fontSize ?>px;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
}
.upload-box {
    background: <?= ($theme === "dark") ? "#2c3e50" : "#ffffff" ?>;
    color: <?= $textColor ?>;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0px 6px 15px rgba(0,0,0,0.2);
    width: 90%;
    max-width: 600px;
    margin-top: 40px;
}
.upload-box h2 {
    text-align: center;
    margin-bottom: 20px;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.3);
}
.upload-box input[type="text"],
.upload-box input[type="file"],
.upload-box textarea {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: none;
    font-size: <?= $fontSize ?>px;
    margin-bottom: 15px;
    box-shadow: inset 0px 2px 6px rgba(0,0,0,0.2);
    font-family: inherit;
    background: <?= $inputBg ?>;
    color: <?= $inputColor ?>;
}
.upload-box textarea { resize: vertical; min-height: 100px; }
.upload-box button, .upload-box a {
    width: 100%;
    background: #4a5675ff;
    color: #fff;
    padding: 12px;
    font-size: <?= $fontSize ?>px;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
    margin-bottom: 10px;
    text-align: center;
    display: inline-block;
    text-decoration: none;
}
.upload-box button:hover, .upload-box a:hover {
    background: #16275c;
    transform: translateY(-2px);
}
.mensaje { text-align: center; margin-top: 15px; font-weight: bold; }
.mensaje.exito { color: #2ecc71; }
.mensaje.error { color: #e74c3c; }
</style>
</head>
<body>

<div class="upload-box">
    <h2>Subir archivos</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="nombre" placeholder="Nombre del archivo" required>
        <textarea name="descripcion" placeholder="Descripción del archivo" required></textarea>
        <input type="file" name="comprobante" required>
        <button type="submit">⬆ Subir</button>
    </form>

    <a href="archivo.php">⬅ Volver</a>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $archivo = $_FILES['comprobante'];

        if (!file_exists("uploads")) { mkdir("uploads"); }

        $archivoNombre = time() . "_" . basename($archivo['name']);
        $destino = "uploads/" . $archivoNombre;

        if (move_uploaded_file($archivo['tmp_name'], $destino)) {
            $fechaHoy = date("Y-m-d");
            $stmt = $conn->prepare("INSERT INTO Archivos (NombreArchivo, Fecha, DescripcionArch, RutaArchivo) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $fechaHoy, $descripcion, $destino);

            if ($stmt->execute()) {
                echo "<p class='mensaje exito'>✅ Archivo subido correctamente y guardado en la base de datos.</p>";
            } else {
                echo "<p class='mensaje error'>❌ Error al guardar en la BD: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p class='mensaje error'>❌ Error al subir el archivo.</p>";
        }
    }

    $conn->close();
    ?>
</div>

</body>
</html>
