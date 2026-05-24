<?php
	include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.horarios.php");
	$horario = new horarios();
	$id_paralelo = $_POST["id_paralelo"];
	$id_dia_semana = $_POST["id_dia_semana"];
	$id_hora_clase = $_POST["id_hora_clase"];
	$id_horario_def = $_POST["id_horario_def"];
	//consultar si ya existe asociada una asignatura...
	if ($horario->existeAsignaturaHoraClase($id_paralelo, $id_dia_semana, $id_hora_clase, $id_horario_def)) {
		echo json_encode(array('error' => true));
	} else {
		echo json_encode(array('error' => false));
	}
?>
