<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.cursos.php");
	session_start();
	$cursos = new cursos();
	$cursos->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $cursos->cargarCursos();
?>
