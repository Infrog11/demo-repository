<?php
session_start();


if (!isset($_SESSION['Cedula'])) {
    header("Location: login.php");
    exit();
}

$cedula = $_SESSION['Cedula'];
$mensaje = "";


$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["msg"])) {
    $msg = trim($_POST["msg"]);

    $stmt = $conn->prepare("INSERT INTO Mensajes (Mensaje, Cedula) VALUES (?, ?)");
    $stmt->bind_param("si", $msg, $cedula);

    if ($stmt->execute()) {
        $mensaje = "<p style='color:green;'>✅ Mensaje enviado correctamente.</p>";
    } else {
        $mensaje = "<p style='color:red;'>❌ Error al enviar mensaje: " . $conn->error . "</p>";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop - Enviar Mensaje</title>
    <link rel="stylesheet" href="Style.css" />
</head>
<body>
<nav>
    <div id="Navegador">
        <a href="usuarioUsuario.php"><img src="iconoUsuario.png" height="70px"></a>
        <a href="fechasUsuarios.php"><img src="iconoCalendario.png" height="70px"></a>
        <a href="comunicacionUsuarios.php"><img src="iconoComunicacion.png" height="70px"></a>
        <a href="archivoUsuarios.php"><img src="iconoDocumentos.png" height="70px"></a>
        <a href="configuracionUsuarios.php"><img src="iconoConfiguracion.png" height="70px"></a>
        <a href="notificacionesUsuario.php"><img src="iconoNotificacion.png" height="70px"></a>
        <a href="TesoreroUsuario.php"><img src="Tesorero.png" height="70px"></a>
    </div>
</nav>

<div id="Logo">
    <img src="logoMyCoop.png" height="200px" alt="Logo MyCoop">
</div>

<h2>Enviar mensaje a un administrador</h2>
<form method="POST">
    <label for="msg">Ingrese su mensaje:</label><br>
    <input type="text" id="msg" name="msg" required style="width:80%; padding:5px;"><br><br>
    <button type="submit">Enviar</button>
</form>

<?php
if (!empty($mensaje)) echo $mensaje;
?>
</body>
</html>
