<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop-Iniciar Sesion</title>
    <link rel="stylesheet" href="StyleInicio.css" />
</head>
<body>
    <div id="Inicio"></div>
    <h1>REGISTRARSE</h1>
    <from method="POST">
        <label for="Ced" >Cedula de Identidad</label>
        <br>
        <input type="number" id="ced" name="ced" required />
        <br>

        <label for="Nom" >Nombre</label>
        <br>
        <input type="text" id="nom" name="nom" required />
        <br>
        
        <label for="ape" >Apellido</label>
        <br>
        <input type="text" id="ape" name="ape" required />
        <br>

        <label for="contact"> Metodo de contacto para audiencia (Numero o Correo) </label>
        <br>
        <input type="number" id="contact" name="contact" required />
        <br>

        <label for="Edad"> Edad </label>
        <br>
        <input type="number" id="edad" name="edad" required />
        <br>

        <label for="passw">Contraseña</label>
        <br>
        <input type="text" id="passw" name="passw" required />
        <br>

        <label for="passw2">Repita la Contraseña</label>
        <br>
        <input type="text" id="passw2" name="passw2" required />
        <br>


        <br>
        <a href="ConfirmacionRegistro.html"><button type="submit">Solicitar registro</button></a>
        </div>
    </from>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nom'];
    $apellido = $_POST['ape'];
    $edad = $_POST['edad'];
    $contacto = $_POST['contact'];
    $contrasena = $_POST['contrasena'];

    $datos = "Nombre: $nombre.$apellido | Edad: $edad | Contacto: $contacto | Contraseña: $contrasena\n";

    file_put_contents("logins.txt", $datos, FILE_APPEND);

    echo "Datos guardados correctamente.";
}
?>
</body>
</html>