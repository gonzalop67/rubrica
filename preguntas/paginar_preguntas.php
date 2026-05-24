<?php
include_once "../scripts/clases/class.mysql.php";

$id_category = $_POST['id_categoria'];
$paginaActual = $_POST['partida'];

$db = new MySQL();

$preguntas = $db->consulta("SELECT * FROM `sw_questions` WHERE id_category = $id_category");
$nroPreguntas = $db->num_rows($preguntas);

$nroLotes = 2;
$nroPaginas = ceil($nroPreguntas / $nroLotes);

$lista = '';
$tabla = '';

if ($nroPreguntas > 0) {
    if ($paginaActual == 1) {
        $lista = $lista . '<li><a href="javascript:;" disabled>‹</a></li>';
    } else {
        $lista = $lista . '<li><a href="javascript:paginarPreguntas(' . ($paginaActual - 1) . ',' . $id_category . ');">‹</a></li>';
    }

    for ($i = 1; $i <= $nroPaginas; $i++) {
        if ($i == $paginaActual) {
            $lista = $lista . '<li class="active"><a href="javascript:paginarPreguntas(' . ',' . $id_category . ');">' . $i . '</a></li>';
        } else {
            $lista = $lista . '<li><a href="javascript:paginarPreguntas(' . $i . ',' . $id_category . ');">' . $i . '</a></li>';
        }
    }

    if ($paginaActual == $nroPaginas) {
        $lista = $lista . '<li><a href="javascript:;" disabled>›</a></li>';
    } else {
        $lista = $lista . '<li><a href="javascript:paginarPreguntas(' . ($paginaActual + 1) . ',' . $id_category . ');">›</a></li>';
    }

    if ($paginaActual <= 1) {
        $limit = 0;
    } else {
        $limit = $nroLotes * ($paginaActual - 1);
    }

    $preguntas = $db->consulta("SELECT * FROM `sw_questions` WHERE id_category = $id_category ORDER BY id ASC LIMIT $limit, $nroLotes");
    $num_total_registros = $db->num_rows($preguntas);

    if ($num_total_registros > 0) {
        $contador = $limit;
        while ($v = $db->fetch_object($preguntas)) {
            $contador++;
            $tabla .= "<tr>\n";
            $code = $v->id;
            $question_no = $v->question_no;
            $question = htmlspecialchars($v->question);
            $tabla .= "<td>$question_no</td>";
            $tabla .= "<td>$question</td>\n";
            $tabla .= "<td>\n";
            $tabla .= "<div class=\"btn-group\">\n";
            $tabla .= "<a href=\"#\" class=\"btn btn-warning\" onclick=\"obtenerPregunta(" . $v->id . ")\" data-toggle=\"modal\" data-target=\"#editarPreguntaModal\" title=\"Editar Pregunta\"><span class=\"fa fa-pencil\"></span></a>\n";
            $tabla .= "<a href=\"#\" class=\"btn btn-primary\" onclick=\"seleccionarPregunta(" . $v->id . ", " . $v->question_no . ")\" title=\"Listar Alternativas\"><span class=\"fa fa-list\"></span></a>\n";
            $tabla .= '<a href="#" class="btn btn-danger item-delete" data="' . $v->id . '" title="Eliminar"><span class="fa fa-trash"></span></a>';
            $tabla .= "</div>\n";
            $tabla .= "</td>\n";
            $tabla .= "</tr>\n";
        }
    }
} else {
    $tabla .= "<tr>\n";
    $tabla .= "<td colspan=\"3\" align=\"center\">A&uacute;n no se han insertado preguntas...</td>\n";
    $tabla .= "</tr>\n";
}

$array = array(
    0 => $tabla,
    1 => $lista
);

echo json_encode($array);
