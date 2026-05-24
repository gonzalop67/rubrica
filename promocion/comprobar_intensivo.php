<?php
$id_paralelo = $_POST['id_paralelo'];
include("../scripts/clases/class.mysql.php");
$db = new MySQL();
$query = $db->consulta("SELECT es_intensivo FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND p.id_paralelo = $id_paralelo");
$registro = $db->fetch_object($query);
$es_intensivo = $registro->es_intensivo;
$data = array(
    'es_intensivo' => $es_intensivo
);
echo json_encode($array);
