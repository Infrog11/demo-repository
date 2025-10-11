<?php

$host = "localhost";   
$user = "root";    
$pass = "equipoinfrog";            
$db   = "proyect_database_mycoop6"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}


if (isset($_POST['guardar'])) {
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'];
    $cedula = $_POST['cedula'];

    $sql = "INSERT INTO FondoMonetario (Monto, DescripcionFondo, Cedula_Tesorero) 
            VALUES ('$monto', '$descripcion', '$cedula')";
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Movimiento registrado con éxito.</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
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
</head>
<body>
    <h2>Administración del Fondo</h2>

    <h3>Saldo Actual: $<?php echo number_format($saldo, 2); ?></h3>

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
