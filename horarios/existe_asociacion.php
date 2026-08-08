<?php
include("../scripts/clases/class.mysql.php");
include("../scripts/clases/class.horarios.php");

// Cabecera obligatoria para que el navegador entienda que es un JSON real
header('Content-Type: application/json; charset=utf-8');

$horario = new horarios();

// Usamos el operador ?? para evitar errores si AJAX no envía algún campo
$id_paralelo    = $_POST["id_paralelo"] ?? 0;
$id_dia_semana  = $_POST["id_dia_semana"] ?? 0;
$id_hora_clase  = $_POST["id_hora_clase"] ?? "";
$id_horario_def = $_POST["id_horario_def"] ?? "";

// Consultar si ya existe asociada una asignatura
$existe = $horario->existeAsignaturaHoraClase($id_paralelo, $id_dia_semana, $id_hora_clase, $id_horario_def);

if ($existe) {
	echo json_encode(array('error' => true));
} else {
	echo json_encode(array('error' => false));
}
