<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

$sql = "SELECT * FROM sw_nivel_educacion ORDER BY orden";
$result = $db->consulta($sql);

$cadena = "";

while ($row = $db->fetch_object($result)) {
    $cadena .= "<option value='$row->id'>$row->nombre</option>\n";
}

echo $cadena;