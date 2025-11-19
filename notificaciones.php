<?php
session_start();

if (!isset($_SESSION["Rol"]) || $_SESSION["Rol"] !== "administrador") {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

$ced = $_SESSION["Cedula"];


$config = [
    "font_size" => 16,
    "theme" => "light",
    "icons" => "icons"
];

$stmt = $conn->prepare("
    SELECT font_size, theme, icons
    FROM ConfiguracionUsuario
    WHERE Cedula = ?
");
$stmt->bind_param("i", $ced);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $config["font_size"] = intval($row["font_size"]);
    $config["theme"] = $row["theme"];
    $config["icons"] = $row["icons"];
}

$stmt->close();


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


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['idReporteRevisado'])) {
    $idRep = intval($_POST['idReporteRevisado']);
    $stmt = $conn->prepare("UPDATE ReportesForo SET Estado = 'Revisado' WHERE idReporte = ?");
    $stmt->bind_param("i", $idRep);
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

$reportesForo = $conn->query("
    SELECT r.idReporte, r.Motivo, r.FechaReporte, 
           COALESCE(r.Estado, 'Pendiente') AS Estado,
           f.IdForo, f.Titulo, p.Nombre, p.Apellido
    FROM ReportesForo r
    JOIN Foros f ON r.IdForo = f.IdForo
    JOIN Persona p ON r.CedulaReportante = p.Cedula
    ORDER BY r.FechaReporte DESC
");

$reportes = $conn->query("
    SELECT IdNotificacion, Mensaje, Fecha, Estado
    FROM Notificaciones
    WHERE Tipo = 'Reporte'
    ORDER BY Fecha DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de Administración - MyCoop</title>

<style>
:root {
    --font-size: <?= intval($config["font_size"]) * 4 ?>px;
    --bg: <?= $config["theme"] === "dark" ? "#1a1a1a" : "#f4f6f9" ?>;
    --text: <?= $config["theme"] === "dark" ? "#ffffff" : "#333333" ?>;
    --card-bg: <?= $config["theme"] === "dark" ? "#242424" : "#ffffff" ?>;
    --nav-bg: <?= $config["theme"] === "dark" ? "#111" : "#2c3e50" ?>;
    --icon-filter: <?= $config["theme"] === "dark" ? "invert(1)" : "invert(0)" ?>;
}

body {
    font-family: "Segoe UI", Arial, sans-serif;
    margin: 20px;
    background: var(--bg);
    color: var(--text);
    font-size: var(--font-size);
}

h1, h2 {
    color: var(--text);
}

table {
    border-collapse: collapse;
    width: 95%;
    margin: 20px auto;
    background: var(--card-bg);
    border-radius: 8px;
    overflow: hidden;
    font-size: var(--font-size);
}

th {
    background: #2c3e50;
    color: white;
}

tr:nth-child(even) {
    background: <?= $config["theme"] === "dark" ? "#333" : "#f9fbfc" ?>;
}

textarea {
    width: 100%;
    border-radius: 4px;
    font-size: var(--font-size);
}

button {
    background: #3498db;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
    font-size: var(--font-size);
}

nav {
    padding: 5px 0;
    width: 100%;
    position: sticky;
    top: 0;
    z-index: 20;
}

#Navegador {
    display: flex;
    justify-content: center;
    gap: 10px;
}

#Navegador a img {
    height: 50px;
    width: 50px;
    filter: var(--icon-filter);
    transition: 0.3s;
}

#Navegador a img:hover {
    transform: scale(1.1);
}
</style>

<nav>
    <div id="Navegador">

        <?php if ($config["icons"] == "icons"): ?>
            <!-- MODO ICONOS -->
            <a href="aprobarUsuarios.php"><img src="iconoAdministracion.png" height="70px"></a>
            <a href="usuario.php"><img src="iconoUsuario.png" height="70px"></a>
            <a href="fechas.php"><img src="iconoCalendario.png" height="70px"></a>
            <a href="comunicacion.php"><img src="iconoComunicacion.png" height="70px"></a>
            <a href="archivo.php"><img src="iconoDocumentos.png" height="70px"></a>
            <a href="Construccion.php"><img src="iconoConstruccion.png" height="70px"></a>
            <a href="foro.php"><img src="redes-sociales.png" height="70px"></a>
            <a href="configuracion.php"><img src="iconoConfiguracion.png" height="70px"></a>
            <a href="inicio.php"><img src="anuncios.png" height="70px"></a>
            <a href="TesoreroAdmin.php"><img src="Tesorero.png" height="70px"></a>

        <?php else: ?>
            <!-- MODO PALABRAS -->
            <a href="aprobarUsuarios.php">Administración</a>
            <a href="usuario.php">Usuario</a>
            <a href="fechas.php">Calendario</a>
            <a href="comunicacion.php">Comunicación</a>
            <a href="archivo.php">Archivos</a>
            <a href="Construccion.php">Construcción</a>
            <a href="foro.php">Foro</a>
            <a href="configuracion.php">Configuración</a>
            <a href="inicio.php"> Novedades</a>
            <a href="TesoreroAdmin.php">Tesorería</a>
        <?php endif; ?>

    </div>
</nav>
</head>
<body>

<h1>Panel de Administración</h1>

<!-- REPORTES -->
<h2>Reportes de Usuarios</h2>

<?php if ($reportes->num_rows > 0): ?>
<table>
<tr>
    <th>ID</th>
    <th>Mensaje</th>
    <th>Fecha</th>
    <th>Estado</th>
    <th>Acción</th>
</tr>
<?php while ($rep = $reportes->fetch_assoc()): ?>
<tr>
    <td><?= $rep['IdNotificacion'] ?></td>
    <td><?= htmlspecialchars($rep['Mensaje']) ?></td>
    <td><?= $rep['Fecha'] ?></td>
    <td><?= $rep['Estado'] ?></td>
    <td>
        <?php if ($rep['Estado'] !== 'Leído'): ?>
        <form method="POST">
            <input type="hidden" name="idNotificacionLeida" value="<?= $rep['IdNotificacion'] ?>">
            <button type="submit">Marcar leído</button>
        </form>
        <?php else: ?>✔<?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?><p>No hay reportes.</p><?php endif; ?>

<!-- REPORTES DE FORO -->
<h2>Reportes de Foros</h2>

<?php if ($reportesForo->num_rows > 0): ?>
<table>
<tr>
    <th>ID</th>
    <th>Foro</th>
    <th>Reportante</th>
    <th>Motivo</th>
    <th>Fecha</th>
    <th>Estado</th>
    <th>Acción</th>
</tr>

<?php while ($r = $reportesForo->fetch_assoc()): ?>
<tr>
    <td><?= $r['idReporte'] ?></td>
    <td><a href="verforoTesorero.php?id=<?= $r['IdForo'] ?>" target="_blank"><?= htmlspecialchars($r['Titulo']) ?></a></td>
    <td><?= htmlspecialchars($r['Nombre'] . " " . $r['Apellido']) ?></td>
    <td><?= htmlspecialchars($r['Motivo']) ?></td>
    <td><?= $r['FechaReporte'] ?></td>
    <td><?= $r['Estado'] ?></td>
    <td>
        <?php if ($r['Estado'] !== 'Revisado'): ?>
        <form method="POST">
            <input type="hidden" name="idReporteRevisado" value="<?= $r['idReporte'] ?>">
            <button type="submit">Marcar revisado</button>
        </form>
        <?php else: ?>✔<?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?><p>No hay reportes de foros.</p><?php endif; ?>

<!-- MENSAJES -->
<h2>Mensajes de Usuarios</h2>

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
            <textarea name="respuesta" required></textarea>
            <button type="submit">Enviar</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?><p>No hay mensajes.</p><?php endif; ?>

<!-- USUARIOS PENDIENTES -->
<h2>Usuarios pendientes de aprobación</h2>

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

<?php while($u = $pendientes->fetch_assoc()): ?>
<tr>
    <td><?= $u['Cedula'] ?></td>
    <td><?= htmlspecialchars($u['Nombre']) ?></td>
    <td><?= htmlspecialchars($u['Apellido']) ?></td>
    <td><?= $u['Edad'] ?></td>
    <td><?= htmlspecialchars($u['Comunicacion']) ?></td>
    <td>
        <form method="POST">
            <input type="hidden" name="cedulaAprobar" value="<?= $u['Cedula'] ?>">
            <button type="submit">Aprobar</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?><p>No hay usuarios pendientes.</p><?php endif; ?>

</body>
</html>

<?php $conn->close(); ?>
