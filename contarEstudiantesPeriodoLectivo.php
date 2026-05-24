<?php
require_once("scripts/clases/class.mysql.php");

$db = new MySQL();

session_start();
$id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

$qryString = "SELECT e.id_estudiante
                FROM sw_estudiante e, 
                     sw_estudiante_periodo_lectivo ep
               WHERE e.id_estudiante = ep.id_estudiante
                 AND activo = 1 
                 AND id_periodo_lectivo = $id_periodo_lectivo";

$result = $db->consulta($qryString);
$num_estudiantes = $db->num_rows($result);

echo json_encode($num_estudiantes);
