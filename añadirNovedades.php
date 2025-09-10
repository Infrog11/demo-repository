<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop</title>
    <link rel="stylesheet" href="Style.css" />
</head>
<body>
    <form method="POST">
        <label for="Nov">Ingresa las novedades</label>
        <input type="text" id="Nov" name="Nov" required>
        <button type="submit" id="button">Guardar</button>
    </form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Conexión a la base de datos
    $host = "localhost";
    $user = "root";        // tu usuario de MySQL
    $pass = "equipoinfrog";            // tu contraseña
    $db   = "proyect_database_MyCoop2"; // cámbiala por el nombre real de tu base
    
    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Sanitizar entrada
    $Novedad = trim($_POST["Nov"]);

    if (!empty($Novedad)) {
        // Insertar novedad en la base
        $stmt = $conn->prepare("INSERT INTO Novedades (Novedad) VALUES (?)");
        $stmt->bind_param("s", $Novedad);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>✔ Novedad guardada con éxito</p>";
        } else {
            echo "<p style='color:red;'>❌ Error al guardar: " . $conn->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color:red;'>⚠ Debes ingresar una novedad</p>";
    }

    $conn->close();
}
?>
</body>
</html>
