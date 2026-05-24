<?php
include_once("../scripts/clases/class.mysql.php");
include_once("../scripts/clases/class.estudiantes.php");
session_start();
$estudiante = new estudiantes();
$estudiante->id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
$estudiante->id_paralelo = $_POST["id_paralelo"];
$estudiante->id_tipo_documento = $_POST["id_tipo_documento"];
$estudiante->id_def_genero = $_POST["id_def_genero"];
$estudiante->id_def_nacionalidad = $_POST["id_def_nacionalidad"];
$estudiante->es_apellidos = trim($_POST["es_apellidos"]);
$estudiante->es_nombres = trim($_POST["es_nombres"]);
$estudiante->es_cedula = trim($_POST["es_cedula"]);
$estudiante->es_email = strtolower(trim($_POST["es_email"]));
$estudiante->es_direccion = trim($_POST["es_direccion"]);
$estudiante->es_sector = trim($_POST["es_sector"]);
$estudiante->es_telefono = trim($_POST["es_telefono"]);
$estudiante->es_fec_nacim = trim($_POST["es_fec_nacim"]);
if ($estudiante->existeNombreEstudiante($estudiante->es_apellidos, $estudiante->es_nombres)) {
	//echo "Ya existe el Estudiante en la base de datos...";
	$datos = array(
		'titulo' => "Error!",
		'mensaje' => "Ya existe el estudiante en la base de datos...",
		'estado' => "error",
		'id_estudiante' => 0
	);
	//Seteamos el header de "content-type" como "JSON" para que jQuery lo reconozca como tal
	header('Content-Type: application/json');
	//Devolvemos el array pasado a JSON como objeto
	echo json_encode($datos, JSON_FORCE_OBJECT);
} else if ($estudiante->estudianteYaMatriculado($estudiante->es_apellidos, $estudiante->es_nombres, $estudiante->id_periodo_lectivo)) {
	//echo "Ya se matriculó al estudiante en el presente año lectivo...";
	$datos = array(
		'titulo' => "Error!",
		'mensaje' => "Ya está matriculado el estudiante en el presente periodo lectivo...",
		'estado' => "error",
		'id_estudiante' => 0
	);
	//Seteamos el header de "content-type" como "JSON" para que jQuery lo reconozca como tal
	header('Content-Type: application/json');
	//Devolvemos el array pasado a JSON como objeto
	echo json_encode($datos, JSON_FORCE_OBJECT);
} else {
	echo $estudiante->insertarEstudiante();
}
