<?php
include_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

session_start();
$id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
$id_usuario = $_SESSION['id_usuario'];

$consulta = $db->consulta("SELECT DISTINCT(p.id_paralelo),
                                pa_nombre,
                                cu_abreviatura,
                                es_abreviatura, 
                                pa_orden 
                                FROM sw_distributivo d, 
                                sw_paralelo p, 
                                sw_curso c,
                                sw_especialidad e 
                                WHERE e.id_especialidad = c.id_especialidad
                                AND c.id_curso = p.id_curso 
                                AND p.id_paralelo = d.id_paralelo 
                                AND d.id_usuario = $id_usuario
                                AND d.id_periodo_lectivo = $id_periodo_lectivo
                                ORDER BY pa_orden");

$num_total_registros = $db->num_rows($consulta);

$cadena = "";
if ($num_total_registros > 0) {
    while ($v = $db->fetch_object($consulta)) {
        $cadena .= "<option value=\"$v->id_paralelo\">$v->cu_abreviatura  $v->pa_nombre $v->es_abreviatura</option>";
    }
}

echo $cadena;
