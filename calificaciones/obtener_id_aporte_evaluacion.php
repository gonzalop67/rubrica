<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.periodos_evaluacion.php");
	$periodos_evaluacion = new periodos_evaluacion();
	$periodos_evaluacion->id_paralelo = $_POST["id_paralelo"];
	$periodos_evaluacion->pe_principal = $_POST["pe_principal"];
	echo $periodos_evaluacion->obtenerIdAporteEvaluacionSupRemGracia();
?>
