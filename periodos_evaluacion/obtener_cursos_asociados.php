<?php
require_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

$consulta = $db->consulta("SELECT * FROM sw_periodo_evaluacion_curso WHERE id_periodo_evaluacion = $_POST[id_periodo_evaluacion]");
$records = [];

while ($row = $db->fetch_object($consulta)) {
    array_push($records, $row->id_curso);
}

echo json_encode($records);