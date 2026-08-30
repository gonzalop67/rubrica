<?php
// session_start();
include_once "../scripts/clases/class.mysql.php"; 

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_paralelo'])) {
    
    $db = new MySQL();
    
    // 1. Recibir y limpiar parámetros del AJAX
    $id_paralelo        = $db->filtrar($_POST['id_paralelo']);
    $id_asignatura      = $db->filtrar($_POST['id_asignatura']);
    $id_horario_detalle = $db->filtrar($_POST['id_horario_detalle']);
    $fecha              = $db->filtrar($_POST['fecha']);
    $id_horario_def     = $db->filtrar($_POST['id_horario_def'] ?? 0); 

    try {
        // ====================================================================
        // PASO A: Verificar si YA se tomó asistencia en este bloque y fecha (Singular)
        // ====================================================================
        $sqlCheck = "SELECT COUNT(*) AS total FROM sw_asistencia_estudiante 
                     WHERE id_paralelo = '$id_paralelo' 
                       AND id_asignatura = '$id_asignatura' 
                       AND id_horario_detalle = '$id_horario_detalle' 
                       AND ae_fecha = '$fecha'";
        
        $resCheck = $db->consulta($sqlCheck);
        $rowCheck = $db->fetch_assoc($resCheck);
        
        // Si es la PRIMERA VEZ que el docente abre este bloque hoy (total es 0)
        if (intval($rowCheck['total']) === 0) {
            
            // 1. Traer todos los estudiantes matriculados y activos en este paralelo
            $sqlAlumnos = "SELECT id_estudiante FROM sw_estudiante_periodo_lectivo 
                           WHERE id_paralelo = '$id_paralelo' AND activo = 1";
            $resAlumnos = $db->consulta($sqlAlumnos);
            
            // 2. Insertar a todo el salón automáticamente con estado 1 (Asiste) en singular
            while ($alumno = $db->fetch_assoc($resAlumnos)) {
                $id_est = $alumno['id_estudiante'];
                
                $sqlPreGuardar = "INSERT INTO sw_asistencia_estudiante (
                                    id_estudiante, id_horario_def, id_paralelo, id_asignatura, id_horario_detalle, ae_fecha, id_tipo_inasistencia
                                 ) VALUES (
                                    '$id_est', '$id_horario_def', '$id_paralelo', '$id_asignatura', '$id_horario_detalle', '$fecha', 1
                                 ) ON DUPLICATE KEY UPDATE id_tipo_inasistencia = id_tipo_inasistencia";
                
                $db->consulta($sqlPreGuardar);
            }
        }

        // ====================================================================
        // PASO B: Consultar y devolver la nómina (Unificado a Singular 'sw_asistencia_estudiante')
        // ====================================================================
        $sql = "SELECT e.id_estudiante, 
                       CONCAT(e.es_apellidos, ' ', e.es_nombres) AS apellidos_nombres,
                       IFNULL(a.id_tipo_inasistencia, 1) AS id_tipo_inasistencia
                  FROM sw_estudiante_periodo_lectivo m
                 INNER JOIN sw_estudiante e ON m.id_estudiante = e.id_estudiante
                 LEFT JOIN sw_asistencia_estudiante a ON e.id_estudiante = a.id_estudiante 
                   AND a.id_asignatura = '$id_asignatura'
                   AND a.id_horario_detalle = '$id_horario_detalle'
                   AND a.ae_fecha = '$fecha'
                 WHERE m.id_paralelo = '$id_paralelo' 
                   AND m.activo = 1
                 ORDER BY e.es_apellidos ASC, e.es_nombres ASC";

        $consulta = $db->consulta($sql);
        $nomina = array();
        while ($row = $db->fetch_assoc($consulta)) {
            $nomina[] = $row;
        }

        // 3. Traer los tipos de inasistencia maestros
        $sqlTipos = "SELECT id_tipo_inasistencia, ti_nombre FROM sw_tipo_inasistencia ORDER BY id_tipo_inasistencia ASC";
        $resTipos = $db->consulta($sqlTipos);
        $tipos = array();
        while ($row = $db->fetch_assoc($resTipos)) {
            $tipos[] = $row;
        }

        echo json_encode([
            "nomina" => $nomina,
            "tipos" => $tipos
        ]);

    } catch (Exception $e) {
        echo json_encode(["nomina" => [], "tipos" => []]);
    }
} else {
    echo json_encode(array());
}
