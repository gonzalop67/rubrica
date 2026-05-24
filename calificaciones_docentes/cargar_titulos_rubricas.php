<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$id_asignatura = $_POST['id_asignatura'];
$id_paralelo = $_POST['id_paralelo'];
$id_aporte_evaluacion = $_POST['id_aporte_evaluacion'];

// Obtener el id_periodo_lectivo
$sql = "SELECT id_periodo_lectivo FROM sw_paralelo WHERE id_paralelo = $id_paralelo";
$consulta = $db->consulta($sql);
$id_periodo_lectivo = $db->fetch_object($consulta)->id_periodo_lectivo;

// Obtener el tipo de asignatura: 1: Cuantitativa, 2: Cualitativa
$sql = "SELECT id_tipo_asignatura FROM sw_asignatura WHERE id_asignatura = $id_asignatura";
$consulta = $db->consulta($sql);
$id_tipo_asignatura = $db->fetch_object($consulta)->id_tipo_asignatura;

$cadena = "";

// Obtener los nombres de los insumos
$sql = "SELECT * FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion AND id_tipo_asignatura = $id_tipo_asignatura";
$result = $db->consulta($sql);
while ($row = $db->fetch_object($result)) {
    $cadena .= "<th class='text-center'>" . $row->ru_nombre . "</th>";
}

if ($id_tipo_asignatura == 1) {
    $cadena .= "<th class='text-center'>PROMEDIO</th>";
}

$sql = "SELECT qi.nombre FROM sw_periodo_lectivo pl, sw_quien_inserta_comp qi WHERE qi.id = quien_inserta_comp_id AND pl.id_periodo_lectivo = $id_periodo_lectivo";
$result = $db->consulta($sql);
$quien_inserta_comportamiento = $db->fetch_object($result)->nombre;

if (strtolower($quien_inserta_comportamiento) == 'docente' && $id_tipo_asignatura == 1) {
    $cadena .= "<th class='text-center'>COMPORTAMIENTO</th>";
}

$data = array(
    "titulos" => $cadena,
    "cantidad" => 2
);
echo json_encode($data);
