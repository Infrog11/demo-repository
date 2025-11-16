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


// =====================================================================
// 1) DATOS DEL USUARIO
// =====================================================================

$stmt = $conn->prepare("
    SELECT Nombre, Apellido, edad AS Edad,
        COALESCE(Pronombres, '') AS Pronombres,
        COALESCE(FotoPerfil, 'DefaultPerfile.png') AS FotoPerfil
    FROM Persona WHERE Cedula = ?
");
$stmt->bind_param("i", $ced);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();


// =====================================================================
// 2) CONFIGURACIONES DEL USUARIO
// =====================================================================

$stmtConf = $conn->prepare("
    SELECT font_size, theme, icons 
    FROM configuracionUsuario 
    WHERE Cedula = ?
");
$stmtConf->bind_param("i", $ced);
$stmtConf->execute();
$config = $stmtConf->get_result()->fetch_assoc();

// Valores por defecto si no hay configuración guardada
$fontSize = isset($config["font_size"]) ? (int)$config["font_size"] : 3;
$theme = isset($config["theme"]) ? $config["theme"] : "light";      // light / dark
$iconsMode = isset($config["icons"]) ? $config["icons"] : "icons";  // icons / words


// =====================================================================
// 3) DATOS DE CONSTRUCCIÓN
// =====================================================================

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
        $reset = $conn->prepare("
            UPDATE Construye 
            SET HorasSemanales = 0, SemanaInicio = ? 
            WHERE Cedula = ?
        ");
        $reset->bind_param("si", $inicioSemanaActual, $ced);
        $reset->execute();

        $construccion["HorasSemanales"] = 0;
        $construccion["SemanaInicio"] = $inicioSemanaActual;
    }
}


// =====================================================================
// 4) ACTUALIZAR PRONOMBRES
// =====================================================================

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["pronombres"])) {
    $pronombres = trim($_POST["pronombres"]);
    $update = $conn->prepare("UPDATE Persona SET Pronombres = ? WHERE Cedula = ?");
    $update->bind_param("si", $pronombres, $ced);
    $update->execute();

    $usuario['Pronombres'] = $pronombres;
}


// =====================================================================
// 5) REGISTRO DE HORAS
// =====================================================================

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["hor"])) {
    $horasNew = (int) $_POST["hor"];
    if ($horasNew < 0) $horasNew = 0;

    if ($construccion) {
        $horasTotales = $construccion["HorasTotales"];
        $horasSemanales = $construccion["HorasSemanales"];

        if ($horasSemanales + $horasNew > 168) {
            $error = "⚠ No puedes registrar más de 168 horas en una semana.";
        } else {
            $horasTotales += $horasNew;
            $horasSemanales += $horasNew;

            $update = $conn->prepare("
                UPDATE Construye 
                SET HorasTotales=?, HorasSemanales=? 
                WHERE Cedula=?
            ");
            $update->bind_param("iii", $horasTotales, $horasSemanales, $ced);
            $update->execute();

            $construccion["HorasTotales"] = $horasTotales;
            $construccion["HorasSemanales"] = $horasSemanales;
        }
    } else {
        $error = "❌ No tienes una unidad habitacional asignada en Construye.";
    }
}


// =====================================================================
// 6) SUBIR FOTO
// =====================================================================

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["foto"])) {
    $dir = "uploads/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);

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

<style>
body {
    font-family: Arial, sans-serif;
    margin: 0px;
    padding: 0px;

    font-size: <?= $fontSize * 4 ?>px;

    <?php if ($theme === "dark"): ?>
        background: #1e1e1e;
        color: #ffffff;
    <?php else: ?>
        background: #f4f6f9;
        color: #333;
    <?php endif; ?>

    display: flex;
    flex-direction: column;
    align-items: center;
}

nav {
    <?php if ($theme === "dark"): ?>
        background: #111;
    <?php else: ?>
        background: #2c3e50;
    <?php endif; ?>
    padding: 10px 0;
    width: 100%;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
}

#Navegador {
    display: flex;
    justify-content: center;
    gap: 20px;
}

#Navegador a {
    text-decoration: none;
    color: inherit;
    font-weight: bold;
}

#Navegador a img {
    transition: transform 0.3s, filter 0.3s;
    border-radius: 50%;
    padding: 5px;
    background: #fff;
}

#Logo {
    position: absolute;
    top: 7px;
    left: 10px;
}

#Logo img {
    height: 120px;
    border-radius: 20px;
    box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
    background: white;
    padding: 15px;
}

#fotoperfil {
    background: #fff;
    padding: 25px 35px;
    border-radius: 15px;
    margin-top: 20px;
    max-width: 500px;
    width: 100%;
    text-align: center;

    <?php if ($theme === "dark"): ?>
        background: #222;
        color: #fff;
    <?php endif; ?>
}

#fotoperfil img {
    border-radius: 50%;
    margin-bottom: 15px;
    border: 3px solid #2c3e50;
}
</style>

</head>
<body>

<!-- LOGO -->
<div id="Logo">
    <img src="logoMyCoop.png" alt="Logo MyCoop">
</div>

<!-- MENÚ ADAPTADO -->
<nav>
    <div id="Navegador">

        <?php if ($config["icons"] === "icons"): ?>
            <a href="inicioTesorero.php"><img src="anuncios.png" height="70px"></a>
            <a href="fechasTesorero.php"><img src="iconoCalendario.png"height="70px"></a>
            <a href="comunicacionTesorero.php"><img src="iconoComunicacion.png"height="70px"></a>
            <a href="archivoTesorero.php"><img src="iconoDocumentos.png"height="70px"></a>
            <a href="foroTesorero.php"><img src="redes-sociales.png"height="70px"></a>
            <a href="configuracionTesorero.php"><img src="iconoConfiguracion.png"height="70px"></a>
            <a href="notificacionesTesorero.php"><img src="iconoNotificacion.png"height="70px"></a>
            <a href="SeccionTesorero.php"><img src="Tesorero.png"height="70px"></a>

        <?php else: ?>
            <a href="inicioTesorero.php">Novedades</a>
            <a href="fechasTesorero.php">Calendario</a>
            <a href="comunicacionTesorero.php">Comunicación</a>
            <a href="archivoTesorero.php">Archivos</a>
            <a href="foroTesorero.php">Foro</a>
            <a href="configuracionTesorero.php">Configuración</a>
            <a href="notificacionesTesorero.php">Notificaciones</a>
            <a href="seccionTesorero.php">Tesorería</a>
        <?php endif; ?>

    </div>
</nav>


<h1>USUARIO</h1>

<div id="fotoperfil">
    <img src="<?= htmlspecialchars($usuario['FotoPerfil']); ?>" height="150">

    <p><b>Nombre:</b> <?= htmlspecialchars($usuario['Nombre']) ?></p>
    <p><b>Apellido:</b> <?= htmlspecialchars($usuario['Apellido']) ?></p>
    <p><b>Edad:</b> <?= htmlspecialchars($usuario['Edad']) ?></p>
    <p><b>Pronombres:</b> <?= $usuario['Pronombres'] !== '' ? htmlspecialchars($usuario['Pronombres']) : "No definidos" ?></p>

    <form method="POST">
        <label for="pronombres">Editar pronombres:</label>
        <input type="text" id="pronombres" name="pronombres" required>
        <button type="submit">Guardar</button>
    </form>

    <h3>Cambiar foto de perfil</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="foto" required>
        <button type="submit">Subir</button>
    </form>

    <h3>Horas trabajadas</h3>

    <?php if ($construccion): ?>
        <p><b>Totales:</b> <?= (int)$construccion['HorasTotales'] ?> horas</p>
        <p><b>Semanales:</b> <?= (int)$construccion['HorasSemanales'] ?> horas (máx. 168)</p>

        <form method="POST">
            <label for="hor">Ingresar horas trabajadas</label>
            <input type="number" id="hor" name="hor" min="0" max="24" required>
            <button type="submit">Guardar</button>
        </form>

    <?php else: ?>
        <p style="color:red;">❌ No tienes registros en construcción.</p>
    <?php endif; ?>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
</div>

</body>
</html>
