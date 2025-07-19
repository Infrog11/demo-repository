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
    <form method="POST"> 
    <label for="msg">Ingrese un mensaje para enviar a un administrador:</label>
    <br>
    <input type="text" id="msg" name="msg"/>
    <button type="subimt">Enviar</button>
    <form>
</body>
</html>