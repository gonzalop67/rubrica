<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.valores_mes.php");
	$valor = new valores_mes();
	echo $valor->listarValoresMes();
?>
