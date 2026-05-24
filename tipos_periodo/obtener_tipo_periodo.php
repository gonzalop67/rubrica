<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_periodo.php");
	$tipo_periodo = new tipos_periodo();
	$tipo_periodo->code = $_POST["id_tipo_periodo"];
	echo $tipo_periodo->obtenerTipoPeriodo();
?>