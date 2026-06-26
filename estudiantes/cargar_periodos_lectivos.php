<?php
require_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

$meses_abrev = array(0, "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
$consulta = $db->consulta("SELECT * FROM sw_ofertas_educativas ORDER BY orden");
$num_total_registros = $db->num_rows($consulta);
$cadena = "";
if ($num_total_registros > 0) {
    while ($oferta = $db->fetch_assoc($consulta)) {
        $code = $oferta["id"];
        $name = $oferta["nombre"];
        $cadena .= "<optgroup label='$name'>\n";
        $consulta2 = $db->consulta("SELECT p.*, pe_descripcion FROM sw_periodo_lectivo p, sw_periodo_estado pe WHERE pe.id_periodo_estado = p.id_periodo_estado AND pe_estado = 'A' AND id_modalidad = $code ORDER BY pe_fecha_inicio DESC");
        //$consulta2 = $db->consulta("SELECT p.*, pe_descripcion FROM sw_periodo_lectivo p, sw_periodo_estado pe WHERE pe.id_periodo_estado = p.id_periodo_estado AND id_modalidad = $code ORDER BY pe_fecha_inicio DESC");
        while ($periodo_lectivo = $db->fetch_assoc($consulta2)) {
            $code2 = $periodo_lectivo["id_periodo_lectivo"];
            $fecha_inicial = explode("-", $periodo_lectivo["pe_fecha_inicio"]);
            $fecha_final = explode("-", $periodo_lectivo["pe_fecha_fin"]);
            $name2 = $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0] . " [" . $periodo_lectivo["pe_descripcion"] . "]";
            $cadena .= "<option value=\"$code2\">$name2</option>";
        }
        $cadena .= "</optgroup>\n";
    }
}
echo $cadena;