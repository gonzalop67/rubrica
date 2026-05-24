<?php
include_once "../scripts/clases/class.mysql.php";

$id = $_POST['id'];

$db = new MySQL();
$alternativa = $db->consulta("SELECT * FROM `sw_choices` WHERE id = " . $id);

echo json_encode($db->fetch_assoc($alternativa));
