<?php
require_once("clases/class.mysql.php");

session_start();

$db = new MySQL();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$cadena = "";

$consulta = $db->consulta("SELECT id_periodo_evaluacion, pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = " . $id_periodo_lectivo . " AND id_tipo_periodo IN (1, 5) ORDER BY id_periodo_evaluacion ASC");
$num_total_registros = $db->num_rows($consulta);
$cadena = "";
if ($num_total_registros > 0) {
    while ($periodos_evaluacion = $db->fetch_assoc($consulta)) {
        $code = $periodos_evaluacion["id_periodo_evaluacion"];
        $name = $periodos_evaluacion["pe_nombre"];
        $cadena .= "<optgroup label='$name'>\n";
        $consulta2 = $db->consulta("SELECT id_aporte_evaluacion, id_tipo_aporte, ap_nombre FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $code);
        while ($aporte_evaluacion = $db->fetch_assoc($consulta2)) {
            if ($aporte_evaluacion["id_tipo_aporte"] == 1) {
                $code2 = $aporte_evaluacion["id_aporte_evaluacion"];
                $name2 = $aporte_evaluacion["ap_nombre"];
                $cadena .= "<option value=\"$code2\">$name2</option>";
            }
        }
        $cadena .= "</optgroup>\n";
    }
}
echo $cadena;
