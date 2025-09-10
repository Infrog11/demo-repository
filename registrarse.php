<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop - Registrarse</title>
    <link rel="stylesheet" href="StyleInicio.css" />
</head>
<body>
    <div id="Inicio">
    <h1>REGISTRARSE</h1>
    <form method="POST">
        <label for="Ced">Cédula de Identidad</label><br>
        <input type="number" id="ced" name="ced" required /><br>

        <label for="Nom">Nombre</label><br>
        <input type="text" id="nom" name="nom" required /><br>
        
        <label for="ape">Apellido</label><br>
        <input type="text" id="ape" name="ape" required /><br>

        <label for="contact">Método de contacto (Número o Correo)</label><br>
        <input type="text" id="contact" name="contact" required /><br>

        <label for="Edad">Edad</label><br>
        <input type="number" id="edad" name="edad" required /><br>

        <label for="passw">Contraseña</label><br>
        <input type="password" id="passw" name="passw" required /><br>

        <label for="passw2">Repita la Contraseña</label><br>
        <input type="password" id="passw2" name="passw2" required /><br>

        <br>
        <button type="submit">Solicitar registro</button>
    </form>
    </div>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $Nombre = $_POST["nom"];
    $Apellido = $_POST["ape"];
    $Cedula = $_POST["ced"];
    $contacto = $_POST["contact"];
    $edad = $_POST["edad"]; 
    $password = $_POST["passw"];
    $password2 = $_POST["passw2"];

    if ($password !== $password2) {
        echo "<p style='color:red;'>Las contraseñas no coinciden.</p>";
    } else {
        // Conectar a la base de datos
        $conexion = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop2"); 
        if ($conexion->connect_error) {
            die("Conexión fallida: " . $conexion->connect_error);
        }

        // Query para insertar en la tabla Persona
        $sql = "INSERT INTO Persona 
                (Cedula, Nombre, Apellido, Direccion, edad, Comunicacion, Contrasena, Aceptado) 
                VALUES (?, ?, ?, '', ?, ?, ?, 0)";

        // Preparamos el statement
        $stmt = $conexion->prepare($sql);
        // bind_param: Cedula (i), Nombre (s), Apellido (s), Edad (i), Contacto (s), Password (s)
        $stmt->bind_param("ississ", $Cedula, $Nombre, $Apellido, $edad, $contacto, $password);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>✔ Registro solicitado correctamente. Queda pendiente de aprobación.</p>";
        } else {
            echo "<p style='color:red;'>❌ Error al registrar: " . $conexion->error . "</p>";
        }

        $stmt->close();
        $conexion->close();
    }
}
?>
</body>
</html>
