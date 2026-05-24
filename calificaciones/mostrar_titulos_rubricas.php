<?php
	include_once("../scripts/clases/class.mysql.php");
	include_once("../scripts/clases/class.asignaturas.php");
	include_once("../scripts/clases/class.aportes_evaluacion.php");

	$aportes_evaluacion = new aportes_evaluacion();
	$alineacion = $_POST["alineacion"];
	$aportes_evaluacion->code = $_POST["id_aporte_evaluacion"];
	$aportes_evaluacion->id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	$aportes_evaluacion->id_asignatura = $_POST["id_asignatura"];
	$aportes_evaluacion->id_curso = $_POST["id_curso"];

	$asignaturas = new asignaturas();
	$asignatura = $asignaturas->obtenerAsignatura($_POST["id_asignatura"]);
	$aportes_evaluacion->id_tipo_asignatura = $asignatura->id_tipo_asignatura;

	session_start();
	echo $aportes_evaluacion->mostrarTitulosRubricas($alineacion, $_SESSION["id_periodo_lectivo"]);
?>
