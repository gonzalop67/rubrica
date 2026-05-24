<?php
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

$paginaActual = $_POST['partida'];
$total_registros = $_POST['numero_asignaturas'];
$cantidad_registros = $_POST['cantidad_registros'];

$nroPaginas = ceil($total_registros / $cantidad_registros);

$lista = '';
$tabla = '';

if ($total_registros > 0) {
    if ($paginaActual == 1) {
        $lista = $lista . '<li><a href="javascript:;" disabled>‹‹</a></li>';
        $lista = $lista . '<li><a href="javascript:;" disabled>‹</a></li>';
    } else {
        $lista = $lista . '<li><a href="javascript:pagination(' . $cantidad_registros . ', 1,' . $total_registros . ');">‹‹</a></li>';
        $lista = $lista . '<li><a href="javascript:pagination(' . $cantidad_registros . ', ' . ($paginaActual - 1) . ',' . $total_registros . ');">‹</a></li>';
    }
}

for ($i = 1; $i <= $nroPaginas; $i++) {
    if ($i == $paginaActual) {
        $lista = $lista . '<li class="active"><a href="javascript:pagination(' . $cantidad_registros . ', ' . $i . ', ' . $total_registros . ');">' . $i . '</a></li>';
    } else {
        $lista = $lista . '<li><a href="javascript:pagination(' . $cantidad_registros . ', ' . $i . ', ' . $total_registros . ');">' . $i . '</a></li>';
    }
}

if ($paginaActual == $nroPaginas) {
    $lista = $lista . '<li><a href="javascript:;" disabled>›</a></li>';
    $lista = $lista . '<li><a href="javascript:;" disabled>››</a></li>';
} else {
    $lista = $lista . '<li><a href="javascript:pagination(' . $cantidad_registros . ', ' . ($paginaActual + 1) . ', ' . $total_registros . ');">›</a></li>';
    $lista = $lista . '<li><a href="javascript:pagination(' . $cantidad_registros . ', ' . $nroPaginas . ', ' . $total_registros . ');">››</a></li>';
}

if ($paginaActual <= 1) {
    $limit = 0;
} else {
    $limit = $cantidad_registros * ($paginaActual - 1);
}

session_start();
$id_usuario = $_SESSION['id_usuario'];
$id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

$result = $db->consulta("SELECT c.id_curso, 
                                d.id_paralelo, 
                                d.id_asignatura, 
                                as_nombre, 
                                es_figura, 
                                cu_nombre, 
                                pa_nombre 
                           FROM sw_asignatura a, 
                                sw_distributivo d, 
                                sw_paralelo pa, 
                                sw_curso c, 
                                sw_especialidad e 
                          WHERE a.id_asignatura = d.id_asignatura 
                            AND d.id_paralelo = pa.id_paralelo 
                            AND pa.id_curso = c.id_curso 
                            AND c.id_especialidad = e.id_especialidad 
                            AND d.id_usuario = $id_usuario
                            AND d.id_periodo_lectivo = $id_periodo_lectivo
                            AND as_curricular = 1
                        ORDER BY c.id_curso, pa.id_paralelo, as_nombre ASC 
                        LIMIT $limit, $cantidad_registros");
$num_total_registros = $db->num_rows($result);

if ($num_total_registros > 0) {
    $contador = $limit;
    while ($v = $db->fetch_object($result)) {
        $contador++;
        $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
        $tabla .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
        $id_curso = $v->id_curso;
        $codigo = $v->id_paralelo;
        $id_asignatura = $v->id_asignatura;
        $nombre = $v->as_nombre;
        $curso = $v->cu_nombre . " " . $v->es_figura;
        $paralelo = $v->pa_nombre;
        $tabla .= "<td>$contador</td>\n";
        $tabla .= "<td>$nombre</td>\n";
        $tabla .= "<td>$curso</td>\n";
        $tabla .= "<td>$paralelo</td>\n";
        $tabla .= "<td><a href=\"#\" class=\"btn btn-primary btn-sm\" onclick=\"seleccionarParalelo(" . $id_curso . "," . $codigo . "," . $id_asignatura . ",'" . $nombre . "','" . $curso . "','" . $paralelo . "')\" title=\"Seleccionar\"><span class=\"fa fa-check\"></span></a></td>\n";
        $tabla .= "</tr>\n";
    }
} else {
    $tabla .= "<tr>\n";
    $tabla .= "<td>No se han asociado asignaturas a este docente...</td>\n";
    $tabla .= "</tr>\n";
}

$array = array(
    0 => $tabla,
    1 => $lista
);

echo json_encode($array);
