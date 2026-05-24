<?php
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

// Variables POST
$id_estudiante = $_POST['id_estudiante'];
$id_paralelo = $_POST['id_paralelo'];
$id_asignatura = $_POST['id_asignatura'];
$id_rubrica_personalizada = $_POST['id_rubrica_personalizada'];
$re_calificacion = $_POST['re_calificacion'];

if ($re_calificacion != 0) {
    $consulta = $db->consulta("SELECT * FROM sw_rubrica_estudiante WHERE id_estudiante = " . $id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_rubrica_personalizada = " . $id_rubrica_personalizada);
    $num_total_registros = $db->num_rows($consulta);

    if ($num_total_registros > 0) {
        // actualizarRubricaEstudiante
        $qry = "UPDATE sw_rubrica_estudiante SET ";
        $qry .= "re_calificacion = " . $re_calificacion;
        $qry .= " WHERE id_estudiante = " . $id_estudiante;
        $qry .= " AND id_paralelo = " . $id_paralelo;
        $qry .= " AND id_asignatura = " . $id_asignatura;
        $qry .= " AND id_rubrica_personalizada = " . $id_rubrica_personalizada;
    } else {
        // insertarRubricaEstudiante
        $qry = "INSERT INTO sw_rubrica_estudiante SET ";
        $qry .= "id_estudiante = " . $id_estudiante . ",";
        $qry .= "id_paralelo = " . $id_paralelo . ",";
        $qry .= "id_asignatura = " . $id_asignatura . ",";
        $qry .= "id_rubrica_personalizada = " . $id_rubrica_personalizada . ",";
        $qry .= "re_calificacion = " . $re_calificacion;
    }
} else {
    // eliminarRubricaEstudiante
    $qry = "DELETE FROM sw_rubrica_estudiante ";
    $qry .= " WHERE id_estudiante = " . $id_estudiante;
    $qry .= " AND id_paralelo = " . $id_paralelo;
    $qry .= " AND id_asignatura = " . $id_asignatura;
    $qry .= " AND id_rubrica_personalizada = " . $id_rubrica_personalizada;
}

try {
    $db->consulta($qry);
    echo "success";
} catch (Exception $e) {
    echo $e->getMessage();
}
