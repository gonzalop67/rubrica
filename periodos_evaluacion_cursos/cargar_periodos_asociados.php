<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.cursos.php");
	$cursos = new cursos();
	$id_curso = $_POST["id_curso"];
	echo $cursos->cargarPeriodosEvaluacionCurso($id_curso);
?>
