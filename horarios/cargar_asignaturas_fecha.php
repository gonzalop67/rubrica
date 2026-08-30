<?php
session_start();
require_once("../scripts/clases/class.mysql.php");

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_horario_def']) && isset($_POST['dia_semana'])) {

    $db = new MySQL();

    // Capturamos el ID del docente desde tu variable global de sesión activa
    $id_usuario     = $_SESSION['id_usuario'] ?? 0;
    $id_horario_def = $db->filtrar($_POST['id_horario_def']);
    $dia_semana     = $db->filtrar($_POST['dia_semana']);

    try {
        // Consulta unificada: Ahora extrae el nombre y horas del bloque detallado
        $sql = "SELECT 
            c.id_paralelo, 
            c.id_asignatura, 
            c.id_horario_detalle,
            a.as_nombre,
            e.es_nombre, 
            p.pa_nombre AS paralelo_nombre,
            cur.cu_nombre AS curso_nombre,
            hd.nombre AS hora_nombre,       -- Nombre (Ej: Hora 1, Receso)
            hd.hora_inicio,                 -- Rango Inicio
            hd.hora_fin                     -- Rango Fin
        FROM sw_horario_clases c
        INNER JOIN sw_asignatura a        ON c.id_asignatura = a.id_asignatura
        INNER JOIN sw_horario_detalles hd ON c.id_horario_detalle = hd.id_horario_detalle 
        LEFT JOIN sw_paralelo p           ON c.id_paralelo = p.id_paralelo
        LEFT JOIN sw_curso cur            ON p.id_curso = cur.id_curso
        LEFT JOIN sw_especialidad e       ON e.id_especialidad = cur.id_especialidad 
        WHERE c.id_usuario = '$id_usuario' 
          AND c.id_horario_def = '$id_horario_def' 
          AND c.dia_semana = '$dia_semana'
        ORDER BY hd.hora_inicio ASC"; // Ordenado cronológicamente desde la primera hora

        $consulta = $db->consulta($sql);
        $listadoMaterias = array();

        while ($row = $db->fetch_assoc($consulta)) {
            $listadoMaterias[] = $row;
        }

        echo json_encode($listadoMaterias);
    } catch (Exception $e) {
        echo json_encode(array());
    }
} else {
    echo json_encode(array());
}
