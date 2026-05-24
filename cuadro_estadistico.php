<?php
require_once("scripts/clases/class.mysql.php");

$db = new MySQL();
session_start();

$id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
$id_jornada = $_POST['id_jornada'];

$cadena = "<table class='table table-striped'>\n";
$cadena .= "<thead style='background-color: #b2dafa'>\n";
$cadena .= "<tr>\n";
$cadena .= "<th style='border-bottom: 1px solid #000'>Paralelo</th>\n";
$cadena .= "<th style='border-bottom: 1px solid #000'>Tutor</th>\n";
$cadena .= "<th style='border-bottom: 1px solid #000'>Nro. Estudiantes</th>\n";
$cadena .= "<th style='border-bottom: 1px solid #000'>Nro. Mujeres</th>\n";
$cadena .= "<th style='border-bottom: 1px solid #000'>Nro. Hombres</th>\n";
$cadena .= "</tr>\n";
$cadena .= "</thead>\n";
$cadena .= "<tbody>\n";

$query = "SELECT ep.id_paralelo, 
				 CONCAT(cu_abreviatura, pa_nombre, ' ', es_abreviatura) AS paralelo, 
				 COUNT(*) AS numero
			FROM sw_estudiante_periodo_lectivo ep, 
				 sw_paralelo p, 
				 sw_curso c, 
				 sw_especialidad e, 
				 sw_jornada j 
		   WHERE p.id_paralelo = ep.id_paralelo 
             AND j.id_jornada = p.id_jornada 
             AND c.id_curso = p.id_curso 
             AND e.id_especialidad = c.id_especialidad 
             AND ep.activo = 1 
			 AND ep.id_periodo_lectivo = $id_periodo_lectivo 
			 AND p.id_jornada = $id_jornada
           GROUP BY ep.id_paralelo 
		   ORDER BY pa_orden";

$result = $db->consulta($query);

$suma_total = 0;
$suma_mujeres = 0;
$suma_hombres = 0;

if ($db->num_rows($result) > 0) {
    while ($dato = $db->fetch_assoc($result)) {
        $cadena .= "<tr>\n";
        $id_paralelo = $dato['id_paralelo'];
        $nombre_paralelo = $dato['paralelo'];

        $consulta = $db->consulta("SELECT us_titulo, us_fullname FROM sw_paralelo_tutor pt, sw_paralelo p, sw_usuario u WHERE p.id_paralelo = pt.id_paralelo AND u.id_usuario = pt.id_usuario AND pt.id_periodo_lectivo = $id_periodo_lectivo AND pt.id_paralelo = $id_paralelo");

        $record = $db->fetch_assoc($consulta);
        $tutor = $record["us_titulo"] . " " . $record["us_fullname"];

        $numero_total = $dato['numero'];
        $suma_total += $numero_total;

        // Obtener el numero de mujeres
        $query = "SELECT id_def_genero, COUNT(*) AS numero FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = $id_paralelo AND activo = 1 AND id_def_genero = 1 GROUP BY id_def_genero ORDER BY id_def_genero";
        $consulta = $db->consulta($query);
        $registro = $db->fetch_assoc($consulta);

        $numero_mujeres = $registro["numero"];
        $suma_mujeres += $numero_mujeres;

        // Obtener el numero de hombres
        $query = "SELECT id_def_genero, COUNT(*) AS numero FROM sw_estudiante_periodo_lectivo ep, sw_estudiante e WHERE e.id_estudiante = ep.id_estudiante AND ep.id_paralelo = $id_paralelo AND activo = 1 AND id_def_genero = 2 GROUP BY id_def_genero ORDER BY id_def_genero";
        $consulta = $db->consulta($query);
        $registro = $db->fetch_assoc($consulta);
        $numero_hombres = $registro["numero"];
        $suma_hombres += $numero_hombres;

        $cadena .= "<td>$nombre_paralelo</td>\n";
        $cadena .= "<td>$tutor</td>\n";
        $cadena .= "<td>$numero_total</td>\n";
        $cadena .= "<td>$numero_mujeres</td>\n";
        $cadena .= "<td>$numero_hombres</td>\n";
        $cadena .= "</tr>\n";
    }

    $cadena .= "</tbody>\n";
    $cadena .= "<tfoot style='background-color: #fdfd96; font-weight: bold;'>\n";
    $cadena .= "<tr>\n";
    $cadena .= "<td colspan='2' align='center'>TOTALES:</td>\n";
    $cadena .= "<td style='border-top: 1px solid #aaa'>$suma_total</td>\n";
    $cadena .= "<td style='border-top: 1px solid #aaa'>$suma_mujeres</td>\n";
    $cadena .= "<td style='border-top: 1px solid #aaa'>$suma_hombres</td>\n";
    $cadena .= "</tr>\n";
    $cadena .= "</tfoot>\n";
    $cadena .= "</table>\n";
} else {
    $cadena .= "<tr>\n";
    $cadena .= "<td colspan='5' align='center'>No se han definido paralelos y tutores todavía...</td>";
    $cadena .= "</tr>\n";
}

echo $cadena;
