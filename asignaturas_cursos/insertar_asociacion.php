<?php
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

session_start();

$id_curso = $_POST["id_curso"];
$id_asignatura = $_POST["id_asignatura"];
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

// Verifico si ya existe la asociacion
$consulta = $db->consulta("SELECT * FROM sw_asignatura_curso WHERE id_curso = " . $id_curso . " AND id_asignatura = " . $id_asignatura);

$num_total_registros = $db->num_rows($consulta);

if ($num_total_registros > 0) {
    $datos = [
        'estado' => 'error',
        'mensaje' => 'Ya existe la asociacion entre el curso y la asignatura seleccionados.',
        'titulo' => 'Error'
    ];
} else {
    // Aqui primero llamo a la funcion almacenada secuencial_curso_asignatura
    $consulta = $db->consulta("SELECT secuencial_curso_asignatura(" . $id_curso . ") AS secuencial");
    $ac_orden = $db->fetch_object($consulta)->secuencial;

    $qry = "INSERT INTO sw_asignatura_curso (id_curso, id_asignatura, id_periodo_lectivo, ac_orden) VALUES (";
    $qry .= $id_curso . ",";
    $qry .= $id_asignatura . ",";
    $qry .= $id_periodo_lectivo . ",";
    $qry .= $ac_orden . ")";
    $consulta = $db->consulta($qry);
    $mensaje = "";
    $datos = [
        'estado' => 'success',
        'mensaje' => 'Asignatura asociada exitosamente...',
        'titulo' => 'Éxito!'
    ];
}

echo json_encode($datos);