<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.cursos.php");
	$curso = new cursos();
	$curso->code = $_POST["id_curso"];
	$curso->id_especialidad = $_POST["id_especialidad"];
	$curso->cu_nombre = $_POST["cu_nombre"];
	$curso->cu_abreviatura = $_POST["cu_abreviatura"];
	$curso->cu_shortname = $_POST["cu_shortname"];
	$curso->bach_tecnico = $_POST["bach_tecnico"];
	$curso->es_intensivo = $_POST["es_intensivo"];
	$curso->es_fin_subnivel = $_POST["es_fin_subnivel"];
	echo $curso->actualizarCurso();
?>
