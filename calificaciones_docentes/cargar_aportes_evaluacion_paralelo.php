<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$id_paralelo = $_POST["id_paralelo"];
$id_asignatura = $_POST["id_asignatura"];
$id_periodo_lectivo = $_POST["id_periodo_lectivo"];

// Obtener id_tipo_asignatura
$qry = "SELECT id_tipo_asignatura FROM sw_asignatura WHERE id_asignatura = $id_asignatura";
$consulta = $db->consulta($qry);
$id_tipo_asignatura = $db->fetch_object($consulta)->id_tipo_asignatura;

$qry = "SELECT c.id_curso FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND p.id_paralelo = $id_paralelo";
$consulta = $db->consulta($qry);
$id_curso = $db->fetch_object($consulta)->id_curso;

$qry = "SELECT * FROM sw_periodo_evaluacion_curso pc, sw_periodo_evaluacion pe WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion AND pc.id_periodo_lectivo = $id_periodo_lectivo AND id_curso = $id_curso ORDER BY pc_orden";

$consulta = $db->consulta($qry);

$num_total_registros = $db->num_rows($consulta);

$cadena = "<option value=\"\">Seleccione...</option>";

if ($num_total_registros > 0) {
    while ($periodo_evaluacion = $db->fetch_object($consulta)) {
        $id_periodo_evaluacion = $periodo_evaluacion->id_periodo_evaluacion;
        $name = $periodo_evaluacion->pe_nombre;
        
        $qry = "SELECT DISTINCT ap.* FROM sw_aporte_evaluacion ap, sw_rubrica_evaluacion ru WHERE ap.id_aporte_evaluacion = ru.id_aporte_evaluacion AND id_periodo_evaluacion = $id_periodo_evaluacion AND id_tipo_asignatura = $id_tipo_asignatura ORDER BY ap_orden";

        // echo $qry;
        // die();

        $cadena .= "<optgroup label='$name'>\n";
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
