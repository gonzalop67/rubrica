<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$id_estudiante =  $_POST["id_estudiante"];
$id_paralelo =  $_POST["id_paralelo"];
$id_aporte_evaluacion =  $_POST["id_aporte_evaluacion"];
$calificacion =  $_POST["calificacion"];

function existeCalificacionProyecto()
{
    global $db, $id_estudiante, $id_paralelo, $id_aporte_evaluacion;

    $consulta = $db->consulta("SELECT * FROM sw_notas_proyectos WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_aporte_evaluacion = $id_aporte_evaluacion");

    return $db->num_rows($consulta) > 0;
}

function existeNotaProyectoDocente($id_rubrica_evaluacion, $id_asignatura)
{
    global $db, $id_estudiante, $id_paralelo;

    $consulta = $db->consulta("SELECT * FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");

    return $db->num_rows($consulta) > 0;
}

$mensaje = "";

$qry = "SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion  
        WHERE id_aporte_evaluacion = $id_aporte_evaluacion";

$consulta = $db->consulta($qry);
$id_rubrica_evaluacion = $db->fetch_object($consulta)->id_rubrica_evaluacion;

// Obtener id_asignatura de las asignaturas del paralelo
$qry = "SELECT id_curso FROM sw_paralelo WHERE id_paralelo = $id_paralelo";

$consulta = $db->consulta($qry);
$id_curso = $db->fetch_object($consulta)->id_curso;

if (!existeCalificacionProyecto()) {
    if ($calificacion > 0) {
        $qry = "INSERT INTO sw_notas_proyectos SET 
            id_estudiante = $id_estudiante, 
            id_paralelo = $id_paralelo, 
            id_aporte_evaluacion = $id_aporte_evaluacion, 
            calificacion = $calificacion";

        try {
            $db->consulta($qry);

            $qry = "SELECT id_asignatura FROM sw_asignatura_curso WHERE id_curso = $id_curso";
            $consulta = $db->consulta($qry);

            while ($asignatura = $db->fetch_object($consulta)) {
                $id_asignatura = $asignatura->id_asignatura;

                // Verificar la existencia de la nota por parte del docente

                if (!existeNotaProyectoDocente($id_rubrica_evaluacion, $id_asignatura)) {
                    $qry = "INSERT INTO sw_rubrica_estudiante SET 
                            id_estudiante = $id_estudiante, 
                            id_paralelo = $id_paralelo, 
                            id_asignatura = $id_asignatura, 
                            id_rubrica_personalizada = $id_rubrica_evaluacion, 
                            re_calificacion = $calificacion";

                    $db->consulta($qry);
                } else {
                    $qry = "UPDATE sw_rubrica_estudiante SET 
                            re_calificacion = $calificacion 
                            WHERE id_estudiante = $id_estudiante  
                            AND id_paralelo = $id_paralelo  
                            AND id_asignatura = $id_asignatura  
                            AND id_rubrica_personalizada = $id_rubrica_evaluacion";

                    $db->consulta($qry);
                }
            }

            $mensaje = "Nota de Proyecto insertada exitosamente.";
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
        }
    }
} else {
    if ($calificacion > 0) {
        $qry = "UPDATE sw_notas_proyectos SET  
            calificacion = $calificacion 
            WHERE id_estudiante = $id_estudiante  
            AND id_paralelo = $id_paralelo 
            AND id_aporte_evaluacion = $id_aporte_evaluacion";

        try {
            $db->consulta($qry);

            $qry = "SELECT id_asignatura FROM sw_asignatura_curso WHERE id_curso = $id_curso";
            $consulta = $db->consulta($qry);

            while ($asignatura = $db->fetch_object($consulta)) {
                $id_asignatura = $asignatura->id_asignatura;

                // Verificar la existencia de la nota por parte del docente

                if (!existeNotaProyectoDocente($id_rubrica_evaluacion, $id_asignatura)) {
                    $qry = "INSERT INTO sw_rubrica_estudiante SET 
                    id_estudiante = $id_estudiante, 
                    id_paralelo = $id_paralelo, 
                    id_asignatura = $id_asignatura, 
                    id_rubrica_personalizada = $id_rubrica_evaluacion, 
                    re_calificacion = $calificacion";

                    $db->consulta($qry);
                } else {
                    $qry = "UPDATE sw_rubrica_estudiante SET 
                    re_calificacion = $calificacion 
                    WHERE id_estudiante = $id_estudiante  
                    AND id_paralelo = $id_paralelo  
                    AND id_asignatura = $id_asignatura  
                    AND id_rubrica_personalizada = $id_rubrica_evaluacion";

                    $db->consulta($qry);
                }
            }

            $mensaje = "Nota de Proyecto actualizada exitosamente.";
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
        }
    } else {
        $qry = "DELETE FROM sw_notas_proyectos 
            WHERE id_estudiante = $id_estudiante  
            AND id_paralelo = $id_paralelo 
            AND id_aporte_evaluacion = $id_aporte_evaluacion";

        try {
            $db->consulta($qry);

            $qry = "SELECT id_asignatura FROM sw_asignatura_curso WHERE id_curso = $id_curso";
            $consulta = $db->consulta($qry);

            while ($asignatura = $db->fetch_object($consulta)) {
                $id_asignatura = $asignatura->id_asignatura;

                $qry = "DELETE FROM sw_rubrica_estudiante 
                    WHERE id_estudiante = $id_estudiante  
                    AND id_paralelo = $id_paralelo  
                    AND id_asignatura = $id_asignatura  
                    AND id_rubrica_personalizada = $id_rubrica_evaluacion";

                    $db->consulta($qry);
            }

            $mensaje = "Nota de Proyecto eliminada exitosamente.";
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
        }
    }
}

echo $mensaje;
