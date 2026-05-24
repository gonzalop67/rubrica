<?php
	$nombrePerfil = $_POST["nombrePerfil"];
	$id_usuario = $_POST["id_usuario"];
	// Esto es para formatear la fecha del comentario
	$meses = array(0, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
	require_once("../scripts/clases/class.mysql.php");
	$db = new MySQL();
	$query = "
	SELECT * FROM sw_comentario 
	WHERE id_comentario_padre = '0' 
	AND id_usuario_para IN ($id_usuario, 0) 
	ORDER BY id_comentario DESC
	";
	$result = $db->consulta($query);
	$output = '';
	foreach($result as $row)
	{
		$fechadb = $row["co_fecha"];
		list($yy,$mm,$dd)=explode("-",$fechadb);
		$fecha_formateada = (int)substr($dd, 0, 2) . " de " . $meses[(int)$mm] . " del " . $yy;
		$output .= '
		<div class="panel panel-default">
			<div class="panel-heading"><b>'.$row["co_nombre"].'</b> coment&oacute; el <i>'.$fecha_formateada.'</i></div>
			<div class="panel-body text-uppercase">'.$row["co_texto"].'</div>
			<div class="panel-footer" align="right">
				<button type="button" class="btn btn-default reply" id="'.$row["id_comentario"].'">Responder</button>
		';
		if($nombrePerfil=="ADMINISTRADOR"){
			$output .= '
				<button type="button" class="btn btn-danger delete" id="'.$row["id_comentario"].'">Eliminar</button>
			';
		}
		$output .= '
			</div>
		</div>
		';
		$output .= get_reply_comment($row["id_comentario"]);
    }

	function get_reply_comment($parent_id = 0, $marginleft = 0)
	{
        global $db, $meses, $nombrePerfil;
		$query = "
		SELECT * FROM sw_comentario WHERE id_comentario_padre = '".$parent_id."'
		";
		$output = '';
		$result = $db->consulta($query);
		$count = $db->num_rows($result);
		if($parent_id == 0)
		{
			$marginleft = 0;
		}
		else
		{
			$marginleft = $marginleft + 48;
		}
		if($count > 0)
		{
			foreach($result as $row)
			{
				$fechadb = $row["co_fecha"];
				list($yy,$mm,$dd)=explode("-",$fechadb);
				$fecha_formateada = (int)substr($dd, 0, 2) . " de " . $meses[(int)$mm] . " del " . $yy;
				$output .= '
				<div class="panel panel-default" style="margin-left:'.$marginleft.'px">
				<div class="panel-heading"><b>'.$row["co_nombre"].'</b> coment&oacute; el <i>'.$fecha_formateada.'</i></div>
				<div class="panel-body text-uppercase">'.$row["co_texto"].'</div>
				<div class="panel-footer" align="right">
				<button type="button" class="btn btn-default reply" id="'.$row["id_comentario"].'">Responder</button>
				';
				if($nombrePerfil=="ADMINISTRADOR"){
					$output .= '<button type="button" class="btn btn-danger delete" id="'.$row["id_comentario"].'">Eliminar</button>';
				}
				$output .= '
				</div>
				</div>
				';
				$output .= get_reply_comment($row["id_comentario"], $marginleft);
			}
		}
		return $output;
	}

	echo $output;
?>