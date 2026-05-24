<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.sub_periodos.php");
	$sub_periodo = new sub_periodos();
    $sub_periodo->pe_nombre = $_POST["nombre"];
    $sub_periodo->pe_abreviatura = $_POST["abreviatura"];
    $sub_periodo->id_tipo_periodo = $_POST["tipo_periodo"];
	echo $sub_periodo->insertarSubPeriodo();
?>