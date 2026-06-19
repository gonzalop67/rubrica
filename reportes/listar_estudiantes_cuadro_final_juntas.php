<?php

function truncar($numero, $digitos)
{
    $truncar = pow(10, $digitos);
    return intval($numero * $truncar) / $truncar;
}


$cadena = "";
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$id_paralelo = $_POST["id_paralelo"];
session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$consulta = $db->consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, dg_abreviatura, es_retirado FROM sw_estudiante e, sw_def_genero dg, sw_estudiante_periodo_lectivo p WHERE dg.id_def_genero = e.id_def_genero AND e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");

$num_total_registros = $db->num_rows($consulta);

if ($num_total_registros > 0) {
    $contador = 0;
    while ($paralelo = $db->fetch_object($consulta)) {
        $id_estudiante = $paralelo->id_estudiante;
        $apellidos = $paralelo->es_apellidos;
        $nombres = $paralelo->es_nombres;
        $retirado = $paralelo->es_retirado;
        $terminacion = ($paralelo->dg_abreviatura == "M") ? "O" : "A";

        $contador++;

        $cadena .= "<tr>\n";
        // $fondolinea = ($contador % 2 == 0) ? "#ffffff" : "#d3d3d3"; // Alternar color de fondo
        // $cadena .= "<td style=\"background:$fondolinea;\">$contador</td>\n";
        $cadena .= "<td width=\"50px\" align=\"center\">$contador</td>\n"; // Número de fila
        $cadena .= "<td width=\"50px\" align=\"center\">$id_estudiante</td>\n";
        $cadena .= "<td class='sticky' width=\"150px\">" . $apellidos . " " . $nombres . "</td>\n";

        $asignaturas = $db->consulta("SELECT a.id_asignatura, as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND a.id_tipo_asignatura = 1 AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");

        $contNoAprobadas = 0;
        $contSupletorios = 0;

        while ($asignatura = $db->fetch_assoc($asignaturas)) {
            $id_asignatura = $asignatura["id_asignatura"];

            $qry = $db->consulta("SELECT calcular_promedio_periodo_lectivo($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
            $registro = $db->fetch_assoc($qry);
            $promedio_anual = $registro["promedio"];

            if ($promedio_anual <= 4) {
                $contNoAprobadas++;
            } elseif ($promedio_anual > 4 && $promedio_anual < 7) {
                $query = $db->consulta("SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura, 2) AS examen_supletorio");
                $registro = $db->fetch_object($query);
                $examen_supletorio = $registro->examen_supletorio;

                if ($examen_supletorio >= 7) {
                    // $puntaje_final = 7;
                    $qry = "SELECT nota_final FROM sw_escala_supletorios WHERE $examen_supletorio >= nota_minima AND $examen_supletorio <= nota_maxima";
                    $examen_supletorio = $db->consulta($qry);
                    if ($db->num_rows($examen_supletorio) > 0) {
                        $promedio_anual = $db->fetch_object($examen_supletorio)->nota_final;
                    }
                } else {
                    $contSupletorios++;
                }
            }

            $promedio_anual = $promedio_anual == 0 ? "" : substr($promedio_anual, 0, strpos($promedio_anual, '.') + 3);

            $color = "#ffffff";

            if ($promedio_anual != "") {
                if ($promedio_anual <= 4) {
                    $color = "#FF4B4B"; // Rojo para no aprobadas
                } elseif ($promedio_anual > 4 && $promedio_anual < 7) {
                    $color = "#ffff00"; // Naranja para supletorios
                }
            }

            $cadena .= "<td align=\"center\" width=\"150px\" style=\"background:$color;\">$promedio_anual</td>\n";
        }

        $observacion = "";
        $color = "#666";

        if ($contNoAprobadas > 0) {
            $observacion = "NO APRUEBA";
            $color = "#FF4B4B";
        } else {
            if ($contSupletorios > 0) {
                $observacion = "SUPLETORIO";
                $color = "#ffff00";
            } else {
                $observacion = "APRUEBA";
                $color = "#88dc65";
            }
        }

        $cadena .= "<td width=\"150px\" style=\"background:$color;\">$observacion</td>\n"; // Observaciones

        $cadena .= "</tr>\n";
    }
}

echo $cadena;
