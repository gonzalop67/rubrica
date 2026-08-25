<?php
// 1. Incluir el archivo donde definiste tu clase MySQL
include("../scripts/clases/class.mysql.php"); 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Instanciamos tu clase personalizada
    $db = new MySQL();

    // 2. Recibir y limpiar los datos usando tu método $db->filtrar()
    $id_horario_def     = $db->filtrar($_POST['id_horario_def'] ?? '');
    $id_periodo_lectivo = $db->filtrar($_POST['id_periodo_lectivo'] ?? '');
    $ho_titulo          = $db->filtrar($_POST['ho_titulo'] ?? '');
    $fecha_inicial      = $db->filtrar($_POST['fecha_inicial'] ?? '');
    $fecha_final        = $db->filtrar($_POST['fecha_final'] ?? '');
    $estado             = $db->filtrar($_POST['status'] ?? '1'); // Captura 'status' del POST, mapea a la columna 'estado'
    $hora_entrada       = $db->filtrar($_POST['hora_entrada'] ?? '');
    $nro_horas          = $db->filtrar($_POST['nro_horas'] ?? '0');
    $duracion           = $db->filtrar($_POST['duracion'] ?? '0');

    // 3. Recibir los arreglos actualizados de las horas dinámicas
    $detalle_nombres    = $_POST['detalle_hora_nombre'] ?? [];
    $detalle_inicios    = $_POST['detalle_hora_inicio'] ?? [];
    $detalle_fines      = $_POST['detalle_hora_fin'] ?? [];

    // Validar que tengamos el ID del registro maestro
    if (empty($id_horario_def) || $id_horario_def == "0") {
        echo json_encode([
            "titulo" => "Error de validación",
            "mensaje" => "No se especificó un ID de horario válido para actualizar.",
            "tipo_mensaje" => "error"
        ]);
        exit;
    }

    // Habilitar el reporte de errores en MySQLi para manejo de excepciones
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        // Iniciar transacción usando la propiedad conexion de tu clase
        mysqli_begin_transaction($db->conexion);

        // 4. ACTUALIZAR LA TABLA MAESTRA
        $sqlActualizarMaestro = "UPDATE sw_horario_def SET 
                                    id_periodo_lectivo = '$id_periodo_lectivo',
                                    ho_titulo          = '$ho_titulo',
                                    fecha_inicial      = '$fecha_inicial',
                                    fecha_final        = '$fecha_final',
                                    estado             = '$estado',
                                    hora_entrada       = '$hora_entrada',
                                    nro_horas          = '$nro_horas',
                                    duracion           = '$duracion'
                                 WHERE id_horario_def  = '$id_horario_def'";
        
        $db->consulta($sqlActualizarMaestro);

        // 5. RE-ESTRUCTURAR LOS DETALLES: Primero borramos los anteriores
        $sqlBorrarDetalles = "DELETE FROM sw_horario_detalles WHERE id_horario_def = '$id_horario_def'";
        $db->consulta($sqlBorrarDetalles);

        // 6. INSERTAR LOS NUEVOS BLOQUES ACTUALIZADOS
        for ($i = 0; $i < count($detalle_nombres); $i++) {
            
            $nombre_bloque = $db->filtrar($detalle_nombres[$i]);
            $hora_inicio   = $db->filtrar($detalle_inicios[$i]);
            $hora_fin      = $db->filtrar($detalle_fines[$i]);

            $sqlInsertarDetalle = "INSERT INTO sw_horario_detalles (
                                        id_horario_def, 
                                        nombre, 
                                        hora_inicio, 
                                        hora_fin
                                   ) VALUES (
                                        '$id_horario_def', 
                                        '$nombre_bloque', 
                                        '$hora_inicio', 
                                        '$hora_fin'
                                   )";
            
            $db->consulta($sqlInsertarDetalle);
        }

        // Si todo el proceso se ejecutó de forma correcta, guardamos cambios en la BDD
        mysqli_commit($db->conexion);

        echo json_encode([
            "titulo" => "¡Actualizado!",
            "mensaje" => "El horario y sus bloques de horas se actualizaron correctamente.",
            "tipo_mensaje" => "success"
        ]);

    } catch (Exception $e) {
        // Si algo falla, revertimos y dejamos los datos intactos como estaban antes
        mysqli_rollback($db->conexion);

        echo json_encode([
            "titulo" => "Error al actualizar",
            "mensaje" => "No se guardaron los cambios: " . $e->getMessage(),
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
