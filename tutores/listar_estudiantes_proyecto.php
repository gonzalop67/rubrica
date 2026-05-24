<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$id_paralelo = $_POST["id_paralelo"];
$id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];

$qry = "SELECT e.id_estudiante,
			   e.es_apellidos, 
			   e.es_nombres, 
			   es_retirado 
          FROM `sw_estudiante` e,
               `sw_estudiante_periodo_lectivo` ep 
         WHERE e.id_estudiante = ep.id_estudiante 
           AND ep.id_paralelo = $id_paralelo 
         ORDER BY es_apellidos, es_nombres";
$estudiantes = $db->consulta($qry);
$num_total_registros = $db->num_rows($estudiantes);

$cadena = "<table id=\"tabla_calificaciones\" class=\"table table-striped table-hover fuente8\">\n";
$cadena .= "<thead>\n";
$cadena .= "<tr>\n";
$cadena .= "<th>#</th>\n";
$cadena .= "<th>Id</th>\n";
$cadena .= "<th>Nómina</th>\n";
$cadena .= "<th>Nota</th>\n";
$cadena .= "</tr>\n";
$cadena .= "</thead>\n";
$cadena .= "<tbody>\n";

if ($num_total_registros > 0) {
    $contador = 0;
    while ($paralelos = $db->fetch_assoc($estudiantes)) {
        $contador++;
        $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
        $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
        $id_estudiante = $paralelos["id_estudiante"];
        $apellidos = $paralelos["es_apellidos"];
        $nombres = $paralelos["es_nombres"];
        $cadena .= "<td class='text-left' style='width: 60px !important'>$contador</td>\n";
        $cadena .= "<td class='text-left' style='width: 60px !important'>$id_estudiante</td>\n";
        $cadena .= "<td class='text-left' style='width: 300px !important'>" . $apellidos . " " . $nombres . "</td>\n";

        $aporte_evaluacion = $db->consulta("SELECT calificacion 
                                              FROM sw_notas_proyectos 
                                             WHERE id_estudiante = $id_estudiante 
                                               AND id_paralelo = $id_paralelo 
                                               AND id_aporte_evaluacion = $id_aporte_evaluacion");

        $num_total_registros = $db->num_rows($aporte_evaluacion);

        if ($num_total_registros > 0) {
            $calificacion_proyecto = $db->fetch_assoc($aporte_evaluacion);
            $calificacion = $calificacion_proyecto["calificacion"];
        } else {
            $calificacion = "";
        }

        $cadena .= "<td class='text-left'><input type=\"text\" class=\"inputPequenio\" id=\"puntaje_" . $contador . "\" value=\"$calificacion\"";
        $qry = $db->consulta("SELECT ac.ap_estado
                                   FROM sw_aporte_evaluacion a,
                                        sw_aporte_paralelo_cierre ac,
                                        sw_curso c,
                                        sw_paralelo p
                                  WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion
                                    AND c.id_curso = p.id_curso
                                    AND ac.id_paralelo = p.id_paralelo
                                    AND p.id_paralelo = $id_paralelo 
                                    AND a.id_aporte_evaluacion = $id_aporte_evaluacion");
        $aporte = $db->fetch_assoc($qry);
        $estado_aporte = $aporte["ap_estado"];
        if ($estado_aporte == 'A') {
            $cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_aporte_evaluacion . ", " . $calificacion . ")\" /></td>\n";
        } else {
            $cadena .= " disabled /></td>\n";
        }

        $cadena .= "</tr>\n";
    }
}

$cadena .= "</tbody>\n";
$cadena .= "</table>";

echo $cadena;
