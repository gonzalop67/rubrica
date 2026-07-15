<?php
require_once("scripts/clases/class.mysql.php");

$db = new MySQL();

$id_paralelo = $_POST['id_paralelo'];
$id_periodo_lectivo = $_POST['id_periodo_lectivo'];

// Obtener el nombre del tutor
$consulta = $db->consulta("SELECT us_titulo, us_fullname FROM sw_paralelo_tutor pt, sw_paralelo p, sw_usuario u WHERE p.id_paralelo = pt.id_paralelo AND u.id_usuario = pt.id_usuario AND pt.id_periodo_lectivo = $id_periodo_lectivo AND pt.id_paralelo = $id_paralelo");

$record = $db->fetch_assoc($consulta);
$tutor = $record["us_titulo"] . " " . $record["us_fullname"];

// Obtener el nombre del paralelo
$query = "SELECT CONCAT(cu_abreviatura, ' ', pa_nombre, ' ', es_abreviatura) AS paralelo FROM sw_paralelo p, sw_curso c, sw_especialidad e, sw_jornada j WHERE c.id_curso = p.id_curso AND e.id_especialidad = c.id_especialidad AND j.id_jornada = p.id_jornada AND p.id_paralelo = $id_paralelo";

$consulta = $db->consulta($query);
$registro = $db->fetch_assoc($consulta);
$nom_paralelo = $registro["paralelo"];

// Obtener el numero de mujeres
$query = "SELECT id_def_genero, COUNT(*) AS numero FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = $id_paralelo AND activo = 1 AND es_retirado = 'N' AND id_def_genero = 1 GROUP BY id_def_genero";

$consulta = $db->consulta($query);
$registro = $db->fetch_assoc($consulta);
$numero_mujeres = $registro["numero"];

// Obtener el numero de hombres
$query = "SELECT id_def_genero, COUNT(*) AS numero FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = $id_paralelo AND activo = 1 AND es_retirado = 'N' AND id_def_genero = 2 GROUP BY id_def_genero";
$consulta = $db->consulta($query);
$registro = $db->fetch_assoc($consulta);
$numero_hombres = $registro["numero"];

$datos = [
    'paralelo' => $nom_paralelo,
    'tutor' => $tutor,
    'numero_mujeres' => $numero_mujeres,
    'numero_hombres' => $numero_hombres
];

echo json_encode($datos);