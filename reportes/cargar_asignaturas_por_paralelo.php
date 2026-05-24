<?php
$cadena = "";
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();
$id_paralelo = $_POST["id_paralelo"];
$cadena .= "<th width=\"50px\">Nro.</th>\n";
$cadena .= "<th width=\"50px\">Id</th>";
$cadena .= "<th class='sticky' width=\"150px\">Apellidos y Nombres</th>\n";
$consulta = $db->consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo AND id_tipo_asignatura = 1 ORDER BY ac_orden");
if ($db->num_rows($consulta) > 0) {
    while ($fila = $db->fetch_array($consulta)) {
        $cadena .= "<th width=\"150px\">" . $fila["as_nombre"] . "</th>\n";
    }
} else {
    $cadena .= "<th>No existen asignaturas asociadas a este paralelo</th>";
}
$cadena .= "<th width=\"150px\">Observaciones</th>\n";
echo $cadena;
?>