<?php
include("../scripts/clases/class.mysql.php");
// include("../scripts/clases/class.paralelos.php");
// $paralelo = new paralelos();
$id_paralelo = $_POST["id_paralelo"];
// echo $paralelo->obtenerIdCurso($id_paralelo);

$db = new MySQL();

$consulta = $db->consulta("SELECT cu.id_curso FROM sw_curso cu, sw_paralelo pa WHERE cu.id_curso = pa.id_curso AND pa.id_paralelo = $id_paralelo");
$resultado = $db->fetch_object($consulta);
echo $resultado->id_curso;
