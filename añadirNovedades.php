<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop</title>
    <link rel="stylesheet" href="Style.css" />
</head>
<body>
<form method="POST">
        <label for="Nov">Ingresa las novedades</label>
        <input type="value" id="Nov" name="Nov">
        <button type="subimt" id="button">Guardar</button>
        </from>
<?php
     if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $Novedad = $_POST["Nov"];

        function GuardarNovs($Novedad){
            $guardarNovs = fopen("Novedades.txt", "r+") or die("ERROR AL GUARDAR"); 
            fputs($guardarNovs,$Novedad);
            fclose($guardarNovs);
            print("Contenido guardado");
        }
        GuardarNovs($Novedad);

        
    }

?>
</body>
</html>