<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.niveles_educacion.php");
	$nivel_educacion = new niveles_educacion();
	$nivel_educacion->code = $_POST["id_nivel_educacion"];
	echo $nivel_educacion->obtenerJsonNivelEducacion($nivel_educacion->code);
?>