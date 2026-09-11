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
$id_aporte     = isset($_POST['id_aporte'])     ? (int)$_POST['id_aporte']     : 0; // 🎯 NUEVO: Necesario para el comportamiento
$id_periodo    = isset($_POST['id_periodo'])    ? (int)$_POST['id_periodo']    : 0; // 🎯 NUEVO: Necesario para el comportamiento

// 🎯 NUEVO PARAMETRO: Identifica si es una nota académica o de conducta
$tipo_nota     = isset($_POST['tipo_nota'])     ? trim($_POST['tipo_nota'])    : 'academica'; 

// Validar la calificación y limpiarla
$calificacion_raw = isset($_POST['calificacion']) ? $db->filtrar($_POST['calificacion']) : '';

// 🔒 VALIDACIÓN DE SEGURIDAD INTERNA ADAPTADA
// 🎯 CORRECCIÓN: Si es comportamiento, permitimos que id_rubrica sea 0. Si es académica, se mantiene estricto.
if ($id_estudiante <= 0 || ($tipo_nota === 'academica' && $id_rubrica <= 0)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Parámetros obligatorios faltantes o inválidos (Estudiante/Rúbrica)."
    ]);
    exit;
}

// 🔒 VALIDACIÓN DE SEGURIDAD ADAPTADA PARA COMPORTAMIENTO
if ($tipo_nota === 'comportamiento') {
    if ($id_aporte <= 0) {
        echo json_encode([
            "status"  => "error",
            "message" => "Parámetro obligatorio faltante para conducta (Aporte/Parcial)."
        ]);
        exit;
    }
} else {
    // Tratamiento numérico tradicional exclusivo para notas académicas cuantitativas
    $calificacion = ($calificacion_raw === '' || $calificacion_raw === '-') ? "NULL" : (float)$calificacion_raw;

    if ($calificacion !== "NULL") {
        if ($calificacion < 0 || $calificacion > 10) {
            echo json_encode([
                "status"  => "error",
                "message" => "La calificación enviada está fuera del rango permitido (0 - 10)."
            ]);
            exit;
        }
    }
}

// 1. Capturar el tipo de asignatura enviado desde el cliente
$id_tipo_asignatura = isset($_POST['id_tipo_asignatura']) ? (int)$_POST['id_tipo_asignatura'] : 1;

$db->consulta("START TRANSACTION");

// =========================================================================
// 🎯 ENRUTAMIENTO SEGÚN EL TIPO DE NOTA (COMPORTAMIENTO VS ACADÉMICA)
// =========================================================================
if ($tipo_nota === 'comportamiento') {
    
    // =========================================================================
    // 🎭 CAMINO COMPORTAMIENTO (TABLA: sw_calificacion_comportamiento)
    // =========================================================================
    if ($calificacion_raw === '' || $calificacion_raw === '-') {
        // Si el profesor borra la celda, removemos la fila de conducta
        $sql_accion = "DELETE FROM sw_calificacion_comportamiento 
                       WHERE id_estudiante = {$id_estudiante} 
                       AND id_paralelo = {$id_paralelo}
                       AND id_asignatura = {$id_asignatura}
                       AND id_aporte_evaluacion = {$id_aporte}";
        $msg_exito = "Nota de comportamiento removida.";
    } else {
        // 1. Limpieza y estandarización a mayúsculas
        $letra = strtoupper(trim($calificacion_raw));
        
        // 2. Mapeo dinámico basado en tu tabla 'sw_escala_comportamiento'
        // Letra -> [ec_correlativa (int), ec_equivalencia (varchar)]
        $mapa_escala = [
            'S' => 5, // Muy satisfactorio
            'F' => 4, // Satisfactorio
            'O' => 3, // Poco satisfactorio
            'M' => 2, // Mejorable (Añadido por consistencia con tu base de datos)
            'N' => 1  // Insatisfactorio
        ];

        // 3. Validar si la letra enviada existe en tu escala oficial
        if (!array_key_exists($letra, $mapa_escala)) {
            $db->consulta("ROLLBACK");
            echo json_encode(["status" => "error", "message" => "Letra de comportamiento '{$letra}' no válida en la escala institucional."]);
            exit;
        }

        // 4. Extraer el valor numérico correlativo correspondiente
        $valor_numerico_correlativa = $mapa_escala[$letra];

        // 5. Query exacta respetando los tipos de campos de tu tabla 'DESCRIBE sw_calificacion_comportamiento'
        $sql_accion = "INSERT INTO sw_calificacion_comportamiento 
                       (id_paralelo, id_estudiante, id_aporte_evaluacion, id_asignatura, co_calificacion, co_cualitativa) 
                       VALUES 
                       ({$id_paralelo}, {$id_estudiante}, {$id_aporte}, {$id_asignatura}, {$valor_numerico_correlativa}, '{$letra}')
                       ON DUPLICATE KEY UPDATE 
                       co_calificacion = {$valor_numerico_correlativa}, 
                       co_cualitativa = '{$letra}'";
                       
        $msg_exito = "Nota de comportamiento [{$letra}] guardada correctamente.";
    }

} else {

    // =========================================================================
    // 📚 CAMINO ACADÉMICO TRADICIONAL (CUANTITATIVO O CUALITATIVO)
    // =========================================================================
    if ($id_tipo_asignatura === 1) {
        // 🔢 CAMINO CUANTITATIVO: TABLA NUMÉRICA TRADICIONAL
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
        // 🔤 CAMINO CUALITATIVO: TU TABLA DE LETRAS OFICIAL
        if ($calificacion_raw === '' || $calificacion_raw === '-') {
            $sql_accion = "DELETE FROM sw_rubrica_cualitativa 
                           WHERE id_estudiante = {$id_estudiante} 
                           AND id_rubrica_personalizada = {$id_rubrica}";
            $msg_exito = "Calificación cualitativa removida del sistema.";
        } else {
            $sql_accion = "INSERT INTO sw_rubrica_cualitativa (id_estudiante, id_paralelo, id_asignatura, id_rubrica_personalizada, rc_calificacion, fecha_registro) 
                           VALUES ({$id_estudiante}, {$id_paralelo}, {$id_asignatura}, {$id_rubrica}, '{$calificacion_raw}', NOW())
                           ON DUPLICATE KEY UPDATE rc_calificacion = '{$calificacion_raw}', fecha_registro = NOW()";
            $msg_exito = "Calificación cualitativa guardada con éxito.";
        }
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
