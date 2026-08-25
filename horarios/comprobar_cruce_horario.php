<?php
// 1. Incluye tu archivo de conexión (ajusta la ruta según tu proyecto)
require_once("../scripts/clases/class.mysql.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $db = new MySQL();

    // 2. Recibir y limpiar los parámetros que vienen desde el DROP de tu JavaScript
    $id_paralelo        = $db->filtrar($_POST['id_paralelo'] ?? '');
    $id_horario_detalle = $db->filtrar($_POST['id_hora_clase'] ?? ''); // data-hora de la celda
    $id_asignatura      = $db->filtrar($_POST['id_asignatura'] ?? ''); // id de la materia arrastrada
    $dia_semana         = $db->filtrar($_POST['id_dia_semana'] ?? ''); // data-dia de la celda
    $id_horario_def     = $db->filtrar($_POST['id_horario_def'] ?? '');

    try {
        // STEP 1: Buscar qué docente tiene asignada esta materia en este paralelo originalmente.
        // NOTA: Ajusta el nombre de tu tabla distributiva (ej: sw_distributivo o sw_materia_docente)
        // Necesitamos obtener el 'id_usuario' de esa relación.
        $sqlDocente = "SELECT id_usuario FROM sw_distributivo 
                       WHERE id_asignatura = '$id_asignatura' 
                         AND id_paralelo = '$id_paralelo' 
                       LIMIT 1";
        
        $resDocente = $db->consulta($sqlDocente);

        if ($db->num_rows($resDocente) === 0) {
            // Error 2: No se ha designado un docente para esta asignatura en este paralelo
            echo json_encode(["errorno" => 2, "mensaje" => "No se ha designado un docente para esta asignatura."]);
            exit;
        }

        $rowDocente = $db->fetch_assoc($resDocente);
        $id_usuario = $rowDocente['id_usuario']; // El ID del profesor

        // Si el id_usuario es nulo o vacío por alguna razón, también es un error tipo 2
        if (empty($id_usuario) || $id_usuario == 0) {
            echo json_encode(["errorno" => 2, "mensaje" => "No se ha designado un docente para esta asignatura."]);
            exit;
        }

        // STEP 2: Comprobar si ESTE docente ya tiene una clase registrada a la MISMA HORA y MISMO DÍA
        // pero en un paralelo DISTINTO dentro de tu tabla 'sw_horario_clases'
        $sqlCruce = "SELECT id_horario_clase FROM sw_horario_clases 
                     WHERE id_usuario = '$id_usuario' 
                       AND dia_semana = '$dia_semana' 
                       AND id_horario_detalle = '$id_horario_detalle' 
                       AND id_horario_def = '$id_horario_def'
                       AND id_paralelo != '$id_paralelo' 
                     LIMIT 1";
        
        $resCruce = $db->consulta($sqlCruce);

        if ($db->num_rows($resCruce) > 0) {
            // Error 1: Existe cruce de horario (el docente está ocupado en otra aula)
            echo json_encode(["errorno" => 1, "mensaje" => "El docente tiene un cruce en esta hora."]);
        } else {
            // Error 0: Todo está perfecto, el docente está libre para este casillero
            echo json_encode(["errorno" => 0, "mensaje" => "Disponible."]);
        }

    } catch (Exception $e) {
        echo json_encode(["errorno" => 3, "mensaje" => "Error interno: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["errorno" => 3, "mensaje" => "Método no permitido."]);
}
