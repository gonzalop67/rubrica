<?php
require_once("../scripts/clases/class.mysql.php");
// Instanciamos la clase MySQL
$db = new MySQL();

// Recibimos los parámetros vía POST
$paginaActual = $_POST["partida"];
$num_registros = $_POST["num_registros"];
$filtro = $_POST["filtro"];

$tabla = "";
$lista = "";
$limit = 0;

$consulta = $db->consulta("SELECT * FROM sw_por_hacer " . $filtro . " ORDER BY fecha DESC");

$num_tareas = $db->num_rows($consulta);
$nroPaginas = ceil($num_tareas / $num_registros);

if ($num_tareas > 0) {
    //paginarTareas(partida, num_registros, filtro)
    if ($paginaActual == 1) {
        $lista = $lista . "<li class='disabled'><a href='javascript:;'>‹</a></li>";
    } else {
        $pagina = $paginaActual - 1;
        $lista = $lista . "<li><a href=\"javascript:paginarTareas($pagina, $num_registros, '$filtro');\">‹</a></li>";
    }

    for ($i = 1; $i <= $nroPaginas; $i++) {
        if ($i == $paginaActual) {
            $lista = $lista . "<li class='active'><a href=\"javascript:paginarTareas($i, $num_registros, '$filtro');\">$i</a></li>";
        } else {
            $lista = $lista . "<li><a href=\"javascript:paginarTareas($i, $num_registros, '$filtro');\">$i</a></li>";
        }
    }

    if ($paginaActual == $nroPaginas) {
        $lista = $lista . "<li class='disabled'><a href='javascript:;'>›</a></li>";
    } else {
        $pagina = $paginaActual + 1;
        $lista = $lista . "<li><a href=\"javascript:paginarTareas($pagina, $num_registros, '$filtro');\">›</a></li>";
    }

    if ($paginaActual <= 1) {
        $limit = 0;
    } else {
        $limit = $num_registros * ($paginaActual - 1);
    }

    $strQuery = "SELECT * FROM sw_por_hacer " . $filtro . " ORDER BY fecha DESC LIMIT $limit, $num_registros";
    $consulta = $db->consulta($strQuery);

    $num_total_registros = $db->num_rows($consulta);

    if ($num_total_registros > 0) {
        $contador = $limit;
        while ($tarea = $db->fetch_object($consulta)) {
            $contador++;
            $tabla .= "<tr>";
            $id = $tarea->id;
            $task = $tarea->tarea;
            $checked = $tarea->hecho ? "checked" : "";
            $clase = $tarea->hecho ? "taskDone" : "";
            $tabla .= "<td><input type='checkbox' onclick='checkTask(this," . $id . ")' $checked></td>";
            $tabla .= "<td><div class='" . $clase . "'>" . $task . "</div></td>";
            $tabla .= "<td>";
            $tabla .= "<div class='btn-group'>";
            $tabla .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $tarea->id . ")\" data-toggle=\"modal\" data-target=\"#editarTareaModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
            $tabla .= "<button class=\"btn btn-danger\" onclick=\"eliminarTarea(" . $tarea->id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
            $tabla .= "</div>";
            $tabla .= "</td>";
            $tabla .= "</tr>";
        }
    }
} else {
    $tabla = "<tr><td colspan='4' align='center'>No se han ingresado tareas todavia...</td></tr>";
}

$array = array(
    0 => $tabla,
    1 => $lista,
    2 => $num_tareas
);

echo json_encode($array);
