<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_educacion.php");
	$tipos_educacion = new tipos_educacion();
	$tipos_educacion->id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	echo $tipos_educacion->cargar_tipos_educacion();
?>
