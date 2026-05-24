<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.sub_periodos.php");
	$sub_periodo = new sub_periodos();
	$sub_periodo->code = $_POST["id_sub_periodo_evaluacion"];
	$sub_periodo->pe_nombre = $_POST["pe_nombre"];
	$sub_periodo->pe_abreviatura = $_POST["pe_abreviatura"];
	$sub_periodo->id_tipo_periodo = $_POST["id_tipo_periodo"];
	echo $sub_periodo->actualizarSubPeriodo();
?>