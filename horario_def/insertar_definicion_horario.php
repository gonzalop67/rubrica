<?php
// 1. Incluir el archivo donde definiste tu clase MySQL
include("../scripts/clases/class.mysql.php"); 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Instanciamos tu clase personalizada
    $db = new MySQL();

    // 2. Recibir y limpiar los datos usando tu método $db->filtrar()
    $id_periodo_lectivo = $db->filtrar($_POST['id_periodo_lectivo'] ?? '');
    $ho_titulo          = $db->filtrar($_POST['ho_titulo'] ?? '');
    $fecha_inicial      = $db->filtrar($_POST['fecha_inicial'] ?? '');
    $fecha_final        = $db->filtrar($_POST['fecha_final'] ?? '');
    $estado             = $db->filtrar($_POST['status'] ?? '1'); // Captura 'status' del POST, pero limpia para la columna 'estado'
    $hora_entrada       = $db->filtrar($_POST['hora_entrada'] ?? '');
    $nro_horas          = $db->filtrar($_POST['nro_horas'] ?? '0');
    $duracion           = $db->filtrar($_POST['duracion'] ?? '0');

    // 3. Recibir los arreglos de las horas dinámicas (Nombres, inicios y fines)
    $detalle_nombres    = $_POST['detalle_hora_nombre'] ?? [];
    $detalle_inicios    = $_POST['detalle_hora_inicio'] ?? [];
    $detalle_fines      = $_POST['detalle_hora_fin'] ?? [];

    // Habilitar el reporte de errores en MySQLi para que tire excepciones si falla algo dentro de try/catch
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        // Iniciar transacción usando la propiedad conexion de tu clase
        mysqli_begin_transaction($db->conexion);

        // 4. INSERTAR EN LA TABLA MAESTRA (Usando tus columnas reales: sw_horario_def y estado)
        $sqlPrincipal = "INSERT INTO sw_horario_def (
                            id_periodo_lectivo, 
                            ho_titulo, 
                            fecha_creacion, 
                            fecha_inicial, 
                            fecha_final, 
                            estado, 
                            hora_entrada, 
                            nro_horas, 
                            duracion
                         ) VALUES (
                            '$id_periodo_lectivo', 
                            '$ho_titulo', 
                            NOW(), 
                            '$fecha_inicial', 
                            '$fecha_final', 
                            '$estado', 
                            '$hora_entrada', 
                            '$nro_horas', 
                            '$duracion'
                         )";
        
        // Ejecutar consulta principal
        $db->consulta($sqlPrincipal);

        // Obtener el ID autoincremental asignado de forma segura
        $id_horario_reciente = mysqli_insert_id($db->conexion);

        // 5. INSERTAR EN LA TABLA DETALLE (Ajusta 'sw_horario_detalles' si usas el prefijo 'sw_')
        for ($i = 0; $i < count($detalle_nombres); $i++) {
            
            // Limpiamos individualmente cada celda del arreglo antes de meterlo al SQL
            $nombre_bloque = $db->filtrar($detalle_nombres[$i]);
            $hora_inicio   = $db->filtrar($detalle_inicios[$i]);
            $hora_fin      = $db->filtrar($detalle_fines[$i]);

            $sqlDetalle = "INSERT INTO sw_horario_detalles (
                                id_horario_def, 
                                nombre, 
                                hora_inicio, 
                                hora_fin
                           ) VALUES (
                                '$id_horario_reciente', 
                                '$nombre_bloque', 
                                '$hora_inicio', 
                                '$hora_fin'
                           )";
            
            $db->consulta($sqlDetalle);
        }

        // Si todas las inserciones fueron exitosas, confirmamos definitivamente en la base de datos
        mysqli_commit($db->conexion);

        echo json_encode([
            "titulo" => "¡Excelente!",
            "mensaje" => "El horario y sus bloques de horas fueron guardados con éxito.",
            "tipo_mensaje" => "success"
        ]);

    } catch (Exception $e) {
        // Si hay cualquier error en el proceso, cancelamos todo para no dejar filas huérfanas
        mysqli_rollback($db->conexion);

        echo json_encode([
            "titulo" => "Error del Sistema",
            "mensaje" => "No se pudo registrar: " . $e->getMessage(),
            "tipo_mensaje" => "error"
        ]);
    }

} else {
    echo json_encode([
        "titulo" => "Acceso denegado",
        "mensaje" => "Método de petición no permitido.",
        "tipo_mensaje" => "error"
    ]);
}
