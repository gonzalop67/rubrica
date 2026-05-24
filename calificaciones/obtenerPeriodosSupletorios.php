<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

session_start();
$id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

$consulta = $db->consulta("SELECT * FROM sw_periodo_evaluacion WHERE id_tipo_periodo <> 1 AND id_periodo_lectivo = $id_periodo_lectivo");

$cadena = "";

while ($row = $db->fetch_object($consulta)) {
    $code = $row->id_periodo_evaluacion;
    $name = $row->pe_nombre;
    $cadena .= "<option value=\"$code\">$name</option>";
}

echo $cadena;
