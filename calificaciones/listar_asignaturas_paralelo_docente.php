<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

session_start();
$id_usuario = $_SESSION["id_usuario"];
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$id_paralelo = $_POST["id_paralelo"];

$sql = "SELECT c.id_curso, 
                d.id_paralelo, 
                d.id_asignatura, 
                as_nombre, 
                es_figura, 
                cu_nombre, 
                pa_nombre 
        FROM sw_asignatura a, 
                sw_distributivo d, 
                sw_paralelo pa, 
                sw_curso c, 
                sw_especialidad e 
        WHERE a.id_asignatura = d.id_asignatura 
        AND d.id_paralelo = pa.id_paralelo 
        AND pa.id_curso = c.id_curso 
        AND c.id_especialidad = e.id_especialidad 
        AND d.id_usuario = $id_usuario
        AND d.id_paralelo = $id_paralelo 
        AND d.id_periodo_lectivo = $id_periodo_lectivo
        AND as_curricular = 1
        ORDER BY c.id_curso, pa.id_paralelo, as_nombre ASC";

$cadena = "";

try {
    $consulta = $db->consulta($sql);
    $cadena .= "<option value='0'>Seleccione...</option>";
    while ($row = $db->fetch_object($consulta)) {
        $id_asignatura = $row->id_asignatura;
        $as_nombre = $row->as_nombre;
        $cadena .= "<option value=\"$id_asignatura\">$as_nombre</option>";
    }
    $ok = true;
} catch (Exception $e) {
    $ok = false;
    $cadena = $e->getMessage();
}

$data = array(
    "cadena" => $cadena,
    "ok" => $ok
);

echo json_encode($data);