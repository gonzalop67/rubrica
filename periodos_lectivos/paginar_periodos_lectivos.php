<?php
include("../scripts/clases/class.mysql.php");

$paginaActual = $_POST['partida'];
$id_modalidad = $_POST['id_modalidad'];

$db = new MySQL();

$query = "SELECT * FROM `sw_periodo_lectivo` WHERE id_modalidad = $id_modalidad";
$consulta = $db->consulta($query);
$nroPeriodosLectivos = $db->num_rows($consulta);

$nroLotes = 5;
$nroPaginas = ceil($nroPeriodosLectivos / $nroLotes);

$lista = '';
$tabla = '';

if ($paginaActual == 1) {
    $lista = $lista . '<li><a href="javascript:;" disabled>‹</a></li>';
} else {
    $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual - 1) . ',' . $id_modalidad . ');">‹</a></li>';
}

for ($i = 1; $i <= $nroPaginas; $i++) {
    if ($i == $paginaActual) {
        $lista = $lista . '<li class="active"><a href="javascript:pagination(' . $i . ',' . $id_modalidad . ');">' . $i . '</a></li>';
    } else {
        $lista = $lista . '<li><a href="javascript:pagination(' . $i . ',' . $id_modalidad . ');">' . $i . '</a></li>';
    }
}

if ($paginaActual == $nroPaginas) {
    $lista = $lista . '<li><a href="javascript:;" disabled>›</a></li>';
} else {
    $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual + 1) . ',' . $id_modalidad . ');">›</a></li>';
}

if ($paginaActual <= 1) {
    $limit = 0;
} else {
    $limit = $nroLotes * ($paginaActual - 1);
}

$consulta = $db->consulta("SELECT * FROM `sw_periodo_lectivo` WHERE id_modalidad = $id_modalidad ORDER BY pe_fecha_inicio DESC LIMIT $limit, $nroLotes");
$num_total_registros = $db->num_rows($consulta);
if ($num_total_registros > 0) {
    $contador = $limit;
    while ($periodo_lectivo = $db->fetch_assoc($consulta)) {
        $contador++;
        $tabla .= "<tr>\n";
        $code = $periodo_lectivo["id_periodo_lectivo"];
        $anio_lectivo = $periodo_lectivo["pe_anio_inicio"] . " - " . $periodo_lectivo["pe_anio_fin"];
        $pe_fecha_inicio = $periodo_lectivo["pe_fecha_inicio"];
        $pe_fecha_fin = $periodo_lectivo["pe_fecha_fin"];
        $pe_estado = $periodo_lectivo["pe_estado"];
        $estado = ($pe_estado == 'A') ? 'ACTUAL' : 'TERMINADO';
        $tabla .= "<td>$contador</td>";
        $tabla .= "<td>$code</td>\n";
        $tabla .= "<td>$anio_lectivo</td>\n";
        $tabla .= "<td>$pe_fecha_inicio</td>\n";
        $tabla .= "<td>$pe_fecha_fin</td>\n";
        $tabla .= "<td>$estado</td>\n";
        $tabla .= "<td>\n";
        $tabla .= "<div class=\"btn-group\">\n";
        $tabla .= "<a href=\"javascript:;\" class=\"btn btn-warning item-edit\" data=\"" . $code . "\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
        $tabla .= "</div>\n";
        $tabla .= "</td>\n";
        $tabla .= "</tr>\n";
    }
} else {
    $tabla .= "<tr>\n";
    $tabla .= "<td colspan=\"7\" align=\"center\">A&uacute;n no se han definido periodos lectivos...</td>\n";
    $tabla .= "</tr>\n";
}

$array = array(
    0 => $tabla,
    1 => $lista
);

echo json_encode($array);
