<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.asignaturas.php");
	$asignatura = new asignaturas();
	echo $asignatura->buscarAsignaturas($_POST["input_search"]);
?>
