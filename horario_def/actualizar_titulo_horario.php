<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horarios.php");
    $id_horario_def = $_POST["id_horario_def"];
	$ho_titulo = $_POST["ho_titulo"];
	$fecha_inicial = $_POST["fecha_inicial"];
	$fecha_final = $_POST["fecha_final"];
	$status = $_POST["status"];
	$datos = [
        'id_horario_def' => $id_horario_def,
		'ho_titulo'      => $ho_titulo,
		'fecha_inicial'  => $fecha_inicial,
		'fecha_final'    => $fecha_final,
		'status'         => $status
	];
	$horarios = new horarios();
	echo $horarios->actualizarTituloHorario($datos);
?>