<?php
session_start();


if (!isset($_SESSION["Rol"]) || $_SESSION["Rol"] !== "administrador") {
    die("Acceso denegado. Solo administradores pueden entrar aquí.");
}


$host = "localhost";
$user = "root";
$pass = "equipoinfrog";
$db   = "proyect_database_mycoop6"; 

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}


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
    <link rel="stylesheet" href="Style.css">
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    background: #f4f6f9;
    color: #333;
}

h1 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 30px;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.2);
}

h2 {
    color: #34495e;
    margin-top: 40px;
    border-left: 6px solid #3498db;
    padding-left: 10px;
}


table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0px 6px 15px rgba(0,0,0,0.1);
}

th, td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

th {
    background: #2c3e50;
    color: #fff;
    text-transform: uppercase;
    font-size: 14px;
}

tr:hover {
    background: #f0f8ff;
}


a {
    text-decoration: none;
    font-weight: bold;
    padding: 6px 12px;
    border-radius: 6px;
    transition: 0.3s;
}

a[href*="aprobar"] {
    background: #27ae60;
    color: #fff !important;
}

a[href*="aprobar"]:hover {
    background: #1e8449;
    transform: scale(1.05);
}


select {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #bbb;
    font-size: 14px;
    outline: none;
    cursor: pointer;
}

button {
    background: #3498db;
    color: #fff;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
}

button:hover {
    background: #2c3e50;
    transform: scale(1.05);
}


p {
    font-style: italic;
    color: #555;
    margin-top: 10px;
}
</style>
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
