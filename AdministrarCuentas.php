<?php
session_start();
if (!isset($_SESSION['Cedula'])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

$cedula = $_SESSION['Cedula'];

// --- Conexión a la BD ---
$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

// --- Obtener configuración del usuario ---
$stmtCfg = $conn->prepare("SELECT font_size, theme FROM ConfiguracionUsuario WHERE Cedula = ?");
$stmtCfg->bind_param("i", $cedula);
$stmtCfg->execute();
$resCfg = $stmtCfg->get_result();

if ($resCfg->num_rows > 0) {
    $cfg = $resCfg->fetch_assoc();
} else {
    $cfg = ["font_size" => 3, "theme" => "light"];
}

$fontSize = intval($cfg["font_size"]) * 4 + 12;
$theme = $cfg["theme"];

if ($theme === "dark") {
    $bodyBg = "#1a1f36";
    $textColor = "#ffffff";
    $boxBg = "#2c3e50";
    $hoverRow = "#34495e";
    $btnBorrar = "#e74c3c";
    $btnBorrarHover = "#c0392b";
    $btnBloquear = "#f39c12";
    $btnDesbloquear = "#27ae60";
    $tableBorder = "#555";
} else {
    $bodyBg = "#f4f6f9";
    $textColor = "#000000";
    $boxBg = "#ffffff";
    $hoverRow = "#eaf2f8";
    $btnBorrar = "#e74c3c";
    $btnBorrarHover = "#c0392b";
    $btnBloquear = "#f39c12";
    $btnDesbloquear = "#27ae60";
    $tableBorder = "#ccc";
}

// --- Obtener información del usuario ---
$stmt = $conn->prepare("
    SELECT Nombre, Apellido, edad AS Edad,
        COALESCE(Pronombres, '') AS Pronombres,
        COALESCE(FotoPerfil, 'DefaultPerfile.png') AS FotoPerfil
    FROM Persona WHERE Cedula = ?
");
$stmt->bind_param("i", $cedula);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$nombreCompleto = $usuario['Nombre'] . ' ' . $usuario['Apellido'];

// --- Consultar si el usuario está bloqueado ---
$stmt = $conn->prepare("SELECT Bloqueado FROM Persona WHERE Cedula = ?");
$stmt->bind_param("i", $cedula);
$stmt->execute();
$estadoUsuario = $stmt->get_result()->fetch_assoc();
$bloqueado = $estadoUsuario['Bloqueado'] ?? 0;

// --- Borrar hilo ---
if (isset($_POST['borrar'])) {
    $id = intval($_POST['idforo']);
    $conn->query("DELETE FROM Foros WHERE IdForo = $id");
}

// --- Bloquear usuario ---
if (isset($_POST['bloquear_usuario'])) {
    $cedulaBloquear = intval($_POST['cedula_usuario']);
    $conn->query("UPDATE Persona SET Bloqueado = 1 WHERE Cedula = $cedulaBloquear");
}

// --- Desbloquear usuario ---
if (isset($_POST['desbloquear_usuario'])) {
    $cedulaDesbloquear = intval($_POST['cedula_usuario']);
    $conn->query("UPDATE Persona SET Bloqueado = 0 WHERE Cedula = $cedulaDesbloquear");
}

// --- Crear nuevo hilo (solo si no está bloqueado) ---
if (!$bloqueado && isset($_POST['crear'])) {
    $titulo = $_POST['titulo'];
    if (!empty($titulo)) {
        $stmt = $conn->prepare("INSERT INTO Foros (Titulo, Autor) VALUES (?, ?)");
        $stmt->bind_param("ss", $titulo, $nombreCompleto);
        $stmt->execute();
    }
}

// --- Consultar todos los hilos ---
$foros = $conn->query("SELECT * FROM Foros ORDER BY Fecha DESC");

// --- Consultar todos los usuarios ---
$usuarios = $conn->query("SELECT Cedula, Nombre, Apellido, Bloqueado FROM Persona");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Foro de la Cooperativa</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: <?= $bodyBg ?>;
    color: <?= $textColor ?>;
    font-size: <?= $fontSize ?>px;
    margin: 20px;
}
.foro-item {
    margin-bottom: 10px;
    padding: 8px;
    border: 1px solid <?= $tableBorder ?>;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 600px;
    background: <?= $boxBg ?>;
}
.foro-item:hover { background: <?= $hoverRow ?>; }
.foro-info a { text-decoration: none; color: #0077cc; font-weight: bold; }
.foro-info a:hover { text-decoration: underline; }
button.borrar, button.bloquear, button.desbloquear {
    border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; color: white;
}
button.borrar { background-color: <?= $btnBorrar ?>; }
button.borrar:hover { background-color: <?= $btnBorrarHover ?>; }
button.bloquear { background-color: <?= $btnBloquear ?>; }
button.desbloquear { background-color: <?= $btnDesbloquear ?>; }
h2, h3 { max-width: 600px; }
table { border-collapse: collapse; max-width: 600px; margin-top: 10px; background: <?= $boxBg ?>; }
table th, table td { border: 1px solid <?= $tableBorder ?>; padding: 6px; text-align: center; }
table tr:hover { background: <?= $hoverRow ?>; }
</style>
</head>
<body>
<h2>Foro de la Cooperativa</h2>

<?php if ($bloqueado): ?>
<p style="color:red;">⚠️ Tu cuenta ha sido bloqueada. No puedes crear nuevos hilos ni responder.</p>
<?php endif; ?>

<h3>Hilos existentes</h3>
<?php while ($fila = $foros->fetch_assoc()) { ?>
    <div class="foro-item">
        <div class="foro-info">
            <a href="verforo.php?id=<?php echo $fila['IdForo']; ?>">
                <?php echo htmlspecialchars($fila['Titulo']); ?>
            </a>
            — por <?php echo htmlspecialchars($fila['Autor']); ?> 
            (<?php echo $fila['Fecha']; ?>)
        </div>
        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas borrar este foro?');">
            <input type="hidden" name="idforo" value="<?php echo $fila['IdForo']; ?>">
            <button type="submit" name="borrar" class="borrar">Borrar</button>
        </form>
    </div>
<?php } ?>

<hr>
<h3>Administrar usuarios</h3>
<table>
<tr><th>Cédula</th><th>Nombre</th><th>Estado</th><th>Acción</th></tr>
<?php while ($u = $usuarios->fetch_assoc()) { ?>
<tr>
    <td><?php echo $u['Cedula']; ?></td>
    <td><?php echo $u['Nombre'] . ' ' . $u['Apellido']; ?></td>
    <td><?php echo $u['Bloqueado'] ? '🔒 Bloqueado' : '🟢 Activo'; ?></td>
    <td>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="cedula_usuario" value="<?php echo $u['Cedula']; ?>">
            <?php if ($u['Bloqueado']) { ?>
                <button type="submit" name="desbloquear_usuario" class="desbloquear">Desbloquear</button>
            <?php } else { ?>
                <button type="submit" name="bloquear_usuario" class="bloquear">Bloquear</button>
            <?php } ?>
        </form>
    </td>
</tr>
<?php } ?>
</table>
</body>
</html>
