<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Subir Comprobante</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Subir comprobante de pago</h2>
<form method="post" enctype="multipart/form-data">
    <input type="text" name="nombre" placeholder="Nombre del archivo" required><br><br>
    <textarea name="descripcion" placeholder="Descripción del archivo" required></textarea><br><br>
    <input type="file" name="comprobante" required><br><br>
    <button type="submit">Subir</button>
</form>
<a href="archivo.php">Volver</a>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $archivo = $_FILES['comprobante'];

    if (!file_exists("uploads")) {
        mkdir("uploads");
    }

    $destino = "uploads/" . time() . "_" . basename($archivo['name']);

    if (move_uploaded_file($archivo['tmp_name'], $destino)) {
       
        $servername = "localhost";
        $username   = "root";      
        $password   = "equipoinfrog";          
        $database   = "proyect_database_mycoop6"; 

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");

    
        $fechaHoy = date("Y-m-d");
        $sql = "INSERT INTO Archivos (NombreArchivo, Fecha, DescripcionArch) 
                VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sss", $nombre, $fechaHoy, $descripcion);
            if ($stmt->execute()) {
                echo "<p style='color:green;'>✅ Comprobante subido y guardado en la base de datos.</p>";
            } else {
                echo "<p style='color:red;'>Error al guardar en la BD: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color:red;'>Error en la consulta: " . $conn->error . "</p>";
        }

        $conn->close();
    } else {
        echo "<p style='color:red;'>❌ Error al subir el comprobante.</p>";
    }
}
?>
</body>
</html>

