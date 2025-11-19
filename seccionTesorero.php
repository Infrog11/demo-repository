<?php
session_start();

if (!isset($_SESSION['Cedula'])) {
    header("Location: inSesion.php");
    exit();
}

$cedula = $_SESSION['Cedula'];

$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);


$stmtConfig = $conn->prepare("
    SELECT font_size, theme, icons 
    FROM ConfiguracionUsuario 
    WHERE Cedula = ?
");
$stmtConfig->bind_param("i", $cedula);
$stmtConfig->execute();
$config = $stmtConfig->get_result()->fetch_assoc();
$stmtConfig->close();

$font_size = $config['font_size'] ?? 3;
$theme     = $config['theme'] ?? "light";
$icons     = $config['icons'] ?? "icons";



if (isset($_POST['guardar'])) {
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'];
    $cedulaTesorero = $_POST['cedula'];

    $sql = "INSERT INTO FondoMonetario (Monto, DescripcionFondo, Cedula_Tesorero) 
            VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dsi", $monto, $descripcion, $cedulaTesorero);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Movimiento registrado con éxito.</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }

    $stmt->close();
}


$result = $conn->query("SELECT SUM(Monto) AS saldo FROM FondoMonetario");
$row = $result->fetch_assoc();
$saldo = $row['saldo'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Fondo de la Cooperativa</title>

<style>
    body {
        font-family: "Segoe UI", Arial, sans-serif;
        margin: 20px;
        font-size: <?= intval($font_size) * 4 ?>px;
        background: <?= $theme === "dark" ? "#1e1e1e" : "#f5f7fa" ?>;
        color: <?= $theme === "dark" ? "white" : "#2c3e50" ?>;
    }

    h2, h3 {
        color: <?= $theme === "dark" ? "#f1f1f1" : "#34495e" ?>;
    }

    table {
        border-collapse: collapse;
        width: 90%;
        margin-top: 15px;
        background: <?= $theme === "dark" ? "#2c2c2c" : "white" ?>;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0px 3px 6px rgba(0,0,0,0.15);
    }

    th {
        background: <?= $theme === "dark" ? "#000" : "#2c3e50" ?>;
        color: white;
        padding: 12px;
        text-transform: uppercase;
    }

    td {
        padding: 12px;
        border-bottom: 1px solid <?= $theme === "dark" ? "#444" : "#eee" ?>;
    }

    tr:nth-child(even) {
        background: <?= $theme === "dark" ? "#3a3a3a" : "#f9f9f9" ?>;
    }

    tr:hover {
        background: <?= $theme === "dark" ? "#555" : "#eaf2f8" ?>;
    }

    input, textarea {
        width: 300px;
        padding: 8px;
        font-size: <?= intval($font_size) * 4 ?>px;
        background: <?= $theme === "dark" ? "#333" : "white" ?>;
        color: <?= $theme === "dark" ? "white" : "black" ?>;
        border: 1px solid <?= $theme === "dark" ? "#666" : "#aaa" ?>;
    }

    button {
        padding: 10px 18px;
        font-size: <?= intval($font_size) * 4 ?>px;
        cursor: pointer;
        border: none;
        border-radius: 5px;
        background: <?= $theme === "dark" ? "#444" : "#2c3e50" ?>;
        color: white;
    }


    <?php if ($icons === "words"): ?>
        .icon-label { display: inline; }
        .icon-img { display: none; }
        nav a { color: white; font-weight: bold; text-decoration:none; padding:10px; }
    <?php else: ?>
        .icon-label { display: none; }
        .icon-img { display: inline; }
    <?php endif; ?>


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
</style>

</head>
<body>

<nav>
    <div id="Navegador">

        <?php if ($config['icons'] === "icons"): ?>
            <a href="usuarioTesorero.php"><img src="iconoUsuario.png" height="70px"></a>
            <a href="fechasTesorero.php"><img src="iconoCalendario.png"height="70px"></a>
            <a href="comunicacionTesorero.php"><img src="iconoComunicacion.png"height="70px"></a>
            <a href="archivoTesorero.php"><img src="iconoDocumentos.png"height="70px"></a>
            <a href="foroTesorero.php"><img src="redes-sociales.png"height="70px"></a>
            <a href="configuracionTesorero.php"><img src="iconoConfiguracion.png"height="70px"></a>
            <a href="notificacionesTesorero.php"><img src="iconoNotificacion.png"height="70px"></a>
            <a href="inicioTesorero.php"><img src="anuncios.png" height="70px"></a>

        <?php else: ?>
            <a href="usuarioTesorero.php">Novedades</a>
            <a href="fechasTesorero.php">Calendario</a>
            <a href="comunicacionTesorero.php">Comunicación</a>
            <a href="archivoTesorero.php">Archivos</a>
            <a href="foroTesorero.php">Foro</a>
            <a href="configuracionTesorero.php">Configuración</a>
            <a href="notificacionesTesorero.php">Notificaciones</a>
            <a href="inicioTesorero.php">Notificaciones</a>
        <?php endif; ?>

    </div>
</nav>

<h2>Administración del Fondo</h2>

<h3>Saldo Actual: $<?= number_format($saldo, 2) ?></h3>

<h3>Registrar movimiento</h3>
<form method="POST">
    <label>Monto (positivo = ingreso, negativo = gasto):</label><br>
    <input type="number" step="0.01" name="monto" required><br><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion" required></textarea><br><br>

    <label>Cédula del Tesorero:</label><br>
    <input type="number" name="cedula" required><br><br>

    <button type="submit" name="guardar">Guardar</button>
</form>

<h3>Historial de movimientos</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Monto</th>
        <th>Descripción</th>
        <th>Cédula</th>
    </tr>

    <?php
    $movs = $conn->query("SELECT * FROM FondoMonetario ORDER BY IdFondo DESC");
    while ($fila = $movs->fetch_assoc()) {
        echo "<tr>
                <td>{$fila['IdFondo']}</td>
                <td>{$fila['Monto']}</td>
                <td>{$fila['DescripcionFondo']}</td>
                <td>{$fila['Cedula_Tesorero']}</td>
              </tr>";
    }
    ?>
</table>

</body>
</html>

<?php $conn->close(); ?>
