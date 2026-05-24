<?php
$nombrePerfil = $_POST["nombrePerfil"];
// Esto es para formatear la fecha de la notificacion
$meses = array(0, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();
$query = "SELECT * FROM sw_notificacion ORDER BY id_notificacion DESC";
$result = $db->consulta($query);
$num_rows = $db->num_rows($result);
$output = '';
if ($num_rows > 0) {
    foreach ($result as $row) {
        $fechadb = $row["timestamp"];
        list($yy, $mm, $dd) = explode("-", $fechadb);
        $fecha_formateada = (int)substr($dd, 0, 2) . " de " . $meses[(int)$mm] . " del " . $yy;
        $output .= '
		<div class="panel panel-default">
			<div class="panel-heading"><b><i>' . $fecha_formateada . '</i></b></div>
			<div class="panel-body text-uppercase">' . $row["notificacion"] . '</div>	
		';
        if($nombrePerfil=="ADMINISTRADOR"){
			$output .= '
            <div class="panel-footer" align="right">
				<button type="button" class="btn btn-danger delete" id="'.$row["id_notificacion"].'">Eliminar</button>
                </div>
			';
		}
    }
} else {
    $output .= '
		<div class="panel panel-default">
            <div class="panel-body text-uppercase">
                No se han ingresado notificaciones todavía...
            </div>
        </div>
    ';
}

echo $output;
