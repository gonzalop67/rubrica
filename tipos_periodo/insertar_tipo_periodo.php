<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tipos_periodo.php");
	$tipo_periodo = new tipos_periodo();
    $tipo_periodo->tp_descripcion = $_POST["nombre"];
    $tipo_periodo->tp_slug = $_POST["slug"];
	echo $tipo_periodo->insertarTipoPeriodo();
?>