<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

session_start();
$id_usuario = $_SESSION["id_usuario"];
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$id_paralelo = $_POST["id_paralelo"];

$qry = "SELECT c.id_curso, 
                d.id_paralelo, 
                d.id_asignatura, 
                as_nombre, 
                es_figura, 
                cu_nombre, 
                pa_nombre 
        FROM sw_asignatura a, 
                sw_distributivo d, 
                sw_paralelo pa, 
                sw_curso c, 
                sw_especialidad e 
        WHERE a.id_asignatura = d.id_asignatura 
        AND d.id_paralelo = pa.id_paralelo 
        AND pa.id_curso = c.id_curso 
        AND c.id_especialidad = e.id_especialidad 
        AND d.id_usuario = $id_usuario
        AND d.id_paralelo = $id_paralelo 
        AND d.id_periodo_lectivo = $id_periodo_lectivo
        AND as_curricular = 1
        ORDER BY c.id_curso, pa.id_paralelo, as_nombre ASC";

$consulta = $db->consulta($qry);

$num_total_registros = $db->num_rows($consulta);

$cadena = "";
if ($num_total_registros > 0) {
        $contador = 0;
        while ($asignaturas = $db->fetch_assoc($consulta)) {
                $contador++;
                $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                $id_curso = $asignaturas["id_curso"];
                $codigo = $asignaturas["id_paralelo"];
                $id_asignatura = $asignaturas["id_asignatura"];
                $nombre = $asignaturas["as_nombre"];
                $figura = $asignaturas["es_figura"];
                $curso = $asignaturas["cu_nombre"] . " " . $asignaturas["es_figura"];
                $paralelo = $asignaturas["pa_nombre"];
                $cadena .= "<td width=\"5%\">$contador</td>\n";
                $cadena .= "<td width=\"39%\" align=\"left\">$nombre</td>\n";
                $cadena .= "<td width=\"32%\" align=\"left\">$curso</td>\n";
                $cadena .= "<td width=\"6%\" align=\"left\">$paralelo</td>\n";
                $cadena .= "<td width=\"18%\" align=\"center\">
                <a class=\"btn btn-primary btn-sm\" href=\"#\" onclick=\"seleccionarParalelo(" . $id_curso . "," . $codigo . "," . $id_asignatura . ",'" . $nombre . "','" . $curso . "','" . $paralelo . "')\">Seleccionar</a>
                </td>\n";
                $cadena .= "</tr>\n";
        }
} else {
        $cadena .= "<tr>\n";
        $cadena .= "<td>No se han asociado asignaturas a este docente...</td>\n";
        $cadena .= "</tr>\n";
}

echo $cadena;
