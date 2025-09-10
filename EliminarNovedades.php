<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Eliminar Novedad - MyCoop</title>
    <link rel="stylesheet" href="Style.css" />
</head>
<body>
    <h1>Eliminar Novedad</h1>
    <form method="POST">
        <label for="idNovedad">ID de la novedad a eliminar:</label>
        <input type="number" id="idNovedad" name="idNovedad" required>
        <button type="submit">Eliminar</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Conexión a la BD
        $host = "localhost";
        $user = "root";        // tu usuario MySQL
        $pass = "equipoinfrog";            // tu contraseña
        $db   = "proyect_database_Mycoop2"; // cámbialo por el nombre real

        $conn = new mysqli($host, $user, $pass, $db);

        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }

        $id = intval($_POST["idNovedad"]);

        // Verificar si existe antes de eliminar
        $check = $conn->prepare("SELECT * FROM Novedades WHERE idNovedad = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("DELETE FROM Novedades WHERE idNovedad = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo "<p style='color:green;'>✔ Novedad con ID $id eliminada con éxito.</p>";
            } else {
                echo "<p style='color:red;'>❌ Error al eliminar: " . $conn->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p style='color:red;'>⚠ No existe ninguna novedad con el ID $id.</p>";
        }

        $check->close();
        $conn->close();
    }
    ?>
</body>
</html>
