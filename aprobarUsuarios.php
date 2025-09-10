<?php
session_start();

// ✅ Solo deja entrar a administradores
if (!isset($_SESSION["Rol"]) || $_SESSION["Rol"] !== "administrador") {
    die("Acceso denegado. Solo administradores pueden entrar aquí.");
}

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "equipoinfrog";
$db   = "proyect_database_mycoop2"; 

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// ✅ Aprobar usuario
if (isset($_GET["aprobar"])) {
    $cedulaAprobar = intval($_GET["aprobar"]);
    $stmt = $conn->prepare("UPDATE Persona SET Aceptado = 1 WHERE Cedula = ?");
    $stmt->bind_param("i", $cedulaAprobar);
    $stmt->execute();
    $stmt->close();
    echo "<p style='color:green;'>Usuario con cédula $cedulaAprobar aprobado ✅</p>";
}

// ✅ Asignar rol
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

// ✅ Buscar usuarios pendientes
$resultPendientes = $conn->query("SELECT Cedula, Nombre, Apellido, Comunicacion FROM Persona WHERE Aceptado = 0");

// ✅ Buscar usuarios aprobados
$resultAprobados = $conn->query("SELECT Cedula, Nombre, Apellido, Comunicacion, Rol FROM Persona WHERE Aceptado = 1");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Aprobación - MyCoop</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <h1>Panel de Administración</h1>

    <h2>Usuarios Pendientes</h2>
    <?php if ($resultPendientes->num_rows > 0): ?>
        <table border="1" cellpadding="8">
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
                    <td>
                        <a href="aprobarUsuarios.php?aprobar=<?= $row["Cedula"] ?>" 
                           style="color: green; font-weight: bold;">✔ Aprobar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No hay usuarios pendientes 🎉</p>
    <?php endif; ?>

    <h2>Usuarios Aprobados</h2>
    <?php if ($resultAprobados->num_rows > 0): ?>
        <table border="1" cellpadding="8">
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
                        <form method="POST" style="display:inline;">
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
