<?php
// 1. Iniciar la sesión para acceder a los datos del docente logueado
session_start();

// 2. Configurar las cabeceras para que responda en formato JSON estricto
header('Content-Type: application/json; charset=utf-8');

// 3. Incluir e instanciar tu clase personalizada de conexión
include("../scripts/clases/class.mysql.php");
$db = new MySQL();

// 4. Validar parámetros obligatorios por POST y por SESSION
if (!isset($_POST['id_paralelo']) || empty($_POST['id_paralelo'])) {
    echo json_encode(["error" => "Falta el parámetro id_paralelo"]);
    exit;
}

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_periodo_lectivo'])) {
    echo json_encode(["error" => "Sesión expirada o usuario no autorizado"]);
    exit;
}

// 5. Sanitizar variables usando el método filtrar() de tu clase para evitar Inyección SQL
$id_paralelo        = $db->filtrar($_POST['id_paralelo']);
$id_usuario         = $db->filtrar($_SESSION['id_usuario']);
$id_periodo_lectivo = $db->filtrar($_SESSION['id_periodo_lectivo']);

try {
    /**
     * 6. Consulta SQL Corregida.
     * He retirado 'es_figura' que causaba el fallo, manteniendo exactamente
     * la estructura relacional que necesita tu base de datos.
     */
    $sql = "SELECT 
                c.id_curso, 
                d.id_paralelo, 
                d.id_asignatura, 
                a.id_tipo_asignatura, 
                a.as_nombre, 
                c.cu_nombre, 
                pa.pa_nombre 
            FROM 
                sw_asignatura a, 
                sw_distributivo d, 
                sw_paralelo pa, 
                sw_curso c, 
                sw_especialidad e 
            WHERE 
                a.id_asignatura = d.id_asignatura 
                AND d.id_paralelo = pa.id_paralelo 
                AND pa.id_curso = c.id_curso 
                AND c.id_especialidad = e.id_especialidad 
                AND d.id_usuario = '$id_usuario' 
                AND d.id_paralelo = '$id_paralelo' 
                AND d.id_periodo_lectivo = '$id_periodo_lectivo' 
                AND a.as_curricular = 1 
            ORDER BY 
                c.id_curso, 
                pa.id_paralelo, 
                a.as_nombre ASC";
            
    $resultado_consulta = $db->consulta($sql);
    
    // 7. Mapear los registros al arreglo que espera tu interfaz JavaScript
    $asignaturas = array();
    
    while ($fila = $db->fetch_assoc($resultado_consulta)) {
        $asignaturas[] = array(
            "id_asignatura" => $fila['id_asignatura'],
            "tipo_asignatura" => $fila['id_tipo_asignatura'],
            "nombre_asignatura" => $fila['as_nombre'],
            "curso" => $fila['cu_nombre'],
            "paralelo" => $fila['pa_nombre']
        );
    }
    
    // 8. Retornar el JSON limpio al cliente front-end
    echo json_encode($asignaturas);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error interno en la base de datos: " . $e->getMessage()]);
}
