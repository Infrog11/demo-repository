<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop</title>
    <link rel="stylesheet" href="catStyle.css" />
</head>
<body>
<form method="POST">
    <label for="titulo" >Titulo</label>
    <br>
    <input type="text" id="titulo" name="titulo" required />
    <br>
    <label for="descripcion" >Descripcion</label>
    <br>
    <input type="text" id="descripcion" name="descripcion" required />
    <br>
    <label for="fecha" >Fecha</label>
    <br>
    <input type="text" id="fecha" name="fecha" required />
    <br>
    <button type="subimt" id="button">Guardar</button>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];

    $sql = "INSERT INTO eventos (titulo, descripcion, fecha) VALUES (:titulo, :descripcion, :fecha)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':fecha', $fecha);

    if ($stmt->execute()) {
        echo "Evento guardado.";
    } else {
        echo "Error.";
    }
}
?>
</body>
</html>