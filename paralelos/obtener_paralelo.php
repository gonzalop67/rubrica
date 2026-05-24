<?php
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

$id_paralelo = $_POST["id_paralelo"];

$qry = "SELECT id_paralelo, p.id_curso, es_figura, cu_nombre, pa_nombre, id_jornada FROM sw_paralelo p, sw_curso c, sw_especialidad e WHERE c.id_curso = p.id_curso AND e.id_especialidad = c.id_especialidad AND id_paralelo = $id_paralelo";

$consulta = $db->consulta($qry);

echo json_encode($db->fetch_assoc($consulta));
