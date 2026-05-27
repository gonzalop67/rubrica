<?php
include("../scripts/clases/class.mysql.php");

$paginaActual = $_POST['partida'];

$db = new MySQL();

$query = "SELECT * FROM `sw_permiso`";
$consulta = $db->consulta($query);
$nroPermisos = $db->num_rows($consulta);

$nroLotes = 5;
$nroPaginas = ceil($nroPermisos / $nroLotes);

$lista = '';
$tabla = '';

if ($paginaActual == 1) {
    $lista = $lista . '<li><a href="javascript:;" disabled>‹</a></li>';
} else {
    $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual - 1) . ');">‹</a></li>';
}

for ($i = 1; $i <= $nroPaginas; $i++) {
    if ($i == $paginaActual) {
        $lista = $lista . '<li class="active"><a href="javascript:pagination(' . $i . ');">' . $i . '</a></li>';
    } else {
        $lista = $lista . '<li><a href="javascript:pagination(' . $i . ');">' . $i . '</a></li>';
    }
}

if ($paginaActual == $nroPaginas) {
    $lista = $lista . '<li><a href="javascript:;" disabled>›</a></li>';
} else {
    $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual + 1) . ');">›</a></li>';
}

if ($paginaActual <= 1) {
    $limit = 0;
} else {
    $limit = $nroLotes * ($paginaActual - 1);
}

$consulta = $db->consulta("SELECT * FROM `sw_permiso` ORDER BY id_permiso LIMIT $limit, $nroLotes");
$num_total_registros = $db->num_rows($consulta);
if ($num_total_registros > 0) {
    while ($permiso = $db->fetch_assoc($consulta)) {
        $tabla .= "<tr>";
        $nombre = $permiso["nombre"];
        $slug = $permiso["slug"];
        $descripcion = $permiso["descripcion"];
        $id = $permiso["id_permiso"];
        $tabla .= "<td>" . $id . "</td>";
        $tabla .= "<td>" . $nombre . "</td>";
        $tabla .= "<td>" . $slug . "</td>";
        $tabla .= "<td>" . $descripcion . "</td>";
        $tabla .= "<td><div class='btn-group'><a href='javascript:;' class='btn btn-warning item-edit' data='" . $id . "' title='Editar'><span class='fa fa-pencil'></span></a>";
        $tabla .= "<a href='javascript:;' class='btn btn-danger item-delete' data='" . $id . "' title='Eliminar'><span class='fa fa-trash'></span></a></div></td>";
        $tabla .= "</tr>";
    }
} else {
    $tabla .= "<tr>\n";
    $tabla .= "<tr><td colspan='100%' align='center'>No se han ingresado permisos todavia...</td></tr>\n";
    $tabla .= "</tr>\n";
}

$array = array(
    0 => $tabla,
    1 => $lista
);

echo json_encode($array);
