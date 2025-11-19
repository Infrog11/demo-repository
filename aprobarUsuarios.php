<?php
session_start();

if (!isset($_SESSION["Rol"]) || $_SESSION["Rol"] !== "administrador") {
    die("Acceso denegado. Solo administradores pueden entrar aquí.");
}

$cedula = $_SESSION["Cedula"];

$host = "localhost";
$user = "root";
$pass = "equipoinfrog";
$db   = "proyect_database_mycoop6"; 

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}


$configResult = $conn->query("SELECT * FROM ConfiguracionUsuario WHERE Cedula = $cedula");

if ($configResult->num_rows > 0) {
    $config = $configResult->fetch_assoc();
} else {
    
    $config = [
        "font_size" => 3,
        "theme" => "light",
        "icons" => "icons"
    ];
}

$fontSize = intval($config["font_size"]) * 4 + 8;
$themeBg = ($config["theme"] == "dark") ? "#1a1f36" : "#f4f6f9";
$themeColor = ($config["theme"] == "dark") ? "#eee" : "#000";
$icons = $config["icons"];
$linkColor = ($config["theme"] == "dark") ? "#fff" : "#000";




if (isset($_GET["aprobar"])) {
    $cedulaAprobar = intval($_GET["aprobar"]);
    $stmt = $conn->prepare("UPDATE Persona SET Aceptado = 1 WHERE Cedula = ?");
    $stmt->bind_param("i", $cedulaAprobar);
    $stmt->execute();
    $stmt->close();
    echo "<p style='color:green;'>Usuario con cédula $cedulaAprobar aprobado ✅</p>";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cambiarRol"])) {
    $cedula = intval($_POST["cedula"]);
    $nuevoRol = $_POST["rol"];

    $stmt = $conn->prepare("UPDATE Persona SET Rol = ? WHERE Cedula = ?");
    $stmt->bind_param("si", $nuevoRol, $cedula);

    if ($stmt->execute()) {
        echo "<p style='color:blue;'>Rol actualizado para usuario con cédula $cedula ➝ $nuevoRol</p>";
    } else {
        echo "<p style='color:red;'>Error al asignar rol: " . $conn->error . "</p>";
    }
    $stmt->close();
}

$resultPendientes = $conn->query("SELECT Cedula, Nombre, Apellido, Comunicacion FROM Persona WHERE Aceptado = 0");
$resultAprobados = $conn->query("SELECT Cedula, Nombre, Apellido, Comunicacion, Rol FROM Persona WHERE Aceptado = 1");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Aprobación - MyCoop</title>
</head>

<style>
    body {
        background: <?= $themeBg ?>;
        color: <?= $themeColor ?>;
        font-size: <?= $fontSize ?>px;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
    }

    h1, h2 {
        color: <?= $themeColor ?>;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        background: <?= ($config["theme"] == "dark") ? "#2a314a" : "#fff" ?>;
        color: <?= $themeColor ?>;
        border-radius: 10px;
        overflow: hidden;
    }

    th {
        background: <?= ($config["theme"] == "dark") ? "#111726" : "#2c3e50" ?>;
        color: #fff;
    }

    tr:hover {
        background: <?= ($config["theme"] == "dark") ? "#3a4569" : "#f0f8ff" ?>;
    }

    a {
        color: <?= $linkColor ?>;
        text-decoration: none;
        font-weight: bold;
    }

    nav {
        background: <?= ($config["theme"] == "dark") ? "#111726" : "#2c3e50" ?>;
        padding: 10px 0;
        width: 100%;
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
        transition: transform 0.3s;
        border-radius: 50%;
        padding: 5px;
        background: #fff;
    }

    #Navegador a:hover img {
        transform: scale(1.15);
    }

</style>

<nav>
    <div id="Navegador">
        <?php if ($icons == "icons"): ?>
            <a href="inicio.php"><img src="anuncios.png" height="70px"></a>
            <a href="usuario.php"><img src="iconoUsuario.png" height="70px"></a>
            <a href="fechas.php"><img src="iconoCalendario.png" height="70px"></a>
            <a href="comunicacion.php"><img src="iconoComunicacion.png" height="70px"></a>
            <a href="archivo.php"><img src="iconoDocumentos.png" height="70px"></a>
            <a href="Construccion.php"><img src="iconoConstruccion.png" height="70px"></a>
            <a href="foro.php"><img src="redes-sociales.png" height="70px"></a>
            <a href="configuracion.php"><img src="iconoConfiguracion.png" height="70px"></a>
            <a href="notificaciones.php"><img src="iconoNotificacion.png" height="70px"></a>
            <a href="TesoreroAdmin.php"><img src="Tesorero.png" height="70px"></a>
        <?php else: ?>
            <a href="inicio.php">Novedades</a>
            <a href="usuario.php">Usuario</a>
            <a href="fechas.php">Calendario</a>
            <a href="comunicacion.php">Comunicación</a>
            <a href="archivo.php">Archivos</a>
            <a href="Construccion.php">Construcción</a>
            <a href="foro.php">Foro</a>
            <a href="configuracion.php">Configuración</a>
            <a href="notificaciones.php">Notificaciones</a>
            <a href="TesoreroAdmin.php">Tesorería</a>
        <?php endif; ?>
    </div>
</nav>

<body>

    <h1>Panel de Administración</h1>

    <h2>Usuarios Pendientes</h2>
    <?php if ($resultPendientes->num_rows > 0): ?>
        <table>
            <tr>
                <th>Cédula</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Contacto</th>
                <th>Acción</th>
            </tr>
            <?php while ($row = $resultPendientes->fetch_assoc()): ?>
                <tr>
                    <td><?= $row["Cedula"] ?></td>
                    <td><?= $row["Nombre"] ?></td>
                    <td><?= $row["Apellido"] ?></td>
                    <td><?= $row["Comunicacion"] ?></td>
                    <td><a href="aprobarUsuarios.php?aprobar=<?= $row["Cedula"] ?>">✔ Aprobar</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No hay usuarios pendientes 🎉</p>
    <?php endif; ?>

    <h2>Usuarios Aprobados</h2>
    <?php if ($resultAprobados->num_rows > 0): ?>
        <table>
            <tr>
                <th>Cédula</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Contacto</th>
                <th>Rol Actual</th>
                <th>Asignar Rol</th>
            </tr>
            <?php while ($row = $resultAprobados->fetch_assoc()): ?>
                <tr>
                    <td><?= $row["Cedula"] ?></td>
                    <td><?= $row["Nombre"] ?></td>
                    <td><?= $row["Apellido"] ?></td>
                    <td><?= $row["Comunicacion"] ?></td>
                    <td><?= $row["Rol"] ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="cedula" value="<?= $row["Cedula"] ?>">
                            <select name="rol" required>
                                <option value="usuario" <?= $row["Rol"] === "usuario" ? "selected" : "" ?>>Usuario</option>
                                <option value="administrador" <?= $row["Rol"] === "administrador" ? "selected" : "" ?>>Administrador</option>
                                <option value="tesorero" <?= $row["Rol"] === "tesorero" ? "selected" : "" ?>>Tesorero</option>
                            </select>
                            <button type="submit" name="cambiarRol">Cambiar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No hay usuarios aprobados todavía.</p>
    <?php endif; ?>

</body>
</html>
