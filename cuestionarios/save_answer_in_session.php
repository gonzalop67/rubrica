<?php
session_start();

$questionno = $_POST["questionno"];
$value1 = $_POST["value1"];
$_SESSION["answer"]["$questionno"] = $value1;
