<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cedula = $_POST["ced"];
    $password = $_POST["passw"];

    
    $host = "localhost";
    $user = "root";
    $pass = "equipoinfrog";
    $db   = "proyect_database_mycoop6"; 

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    
    $stmt = $conn->prepare("SELECT Cedula, Contrasena, Rol, Aceptado FROM Persona WHERE Cedula = ?");
    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        
        if ($row["Aceptado"] == 0) {
            echo "<p style='color:red;'>⚠ Tu cuenta aún no fue aprobada por un administrador.</p>";
        } else {
            
            if ($password === $row["Contrasena"]) {
                $_SESSION["Cedula"] = $row["Cedula"];
                $_SESSION["Rol"] = $row["Rol"];

                
                if ($row["Rol"] === "administrador") {
                    header("Location: inicio.php");
                } elseif ($row["Rol"] === "usuario") {
                    header("Location: inicioUsuario.php");
                } elseif ($row["Rol"] === "tesorero") {
                    header("Location: inicioTesorero.php");
                } else {
                    echo "Rol no reconocido.";
                }
                exit();
            } else {
                echo "<p style='color:red;'>⚠ Contraseña incorrecta</p>";
            }
        }
    } else {
        echo "<p style='color:red;'>⚠ Usuario no encontrado</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop - Iniciar Sesión</title>
    <link rel="stylesheet" href="StyleInicio.css" />
</head>
<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

h1 {
    color: #fff;
    margin-bottom: 30px;
    text-shadow: 2px 2px 5px rgba(0,0,0,0.5);
}

form {
    background: #fff;
    padding: 30px 40px;
    border-radius: 15px;
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);
    width: 320px;
    animation: fadeIn 0.8s ease-in-out;
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
    border-color: #2a5298;
}

button {
    margin-top: 20px;
    width: 100%;
    padding: 12px;
    background: #2a5298;
    border: none;
    border-radius: 8px;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}

button:hover {
    background: #1e3c72;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
<body>
    <h1>INICIAR SESIÓN</h1>
    <form method="POST">
        <label for="ced">Cédula de Identidad</label><br>
        <input type="number" id="ced" name="ced" required><br><br>

        <label for="passw">Contraseña</label><br>
        <input type="password" id="passw" name="passw" required><br><br>

        <button type="submit">Acceder</button>
    </form>
</body>
</html>
