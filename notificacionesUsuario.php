<?php
session_start();

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['Cedula'])) {
    header("Location: login.php");
    exit();
}

$cedula = $_SESSION['Cedula'];

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop2");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

// --- Enviar un mensaje nuevo ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['mensaje'])) {
    $mensaje = trim($_POST['mensaje']);
    if ($mensaje !== "") {
        $stmt = $conn->prepare("INSERT INTO Mensajes (Mensaje, Cedula) VALUES (?, ?)");
        $stmt->bind_param("si", $mensaje, $cedula);
        $stmt->execute();
        $stmt->close();
    }
}

// --- Traer mensajes del usuario ---
$stmt = $conn->prepare("
    SELECT Mensaje, Respuesta, Archivado 
    FROM Mensajes 
    WHERE Cedula = ? 
    ORDER BY idMensaje DESC
");
$stmt->bind_param("i", $cedula);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Mensajes - MyCoop</title>
<link rel="stylesheet" href="Style.css">
<style>
table { border-collapse: collapse; width: 90%; margin-bottom: 20px; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background-color: #eee; }
textarea { width: 100%; height: 60px; }
button { padding: 5px 10px; margin-top: 5px; }
.archivado { background-color: #f9f9f9; }
</style>
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
    </div>
</nav>

<div id="Logo">
    <img src="logoMyCoop.png" height="200px" alt="Logo MyCoop">
</div>

<h1>Mis Mensajes</h1>

<h2>Enviar un nuevo mensaje</h2>
<form method="POST">
    <textarea name="mensaje" placeholder="Escribe tu mensaje aquí..." required></textarea><br>
    <button type="submit">Enviar</button>
</form>

<h2>Historial de mensajes</h2>
<?php if ($result->num_rows > 0): ?>
<table>
<tr>
    <th>Mensaje</th>
    <th>Respuesta del Administrador</th>
    <th>Estado</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr class="<?= $row['Archivado'] ? 'archivado' : '' ?>">
    <td><?= htmlspecialchars($row['Mensaje']) ?></td>
    <td><?= $row['Respuesta'] !== null ? htmlspecialchars($row['Respuesta']) : "Sin respuesta aún" ?></td>
    <td><?= $row['Archivado'] ? 'Archivado' : 'Pendiente' ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p>No has enviado mensajes aún.</p>
<?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
