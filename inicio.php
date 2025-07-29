<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCoop</title>
    <link rel="stylesheet" href="Style.css" />
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
    <h1>Novedades</h1>
    <a href="añadirNovedades.php">+</a>
    <?php
    function cargarNovs(){
        $cargarArchivo = fopen("Novedades.txt", "r") or die("ERROR AL CARGAR"); 
        while(!feof($cargarArchivo)){
            $leer = fgets($cargarArchivo);
            return $leer;
        }
    }
    print(cargarNovs());
    ?>
</body>
</html> 