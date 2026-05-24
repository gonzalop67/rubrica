<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();
session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
//consulta SQL para determinar si es intensivo
$query = "SELECT mo_intensivo FROM sw_modalidad m, sw_periodo_lectivo p WHERE m.id_modalidad = p.id_modalidad AND id_periodo_lectivo = $id_periodo_lectivo";
$consulta = $db->consulta($query);
$result = $db->fetch_object($consulta);
$mo_intensivo = $result->mo_intensivo;

echo $mo_intensivo;
