<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$id_paralelo = $_POST["id_paralelo"];

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$qry = "SELECT c.id_curso FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND p.id_paralelo = $id_paralelo";
$consulta = $db->consulta($qry);
$registro = $db->fetch_object($consulta);
$id_curso = $registro->id_curso;

$qry = "SELECT * FROM sw_periodo_evaluacion_curso pc, sw_periodo_evaluacion pe WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion AND pc.id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo IN (1, 7, 8) AND id_curso = $id_curso";

$consulta = $db->consulta($qry);

$num_total_registros = $db->num_rows($consulta);

$cadena = "<option value=\"\">Seleccione...</option>";

if ($num_total_registros > 0) {
    while ($periodo_evaluacion = $db->fetch_object($consulta)) {
        $id_periodo_evaluacion = $periodo_evaluacion->id_periodo_evaluacion;
        $name = $periodo_evaluacion->pe_nombre;
        $cadena .= "<optgroup label='$name'>\n";
        $qry = "SELECT * FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion ORDER BY ap_orden";
        $consulta2 = $db->consulta($qry);
        while ($aporte_evaluacion = $db->fetch_object($consulta2)) {
            $id_aporte_evaluacion = $aporte_evaluacion->id_aporte_evaluacion;
            $name2 = $aporte_evaluacion->ap_nombre;
            $cadena .= "<option value=\"$id_periodo_evaluacion*$id_aporte_evaluacion\">$name2</option>";
        }
        $cadena .= "</optgroup>\n";
    }
}

echo $cadena;
