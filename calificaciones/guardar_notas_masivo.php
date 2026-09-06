<?php
// 1. Asegurar que la respuesta devuelta siempre sea un JSON limpio para jQuery
header('Content-Type: application/json; charset=utf-8');

// Incluir tu clase de conexión si está en otro archivo, por ejemplo:
require_once("../scripts/clases/class.mysql.php");

try {
    // Verificar que la petición sea estrictamente por método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método de comunicación no permitido.");
    }

    // 2. Instanciar tu clase MySQL
    $db = new MySQL();

    // 3. Capturar y limpiar variables de contexto enviadas por el AJAX
    $id_paralelo  = isset($_POST['id_paralelo']) ? intval($_POST['id_paralelo']) : 0;
    $id_asignatura = isset($_POST['id_asignatura']) ? intval($_POST['id_asignatura']) : 0;
    $notas_json    = isset($_POST['notas']) ? $_POST['notas'] : '';

    if (empty($notas_json)) {
        throw new Exception("No se recibieron calificaciones para almacenar.");
    }

    // 4. Decodificar la cadena JSON fuerte enviada desde JavaScript
    $loteCalificaciones = json_decode($notas_json, true);

    if (!is_array($loteCalificaciones)) {
        throw new Exception("El formato de los datos de calificaciones es incorrecto.");
    }

    $contadorGuardados = 0;
    $contadorEliminados = 0;

    // 5. Recorrer el lote optimizado de notas modificadas
    foreach ($loteCalificaciones as $nota) {
        // Extraer y sanitizar rigurosamente cada propiedad usando tu método ->filtrar()
        $id_estudiante = intval($nota['id_estudiante']);
        $id_rubrica    = intval($nota['id_rubrica']);
        
        // El valor de la calificación puede ser un número (ej: "8.50") o vacío "" (si se borró)
        $calificacion  = $db->filtrar($nota['calificacion']);

        // 🎯 LÓGICA DE DETECCIÓN: Verificar si la nota ya existe físicamente en tu tabla
        // NOTA: Ajusta los nombres de la tabla ('calificaciones') y columnas según tu esquema real de BD.
        $sqlCheck = "SELECT id_rubrica_estudiante FROM sw_rubrica_estudiante 
                     WHERE id_estudiante = $id_estudiante 
                       AND id_asignatura = $id_asignatura 
                       AND id_rubrica_personalizada = $id_rubrica 
                       AND id_paralelo = $id_paralelo 
                     LIMIT 1";
                     
        $resCheck = $db->consulta($sqlCheck);
        $existeNota = ($db->num_rows($resCheck) > 0);

        if ($calificacion === "") {
            // ❌ ESCENARIO A: El casillero se borró en la pantalla. Si existía en BD, se elimina.
            if ($existeNota) {
                $sqlDelete = "DELETE FROM sw_rubrica_estudiante 
                              WHERE id_estudiante = $id_estudiante 
                                AND id_asignatura = $id_asignatura 
                                AND id_rubrica_personalizada = $id_rubrica 
                                AND id_paralelo = $id_paralelo";
                $db->consulta($sqlDelete);
                $contadorEliminados++;
            }
        } else {
            // Aseguramos que sea un valor numérico seguro antes de guardarlo
            $notaFinal = floatval($calificacion);

            if ($existeNota) {
                // 🔄 ESCENARIO B: La nota ya existía previamente, se procede a ACTUALIZAR (UPDATE)
                $sqlUpdate = "UPDATE sw_rubrica_estudiante 
                              SET re_calificacion = $notaFinal,
                                  fecha_registro = NOW()
                              WHERE id_estudiante = $id_estudiante 
                                AND id_asignatura = $id_asignatura 
                                AND id_rubrica_personalizada = $id_rubrica 
                                AND id_paralelo   = $id_paralelo";
                $db->consulta($sqlUpdate);
            } else {
                // 🆕 ESCENARIO C: Registro totalmente nuevo, se procede a INSERTAR (INSERT)
                $sqlInsert = "INSERT INTO sw_rubrica_estudiante (id_estudiante, id_asignatura, id_paralelo, id_rubrica_personalizada, re_calificacion, fecha_registro) 
                              VALUES ($id_estudiante, $id_asignatura, $id_paralelo, $id_rubrica, $notaFinal, NOW())";
                $db->consulta($sqlInsert);
            }
            $contadorGuardados++;
        }
    }

    // 6. Armar mensaje de respuesta dinámico y exitoso para SweetAlert2
    $msg = "Se procesaron los cambios con éxito.";
    if ($contadorGuardados > 0) $msg .= " Almacenadas: $contadorGuardados.";
    if ($contadorEliminados > 0) $msg .= " Limpiadas: $contadorEliminados.";

    echo json_encode([
        "status" => "success",
        "message" => $msg
    ]);

} catch (Exception $e) {
    // Si ocurre un error controlado o de base de datos, respondemos un JSON estructurado de error
    // Usamos el código 400 para que jQuery sepa que es una respuesta fallida controlada
    http_response_code(400); 
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
