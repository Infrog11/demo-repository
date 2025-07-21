<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop</title>
    <link rel="stylesheet" href="usuario.css" />
</head>
<nav>
    <div id="Navegador">
    <a href="http://localhost/PROYECTOUTU/usuario.php"><img src="iconoUsuario.png" height="70px"></a>
    <!--<a href="http://localhost/PROYECTOUTU/inSesion.php"><img src="iconoForo.png" height="70px"></a>-->
    <a href="http://localhost/PROYECTOUTU/fechas.php"><img src="iconoCalendario.png" height="70px"></a>
    <a href="http://localhost/PROYECTOUTU/comunicacion.php"><img src="iconoComunicacion.png" height="70px"></a>
    <a href="http://localhost/PROYECTOUTU/archivo.php"><img src="iconoDocumentos.png" height="70px"></a>
    <a href="http://localhost/PROYECTOUTU/configuracion.php"><img src="iconoConfiguracion.png" height="70px"></a>
    <a href="http://localhost/PROYECTOUTU/notificaciones.php"><img src="iconoNotificacion.png" height="70px"></a>
    </div>
</nav>
<body>
    <div id="Logo">
        <img src="logoMyCoop.png" height="200px">
    </div>
        <h1>USUARIO</h1>
    <div id="fotoperfil">
        <img src="DefaultPerfile.png" height="150px">
        <a>Nombre: </a>
        <a>Edad: </a>
        <a>Pronombres: </a>
        <h3>Horas semanales trabajadas:</h3>
        <form method="POST">
        <label for="Hor">Ingresar horas trabajadas</label>
        <input type="number" id="hor" name="hor">
        <button type="subimt" id="button" >Guardar</button>
        </from>
    </div> 
    <br>
    <br>
    <br>
    <br>
<?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $horasNew = floatval($_POST["hor"]);
        function cargar(){
            $horGuardar = fopen("Registro.txt", "r") or die("ERROR AL CARGAR"); 
            while(!feof($horGuardar)){
                $leer = fgets($horGuardar);
                return $leer;
            }
        }
         function guardar($horasTot, $horasNew){
            $horGuardar = fopen("Registro.txt", "w+") or die("ERROR AL GUARDAR"); 
            fputs($horGuardar,$horasTot);
            fclose($horGuardar);
            $horasNew=0;
        }
    
        $horasTot = cargar();
        $horasTot = $horasTot+$horasNew;
        print "Las horas trabajadas son: ". $horasTot;
        guardar($horasTot, $horasNew);

    }

?>
</body>
</html> 