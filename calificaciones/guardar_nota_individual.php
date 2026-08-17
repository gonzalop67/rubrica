<?php
// 1. Configuración de cabeceras para responder estrictamente en formato JSON
header('Content-Type: application/json; charset=utf-8');

// 2. Incluir e inicializar tu clase de conexión (Ajusta la ruta según tu proyecto)
require_once '../scripts/clases/class.mysql.php';
$db = new MySQL(); // Creamos la instancia según tu clase

// 3. Capturar, limpiar y sanitizar las variables recibidas usando tu método '$db->filtrar()'
$id_estudiante = isset($_POST['id_estudiante']) ? (int)$_POST['id_estudiante'] : 0;
$id_rubrica    = isset($_POST['id_rubrica'])    ? (int)$_POST['id_rubrica']    : 0;
$id_asignatura = isset($_POST['id_asignatura']) ? (int)$_POST['id_asignatura'] : 0;
$id_paralelo   = isset($_POST['id_paralelo'])   ? (int)$_POST['id_paralelo']   : 0;

// Validar la calificación y limpiarla
$calificacion_raw = isset($_POST['calificacion']) ? $db->filtrar($_POST['calificacion']) : '';
$calificacion     = ($calificacion_raw === '' || $calificacion_raw === '-') ? "NULL" : (float)$calificacion_raw;

// 🔒 VALIDACIÓN DE SEGURIDAD INTERNA
if ($id_estudiante <= 0 || $id_rubrica <= 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "Parámetros obligatorios faltantes o inválidos (Estudiante/Rúbrica)."
    ]);
    exit;
}

if ($calificacion !== "NULL") {
    if ($calificacion < 0 || $calificacion > 10) {
        echo json_encode([
            "status"  => "error",
            "message" => "La calificación enviada está fuera del rango permitido (0 - 10)."
        ]);
        exit;
    }
}

// 1. Capturar el tipo de asignatura enviado desde el cliente
$id_tipo_asignatura = isset($_POST['id_tipo_asignatura']) ? (int)$_POST['id_tipo_asignatura'] : 1;

$db->consulta("START TRANSACTION");

if ($id_tipo_asignatura === 1) {
    // =========================================================================
    // 🔢 CAMINO CUANTITATIVO: TABLA NUMÉRICA TRADICIONAL
    // =========================================================================
    if ($calificacion_raw === '' || $calificacion_raw === '-') {
        $sql_accion = "DELETE FROM sw_rubrica_estudiante 
                       WHERE id_estudiante = {$id_estudiante} 
                       AND id_rubrica_personalizada = {$id_rubrica}";
        $msg_exito = "Calificación numérica removida.";
    } else {
        $nota_num = (float)$calificacion_raw;
        $sql_accion = "INSERT INTO sw_rubrica_estudiante (id_estudiante, id_paralelo, id_asignatura, id_rubrica_personalizada, re_calificacion, fecha_registro) 
                       VALUES ({$id_estudiante}, {$id_paralelo}, {$id_asignatura}, {$id_rubrica}, {$nota_num}, NOW())
                       ON DUPLICATE KEY UPDATE re_calificacion = {$nota_num}, fecha_registro = NOW()";
        $msg_exito = "Calificación numérica guardada con éxito.";
    }
} else {
    // =========================================================================
    // 🔤 CAMINO CUALITATIVO: TU TABLA DE LETRAS OFICIAL
    // =========================================================================
    if ($calificacion_raw === '' || $calificacion_raw === '-') {
        // Si el profesor limpia la celda, removemos la fila de la tabla cualitativa
        $sql_accion = "DELETE FROM sw_rubrica_cualitativa 
                       WHERE id_estudiante = {$id_estudiante} 
                       AND id_rubrica_personalizada = {$id_rubrica}";
        $msg_exito = "Calificación cualitativa removida del sistema.";
    } else {
        // Si ingresa una letra (A+, B-, etc.), la guardamos o actualizamos entre comillas simples
        $sql_accion = "INSERT INTO sw_rubrica_cualitativa (id_estudiante, id_paralelo, id_asignatura, id_rubrica_personalizada, rc_calificacion, fecha_registro) 
                       VALUES ({$id_estudiante}, {$id_paralelo}, {$id_asignatura}, {$id_rubrica}, '{$calificacion_raw}', NOW())
                       ON DUPLICATE KEY UPDATE rc_calificacion = '{$calificacion_raw}', fecha_registro = NOW()";
        $msg_exito = "Calificación cualitativa guardada con éxito.";
    }
}

// 2. Ejecutar la instrucción SQL correspondiente
$resultado = $db->consulta($sql_accion);

if ($resultado) {
    $db->consulta("COMMIT");
    echo json_encode([
        "status"  => "success",
        "message" => $msg_exito
    ]);
} else {
    $db->consulta("ROLLBACK");
    echo json_encode([
        "status"  => "error",
        "message" => "Error interno al procesar la calificación en la base de datos."
    ]);
}
exit;
