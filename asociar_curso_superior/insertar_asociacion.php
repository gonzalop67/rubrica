<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.cursos.php");
	session_start();
	$curso = new cursos();
	$curso->id_curso_inferior = $_POST["id_curso"];
	$curso->id_curso_superior = $_POST["id_curso_superior"];
	$curso->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $curso->asociarCursoSuperior();
?>
