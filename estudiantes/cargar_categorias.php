<?php
include_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

session_start();

$id_estudiante = $_POST['id_estudiante'];
$id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

if (isset($_SESSION["end_time"])) {
    unset($_SESSION["end_time"]);
}

// Consultar el id_paralelo correspondiente al id_estudiante
$consulta = $db->consulta("SELECT id_paralelo FROM sw_estudiante_periodo_lectivo WHERE id_estudiante = $id_estudiante AND id_periodo_lectivo = $id_periodo_lectivo");

$record = $db->fetch_object($consulta);
$id_paralelo = $record->id_paralelo;

$consulta = $db->consulta("SELECT ec.id, category FROM sw_exam_category ec, sw_cuestionario_paralelo cp WHERE ec.id = cp.id_category AND id_paralelo = $id_paralelo");

$lista = "";
while ($v = $db->fetch_object($consulta)) {
    $lista .= "<input type=\"button\" class=\"btn btn-success form-control\" value=\"" . $v->category . "\" style=\"margin-top: 10px; background-color: blue; color: white\" onclick=\"set_exam_type_session(" . $v->id . ")\">\n";
}

echo $lista;