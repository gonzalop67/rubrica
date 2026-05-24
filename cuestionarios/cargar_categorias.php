<?php
include_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

$id_usuario = $_POST['id_usuario'];

session_start();

if (isset($_SESSION["end_time"])) {
    unset($_SESSION["end_time"]);
}

$consulta = $db->consulta("SELECT * FROM sw_exam_category WHERE id_usuario = $id_usuario ORDER BY category ASC");

$lista = "";
while ($v = $db->fetch_object($consulta)) {
    $lista .= "<input type=\"button\" class=\"btn btn-success form-control\" value=\"" . $v->category . "\" style=\"margin-top: 10px; background-color: blue; color: white\" onclick=\"set_exam_type_session(" . $v->id . ")\">\n";
}

echo $lista;
