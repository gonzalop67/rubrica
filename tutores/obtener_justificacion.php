<?php
include("../scripts/clases/class.mysql.php");
$db = new mysql();

// Variables POST
$id_estudiante = $_POST["id_estudiante"];
$id_paralelo = $_POST["id_paralelo"];
$ae_fecha = $_POST["ae_fecha"];

$sql = "SELECT id_asistencia_tutor, at_justificacion FROM sw_asistencia_tutor WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND at_fecha = '$ae_fecha'";

$query = $db->consulta($sql);
$registro = $db->fetch_object($query);
$justificacion = $registro->at_justificacion;
$id_asistencia_tutor = $registro->id_asistencia_tutor;

$datos = [
    'estudiante' => "",
    'id_asistencia_tutor' => $id_asistencia_tutor,
    'justificacion' => $justificacion
];

echo json_encode($datos);
