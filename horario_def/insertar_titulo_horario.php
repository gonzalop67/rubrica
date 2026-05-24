<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horarios.php");
	$ho_titulo = $_POST["ho_titulo"];
	$fecha_inicial = $_POST["fecha_inicial"];
	$fecha_final = $_POST["fecha_final"];
	$id_periodo_lectivo = $_POST["id_periodo_lectivo"];
	$datos = [
		'ho_titulo'          => $ho_titulo,
		'fecha_inicial'      => $fecha_inicial,
		'fecha_final'        => $fecha_final,
		'id_periodo_lectivo' => $id_periodo_lectivo
	];
	$horarios = new horarios();
	echo $horarios->insertarTituloHorario($datos);
?>
