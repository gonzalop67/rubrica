<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

// Variables POST
$id_paralelo = $_POST["id_paralelo"];

$cadena = "";

// Actualizar la justificación e id_inasistencia a 3 (Falta Justificada)
$sql = "SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo AND id_tipo_asignatura = 1 ORDER BY ac_orden";

$consulta = $db->consulta($sql);

if ($db->num_rows($consulta) > 0) {
    while ($asignatura = $db->fetch_assoc($consulta)) {
        $code = $asignatura["id_asignatura"];
        $name = $asignatura["as_nombre"];
        $cadena .= "<option value=\"$code\">$name</option>";
    }
}

echo $cadena;
