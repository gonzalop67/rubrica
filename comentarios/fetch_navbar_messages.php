<?php
// fetch_navbar_messages.php
date_default_timezone_set('America/Guayaquil');

require_once("../scripts/clases/class.mysql.php");
require_once("../scripts/clases/class.encrypter.php");

$db = new MySQL();

// Asegurar que recibimos el ID del usuario logueado
$id_usuario = isset($_POST["emisor_id"]) ? intval($_POST["emisor_id"]) : 0;
$id_perfil = isset($_POST["id_perfil"]) ? intval($_POST["id_perfil"]) : 0;

if ($id_usuario > 0) {
    // 1. Contar mensajes recibidos
    $mensajes_count = $db->consulta("SELECT COUNT(*) AS num_rows FROM sw_mensajes WHERE receptor_id = $id_usuario");
    $num_mensajes = $db->fetch_object($mensajes_count)->num_rows;
    $terminacion = $num_mensajes == 1 ? '' : 's';

    // 2. Obtener el listado de mensajes con la foto del emisor
    // CORREGIDO: Se agregó m.emisor_id a la lista de selección
    $sql = "SELECT u.us_foto, m.emisor_id, m.perfil_emisor, m.fecha, m.mensaje 
        FROM sw_usuario u
        INNER JOIN sw_mensajes m ON u.id_usuario = m.emisor_id 
        WHERE m.receptor_id = $id_usuario
        ORDER BY m.fecha DESC 
        LIMIT 5";

    $mensajes_list = $db->consulta($sql);

    // Función auxiliar para humanizar el tiempo dentro del entorno AJAX
    function tiempoTranscurrido($fecha)
    {
        $timestamp = strtotime($fecha);
        $diferencia = time() - $timestamp;
        if ($diferencia < 60) return 'Ahora mismo';
        if ($diferencia < 3600) return 'Hace ' . floor($diferencia / 60) . ' min';
        if ($diferencia < 86400) return 'Hace ' . floor($diferencia / 3600) . ' hr';
        return date('d/m/Y', $timestamp);
    }
?>

    <!-- Menu toggle button -->
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="<?php echo $num_mensajes . ' mensaje' . $terminacion; ?>">
        <i class="fa fa-comments-o"></i>
        <?php if ($num_mensajes > 0): ?>
            <span class="label label-danger"><?php echo $num_mensajes; ?></span>
        <?php endif; ?>
    </a>
    <ul class="dropdown-menu">
        <li class="header"><?php echo "Tienes " . $num_mensajes . " mensaje" . $terminacion; ?></li>
        <li>
            <!-- inner menu: contains the messages -->
            <ul class="menu">
                <?php
                while ($row = $db->fetch_object($mensajes_list)) {
                    $foto = (!empty($row->us_foto)) ? $row->us_foto : 'default.png'; // Imagen de respaldo si está vacía
                    $texto_corto = (mb_strlen($row->mensaje, 'UTF-8') > 32) ? mb_substr($row->mensaje, 0, 32, 'UTF-8') . "..." : $row->mensaje;
                ?>
                    <li><!-- start message -->
                        <a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&enlace=comentarios.php&nivel=0&id_receptor=<?php echo $row->emisor_id; ?>">
                            <div class="pull-left">
                                <img src="public/uploads/<?php echo htmlspecialchars($foto); ?>" class="img-circle" alt="User Image">
                            </div>
                            <h4>
                                <?php echo htmlspecialchars($row->perfil_emisor); ?>
                                <small><i class="fa fa-clock-o"></i> <?php echo tiempoTranscurrido($row->fecha); ?></small>
                            </h4>
                            <p><?php echo htmlspecialchars($texto_corto); ?></p>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </li>
        <li class="footer"><a href="#">Ver todos los mensajes</a></li>
    </ul>

<?php
}
?>