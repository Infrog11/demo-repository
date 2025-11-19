<?php
session_start();
include "conexion.php";

if (!isset($_SESSION['Cedula'])) {
    header("Location: login.php");
    exit();
}

$ced = (int) $_SESSION['Cedula'];

$stmtCfg = $conn->prepare("SELECT font_size, theme, icons FROM configuracionUsuario WHERE Cedula = ?");
$stmtCfg->bind_param("i", $ced);
$stmtCfg->execute();
$config = $stmtCfg->get_result()->fetch_assoc();

$fontSize = isset($config['font_size']) ? (int)$config['font_size'] : 3;
$theme = isset($config['theme']) ? $config['theme'] : 'light'; 
$iconsMode = isset($config['icons']) ? $config['icons'] : 'icons'; 

if (isset($_GET['mes']) && isset($_GET['anio'])) {
    $mesActual = intval($_GET['mes']);
    $anioActual = intval($_GET['anio']);
} else {
    $mesActual = date("n");
    $anioActual = date("Y");
}

$mesSiguiente = $mesActual + 1;
$anioSiguiente = $anioActual;
if ($mesSiguiente > 12) {
    $mesSiguiente = 1;
    $anioSiguiente++;
}

$mesAnterior = $mesActual - 1;
$anioAnterior = $anioActual;
if ($mesAnterior < 1) {
    $mesAnterior = 12;
    $anioAnterior--;
}


$sql = "SELECT IdEvento, NombreEvento, FechaEvento, DescripcionEvento 
        FROM Eventos 
        WHERE MONTH(FechaEvento) = ? AND YEAR(FechaEvento) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $mesActual, $anioActual);
$stmt->execute();
$result = $stmt->get_result();

$eventos = [];
while ($row = $result->fetch_assoc()) {
    $eventos[] = $row;
}
$stmt->close();


function mostrarCalendario($mes, $anio, $eventos, $theme) {
    $primerDia = mktime(0,0,0,$mes,1,$anio);
    setlocale(LC_TIME, "es_ES.UTF-8");
    $nombreMes = strftime("%B", $primerDia);
    $diaSemana = date("N", $primerDia);
    $diasMes = date("t", $primerDia);

    $bgColor = $theme === 'dark' ? '#2c2c2c' : '#ffffff';
    $textColor = $theme === 'dark' ? '#ffffff' : '#333333';
    $borderColor = $theme === 'dark' ? '#555' : '#ccc';
    $eventBg = $theme === 'dark' ? '#007bff' : '#a0c4ff';
    $eventColor = '#fff';

    echo "<h2 style='text-align:center; color:$textColor;'>$nombreMes $anio</h2>";
    echo "<table class='calendario' style='background:$bgColor; color:$textColor; border:1px solid $borderColor;'>";
    echo "<tr>
            <th>Lun</th><th>Mar</th><th>Mié</th>
            <th>Jue</th><th>Vie</th><th>Sáb</th><th>Dom</th>
          </tr><tr>";

    if ($diaSemana > 1) {
        for ($i = 1; $i < $diaSemana; $i++) echo "<td></td>";
    }

    $contador = $diaSemana;
    for ($dia = 1; $dia <= $diasMes; $dia++) {
        $fechaActual = "$anio-" . str_pad($mes,2,"0",STR_PAD_LEFT) . "-" . str_pad($dia,2,"0",STR_PAD_LEFT);

        echo "<td style='vertical-align:top; padding:5px; border:1px solid $borderColor; height:110px; font-size:0.95rem;'>";
        echo "<strong style='display:block; margin-bottom:5px;'>$dia</strong>";

        foreach ($eventos as $evento) {
            if ($evento["FechaEvento"] === $fechaActual) {
                echo "<div style='background:$eventBg; color:$eventColor; padding:4px; margin:2px 0; border-radius:5px; font-size:0.85rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;'>
                        <strong>" . htmlspecialchars($evento["NombreEvento"]) . "</strong><br>
                        <small>" . htmlspecialchars($evento["DescripcionEvento"]) . "</small>
                      </div>";
            }
        }

        echo "</td>";

        if ($contador % 7 == 0) echo "</tr><tr>";
        $contador++;
    }

    echo "</tr></table><br><br>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MyCoop</title>
<style>
:root {
    --font-size: <?= $fontSize * 4 ?>px;
    --bg-color: #f4f6f9;
    --text-color: #333;
    --nav-bg: #2c3e50;
    --icon-bg: #fff;
    --icon-filter: invert(0);
    --main-bg: #fff;
}

<?php if($theme === "dark"): ?>
:root {
    --bg-color: #1a1a1a;
    --text-color: #fff;
    --nav-bg: #111;
    --icon-bg: #fff;
    --main-bg: #222;
}
<?php endif; ?>

body {
    font-family: Arial, sans-serif;
    margin:0;
    padding:0;
    background: var(--bg-color);
    color: var(--text-color);
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: var(--font-size);
    min-height: 100vh;
}

nav {
    background: var(--nav-bg);
    padding: 10px 0;
    width: 100%;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
    position: sticky;
    top: 0;
    z-index: 100;
}

#Navegador {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
}

#Navegador a {
    text-align: center;
    color: var(--text-color);
    font-weight: bold;
    font-size: 0.9rem;
    text-decoration: none;
}

#Navegador a img {
    height: 60px;
    width: 60px;
    object-fit: cover;
    border-radius: 50%;
    padding: 8px;
    background: var(--icon-bg);
    filter: var(--icon-filter);
    transition: transform 0.3s, filter 0.3s, background 0.3s;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.15);
}

#Navegador a img:hover {
    transform: scale(1.15);
    filter: brightness(1.1) var(--icon-filter);
}

main {
    margin: 30px auto;
    padding: 20px;
    max-width: 1000px;
    width: 95%;
    background: var(--main-bg);
    border-radius: 15px;
    box-shadow: 0px 6px 15px rgba(0,0,0,0.1);
}

table.calendario {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

table.calendario th, table.calendario td {
    padding: 10px;
    border: 1px solid #ccc;
    text-align: center;
    vertical-align: top;
    word-wrap: break-word;
}

table.calendario th {
    background: var(--nav-bg);
    color: var(--text-color);
    font-weight: bold;
}

button, .btn {
    background: #3498db;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s, transform 0.2s;
    text-decoration: none;
    display: inline-block;
}

button:hover, .btn:hover {
    background: #2c3e50;
    transform: scale(1.05);
}

.calendar-btn {
    background-color: #007bff;
    color: white;
    padding: 10px 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: 0.2s;
    margin: 5px;
}

.calendar-btn:hover {
    background-color: #0056b3;
}
</style>
</head>
<body>

<!-- NAV -->
<nav>
<div id="Navegador">
    <?php
    function menuItem($url, $img, $text, $iconsMode) {
        if ($iconsMode === "icons") {
            return "<a href='$url'><img src='$img' height='70'></a>";
        } else {
            return "<a href='$url'>$text</a>";
        }
    }

    echo menuItem("usuarioUsuario.php", "IconoUsuario.png", "Usuario", $iconsMode);
    echo menuItem("inicioUsuario.php", "anuncios.png", "Inicio", $iconsMode);
    echo menuItem("comunicacionUsuarios.php", "iconoComunicacion.png", "Comunicación", $iconsMode);
    echo menuItem("archivoUsuarios.php", "iconoDocumentos.png", "Archivos", $iconsMode);
    echo menuItem("foroUsuarios.php", "redes-sociales.png", "Foro", $iconsMode);
    echo menuItem("configuracionUsuarios.php", "iconoConfiguracion.png", "Configuración", $iconsMode);
    echo menuItem("notificacionesUsuario.php", "iconoNotificacion.png", "Notificaciones", $iconsMode);
    echo menuItem("TesoreroUsuario.php", "Tesorero.png", "Tesorero", $iconsMode);
    ?>
</div>
</nav>

<main>
<?php
echo "<div style='text-align:center; margin:20px;'>
        <a href='?mes=$mesAnterior&anio=$anioAnterior'><button>&laquo; Mes Anterior</button></a>
        <a href='?mes=$mesSiguiente&anio=$anioSiguiente'><button>Mes Siguiente &raquo;</button></a>
      </div>";

mostrarCalendario($mesActual, $anioActual, $eventos, $theme);
$conn->close();
?>

</main>
</body>
</html>
