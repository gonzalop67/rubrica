<?php
// 1. Forzar que la respuesta del servidor sea interpretada siempre como JSON
header('Content-Type: application/json; charset=utf-8');

include("../scripts/clases/class.mysql.php");
include("../scripts/clases/class.horarios.php");

try {
    // 2. Capturar todas las variables enviadas por el formulario AJAX
    $ho_titulo          = isset($_POST["ho_titulo"]) ? trim($_POST["ho_titulo"]) : '';
    $fecha_inicial      = isset($_POST["fecha_inicial"]) ? trim($_POST["fecha_inicial"]) : '';
    $fecha_final        = isset($_POST["fecha_final"]) ? trim($_POST["fecha_final"]) : '';
    $id_periodo_lectivo = isset($_POST["id_periodo_lectivo"]) ? trim($_POST["id_periodo_lectivo"]) : '';
    
    // CORREGIDO: Captura de los 3 nuevos campos que agregamos en JavaScript
    $hora_entrada       = isset($_POST["hora_entrada"]) ? trim($_POST["hora_entrada"]) : '';
    $nro_horas          = isset($_POST["nro_horas"]) ? trim($_POST["nro_horas"]) : '';
    $duracion           = isset($_POST["duracion"]) ? trim($_POST["duracion"]) : '';

    // 3. Validación de seguridad en el backend por si falla el JavaScript
    if (empty($ho_titulo) || empty($fecha_inicial) || empty($fecha_final) || empty($id_periodo_lectivo) || empty($hora_entrada) || empty($nro_horas) || empty($duracion)) {
        echo json_encode([
            "titulo" => "Error de Validación",
            "mensaje" => "Todos los campos obligatorios deben ser completados.",
            "tipo_mensaje" => "error"
        ]);
        exit;
    }

    // 4. Construcción del array indexado con los datos completos
    $datos = [
        'ho_titulo'          => $ho_titulo,
        'fecha_inicial'      => $fecha_inicial,
        'fecha_final'        => $fecha_final,
        'id_periodo_lectivo' => $id_periodo_lectivo,
        'hora_entrada'       => $hora_entrada,  // NUEVO
        'nro_horas'          => $nro_horas,     // NUEVO
        'duracion'           => $duracion       // NUEVO
    ];

    $horarios = new horarios();
    
    // 5. Procesamiento y retorno de la respuesta
    // NOTA: Asegúrate de que tu método insertarTituloHorario() retorne un string en formato JSON válido
    // o un array que puedas pasar por json_encode().
    $resultado = $horarios->insertarTituloHorario($datos);
    
    echo $resultado;

} catch (Exception $e) {
    // Captura de errores inesperados para evitar que la página muera en blanco
    echo json_encode([
        "titulo" => "Ocurrió un error inesperado!",
        "mensaje" => "Error en el servidor: " . $e->getMessage(),
        "tipo_mensaje" => "error"
    ]);
}
?>

