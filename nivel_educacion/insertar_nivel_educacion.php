<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.niveles_educacion.php");
	$nivel_educacion = new niveles_educacion();
    $nivel_educacion->nombre = $_POST["nombre"];
    $nivel_educacion->slug = $_POST["slug"];
	echo $nivel_educacion->insertarNivelEducacion();
?>