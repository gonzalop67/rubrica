<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.subniveles_educacion.php");
	// session_start();
	$subnivel_educacion = new subniveles_educacion();
    $subnivel_educacion->code = $_POST["id"];
    $subnivel_educacion->nivel_id = $_POST["nivel_id"];
    $subnivel_educacion->nombre = $_POST["nombre"];
    $subnivel_educacion->slug = $_POST["slug"];
    $subnivel_educacion->es_bachillerato = $_POST["es_bachillerato"];
	echo $subnivel_educacion->actualizarSubNivelEducacion();
?>