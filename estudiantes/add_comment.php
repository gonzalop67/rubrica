<?php

//add_comment.php

require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$error = '';
$comment_content = '';
$id_estudiante = '';

$comment_content = $_POST["comment_content"];
$id_comentario_padre = $_POST["comment_id"];
$id_estudiante = $_POST["id_estudiante"];

// Obtengo el nombre completo del estudiante
$query = "
SELECT es_nombre_completo
FROM sw_estudiante
WHERE id_estudiante = $id_estudiante
";
$result = $db->consulta($query);
$estudiante = $db->fetch_assoc($result);
$nombreCompleto = $estudiante["es_nombre_completo"];

if($error == '')
{
    $query = "
    INSERT INTO sw_comentario 
    (id_comentario_padre, co_id_usuario, co_tipo, co_perfil, co_nombre, co_texto) 
    VALUES ($id_comentario_padre, $id_estudiante, 1, 'ESTUDIANTE', '$nombreCompleto', '$comment_content')
    ";

    $consulta = $db->consulta($query);
    if($consulta){
        $error = '<label class="text-success">Comentario A&ntilde;adido</label>';
    } else {
        $error .= '<p class="text-danger">No se pudo insertar el comentario...Error: '.mysql_error().'<br />Consulta: '.$query.'</p>';
    }
    
}

$data = array(
    'error'  => $error
);

echo json_encode($data);

?>