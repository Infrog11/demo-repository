<?php
session_start();


if (!isset($_SESSION['Cedula'])) {
    header("Location: login.php");
    exit();
}


$host = "localhost";
$user = "root";
$pass = "equipoinfrog";
$db   = "proyect_database_mycoop6";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$ced = (int) $_SESSION['Cedula'];


$stmt = $conn->prepare("
    SELECT Nombre, Apellido, edad AS Edad,
        COALESCE(Pronombres, '') AS Pronombres,
        COALESCE(FotoPerfil, 'DefaultPerfile.png') AS FotoPerfil
    FROM Persona WHERE Cedula = ?
");
$stmt->bind_param("i", $ced);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();


$stmt2 = $conn->prepare("
    SELECT IdUH, Etapa, HorasTotales, HorasSemanales, SemanaInicio
    FROM Construye WHERE Cedula = ?
");
$stmt2->bind_param("i", $ced);
$stmt2->execute();
$construccion = $stmt2->get_result()->fetch_assoc();


if ($construccion) {
    $hoy = new DateTime();
    $inicioSemanaActual = $hoy->modify("monday this week")->format("Y-m-d");

    if ($construccion["SemanaInicio"] !== $inicioSemanaActual) {
        $reset = $conn->prepare("UPDATE Construye SET HorasSemanales = 0, SemanaInicio = ? WHERE Cedula = ?");
        $reset->bind_param("si", $inicioSemanaActual, $ced);
        $reset->execute();
        $construccion["HorasSemanales"] = 0;
        $construccion["SemanaInicio"] = $inicioSemanaActual;
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["pronombres"])) {
    $pronombres = trim($_POST["pronombres"]);
    $update = $conn->prepare("UPDATE Persona SET Pronombres = ? WHERE Cedula = ?");
    $update->bind_param("si", $pronombres, $ced);
    $update->execute();
    $usuario['Pronombres'] = $pronombres;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["hor"])) {
    $horasNew = (int) $_POST["hor"];
    if ($horasNew < 0) $horasNew = 0;

    if ($construccion) {
        $horasTotales = $construccion["HorasTotales"];
        $horasSemanales = $construccion["HorasSemanales"];
        $semanaInicio = $construccion["SemanaInicio"];

        if ($horasSemanales + $horasNew > 168) {
            $error = "⚠ No puedes registrar más de 168 horas en una semana.";
        } else {
            $horasTotales += $horasNew;
            $horasSemanales += $horasNew;

            $update = $conn->prepare("UPDATE Construye SET HorasTotales=?, HorasSemanales=? WHERE Cedula=?");
            $update->bind_param("iii", $horasTotales, $horasSemanales, $ced);
            $update->execute();

            $construccion["HorasTotales"] = $horasTotales;
            $construccion["HorasSemanales"] = $horasSemanales;
        }
    } else {
        $error = "❌ No tienes una unidad habitacional asignada en Construye.";
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["foto"])) {
    $dir = "uploads/";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $nombreArchivo = $ced . "_" . basename($_FILES["foto"]["name"]);
    $rutaDestino = $dir . $nombreArchivo;
    $tipo = strtolower(pathinfo($rutaDestino, PATHINFO_EXTENSION));

    if ($_FILES["foto"]["size"] > 2*1024*1024) {
        $error = "El archivo es demasiado grande (máx. 2MB).";
    } elseif (!in_array($tipo, ["jpg","jpeg","png","gif"])) {
        $error = "Solo se permiten JPG, JPEG, PNG o GIF.";
    } else {
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaDestino)) {
            $update = $conn->prepare("UPDATE Persona SET FotoPerfil = ? WHERE Cedula = ?");
            $update->bind_param("si", $rutaDestino, $ced);
            $update->execute();
            $usuario['FotoPerfil'] = $rutaDestino;
        } else {
            $error = "Error al subir la imagen.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop</title>
    <link rel="stylesheet" href="usuario.css" />
</head>
<style>

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: #f4f6f9;
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
}

nav {
    background: #2c3e50;
    padding: 10px 0;
    width: 100%;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
    position: sticky;
    top: 0;
    z-index: 10;
}

#Navegador {
    display: flex;
    justify-content: center;
    gap: 20px;
}

#Navegador a img {
    transition: transform 0.3s, filter 0.3s;
    border-radius: 50%;
    padding: 5px;
    background: #fff;
}

#Navegador a img:hover {
    transform: scale(1.15);
    filter: brightness(1.1);
}

#Logo {
    margin: 30px 0 10px;
}

#Logo img {
    border-radius: 20px;
    box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
    background: white;
    padding: 15px;
}

h1 {
    margin: 10px 0 25px;
    color: #2c3e50;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.2);
}

#fotoperfil {
    background: #fff;
    padding: 25px 35px;
    border-radius: 15px;
    box-shadow: 0px 6px 15px rgba(0,0,0,0.15);
    max-width: 500px;
    width: 100%;
    text-align: center;
}

#fotoperfil img {
    border-radius: 50%;
    margin-bottom: 15px;
    border: 3px solid #2c3e50;
}

#fotoperfil p {
    margin: 8px 0;
    font-size: 16px;
}

#fotoperfil b {
    color: #2c3e50;
}

form {
    margin-top: 15px;
    text-align: left;
}

label {
    font-weight: bold;
    display: block;
    margin-bottom: 6px;
    color: #34495e;
}

input[type="text"],
input[type="number"],
input[type="file"] {
    width: 100%;
    padding: 8px 10px;
    border: 1.5px solid #ccc;
    border-radius: 8px;
    outline: none;
    margin-bottom: 12px;
    transition: border-color 0.3s;
}

input:focus {
    border-color: #3498db;
}

button {
    background: #3498db;
    color: #fff;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s, transform 0.2s;
}

button:hover {
    background: #2c3e50;
    transform: scale(1.05);
}

h3 {
    margin-top: 25px;
    color: #2c3e50;
    border-left: 5px solid #3498db;
    padding-left: 8px;
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

<h1>USUARIO</h1>
<div id="fotoperfil">
    <img src="<?php echo htmlspecialchars($usuario['FotoPerfil']); ?>" height="150px" alt="Foto de perfil">

    <p><b>Nombre:</b> <?php echo htmlspecialchars($usuario['Nombre']); ?></p>
    <p><b>Apellido:</b> <?php echo htmlspecialchars($usuario['Apellido']); ?></p>
    <p><b>Edad:</b> <?php echo htmlspecialchars($usuario['Edad']); ?></p>
    <p><b>Pronombres:</b> 
        <?php echo $usuario['Pronombres'] !== '' ? htmlspecialchars($usuario['Pronombres']) : "No definidos"; ?>
    </p>


    <form method="POST" action="">
        <label for="pronombres">Editar pronombres:</label>
        <input type="text" id="pronombres" name="pronombres" placeholder="Ej: él/ella/elle" required>
        <button type="submit">Guardar</button>
    </form>


    <h3>Cambiar foto de perfil</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="foto" accept="image/*" required>
        <button type="submit">Subir</button>
    </form>


    <h3>Horas trabajadas</h3>
    <?php if ($construccion): ?>
        <p><b>Totales:</b> <?php echo (int)$construccion['HorasTotales']; ?> horas</p>
        <p><b>Semanales:</b> <?php echo (int)$construccion['HorasSemanales']; ?> horas (máx. 168)</p>

        <form method="POST" action="">
            <label for="hor">Ingresar horas trabajadas</label>
            <input type="number" id="hor" name="hor" min="0" max="24" required>
            <button type="submit" id="button">Guardar</button>
        </form>
    <?php else: ?>
        <p style="color:red;">❌ No tienes registros en la construcción.</p>
    <?php endif; ?>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
</div>
</body>
</html>
