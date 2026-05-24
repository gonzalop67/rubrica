<?php
include("../scripts/clases/class.mysql.php");
include("../scripts/clases/class.horarios.php");
$horario = new horarios();
$id_paralelo = $_POST["id_paralelo"];
$id_asignatura = $_POST["id_asignatura"];
$id_dia_semana = $_POST["id_dia_semana"];
$id_hora_clase = $_POST["id_hora_clase"];
$id_horario_def = $_POST["id_horario_def"];
//consultar si ya existe asociada una asignatura...
if ($horario->comprobarCruceDeHorario($id_paralelo, $id_asignatura, $id_dia_semana, $id_hora_clase, $id_horario_def) == 2) {
	echo json_encode(array('error' => true, 'errorno' => 2));
} else if ($horario->comprobarCruceDeHorario($id_paralelo, $id_asignatura, $id_dia_semana, $id_hora_clase, $id_horario_def) == 1) {
	echo json_encode(array('error' => true, 'errorno' => 1));
} else {
	echo json_encode(array('error' => false, 'errorno' => 0));
}
