<?php
//Zona horaria para el sitio web
date_default_timezone_set("America/Managua");
//fechas y horas en español
setlocale(LC_ALL,"spanish");

/*-------------------------------
Listado de opciones para el sitio
-------------------------------*/
//El sitio tiene configurado un dominio (1) o esta en una carpeta dentro de localhost (0), en caso de este ultimo, cambiar nombre de carpeta en WEB_DIR
define('ONLINE', '0');
define('WEB_DIR', 'core-blog-php');//desactivar si el sitio tiene dominio configurado

//Nombre de usuario
define("NAME_USER", "Danny Garay");

/*-------------------------
conexion a la base de datos
-------------------------*/
//host de la base de datos, por lo general es localhost
define("HOST_DB", "localhost");
//nombre de la base de datos
define("NAME_DB", "basededatos");
//usuario de la base de datos
define("USER_DB", "root");
//contraseña de la base de datos
define("PASS_DB", "");

/*------------------
No modificar
-------------------*/
if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])){
	$uri_http = 'https://';
}
else{
	$uri_http = 'http://';
}

if (ONLINE==1) {
	$url_base = $uri_http.$_SERVER['HTTP_HOST']; //valdrá: http://dominio.com
}
else{
	$url_base = $uri_http.$_SERVER['HTTP_HOST'].'/'.WEB_DIR.'/'; //valdrá: http://localhost/carpeta/
}
define("URL_BASE", $url_base);
?>
