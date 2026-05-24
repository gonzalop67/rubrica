<?php
	require_once("clases/class.mysql.php");
	require_once("clases/class.institucion.php");
	$institucion = new institucion();
	echo $institucion->obtenerNombreInstitucion();
?>