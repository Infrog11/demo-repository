<?php
session_start();


if (!isset($_SESSION["Rol"]) || $_SESSION["Rol"] !== "administrador") {
    header("Location: login.php");
    exit();
}


$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop6");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["tipo_accion"]) && $_POST["tipo_accion"] === "asignar") {
    $cedula = intval($_POST["cedula"]);
    $idUH = intval($_POST["idUH"]);
    $etapa = trim($_POST["etapa"]);
    $descripcion = trim($_POST["descripcion"]);

    $hoy = new DateTime();
    $semanaInicio = $hoy->modify("monday this week")->format("Y-m-d");

    $check = $conn->prepare("SELECT * FROM Construye WHERE Cedula=? AND IdUH=?");
    $check->bind_param("ii", $cedula, $idUH);
    $check->execute();
    $resCheck = $check->get_result();

    if ($resCheck->num_rows > 0) {
        $mensaje = "<p style='color:red;'>⚠ Ese usuario ya está asignado a esa unidad habitacional.</p>";
    } else {
        $insert = $conn->prepare("
            INSERT INTO Construye (Cedula, IdUH, Etapa, HorasTotales, HorasSemanales, SemanaInicio, DescripcionConst)
            VALUES (?, ?, ?, 0, 0, ?, ?)
        ");
        $insert->bind_param("iisss", $cedula, $idUH, $etapa, $semanaInicio, $descripcion);
        if ($insert->execute()) {
            $mensaje = "<p style='color:green;'>✅ Unidad habitacional asignada correctamente.</p>";
        } else {
            $mensaje = "<p style='color:red;'>❌ Error al asignar: " . $conn->error . "</p>";
        }
        $insert->close();
    }
    $check->close();
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["tipo_accion"]) && $_POST["tipo_accion"] === "horas") {
    $cedula = intval($_POST["cedula"]);
    $idUH = intval($_POST["idUH"]);
    $horasNew = intval($_POST["horas"]);

    $stmt = $conn->prepare("SELECT HorasTotales, HorasSemanales, SemanaInicio FROM Construye WHERE Cedula=? AND IdUH=?");
    $stmt->bind_param("ii", $cedula, $idUH);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $horasTotales = $row["HorasTotales"];
        $horasSemanales = $row["HorasSemanales"];
        $semanaInicio = $row["SemanaInicio"];

        $hoy = new DateTime();
        $inicioSemanaActual = $hoy->modify("monday this week")->format("Y-m-d");

        if ($semanaInicio !== $inicioSemanaActual) {
            $horasSemanales = 0;
            $semanaInicio = $inicioSemanaActual;
        }

        if ($horasSemanales + $horasNew > 168) {
            $mensaje = "<p style='color:red;'>⚠ No se pueden registrar más de 168 horas en una semana.</p>";
        } else {
            $horasTotales += $horasNew;
            $horasSemanales += $horasNew;

            $update = $conn->prepare("UPDATE Construye SET HorasTotales=?, HorasSemanales=?, SemanaInicio=? WHERE Cedula=? AND IdUH=?");
            $update->bind_param("iisii", $horasTotales, $horasSemanales, $semanaInicio, $cedula, $idUH);
            $update->execute();
            $update->close();

            $mensaje = "<p style='color:green;'>✅ Horas actualizadas correctamente.</p>";
        }
    } else {
        $mensaje = "<p style='color:red;'>❌ No se encontró el registro de construcción.</p>";
    }

    $stmt->close();
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["tipo_accion"]) && $_POST["tipo_accion"] === "editar") {
    $cedula = intval($_POST["cedula"]);
    $idUH = intval($_POST["idUH"]);
    $etapa = trim($_POST["etapa"]);
    $descripcion = trim($_POST["descripcion"]);

    $update = $conn->prepare("UPDATE Construye SET Etapa=?, DescripcionConst=? WHERE Cedula=? AND IdUH=?");
    $update->bind_param("ssii", $etapa, $descripcion, $cedula, $idUH);
    if ($update->execute()) {
        $mensaje = "<p style='color:green;'>✅ Registro actualizado correctamente.</p>";
    } else {
        $mensaje = "<p style='color:red;'>❌ Error al actualizar: " . $conn->error . "</p>";
    }
    $update->close();
}


$sql = "
    SELECT c.Cedula, p.Nombre, p.Apellido, c.IdUH, c.Etapa, c.HorasTotales, c.HorasSemanales, c.SemanaInicio, c.DescripcionConst
    FROM Construye c
    JOIN Persona p ON c.Cedula = p.Cedula
    ORDER BY c.IdUH, p.Apellido, p.Nombre
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Construcción</title>
    </head>
    <style>
body {
    font-family: "Segoe UI", Arial, sans-serif;
    margin: 20px;
    background: #f4f6f9;
    color: #333;
}

h1 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 30px;
}

.msg {
    margin: 15px auto;
    padding: 12px;
    border-radius: 6px;
    text-align: center;
    background: #eafaf1;
    color: #27ae60;
    width: 60%;
    font-weight: bold;
}

.form-box {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #ddd;
    margin: 0 auto 30px auto;
    width: 60%;
    box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
}

.form-box h2 {
    margin-bottom: 15px;
    color: #34495e;
}

input, textarea, select {
    margin: 8px 0;
    padding: 8px;
    width: 95%;
    border: 1px solid #ccc;
    border-radius: 5px;
    transition: border 0.3s;
}

input:focus, textarea:focus {
    border: 1px solid #3498db;
    outline: none;
}

button {
    background: #3498db;
    color: #fff;
    padding: 8px 14px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
}

button:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
}

th, td {
    border: 1px solid #eee;
    padding: 12px;
    text-align: center;
}

th {
    background: #2c3e50;
    color: #fff;
    text-transform: uppercase;
    font-size: 14px;
}

tr:nth-child(even) {
    background: #f9fbfc;
}
td form {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}
    </style>

<body>
<h1>Panel de Construcción</h1>

<?php if (isset($mensaje)) echo "<div class='msg'>$mensaje</div>"; ?>


<div class="form-box">
    <h2>Asignar Unidad Habitacional a un Usuario</h2>
    <form method="POST">
        <input type="hidden" name="tipo_accion" value="asignar">
        <label>Cédula:</label>
        <input type="number" name="cedula" required>
        <label>Unidad Habitacional (IdUH):</label>
        <input type="number" name="idUH" required>
        <label>Etapa:</label>
        <input type="text" name="etapa" required>
        <label>Descripción:</label>
        <textarea name="descripcion" required></textarea>
        <button type="submit">➕ Asignar</button>
    </form>
</div>


<table>
    <tr>
        <th>Cédula</th>
        <th>Nombre</th>
        <th>Unidad Habitacional</th>
        <th>Etapa</th>
        <th>Horas Totales</th>
        <th>Horas Semanales</th>
        <th>Semana Inicio</th>
        <th>Descripción</th>
        <th>Actualizar Horas</th>
        <th>Editar Etapa/Descripción</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row["Cedula"] ?></td>
        <td><?= htmlspecialchars($row["Nombre"] . " " . $row["Apellido"]) ?></td>
        <td><?= $row["IdUH"] ?></td>
        <td><?= htmlspecialchars($row["Etapa"]) ?></td>
        <td><?= $row["HorasTotales"] ?></td>
        <td><?= $row["HorasSemanales"] ?></td>
        <td><?= $row["SemanaInicio"] ?></td>
        <td><?= htmlspecialchars($row["DescripcionConst"]) ?></td>

    
        <td>
            <form method="POST">
                <input type="hidden" name="tipo_accion" value="horas">
                <input type="hidden" name="cedula" value="<?= $row["Cedula"] ?>">
                <input type="hidden" name="idUH" value="<?= $row["IdUH"] ?>">
                <input type="number" name="horas" min="1" max="24" required>
                <button type="submit">➕ Agregar</button>
            </form>
        </td>

        
        <td>
            <form method="POST">
                <input type="hidden" name="tipo_accion" value="editar">
                <input type="hidden" name="cedula" value="<?= $row["Cedula"] ?>">
                <input type="hidden" name="idUH" value="<?= $row["IdUH"] ?>">
                <input type="text" name="etapa" value="<?= htmlspecialchars($row["Etapa"]) ?>" required>
                <textarea name="descripcion" required><?= htmlspecialchars($row["DescripcionConst"]) ?></textarea>
                <button type="submit">💾 Guardar</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
<?php
$conn->close();
?>
