<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.subniveles_educacion.php");
	$subnivel_educacion = new subniveles_educacion();
	$subnivel_educacion->code = $_POST["id_subnivel_educacion"];
	echo $subnivel_educacion->obtenerSubNivelEducacion($subnivel_educacion->code);
?>