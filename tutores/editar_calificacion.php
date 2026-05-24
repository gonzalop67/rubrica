<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.tutores.php");
	$tutor = new tutores();
	$tutor->id_estudiante = $_POST["id_estudiante"];
	$tutor->id_paralelo = $_POST["id_paralelo"];
    $tutor->id_escala_comportamiento = $_POST["id_escala_comportamiento"];
	$tutor->id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];
	$tutor->co_calificacion = $_POST["co_calificacion"];
	if (!$tutor->existeCalifComportamiento())
		echo $tutor->insertarCalifComportamiento();
	else
		echo $tutor->actualizarCalifComportamiento();
?>
