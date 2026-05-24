<?php
include_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

// Variables enviadas mediante POST
$id_category = $_POST['id_category'];

$consulta = $db->consulta("SELECT COUNT(*) AS num_registros FROM sw_questions WHERE id_category = $id_category");

echo json_encode($db->fetch_assoc($consulta));
