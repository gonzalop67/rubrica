<?php
include_once "../scripts/clases/class.mysql.php";

$nombre = $_POST["nombre"];
$db = new MySQL();

$query = "SELECT id_categoria "
    . " FROM sw_categoria_especialidad "
    . "WHERE nombre = '" . $nombre . "' ";

$consulta = $db->consulta($query);

if ($db->num_rows($consulta) > 0) {
    $datos = [
        'titulo' => "¡Error!",
        'mensaje' => "Ya existe el nombre de la categoria de especialidad en la base de datos.",
        'estado' => 'error'
    ];
} else {
    $qry = "INSERT INTO sw_categoria_especialidad (nombre) VALUES (";
    $qry .= "'" . $nombre . "')";

    try {
        $consulta = $db->consulta($qry);

        $datos = [
            'titulo' => "¡Agregado con éxito!",
            'mensaje' => "Inserción realizada exitosamente.",
            'estado' => 'success'
        ];
    } catch (\Throwable $th) {
        $datos = [
            'titulo' => "¡Error!",
            'mensaje' => "No se pudo realizar la inserción. Error: " . $th->getMessage(),
            'estado' => 'error'
        ];
    }
}

echo json_encode($datos);
