<?php
include("../scripts/clases/class.mysql.php");

$paginaActual = $_POST['partida'];
$id_periodo_lectivo = $_POST['id_periodo_lectivo'];

$db = new MySQL();

$query = "SELECT * FROM `sw_horario_def` WHERE id_periodo_lectivo = $id_periodo_lectivo";
$consulta = $db->consulta($query);
$nroHorarios = $db->num_rows($consulta);

$nroLotes = 5;
$nroPaginas = ceil($nroHorarios / $nroLotes);

$lista = '';
$tabla = '';

if ($paginaActual == 1) {
    $lista = $lista . '<li><a href="javascript:;" disabled>‹</a></li>';
} else {
    $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual - 1) . ',' . $id_periodo_lectivo . ');">‹</a></li>';
}

for ($i = 1; $i <= $nroPaginas; $i++) {
    if ($i == $paginaActual) {
        $lista = $lista . '<li class="active"><a href="javascript:pagination(' . $i . ',' . $id_periodo_lectivo . ');">' . $i . '</a></li>';
    } else {
        $lista = $lista . '<li><a href="javascript:pagination(' . $i . ',' . $id_periodo_lectivo . ');">' . $i . '</a></li>';
    }
}

if ($paginaActual == $nroPaginas) {
    $lista = $lista . '<li><a href="javascript:;" disabled>›</a></li>';
} else {
    $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual + 1) . ',' . $id_periodo_lectivo . ');">›</a></li>';
}

if ($paginaActual <= 1) {
    $limit = 0;
} else {
    $limit = $nroLotes * ($paginaActual - 1);
}

$consulta = $db->consulta("SELECT * FROM `sw_horario_def` WHERE id_periodo_lectivo = $id_periodo_lectivo ORDER BY fecha_inicial DESC LIMIT $limit, $nroLotes");
$num_total_registros = $db->num_rows($consulta);
if ($num_total_registros > 0) {
    $contador = $limit;
    while ($horario = $db->fetch_assoc($consulta)) {
        $contador++;
        $tabla .= "<tr>\n";
        $code = $horario["id_horario_def"];
        $titulo = $horario["ho_titulo"];
        $fecha_creacion = $horario["fecha_creacion"];
        $fecha_inicial = $horario["fecha_inicial"];
        $fecha_final = $horario["fecha_final"];
        $status = $horario["estado"];
        $estado = ($status == 1) ? 'ACTIVO' : 'INACTIVO';
        $tabla .= "<td>$contador</td>";
        $tabla .= "<td>$code</td>\n";
        $tabla .= "<td>$titulo</td>\n";
        $tabla .= "<td>$fecha_creacion</td>\n";
        $tabla .= "<td>$fecha_inicial</td>\n";
        $tabla .= "<td>$fecha_final</td>\n";
        $tabla .= "<td>$estado</td>\n";
        $tabla .= "<td>\n";
        $tabla .= "<div class=\"btn-group\">\n";
        $tabla .= "<a href=\"javascript:;\" class=\"btn btn-warning item-edit\" data=\"" . $code . "\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
        $tabla .= "<a href=\"javascript:;\" class=\"btn btn-danger item-delete\" data=\"" . $code . "\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></a>\n";
        $tabla .= "</div>\n";
        $tabla .= "</td>\n";
        $tabla .= "</tr>\n";
    }
} else {
    $tabla .= "<tr>\n";
    $tabla .= "<td colspan=\"8\" align='center'>A&uacute;n no se han definido horarios...</td>\n";
    $tabla .= "</tr>\n";
}

$array = array(
    0 => $tabla,
    1 => $lista
);

echo json_encode($array);
