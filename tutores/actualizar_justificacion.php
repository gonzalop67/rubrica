<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

// Variables POST
$id_asistencia_tutor = $_POST["id_asistencia_tutor"];
$justificacion = trim(strtoupper($_POST["justificacion"]));

// Actualizar la justificación e id_inasistencia a 3 (Falta Justificada)
$sql = "UPDATE sw_asistencia_tutor SET at_justificacion = '$justificacion', id_inasistencia = 3 WHERE id_asistencia_tutor = $id_asistencia_tutor";
$db->consulta($sql);

$datos = [
    'id_asistencia_tutor' => $id_asistencia_tutor,
    'justificacion' => $sql
];

echo json_encode($datos);