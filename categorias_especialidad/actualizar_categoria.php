<?php
include_once "../scripts/clases/class.mysql.php";

// Validaciones
$db = new MySQL();

$id_categoria = $_POST['id_categoria'];
$nombreu = $_POST['nombreu'];

try {
    $query = "SELECT id_categoria "
        . " FROM sw_categoria_especialidad "
        . "WHERE nombre = '" . $nombreu . "'";

    $consulta = $db->consulta($query);

    $existeCategoria = $db->num_rows($consulta) > 0;

    $consulta = $db->consulta("SELECT * FROM sw_categoria_especialidad WHERE id_categoria = " . $id_categoria);
    $categoriaActual = $db->fetch_object($consulta);

    if ($categoriaActual->nombre != $_POST['nombreu'] && $existeCategoria) {
        $datos = [
            'titulo' => "¡Error!",
            'mensaje' => "Ya existe el nombre de la categoria de especialidad en la base de datos.",
            'estado' => 'error'
        ];
    } else {
        //Actualizar
        try {
            $query = 'UPDATE sw_categoria_especialidad SET nombre = "' . trim($nombreu) . '" WHERE id_categoria = ' . $id_categoria;

            $consulta = $db->consulta($query);

            $datos = [
                'titulo' => "¡Registro actualizado con éxito!",
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
} catch (\Throwable $e) {
    $datos = [
        'titulo' => "¡Error!",
        'mensaje' => "No se pudo realizar la actualización. Error: " . $e->getMessage(),
        'estado' => 'error'
    ];
}

//Enviar el objeto en formato json
echo json_encode($datos);
