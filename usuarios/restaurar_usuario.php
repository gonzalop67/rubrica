<?php
header('Content-Type: application/json');

// 1. Incluir la clase del modelo o controlador correspondiente
// Ajusta estas rutas según la estructura exacta de carpetas de tu proyecto
require_once "../scripts/clases/class.mysql.php"; 
require_once "../scripts/clases/class.usuarios.php"; // Cambia al nombre real de tu clase o modelo de usuarios

// 2. Verificar que se reciba el ID por método POST
$id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;

if ($id_usuario === 0) {
    echo json_encode([
        "success" => false,
        "titulo"  => "Error de petición.",
        "message" => "El ID de usuario proporcionado no es válido.",
        "estado"  => "error"
    ]);
    exit;
}

try {
    // 3. Instanciar el objeto e inicializar el ID en la propiedad correspondiente (ej. $code)
    $usuario = new usuarios(); 
    $usuario->code = $id_usuario; // Asigna el ID a la propiedad que usa tu método restoreUsuario()

    // 4. Ejecutar el método que creamos anteriormente y retornar su respuesta JSON directa
    echo $usuario->restoreUsuario();

} catch (\Throwable $e) {
    echo json_encode([
        "success" => false,
        "titulo"  => "Error en el servidor.",
        "message" => "No se pudo completar el proceso de restauración: " . $e->getMessage(),
        "estado"  => "error"
    ]);
}
exit;
