<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

// variables POST
$id_paralelo = $_POST["id_paralelo"];

// variables session
session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$sql = "SELECT e.id_estudiante, es_apellidos, es_nombres, es_retirado, es_cedula FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND id_periodo_lectivo = $id_periodo_lectivo AND id_paralelo = $id_paralelo AND activo = 1 ORDER BY es_apellidos, es_nombres ASC";

$consulta = $db->consulta($sql);

$num_total_registros = $db->num_rows($consulta);

$cadena = "";
$qry = "";

if ($num_total_registros > 0) {
    $contador = 0;
    while ($estudiante = $db->fetch_object($consulta)) {
        $contador++;
        $id_estudiante = $estudiante->id_estudiante;
        $nombre_completo = $estudiante->es_apellidos . " " . $estudiante->es_nombres;

        // Cálculo del promedio general del sub periodo de cada estudiante
        $qry = "SELECT calcular_promedio_general(" . $id_periodo_lectivo . "," . $id_estudiante . "," . $id_paralelo . ") AS promedio_general";
        $query = $db->consulta($qry);
        $promedio = $db->fetch_object($query)->promedio_general;

        $promedio_general = ($promedio == 0) ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);

        // Obtención de la escala cualitativa del promedio general
        $qry = "SELECT ec_cualitativa FROM sw_escala_calificaciones WHERE $promedio >= ec_nota_minima AND $promedio <= ec_nota_maxima AND id_periodo_lectivo = $id_periodo_lectivo";

        $query = $db->consulta($qry);
        $escala_cualitativa = $db->fetch_object($query)->ec_cualitativa;

        $cadena .= "<tr>\n";
        $cadena .= "<td>$contador</td>\n";
        $cadena .= "<td>$id_estudiante</td>\n";
        $cadena .= "<td>$nombre_completo</td>\n";
        $cadena .= "<td>$promedio_general</td>\n";
        $cadena .= "<td>$escala_cualitativa</td>\n";
        $cadena .= "<td><a href='#' class='btn btn-success' onclick='seleccionarEstudiante($id_estudiante)'>Seleccionar</a></td>\n";
        $cadena .= "</tr>\n";
    }
} else {
    $cadena .= "<tr>\n";
    $cadena .= "<td class='text-center text-danger' colspan='6'>No se han registrado estudiantes en este paralelo...</td>\n";
    $cadena .= "</tr>\n";
}

echo $cadena;
