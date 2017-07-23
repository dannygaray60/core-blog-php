<?php
//genera una cadena de texto con caracteres aleatorios para un id o contraseña
function generate_text($length){
	$new_id_pass = substr( sha1(microtime()), 1, $length);
	return $new_id_pass;
}

$arrayM = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
$arrayD = array( 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');

//Resultado, (fecha actual 21/09/2012):Viernes, 21 de Septiembre de 2012
function current_date_to_user($arrayD,$arrayM){
	return $arrayD[date('w')].", ".date('d')." de ".$arrayM[date('m')-1]." del ".date('Y');
}


?>
