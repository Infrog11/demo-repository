<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop</title>
    <link rel="stylesheet" href="catStyle.css" />
</head>
<body>
<?php
$servername = "localhost";
$username   = "root";       
$password   = "equipoinfrog";           
$database   = "Proyect_database_mycoop6"; 

$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = $_POST['fecha'];         
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'] ?? "";

    if (!empty($fecha) && !empty($titulo)) {
        $sql = "INSERT INTO Eventos (NombreEvento, FechaEvento, DescripcionEvento) 
                VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sss", $titulo, $fecha, $descripcion);
            if ($stmt->execute()) {
                echo "<p style='color:green;'>✅ Evento guardado correctamente</p>";
            } else {
                echo "<p style='color:red;'>Error al guardar: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color:red;'>Error en la preparación de la consulta: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Por favor completa todos los campos obligatorios.</p>";
    }
}

$conn->close();
?>

<h2>Agregar Evento</h2>
<form method="post">
    <label>Fecha:</label><br>
    <input type="date" name="fecha" required><br><br>

    <label>Título:</label><br>
    <input type="text" name="titulo" maxlength="150" required><br><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion" rows="4"></textarea><br><br>

    <button type="submit">Guardar</button>
</form>
<a href="fechas.php">Volver</a>
</body>
</html>