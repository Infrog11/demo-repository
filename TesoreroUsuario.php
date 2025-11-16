<?php
// =============================
//  Conexión DB
// =============================
$host = "localhost";   
$user = "root";        
$pass = "equipoinfrog";          
$db   = "proyect_database_mycoop6"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// =============================
//  Obtener configuración del usuario
// =============================
session_start();

if (!isset($_SESSION["Cedula"])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

$ced = $_SESSION["Cedula"];

$stmt = $conn->prepare("
    SELECT font_size, theme, icons
    FROM ConfiguracionUsuario
    WHERE Cedula = ?
");
$stmt->bind_param("i", $ced);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $cfg = $res->fetch_assoc();

    // Corrección: evitar valores inválidos
    if (!isset($cfg["icons"]) || ($cfg["icons"] != "icons" && $cfg["icons"] != "words")) {
        $cfg["icons"] = "icons";
    }

} else {
    $cfg = [
        "font_size" => 3,
        "theme"     => "light",
        "icons"     => "icons"
    ];
}

// Aplicar configuración
$fontSize = intval($cfg["font_size"]) * 4 + 12;
$theme = $cfg["theme"];
$icons = $cfg["icons"];

// Carpeta de iconos (si luego quieres cambiar iconos por tema)
$iconFolder = ($icons === "words") ? "icons_white" : "icons";

// Colores según tema
if ($theme === "dark") {
    $bodyBg = "#1a1f36";
    $bodyColor = "#ffffff";
    $tableBg = "#2c3e50";
    $tableText = "#ffffff";
    $tableAlt = "#243447";
    $hoverRow = "#34495e";
    $navBg = "#0f1626";
} else {
    $bodyBg = "#f5f7fa";
    $bodyColor = "#2c3e50";
    $tableBg = "#ffffff";
    $tableText = "#2c3e50";
    $tableAlt = "#f9f9f9";
    $hoverRow = "#eaf2f8";
    $navBg = "#2c3e50";
}

// =============================
//  Saldo del fondo
// =============================
$result = $conn->query("SELECT SUM(Monto) AS saldo FROM FondoMonetario");
$row = $result->fetch_assoc();
$saldo = $row["saldo"] ?? 0;

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Movimientos del Fondo</title>

<style>
    body {
        font-family: "Segoe UI", Arial, sans-serif;
        margin: 20px;
        background: <?= $bodyBg ?>;
        color: <?= $bodyColor ?>;
        font-size: <?= $fontSize ?>px;
    }

    table {
        border-collapse: collapse;
        width: 90%;
        background: <?= $tableBg ?>;
        color: <?= $tableText ?>;
        margin-top: 20px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0px 3px 6px rgba(0,0,0,0.3);
    }

    th, td {
        padding: 12px 15px;
        text-align: center;
        border-bottom: 1px solid #555;
    }

    th {
        background: <?= $navBg ?>;
        color: white;
        text-transform: uppercase;
    }

    tr:nth-child(even) {
        background: <?= $tableAlt ?>;
    }

    tr:hover {
        background: <?= $hoverRow ?>;
    }

    td:nth-child(2) {
        font-weight: bold;
        color: #27ae60;
    }

    /* NAV FIXEADO + REDUCIDO */
    nav {
        background: <?= $navBg ?>;
        padding: 6px;
        width: 100%;
        box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
        position: sticky;
        top: 0;
        z-index: 10;
        margin-bottom: 20px;
    }

    #Navegador {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap; /* evita desbordes */
        gap: 12px;
    }

    #Navegador img {
        height: 45px; /* iconos reducidos */
        padding: 3px;
        border-radius: 50%;
        background: white;
        transition: transform 0.2s;
    }

    #Navegador img:hover {
        transform: scale(1.10);
    }

    #Navegador a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        font-size: <?= $fontSize ?>px;
    }
</style>
</head>

<nav>
    <div id="Navegador">

        <?php if ($icons === "icons"): ?>
            <!-- MODO ICONOS -->
            <a href="usuarioUsuario.php"><img src="iconoUsuario.png"></a>
            <a href="fechasUsuarios.php"><img src="iconoCalendario.png"></a>
            <a href="comunicacionUsuarios.php"><img src="iconoComunicacion.png"></a>
            <a href="archivoUsuarios.php"><img src="iconoDocumentos.png"></a>
            <a href="foroUsuarios.php"><img src="redes-sociales.png"></a>
            <a href="configuracionUsuarios.php"><img src="iconoConfiguracion.png"></a>
            <a href="notificacionesUsuario.php"><img src="iconoNotificacion.png"></a>
            <a href="inicioUsuario.php"><img src="Tesorero.png"></a>

        <?php else: ?>
            <!-- MODO PALABRAS -->
            <a href="usuarioUsuario.php">Usuario</a>
            <a href="fechasUsuarios.php">Calendario</a>
            <a href="comunicacionUsuarios.php">Comunicacion</a>
            <a href="archivoUsuarios.php">Archivos</a>
            <a href="foroUsuarios.php">Foros</a>
            <a href="configuracionUsuarios.php">Configuracion</a>
            <a href="notificacionesUsuario.php">Notificaciones</a>
            <a href="inicioUsuario.php">Novedades</a>
        <?php endif; ?>

    </div>
</nav>

<body>

<h2>Consulta de Movimientos</h2>

<h3>Saldo Actual: $<?= number_format($saldo, 2) ?></h3>

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
        echo "
        <tr>
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
