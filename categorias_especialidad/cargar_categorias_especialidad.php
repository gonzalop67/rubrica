<?php
include_once "../scripts/clases/class.mysql.php";
$db = new MySQL();
$qryString = "SELECT * FROM sw_categoria_especialidad ORDER BY nombre ASC";
$query = $db->consulta($qryString);
$num_registros = $db->num_rows($query);
$cadena = "";
if ($num_registros > 0) {
    $contador = 0;
    while ($categoria = $db->fetch_object($query)) {
        $contador++;
        $cadena .= "<tr>\n";
        $cadena .= "<td>$contador</td>\n";
        $cadena .= "<td>$categoria->id_categoria</td>\n";
        $cadena .= "<td>$categoria->nombre</td>\n";
        $cadena .= "<td>\n";
        $cadena .= "<div class=\"btn-group\">\n";
        $cadena .= "<a href=\"#\" class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $categoria->id_categoria . ")\" data-toggle=\"modal\" data-target=\"#editarCategoriaModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
        $cadena .= "<a href=\"#\" class=\"btn btn-danger\" onclick=\"eliminarCategoria(" . $categoria->id_categoria . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></a>\n";
        $cadena .= "</div>\n";
        $cadena .= "</td>\n";
        $cadena .= "</tr>\n";
    }
} else {
    $cadena .= "<tr>\n";
    $cadena .= "<td colspan='100%' class='text-center'>Aún no se han registrado Categorías de Especialidad...</td>\n";
    $cadena .= "</tr>\n";
}
echo $cadena;
