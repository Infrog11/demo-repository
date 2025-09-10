<?php
session_start();

// Solo administradores pueden acceder
if (!isset($_SESSION["Rol"]) || $_SESSION["Rol"] !== "administrador") {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "equipoinfrog", "proyect_database_mycoop2");
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

// --- ASIGNAR UNIDAD HABITACIONAL ---
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

// --- ACTUALIZAR HORAS ---
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

// --- ACTUALIZAR ETAPA/DESCRIPCIÓN ---
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

// --- CONSULTAR TODOS LOS REGISTROS ---
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
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
        h1 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; background: #fff; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #eee; }
        form { margin: 0; }
        input, textarea, select { margin: 5px; padding: 5px; width: 95%; }
        button { padding: 5px 10px; }
        .msg { margin: 15px 0; font-weight: bold; }
        .form-box { background: #fff; padding: 15px; border: 1px solid #ccc; margin-bottom: 20px; }
    </style>
</head>
<body>
<h1>Panel de Construcción</h1>

<?php if (isset($mensaje)) echo "<div class='msg'>$mensaje</div>"; ?>

<!-- Formulario para asignar una UH -->
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

<!-- Tabla de registros -->
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

        <!-- Form para actualizar horas -->
        <td>
            <form method="POST">
                <input type="hidden" name="tipo_accion" value="horas">
                <input type="hidden" name="cedula" value="<?= $row["Cedula"] ?>">
                <input type="hidden" name="idUH" value="<?= $row["IdUH"] ?>">
                <input type="number" name="horas" min="1" max="24" required>
                <button type="submit">➕ Agregar</button>
            </form>
        </td>

        <!-- Form para editar etapa y descripción -->
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
