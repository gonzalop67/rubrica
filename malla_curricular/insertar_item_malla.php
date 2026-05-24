<?php
	include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.mallas.php");
    session_start();
	$malla = new mallas();
	$malla->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$malla->id_curso = $_POST["id_curso"];
	$malla->id_asignatura = $_POST["id_asignatura"];
	$malla->ma_horas_presenciales = $_POST["ma_horas_presenciales"];
	$malla->ma_horas_autonomas = $_POST["ma_horas_autonomas"];
    $malla->ma_horas_tutorias = $_POST["ma_horas_tutorias"];
    $malla->ma_subtotal = $malla->ma_horas_presenciales + $malla->ma_horas_tutorias;
	echo $malla->insertarMalla();
?>
