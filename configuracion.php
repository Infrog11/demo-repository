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
    <label for="direccion">Tema de la pagina</label>
        <select id="tema" name="tema">
        <option value="claro">Modo claro</option>
        <option value="oscuro">Modo Oscuro</option>
    </select>
    <label for="letra">Tamaño de la letra</label>
        <select id="letra" name="letra">
        <option value="uno">1</option>
        <option value="dos">2</option>
        <option value="tres">3</option>
        <option value="cuatro">4</option>
        <option value="cinco">5</option>
    </select>
    <label for="daltonismo">Activar el modo daltonismo</label>
        <select id="dalt" name="dalt">
        <option value="No">No</option>
        <option value="Si">Si</option>
    </select>
    <label for="Idioma">Idioma</label>
        <select id="idioma" name="idioma">
        <option value="esp">Español</option>
        <option value="ing">Ingles</option>
    </select>
     <label for="simp">Simplificar pagina</label>
        <select id="simp" name="simp">
        <option value="NoSimp">No</option>
        <option value="SiSimp">Si</option>
    </select>
    

</body>
</html>