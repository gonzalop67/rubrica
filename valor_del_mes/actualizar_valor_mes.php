<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.valores_mes.php");
	session_start();
	$valor = new valores_mes();
	$valor->vm_valor = $_POST["vm_valor"];
	$valor->code = $_POST["id_valor_mes"];
	echo $valor->actualizarValorMes();
?>
