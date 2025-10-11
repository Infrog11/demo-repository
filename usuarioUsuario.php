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
