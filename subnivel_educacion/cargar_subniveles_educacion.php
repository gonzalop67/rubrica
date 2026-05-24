<?php
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

$sql = "SELECT ni.nombre AS nivel, sn.* FROM sw_sub_nivel_educacion sn, sw_nivel_educacion ni WHERE ni.id = sn.nivel_id ORDER BY orden";
$result = $db->consulta($sql);

$cadena = "";

if ($db->num_rows($result) > 0) {
    while ($row = $db->fetch_object($result)) {
        $cadena .= "<tr>\n";
        $cadena .= "<td>$row->id</td>\n";
        $cadena .= "<td>$row->nivel</td>\n";
        $cadena .= "<td>$row->nombre</td>\n";
        $es_bachillerato = $row->es_bachillerato == 1 ? 'Sí' : 'No';
        $cadena .= "<td>$es_bachillerato</td>\n";
        $cadena .= "<td>\n";
        $cadena .= "<div class='btn-group'>\n";
        $cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $row->id . ")\" data-toggle=\"modal\" data-target=\"#editarSubNivelModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
        $cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarSubNivelEducacion(" . $row->id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
        $cadena .= "</div>\n";
        $cadena .= "</td>\n";
        $cadena .= "</tr>\n";
    }
} else {
    $cadena .= "<tr>\n";
    $cadena .= "<td colspan='100%' align='center'>No se han definido Sub Niveles de Educación todavía...</td>\n";
    $cadena .= "</tr>\n";
}

echo $cadena;
