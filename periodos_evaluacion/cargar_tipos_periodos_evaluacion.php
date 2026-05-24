<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.combos.php");
	$combo = new selects();
	echo $combo->cargarTiposPeriodoEvaluacion();
?>
