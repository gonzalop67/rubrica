<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();
$consulta = $db->consulta("SELECT * FROM sw_sub_nivel_educacion ORDER BY orden");
$num_total_registros = $db->num_rows($consulta);
$cadena = "";
if ($num_total_registros > 0) {
    while ($row = $db->fetch_assoc($consulta)) {
        $code = $row["id_nivel_educacion"];
        $name = $row["nombre"];
        $cadena .= "<br><input type=\"checkbox\" name=\"niveles[]\" value=\"$code\"> $name\n";
    }
}
echo $cadena;
