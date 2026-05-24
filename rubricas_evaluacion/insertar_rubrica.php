<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.rubricas_evaluacion.php");
	$rubrica_evaluacion = new rubricas_evaluacion();
	$rubrica_evaluacion->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	$rubrica_evaluacion->id_tipo_asignatura = $_POST["id_tipo_asignatura"];
	$rubrica_evaluacion->ru_nombre = $_POST["ru_nombre"];
	$rubrica_evaluacion->ru_abreviatura = $_POST["ru_abreviatura"];
	$rubrica_evaluacion->ru_descripcion = $_POST["ru_descripcion"];
	$rubrica_evaluacion->ru_ponderacion = $_POST["ru_ponderacion"];
	echo $rubrica_evaluacion->insertarRubricaEvaluacion();
?>
