<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cedula = $_POST["ced"];
    $password = $_POST["passw"];

    // Conexión a la base de datos
    $host = "localhost";
    $user = "root";
    $pass = "equipoinfrog";
    $db   = "proyect_database_mycoop2"; 

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Buscar usuario
    $stmt = $conn->prepare("SELECT Cedula, Contrasena, Rol, Aceptado FROM Persona WHERE Cedula = ?");
    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Primero verificamos si está aprobado
        if ($row["Aceptado"] == 0) {
            echo "<p style='color:red;'>⚠ Tu cuenta aún no fue aprobada por un administrador.</p>";
        } else {
            // Verificar contraseña (⚠ texto plano; mejor usar password_hash/password_verify)
            if ($password === $row["Contrasena"]) {
                $_SESSION["Cedula"] = $row["Cedula"];
                $_SESSION["Rol"] = $row["Rol"];

                // Redirección según rol
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
