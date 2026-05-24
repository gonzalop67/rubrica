<?php
include_once "../scripts/clases/class.mysql.php";

// Validaciones
$db = new MySQL();

$query = "SELECT id "
        . " FROM sw_exam_category "
        . "WHERE category = '" . $_POST['category'] . "' "
        . "  AND id_usuario = " . $_POST['id_usuario'];

$consulta = $db->consulta($query);

$existeCategoria = $db->num_rows($consulta) > 0;

$consulta = $db->consulta("SELECT * FROM sw_exam_category WHERE id = " . $_POST['id']);
$categoriaActual = $db->fetch_object($consulta);

if ($categoriaActual->category != $_POST['category'] && $existeCategoria) {
    $datos = [
        'titulo' => "¡Error!",
        'mensaje' => "Ya existe el nombre de la categoria en la base de datos.",
        'estado' => 'error'
    ];
} else {
    //Actualizar
    try {
        $query = 'UPDATE sw_exam_category SET category = "' . trim($_POST['category']) . '", exam_time_in_minutes = ' . trim($_POST['exam_time_in_minutes']) . ' WHERE id = ' . trim($_POST['id']);

        $consulta = $db->consulta($query);
        
        $datos = [
            'titulo' => "¡Actualizado con éxito!",
            'mensaje' => "Actualización realizada exitosamente.",
            'estado' => 'success'
        ];
    } catch (\Throwable $th) {
        $datos = [
            'titulo' => "¡Error!",
            'mensaje' => "No se pudo realizar la actualización. Error: " . $th->getMessage(),
            'estado' => 'error'
        ];
    }
} 

//Enviar el objeto en formato json
echo json_encode($datos);
