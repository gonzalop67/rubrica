<?php
require_once("../scripts/clases/class.mysql.php");

session_start();

$id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
$id_paralelo = $_POST["id_paralelo"];

$db = new MySQL();

$query = "SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_malla_curricular m, sw_paralelo p WHERE a.id_asignatura = m.id_asignatura AND p.id_curso = m.id_curso AND p.id_paralelo = $id_paralelo AND m.id_periodo_lectivo = $id_periodo_lectivo ORDER BY ma_orden";

$consulta = $db->consulta($query);

$cadena = "";

$num_total_registros = $db->num_rows($consulta);

if ($num_total_registros > 0) {
    while ($asignatura = $db->fetch_assoc($consulta)) {
        $code = $asignatura["id_asignatura"];
        $name = $asignatura["as_nombre"];
        $cadena .= "<option value=\"$code\">$name</option>";
    }
}

echo $cadena;


