<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();
$consulta = $db->consulta("SELECT * FROM sw_sub_periodo_evaluacion ORDER BY pe_orden");
$num_total_registros = $db->num_rows($consulta);
$cadena = "";
if ($num_total_registros > 0) {
    while ($row = $db->fetch_assoc($consulta)) {
        $code = $row["id_sub_periodo_evaluacion"];
        $name = $row["pe_nombre"];
        $cadena .= "<br><input type=\"checkbox\" name=\"sub_periodos[]\" value=\"$code\"> $name\n";
    }
}
echo $cadena;