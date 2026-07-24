<?php 
require_once("../scripts/clases/class.mysql.php"); 
$db = new MySQL(); 

// 1. Limpiar y validar parámetros entrantes
$emisor_id   = isset($_POST["emisor_id"]) ? intval($_POST["emisor_id"]) : 0;
$receptor_id = isset($_POST["receptor_id"]) ? intval($_POST["receptor_id"]) : 0;

if ($emisor_id > 0 && $receptor_id > 0) {
    
    // 2. Consulta corregida con la estructura real de tus tablas
    $query = "SELECT m.*, u.us_nombres AS nombre_emisor 
              FROM sw_mensajes m
              INNER JOIN sw_usuario u ON m.emisor_id = u.id_usuario
              WHERE (m.emisor_id = $emisor_id AND m.receptor_id = $receptor_id) 
                 OR (m.emisor_id = $receptor_id AND m.receptor_id = $emisor_id) 
              ORDER BY m.fecha ASC";

    $result = $db->consulta($query);

    if ($db->num_rows($result) > 0) {
        
        // 3. Bucle de renderizado para AdminLTE 2
        while ($row = $db->fetch_assoc($result)) {
            
            // Determinar orientación de la burbuja de chat
            $es_mio       = ($row['emisor_id'] == $emisor_id);
            $clase_lado   = $es_mio ? 'right' : '';
            $clase_nombre = $es_mio ? 'pull-right' : 'pull-left';
            $clase_fecha  = $es_mio ? 'pull-left' : 'pull-right';
            
            // Limpieza de datos contra vulnerabilidades XSS
            $nombre_mostrar = htmlspecialchars($row['nombre_emisor']);
            $mensaje_texto  = htmlspecialchars($row['mensaje']);
            $fecha_formato  = date('d M h:i a', strtotime($row['fecha']));
            
            echo '
            <div class="direct-chat-msg ' . $clase_lado . '">
                <div class="direct-chat-info clearfix">
                    <!-- Muestra el nombre real mapeado desde us_nombres -->
                    <span class="direct-chat-name ' . $clase_nombre . '">' . $nombre_mostrar . ' dice...</span>
                    <span class="direct-chat-timestamp ' . $clase_fecha . '">' . $fecha_formato . '</span>
                </div>
                <div class="direct-chat-text">
                    ' . $mensaje_texto . '
                </div>
            </div>';
        }
        
    } else {
        echo '<div class="text-center text-muted" style="padding: 20px;">No hay mensajes previos. ¡Comienza la conversación!</div>';
    }

} else {
    echo '<div class="alert alert-danger">Error: Parámetros de chat no válidos.</div>';
}
?>
