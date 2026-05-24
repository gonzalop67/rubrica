<?php
	sleep(1);
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_periodo.php");
	$tipos_periodo = new tipos_periodo();
	$tipos_periodo->code = $_POST["id_tipo_periodo"];
	echo $tipos_periodo->eliminarTipoPeriodo();
?>