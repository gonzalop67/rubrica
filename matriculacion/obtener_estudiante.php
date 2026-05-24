<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	$estudiantes = new estudiantes();
	$estudiantes->code = $_POST["id_estudiante"];
	echo $estudiantes->obtenerEstudiante();
?>
