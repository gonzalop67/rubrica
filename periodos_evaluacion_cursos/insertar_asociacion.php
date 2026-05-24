<?php
sleep(1);
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$id_curso = $_POST["id_curso"];
$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
$pe_ponderacion = $_POST["pe_ponderacion"];

//Primero compruebo que el registro no está insertado...
$qry = "SELECT * FROM sw_periodo_evaluacion_curso WHERE id_curso = " . $id_curso . " AND id_periodo_evaluacion = " . $id_periodo_evaluacion;
$consulta = $db->consulta($qry);
$num_total_registros = $db->num_rows($consulta);
if ($num_total_registros > 0)
    echo "El Periodo de Evaluación ya se encuentra asociado al Curso...";
else {
    // Obtener el número de orden del registro nuevo
    $qry = "SELECT MAX(pc_orden) AS max_orden FROM sw_periodo_evaluacion_curso WHERE id_curso = $id_curso AND id_periodo_evaluacion = $id_periodo_evaluacion";
    $consulta = $db->consulta($qry);
    $num_total_registros = $db->num_rows($consulta);
    if ($num_total_registros > 0) {
        $registro = $db->fetch_object($consulta);
        $max_orden = $registro->max_orden + 1;
    } else {
        $max_orden = 1;
    }
    $qry = "INSERT INTO sw_periodo_evaluacion_curso (id_periodo_lectivo, id_periodo_evaluacion, id_curso, pe_ponderacion, pc_orden) VALUES (";
    $qry .= $id_periodo_lectivo . ", ";
    $qry .= $id_periodo_evaluacion . ", ";
    $qry .= $id_curso . ", ";
    if ($pe_ponderacion == '') {
        $qry .= "null, ";
    } else {
        $qry .= $pe_ponderacion . ", ";
    }
    $qry .= $max_orden . ")";
    // $consulta = $db->consulta($qry);
    // echo $qry . "<br>";
    $consulta = $db->consulta($qry);
    if ($consulta) {
        echo "Periodo de Evaluaci&oacute;n asociado exitosamente...";
    } else {
        echo "No se pudo insertar el Periodo de Evaluaci&oacute;n... Consulta: $qry";
    }
}
