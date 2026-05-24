<?php 
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();
$query = "SELECT * FROM sw_nivel_educacion ORDER BY id ASC";
$consulta = $db->consulta($query);
$cadena = "";
$contador = 0;
while ($row = $db->fetch_object($consulta)) {
    $contador++;
    $cadena .= "<tr>\n";
    $cadena .= "<td>$contador</td>\n";
    $cadena .= "<td>$row->nombre</td>\n";
    $cadena .= "<td>\n";
    $cadena .= "<div class='btn-group'>\n";
    $cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $row->id . ")\" data-toggle=\"modal\" data-target=\"#editarNivelModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
    $cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarNivelEducacion(" . $row->id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
    $cadena .= "</div>\n";
    $cadena .= "</td>\n";
    $cadena .= "</tr>\n";
}
echo $cadena;
?>