<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.cursos.php");

	session_start();

	$curso = new cursos();
	$curso->id_especialidad = $_POST["id_especialidad"];
	$curso->cu_nombre = $_POST["cu_nombre"];
	$curso->cu_abreviatura = $_POST["cu_abreviatura"];
	$curso->cu_shortname = $_POST["cu_shortname"];
	$curso->bach_tecnico = $_POST["es_bach_tecnico"];
	$curso->es_intensivo = $_POST["es_intensivo"];
	$curso->es_fin_subnivel = $_POST["es_fin_subnivel"];
	$curso->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

	echo $curso->insertarCurso();
?>
