<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.sub_periodos.php");
	$sub_periodo = new sub_periodos();
	echo $sub_periodo->cargar_subperiodos_evaluacion();
?>