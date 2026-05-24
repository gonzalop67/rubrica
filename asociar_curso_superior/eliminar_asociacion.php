<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.cursos.php");
	$curso = new cursos();
	$curso->code = $_POST["id"];
	echo $curso->eliminarCursoSuperior();
?>
