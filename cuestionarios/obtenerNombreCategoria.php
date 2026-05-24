<?php
include_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

$id = $_POST['id_category'];

$consulta = $db->consulta("SELECT category FROM sw_exam_category WHERE id = $id");
echo $db->fetch_object($consulta)->category;
