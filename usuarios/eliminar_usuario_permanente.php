<?php
header('Content-Type: application/json');

// 1. Incluir las dependencias requeridas
require_once "../scripts/clases/class.mysql.php";
require_once "../scripts/clases/class.usuarios.php"; // Cambia al nombre real de tu clase o modelo de usuarios

// 2. Verificar que se reciba el ID por método POST
$id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;

if ($id_usuario === 0) {
    echo json_encode([
        "success" => false,
        "titulo"  => "Error de petición.",
        "message" => "El ID de usuario proporcionado no es válido para la destrucción.",
        "estado"  => "error"
    ]);
    exit;
}

try {
    // 3. Instanciar el objeto y asignar la propiedad del ID
    $usuario = new usuarios();
    $usuario->code = $id_usuario; // Asigna el ID a la propiedad que usa tu método destruirUsuarioPermanente()

    // 4. Ejecutar el método físico destructivo y retornar su respuesta JSON directa
    echo $usuario->destruirUsuarioPermanente();

} catch (\Throwable $e) {
    echo json_encode([
        "success" => false,
        "titulo"  => "Error crítico.",
        "message" => "No se pudo borrar el usuario permanentemente: " . $e->getMessage(),
        "estado"  => "error"
    ]);
}
exit;
