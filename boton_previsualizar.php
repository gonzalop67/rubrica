<?php
// Tu contenido dinámico en PHP
$titulo = "Reporte de Ventas";
$contenido = "Este es un ejemplo de contenido generado con PHP.";
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?php echo $titulo; ?></title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .boton { padding: 10px 15px; background: #007BFF; color: white; border: none; cursor: pointer; }
    .boton:hover { background: #0056b3; }
  </style>
</head>
<body>
  <h1><?php echo $titulo; ?></h1>
  <p><?php echo $contenido; ?></p>

  <!-- Botón para imprimir -->
  <button class="boton" onclick="window.print()">🖨️ Previsualizar / Imprimir</button>
</body>
</html>