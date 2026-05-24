<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.valores_mes.php");
	session_start();
	$valor = new valores_mes();
	$valor->vm_mes = $_POST["vm_mes"];
	$valor->vm_valor = $_POST["vm_valor"];
	$valor->id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
	echo $valor->insertarValorMes();
?>
