<?php
include_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

$id_category = $_POST['id_category'];

session_start();

$_SESSION["id_category"] = $id_category;

$consulta = $db->consulta("SELECT * FROM sw_exam_category WHERE id = $id_category");
$res = $db->fetch_object($consulta);

$_SESSION["exam_time"] = $res->exam_time_in_minutes;

$date = date("Y-m-d H:i:s");
$_SESSION["end_time"] = date("Y-m-d H:i:s", strtotime($date . "+$_SESSION[exam_time] minutes"));
$_SESSION["exam_start"] = "yes";

if (isset($_SESSION["answer"])) {
    for ($i = 1; $i <= sizeof($_SESSION["answer"]); $i++) {
        unset($_SESSION["answer"][$i]);
    }
}

echo $_SESSION["end_time"];
