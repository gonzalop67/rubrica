<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();
session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
//consulta SQL para determinar si es intensivo
$query = "SELECT intensivo FROM sw_ofertas_educativas oe, sw_periodo_lectivo p WHERE oe.id = p.oferta_educativa_id AND id_periodo_lectivo = $id_periodo_lectivo";
$consulta = $db->consulta($query);
$result = $db->fetch_object($consulta);
$intensivo = $result->intensivo;

echo $intensivo;
