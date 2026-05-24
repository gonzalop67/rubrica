<?php
include_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

$consulta = $db->consulta("SELECT * FROM sw_exam_category WHERE id = " . $_POST['id']);
echo json_encode($db->fetch_assoc($consulta));