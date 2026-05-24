<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.aportes_evaluacion.php");
	$aporte_evaluacion = new aportes_evaluacion();
	$aporte_evaluacion->code = $_POST["id_aporte_evaluacion"];
	$aporte_evaluacion->id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	$aporte_evaluacion->ap_nombre = $_POST["ap_nombre"];
	$aporte_evaluacion->ap_abreviatura = $_POST["ap_abreviatura"];
	$aporte_evaluacion->ap_descripcion = $_POST["ap_descripcion"];
	$aporte_evaluacion->id_tipo_aporte = $_POST["ap_tipo"];
	$aporte_evaluacion->ap_fecha_apertura = $_POST["ap_fecha_apertura"];
	$aporte_evaluacion->ap_fecha_cierre = $_POST["ap_fecha_cierre"];
	$aporte_evaluacion->ap_ponderacion = $_POST["ap_ponderacion"];
	echo $aporte_evaluacion->actualizarAporteEvaluacion();
?>
