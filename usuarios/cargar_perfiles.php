<?php
require_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

$consulta = $db->consulta("SELECT * FROM sw_perfil ORDER BY pe_nombre ASC");

$cadena = "";

while ($perfil = $db->fetch_object($consulta)) {
    $cadena .= "<option value=\"$perfil->id_perfil\">$perfil->pe_nombre</option>\n";
}

echo $cadena;
