<?php
require_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

$id_aporte_evaluacion = $_POST['id_aporte_evaluacion'];

$qry = "SELECT ap_nombre, pe_nombre FROM `sw_aporte_evaluacion` ap, `sw_periodo_evaluacion` pe WHERE pe.id_periodo_evaluacion = ap.id_periodo_evaluacion AND id_aporte_evaluacion = $id_aporte_evaluacion";

$consulta = $db->consulta($qry);
$registro = $db->fetch_object($consulta);

if ($db->num_rows($consulta) > 0) {
    $nombre_aporte_evaluacion = $registro->ap_nombre . " " . $registro->pe_nombre;
} else {
    $nombre_aporte_evaluacion = "No se encontraron registros coincidentes.";
}

echo $nombre_aporte_evaluacion;
