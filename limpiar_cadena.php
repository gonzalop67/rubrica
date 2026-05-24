<?php
// Cadena de texto con múltiples espacios y espacios al inicio/final
$cadena = "  Hola   mundo PHP  ";

// 1. Limpiar espacios al inicio y final
$cadena_limpia = trim($cadena);

// 2. Reemplazar uno o más espacios entre palabras por un solo espacio
$resultado = preg_replace('/\s+/', ' ', $cadena_limpia);

echo $resultado; // Salida: "Hola mundo PHP"
?>