<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop - Registrarse</title>
    <link rel="stylesheet" href="StyleInicio.css" />
</head>
<style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #2c3e50, #3498db);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        #Inicio {
            background: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.25);
            width: 350px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        h1 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        label {
            display: block;
            margin: 12px 0 5px;
            font-weight: bold;
            color: #333;
            text-align: left;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: #3498db;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: #3498db;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #2c3e50;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
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
        
        $conexion = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6"); 
        if ($conexion->connect_error) {
            die("Conexión fallida: " . $conexion->connect_error);
        }

        
        $sql = "INSERT INTO Persona 
                (Cedula, Nombre, Apellido, Direccion, edad, Comunicacion, Contrasena, Aceptado) 
                VALUES (?, ?, ?, '', ?, ?, ?, 0)";

       
        $stmt = $conexion->prepare($sql);
        
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
