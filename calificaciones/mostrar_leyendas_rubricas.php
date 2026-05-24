<?php
	include_once("../scripts/clases/class.mysql.php");
	include_once("../scripts/clases/class.asignaturas.php");
	include_once("../scripts/clases/class.aportes_evaluacion.php");

	$aportes_evaluacion = new aportes_evaluacion();
	$aportes_evaluacion->code = $_POST["id_aporte_evaluacion"];
	$aportes_evaluacion->id_asignatura = $_POST["id_asignatura"];
	$aportes_evaluacion->id_curso = $_POST["id_curso"];

	$asignaturas = new asignaturas();
	$asignatura = $asignaturas->obtenerAsignatura($_POST["id_asignatura"]);
	$aportes_evaluacion->id_tipo_asignatura = $asignatura->id_tipo_asignatura;

	echo $aportes_evaluacion->mostrarLeyendasRubricas("center");
?>
