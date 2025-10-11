<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop</title>
    <link rel="stylesheet" href="catStyle.css" />
</head>
<nav>
    <div id="Navegador">
        <a href="usuarioUsuario.php"><img src="iconoUsuario.png" height="70px"></a>
        <a href="fechasUsuarios.php"><img src="iconoCalendario.png" height="70px"></a>
        <a href="comunicacionUsuarios.php"><img src="iconoComunicacion.png" height="70px"></a>
        <a href="archivoUsuarios.php"><img src="iconoDocumentos.png" height="70px"></a>
        <a href="configuracionUsuarios.php"><img src="iconoConfiguracion.png" height="70px"></a>
        <a href="notificacionesUsuario.php"><img src="iconoNotificacion.png" height="70px"></a>
        <a href="TesoreroUsuario.php"><img src="Tesorero.png" height="70px"></a>
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

</body>
</html>
