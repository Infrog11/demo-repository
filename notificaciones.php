<?php
session_start();

if (!isset($_SESSION["Rol"]) || $_SESSION["Rol"] !== "administrador") {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['idMensaje'], $_POST['respuesta'])) {
    $idMensaje = intval($_POST['idMensaje']);
    $respuesta = trim($_POST['respuesta']);

    $stmt = $conn->prepare("UPDATE Mensajes SET Respuesta = ?, Archivado = 1 WHERE idMensaje = ?");
    $stmt->bind_param("si", $respuesta, $idMensaje);
    $stmt->execute();
    $stmt->close();
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cedulaAprobar'])) {
    $cedula = intval($_POST['cedulaAprobar']);
    $stmt = $conn->prepare("UPDATE Persona SET Aceptado = 1 WHERE Cedula = ?");
    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    $stmt->close();
}


$result = $conn->query("
    SELECT m.idMensaje, m.Mensaje, p.Nombre, p.Apellido
    FROM Mensajes m
    JOIN Persona p ON m.Cedula = p.Cedula
    WHERE m.Archivado = 0
    ORDER BY m.idMensaje ASC
");


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
</head>
<style>
    body {
    font-family: "Segoe UI", Arial, sans-serif;
    margin: 20px;
    background: #f4f6f9;
    color: #333;
}

h1, h2 {
    color: #2c3e50;
    margin-bottom: 15px;
}

table {
    border-collapse: collapse;
    width: 95%;
    margin: 20px auto;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
}

th, td {
    border: 1px solid #eee;
    padding: 12px;
    text-align: left;
}

th {
    background: #2c3e50;
    color: #fff;
    font-size: 14px;
    text-transform: uppercase;
}

tr:nth-child(even) {
    background: #f9fbfc;
}

textarea {
    width: 100%;
    height: 70px;
    border-radius: 6px;
    border: 1px solid #ccc;
    padding: 8px;
    transition: border 0.3s;
}

textarea:focus {
    border-color: #3498db;
    outline: none;
}

button {
    background: #3498db;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
}

button:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.archivado {
    background-color: #f0f0f0;
    font-style: italic;
}
</style>
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
