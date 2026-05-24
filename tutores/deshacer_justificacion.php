<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

// Variables POST
$id_asistencia_tutor = $_POST["id_asistencia_tutor"];

$sql = "UPDATE sw_asistencia_tutor SET at_justificacion = '', id_inasistencia = 2 WHERE id_asistencia_tutor = $id_asistencia_tutor";
$db->consulta($sql);

echo "Justificación deshecha correctamente.";

?>