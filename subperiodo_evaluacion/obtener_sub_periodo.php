<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.sub_periodos.php");
	$sub_periodo = new sub_periodos();
	$sub_periodo->code = $_POST["id_sub_periodo_evaluacion"];
	echo $sub_periodo->obtenerSubPeriodo();
?>