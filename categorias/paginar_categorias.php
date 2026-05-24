<?php
include_once "../scripts/clases/class.mysql.php";

$paginaActual = $_POST['partida'];
$id_usuario = $_POST['id_usuario'];

$db = new MySQL();

$consulta = $db->consulta("SELECT * FROM `sw_exam_category` WHERE id_usuario = $id_usuario");
$nroCategorias = $db->num_rows($consulta);

$nroLotes = 5;
$nroPaginas = ceil($nroCategorias / $nroLotes);

$lista = '';
$tabla = '';

if ($nroCategorias > 0) {
    if ($paginaActual == 1) {
        $lista = $lista . '<li><a href="#" disabled>‹</a></li>';
    } else {
        $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual - 1) . ',' . $id_usuario . ');">‹</a></li>';
    }

    for ($i = 1; $i <= $nroPaginas; $i++) {
        if ($i == $paginaActual) {
            $lista = $lista . '<li class="active"><a href="javascript:pagination(' . ',' . $id_usuario . ');">' . $i . '</a></li>';
        } else {
            $lista = $lista . '<li><a href="javascript:pagination(' . $i . ',' . $id_usuario . ');">' . $i . '</a></li>';
        }
    }

    if ($paginaActual == $nroPaginas) {
        $lista = $lista . '<li><a href="#" disabled>›</a></li>';
    } else {
        $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual + 1) . ',' . $id_usuario . ');">›</a></li>';
    }

    if ($paginaActual <= 1) {
        $limit = 0;
    } else {
        $limit = $nroLotes * ($paginaActual - 1);
    }

    $consulta = $db->consulta("SELECT * FROM `sw_exam_category` WHERE id_usuario = $id_usuario ORDER BY category ASC LIMIT $limit, $nroLotes");
    $num_total_registros = $db->num_rows($consulta);

    if ($num_total_registros > 0) {
        $contador = $limit;
        while($v = $db->fetch_object($consulta)) {
            $contador++;
            $tabla .= "<tr>\n";
            $code = $v->id;
            $category = $v->category;
            $exam_time_in_minutes = $v->exam_time_in_minutes;
            $tabla .= "<td>$contador</td>";
            $tabla .= "<td>$category</td>\n";
            $tabla .= "<td>$exam_time_in_minutes</td>\n";
            $tabla .= "<td>\n";
            $tabla .= "<div class=\"btn-group\">\n";
            $tabla .= "<a href=\"#\" class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $code . ")\" data-toggle=\"modal\" data-target=\"#editarCategoriaModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
            $tabla .= "<a href=\"#\" class=\"btn btn-danger\" onclick=\"eliminarCategoria(" . $code . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></a>\n";
            $tabla .= "</div>\n";
            $tabla .= "</td>\n";
            $tabla .= "</tr>\n";
        }
    }
} else {
    $tabla .= "<tr>\n";
    $tabla .= "<td colspan=\"4\" align=\"center\">A&uacute;n no se han definido categorias...</td>\n";
    $tabla .= "</tr>\n";
}

$array = array(
    0 => $tabla,
    1 => $lista
);

echo json_encode($array);
