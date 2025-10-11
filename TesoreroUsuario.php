<?php

$host = "localhost";   
$user = "root";       
$pass = "equipoinfrog";          
$db   = "proyect_database_mycoop6"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}


$result = $conn->query("SELECT SUM(Monto) AS saldo FROM FondoMonetario");
$row = $result->fetch_assoc();
$saldo = $row['saldo'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Movimientos del Fondo</title>
</head>
<body>
    <h2>Consulta de Movimientos</h2>

    <h3>Saldo Actual: $<?php echo number_format($saldo, 2); ?></h3>

    <h3>Historial de movimientos</h3>
    <table border="1" cellpadding="5">
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
