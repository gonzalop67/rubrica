<?php
require_once("../scripts/clases/class.mysql.php"); 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_horario_def']) && isset($_POST['id_paralelo'])) {
    
    $db = new MySQL();

    $id_horario_def = $db->filtrar($_POST['id_horario_def']);
    $id_paralelo    = $db->filtrar($_POST['id_paralelo']);

    try {
        // CORREGIDO: Ajustado a tus columnas reales de sw_horario_clases
        // Nota: Asegúrate de verificar si tu tabla de nombres de materias se llama sw_asignaturas
        $sqlClases = "SELECT c.id_horario_clase, 
                             c.id_horario_detalle, 
                             c.id_dia_semana, 
                             m.nombre AS nombre_materia
                      FROM sw_horario_clases c
                      INNER JOIN sw_asignatura m ON c.id_asignatura = m.id_asignatura
                      WHERE c.id_horario_def = '$id_horario_def' 
                        AND c.id_paralelo = '$id_paralelo'";
        
        $resClases = $db->consulta($sqlClases);

        $listadoClases = [];
        while ($row = $db->fetch_assoc($resClases)) {
            $listadoClases[] = $row;
        }

        echo json_encode($listadoClases);

    } catch (Exception $e) {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
