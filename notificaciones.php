<?php
session_start();

// Solo administradores pueden acceder
if (!isset($_SESSION["Rol"]) || $_SESSION["Rol"] !== "administrador") {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop2");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

// --- Responder mensaje ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['idMensaje'], $_POST['respuesta'])) {
    $idMensaje = intval($_POST['idMensaje']);
    $respuesta = trim($_POST['respuesta']);

    $stmt = $conn->prepare("UPDATE Mensajes SET Respuesta = ?, Archivado = 1 WHERE idMensaje = ?");
    $stmt->bind_param("si", $respuesta, $idMensaje);
    $stmt->execute();
    $stmt->close();
}

// --- Aprobar usuario pendiente ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cedulaAprobar'])) {
    $cedula = intval($_POST['cedulaAprobar']);
    $stmt = $conn->prepare("UPDATE Persona SET Aceptado = 1 WHERE Cedula = ?");
    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    $stmt->close();
}

// --- Traer mensajes no archivados ---
$result = $conn->query("
    SELECT m.idMensaje, m.Mensaje, p.Nombre, p.Apellido
    FROM Mensajes m
    JOIN Persona p ON m.Cedula = p.Cedula
    WHERE m.Archivado = 0
    ORDER BY m.idMensaje ASC
");

// --- Traer usuarios pendientes ---
$pendientes = $conn->query("
    SELECT Cedula, Nombre, Apellido, Edad, Comunicacion
    FROM Persona
    WHERE Aceptado = 0
    ORDER BY Apellido, Nombre
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Bandeja de Mensajes y Usuarios Pendientes - Admin</title>
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
<h1>Bandeja de Mensajes - Administrador</h1>

<?php if ($result->num_rows > 0): ?>
<table>
<tr>
    <th>ID</th>
    <th>Usuario</th>
    <th>Mensaje</th>
    <th>Responder</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['idMensaje'] ?></td>
    <td><?= htmlspecialchars($row['Nombre'] . " " . $row['Apellido']) ?></td>
    <td><?= htmlspecialchars($row['Mensaje']) ?></td>
    <td>
        <form method="POST">
            <input type="hidden" name="idMensaje" value="<?= $row['idMensaje'] ?>">
            <textarea name="respuesta" placeholder="Escribe tu respuesta..." required></textarea>
            <button type="submit">Enviar respuesta</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p>No hay mensajes nuevos.</p>
<?php endif; ?>

<h2>Usuarios pendientes por aprobación</h2>
<?php if ($pendientes->num_rows > 0): ?>
<table>
<tr>
    <th>Cédula</th>
    <th>Nombre</th>
    <th>Apellido</th>
    <th>Edad</th>
    <th>Contacto</th>
    <th>Aprobar</th>
</tr>
<?php while($user = $pendientes->fetch_assoc()): ?>
<tr>
    <td><?= $user['Cedula'] ?></td>
    <td><?= htmlspecialchars($user['Nombre']) ?></td>
    <td><?= htmlspecialchars($user['Apellido']) ?></td>
    <td><?= $user['Edad'] ?></td>
    <td><?= htmlspecialchars($user['Comunicacion']) ?></td>
    <td>
        <form method="POST">
            <input type="hidden" name="cedulaAprobar" value="<?= $user['Cedula'] ?>">
            <button type="submit">Aprobar</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p>No hay usuarios pendientes por aprobar.</p>
<?php endif; ?>

<h2>Mensajes Archivados</h2>
<?php
$resultArch = $conn->query("
    SELECT m.idMensaje, m.Mensaje, m.Respuesta, p.Nombre, p.Apellido
    FROM Mensajes m
    JOIN Persona p ON m.Cedula = p.Cedula
    WHERE m.Archivado = 1
    ORDER BY m.idMensaje DESC
");
if ($resultArch->num_rows > 0):
?>
<table>
<tr>
    <th>ID</th>
    <th>Usuario</th>
    <th>Mensaje</th>
    <th>Respuesta</th>
</tr>
<?php while($row = $resultArch->fetch_assoc()): ?>
<tr>
    <td><?= $row['idMensaje'] ?></td>
    <td><?= htmlspecialchars($row['Nombre'] . " " . $row['Apellido']) ?></td>
    <td><?= htmlspecialchars($row['Mensaje']) ?></td>
    <td><?= htmlspecialchars($row['Respuesta']) ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p>No hay mensajes archivados.</p>
<?php endif; ?>

</body>
</html>

<?php $conn->close(); ?>
