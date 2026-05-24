<?php
include_once "../scripts/clases/class.mysql.php";

$id = $_POST['id'];

$db = new MySQL();
$pregunta = $db->consulta("SELECT * FROM `sw_questions` WHERE id = " . $id);

echo json_encode($db->fetch_assoc($pregunta));
