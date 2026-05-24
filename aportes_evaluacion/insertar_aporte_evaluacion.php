<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.aportes_evaluacion.php");
	session_start();
	$aporte_evaluacion = new aportes_evaluacion();
	$aporte_evaluacion->id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
	$aporte_evaluacion->ap_nombre = $_POST["ap_nombre"];
	$aporte_evaluacion->ap_abreviatura = $_POST["ap_abreviatura"];
	$aporte_evaluacion->ap_descripcion = $_POST["ap_descripcion"];
	$aporte_evaluacion->id_tipo_aporte = $_POST["ap_tipo"];
	$aporte_evaluacion->ap_fecha_apertura = $_POST["ap_fecha_apertura"];
	$aporte_evaluacion->ap_fecha_cierre = $_POST["ap_fecha_cierre"];
	$aporte_evaluacion->ap_ponderacion = $_POST["ap_ponderacion"];
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	echo $aporte_evaluacion->insertarAporteEvaluacion($id_periodo_lectivo);
?>
