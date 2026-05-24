<?php
	session_start();
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.estudiantes.php");
	$estudiantes = new estudiantes();
	$patron = $_POST["valor"];
	echo $estudiantes->buscarEstudiantes($patron);
?>
