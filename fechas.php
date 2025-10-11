<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop</title>
    <link rel="stylesheet" href="catStyle.css" />
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: #f4f6f9;
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
}

nav {
    background: #2c3e50;
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
    gap: 20px;
}

#Navegador a img {
    transition: transform 0.3s, filter 0.3s;
    border-radius: 50%;
    padding: 5px;
    background: #fff;
}

#Navegador a img:hover {
    transform: scale(1.15);
    filter: brightness(1.1);
}

main {
    margin: 30px auto;
    padding: 20px;
    max-width: 1000px;
    width: 90%;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0px 6px 15px rgba(0,0,0,0.1);
    animation: fadeIn 0.6s ease-in-out;
}

h1, h2, h3 {
    color: #2c3e50;
    margin-bottom: 15px;
}

h1 {
    text-align: center;
    margin-bottom: 30px;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.2);
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

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
<nav>
    <div id="Navegador">
        <a href="http://localhost/PROYECTOUTU/usuario.php"><img src="iconoUsuario.png" height="70px"></a>
        <a href="http://localhost/PROYECTOUTU/fechas.php"><img src="iconoCalendario.png" height="70px"></a>
        <a href="http://localhost/PROYECTOUTU/comunicacion.php"><img src="iconoComunicacion.png" height="70px"></a>
        <a href="http://localhost/PROYECTOUTU/archivo.php"><img src="iconoDocumentos.png" height="70px"></a>
        <a href="http://localhost/PROYECTOUTU/configuracion.php"><img src="iconoConfiguracion.png" height="70px"></a>
        <a href="http://localhost/PROYECTOUTU/inSesion.php"><img src="iconoNotificacion.png" height="70px"></a>
        <a href="TesoreroAdmin.php"><img src="Tesorero.png" height="70px"></a>    
    </div>
</nav>
<body>

<?php
include "conexion.php";


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


function mostrarCalendario($mes, $anio, $eventos) {
    $primerDia = mktime(0, 0, 0, $mes, 1, $anio);
    setlocale(LC_TIME, "es_ES.UTF-8");
    $nombreMes = strftime("%B", $primerDia);
    $diaSemana = date("N", $primerDia);
    $diasMes = date("t", $primerDia);

    echo "<h2 style='text-align:center;'>$nombreMes $anio</h2>";
    echo "<table border='1' cellpadding='10' cellspacing='0' style='margin:auto; text-align:center;'>";
    echo "<tr>
            <th>Lun</th><th>Mar</th><th>Mié</th>
            <th>Jue</th><th>Vie</th><th>Sáb</th><th>Dom</th>
        </tr><tr>";


    if ($diaSemana > 1) {
        for ($i = 1; $i < $diaSemana; $i++) {
            echo "<td></td>";
        }
    }

    $contador = $diaSemana;
    for ($dia = 1; $dia <= $diasMes; $dia++) {
        $fechaActual = "$anio-" . str_pad($mes, 2, "0", STR_PAD_LEFT) . "-" . str_pad($dia, 2, "0", STR_PAD_LEFT);
        
        echo "<td><strong>$dia</strong><br>";


        foreach ($eventos as $evento) {
            if ($evento["FechaEvento"] == $fechaActual) {
                echo "<div style='background:lightblue; padding:3px; margin:2px; border-radius:5px; font-size:12px;'>";
                echo "<strong>" . htmlspecialchars($evento["NombreEvento"]) . "</strong><br>";
                echo "<small>" . htmlspecialchars($evento["DescripcionEvento"]) . "</small>";
                echo "</div>";
            }
        }

        echo "</td>";

        if ($contador % 7 == 0) {
            echo "</tr><tr>";
        }
        $contador++;
    }

    echo "</tr></table><br><br>";
}


echo "<div style='text-align:center; margin:20px;'>
        <a href='?mes=$mesAnterior&anio=$anioAnterior'>
            <button>&laquo; Mes Anterior</button>
        </a>
        <a href='?mes=$mesSiguiente&anio=$anioSiguiente'>
            <button>Mes Siguiente &raquo;</button>
        </a>
    </div>";


mostrarCalendario($mesActual, $anioActual, $eventos);

$conn->close();
?>
<a href="guardarFechas.php">Añadir fechas</a>
</body>
</html>
