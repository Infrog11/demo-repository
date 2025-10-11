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
<style>
    body {
    font-family: "Segoe UI", Arial, sans-serif;
    margin: 20px;
    background: #f5f7fa;
    color: #2c3e50;
}

h2, h3 {
    color: #34495e;
    margin-bottom: 10px;
}

h3 {
    font-weight: normal;
}

table {
    border-collapse: collapse;
    width: 90%;
    margin-top: 15px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0px 3px 6px rgba(0,0,0,0.1);
}

th, td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #eee;
}

th {
    background: #2c3e50;
    color: #fff;
    font-size: 14px;
    text-transform: uppercase;
}

tr:nth-child(even) {
    background: #f9f9f9;
}

tr:hover {
    background: #eaf2f8;
}

td:nth-child(2) { 
    font-weight: bold;
    color: #27ae60;
}

h3:nth-of-type(1) {
    background: #2ecc71;
    color: white;
    display: inline-block;
    padding: 8px 14px;
    border-radius: 6px;
    margin-top: 0;
}

</style>
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
