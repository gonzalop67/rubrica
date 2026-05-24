<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$id_paralelo = $_POST["id_paralelo"];

session_start();

$id_usuario = $_SESSION["id_usuario"];
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$consulta = $db->consulta("SELECT COUNT(*) AS num_registros 
    FROM sw_distributivo di,
            sw_asignatura a 
    WHERE a.id_asignatura = di.id_asignatura
        AND id_usuario = " . $id_usuario 
    . " AND id_paralelo = " . $id_paralelo 
    . " AND id_periodo_lectivo = " . $id_periodo_lectivo
    . " AND as_curricular = 1");

echo json_encode($db->fetch_assoc($consulta));
