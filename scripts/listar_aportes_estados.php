<?php
    include("clases/class.mysql.php");
    $db = new MySQL();
    $id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
    $id_paralelo = $_POST["id_paralelo"];
    $consulta = $db->consulta("
    SELECT a.id_aporte_evaluacion, 
           ac.id_aporte_paralelo_cierre, 
           ap_nombre, 
           ac.ap_estado, 
           ac.ap_fecha_apertura, 
           ac.ap_fecha_cierre 
      FROM sw_aporte_evaluacion a, 
           sw_aporte_paralelo_cierre ac 
     WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
       AND a.id_periodo_evaluacion = $id_periodo_evaluacion 
       AND id_paralelo = $id_paralelo 
     ORDER BY a.id_aporte_evaluacion ASC
    ");
    $num_total_registros = $db->num_rows($consulta);
    $cadena = "";
    if($num_total_registros > 0)
    {
        $contador = 0;
        while($aportes_evaluacion = $db->fetch_assoc($consulta))
        {
            $contador ++;
            $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
            $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
            $code = $aportes_evaluacion["id_aporte_evaluacion"];
            $name = $aportes_evaluacion["ap_nombre"];
            $estado = $aportes_evaluacion["ap_estado"];
            $fecha_apertura = $aportes_evaluacion["ap_fecha_apertura"];
            $fecha_cierre = $aportes_evaluacion["ap_fecha_cierre"];
            $id_aporte_paralelo_cierre = $aportes_evaluacion["id_aporte_paralelo_cierre"];
            if($estado=='A') {
                $mensaje = 'ABIERTO';
                $accion = 'cerrar';
            } else {
                $mensaje = 'CERRADO';
                $accion = 'reabrir';
            }
            $cadena .= "<td width='2%'><input name='row-check' type='checkbox' class='delete_checkbox' value='$id_aporte_paralelo_cierre'></td>\n";
            $cadena .= "<td>$contador</td>\n";	
            $cadena .= "<td>$code</td>\n";
            $cadena .= "<td>$name</td>\n";
			$cadena .= "<td><input id=\"fechaapertura_".$contador."\" class=\"cajaPequenia\" type=\"text\" value=\"$fecha_apertura\" onfocus=\"sel_texto(this)\" /> <img src=\"imagenes/calendario.png\" id=\"calendario_apertura".$contador."\" name=\"calendario_apertura".$contador."\" width=\"16\" height=\"16\" title=\"calendario\" alt=\"calendario\" onmouseover=\"style.cursor=cursor\"/> 
			<script type=\"text/javascript\">
				Calendar.setup(
					{
					inputField : \"fechaapertura_".$contador."\",
					ifFormat   : \"%Y-%m-%d\",
					button     : \"calendario_apertura".$contador."\"
					}
				);
			</script></td>\n";
			$cadena .= "<td><input id=\"fechacierre_".$contador."\" class=\"cajaPequenia\" type=\"text\" value=\"$fecha_cierre\" onfocus=\"sel_texto(this)\" /> <img src=\"imagenes/calendario.png\" id=\"calendario_cierre".$contador."\" name=\"calendario_cierre".$contador."\" width=\"16\" height=\"16\" title=\"calendario\" alt=\"calendario\" onmouseover=\"style.cursor=cursor\"/> 
			<script type=\"text/javascript\">
				Calendar.setup(
					{
					inputField : \"fechacierre_".$contador."\",
					ifFormat   : \"%Y-%m-%d\",
					button     : \"calendario_cierre".$contador."\"
					}
				);
			</script></td>\n";
			$cadena .= "<td>$mensaje</td>\n";
            $cadena .= "<td class=\"link_table\"><a href=\"#\" onclick=\"actualizarAporteEvaluacion(".$code.",'".$name."',document.getElementById('fechaapertura_".$contador."'),document.getElementById('fechacierre_".$contador."'))\">actualizar</a></td>\n";
            $cadena .= "<td></td>";
            $cadena .= "</tr>\n";
        }
    } else {
        $cadena .= "<tr>\n";	
        $cadena .= "<td>No se han definido Cierres para los Aportes de Evaluaci&oacute;n...</td>\n";
        $cadena .= "</tr>\n";
    }	
	echo $cadena;
?>
