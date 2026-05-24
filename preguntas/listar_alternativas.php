<?php
include_once "../scripts/clases/class.mysql.php";

$id_pregunta = $_POST['id_pregunta'];

$db = new MySQL();

$choices = $db->consulta("SELECT * FROM `sw_choices` WHERE id_question = $id_pregunta");
$num_total_registros = $db->num_rows($choices);

$tabla = "";

if ($num_total_registros > 0) {
    $contador = 0;
    while ($v = $db->fetch_object($choices)) {
        $contador++;
        $checked = ($v->is_correct == "0") ? "" : "checked";
        $tabla .= "<tr>\n";
        $tabla .= "<td>$contador</td>";
        $tabla .= "<td>" . htmlentities($v->text) . "</td>";
        $tabla .= "<td><input type=\"checkbox\" name=\"chkretirado_" . $contador . "\" $checked  onclick=\"set_correct_answer(this," . $v->id . ", $id_pregunta)\"></td>\n";
        $tabla .= "<td>\n";
        $tabla .= "<div class='btn-group'>\n";
        $tabla .= "<a href=\"#\" class=\"btn btn-warning\" onclick=\"obtenerAlternativa(" . $v->id . ")\" data-toggle=\"modal\" data-target=\"#editarAlternativaModal\" title=\"Editar Alternativa\"><span class=\"fa fa-pencil\"></span></a>\n";
        $tabla .= '<a href="#" class="btn btn-danger item-delete" data="' . $v->id . '" title="Eliminar"><span class="fa fa-trash"></span></a>';
        $tabla .= "</div>\n";
        $tabla .= "</td>\n";
        $tabla .= "<tr>\n";
    }
} else {
    $tabla .= "<tr>\n";
    $tabla .= "<td colspan=\"4\" align=\"center\">A&uacute;n no se han insertado alternativas...</td>\n";
    $tabla .= "</tr>\n";
}

echo $tabla;
