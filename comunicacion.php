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
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body {
    background-color: #f4f6f9;
    text-align: center;
    padding: 20px;
}

nav {
    background-color: #2c3e50;
    padding: 10px 0;
    margin-bottom: 20px;
}

#Navegador {
    display: flex;
    justify-content: center;
    gap: 15px;
}

#Navegador a img {
    transition: transform 0.2s ease-in-out;
}

#Navegador a img:hover {
    transform: scale(1.1);
}

#Logo img {
    margin: 20px 0;
}

h2 {
    color: #2c3e50;
    margin-bottom: 20px;
}

form {
    background: #ffffff;
    border-radius: 10px;
    padding: 20px;
    width: 60%;
    margin: 0 auto;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
    text-align: left;
}

label {
    font-weight: bold;
    color: #34495e;
}

input[type="text"] {
    margin-top: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
}

button {
    background-color: #27ae60;
    color: white;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    cursor: pointer;
    transition: background 0.3s;
}

button:hover {
    background-color: #219150;
}
</style>
<body>
<nav>
    <div id="Navegador">
        <a href="usuario.php"><img src="iconoUsuario.png" height="70px"></a>
        <a href="fechas.php"><img src="iconoCalendario.png" height="70px"></a>
        <a href="comunicacion.php"><img src="iconoComunicacion.png" height="70px"></a>
        <a href="archivo.php"><img src="iconoDocumentos.png" height="70px"></a>
        <a href="configuracion.php"><img src="iconoConfiguracion.png" height="70px"></a>
        <a href="notificaciones.php"><img src="iconoNotificacion.png" height="70px"></a>
        <a href="TesoreroAdmin.php"><img src="Tesorero.png" height="70px"></a>    
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
