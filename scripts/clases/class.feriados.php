<?php

class feriados extends MySQL
{
    var $code = "";
	var $fe_fecha = "";
    var $id_periodo_lectivo = "";
    
    function getFeriado($id_feriado){
        $qry = "SELECT id_feriado, fe_fecha FROM sw_feriado WHERE id_feriado = $id_feriado";
        $res = parent::consulta($qry);
        return json_encode(parent::fetch_assoc($res));
    }

    function listarFeriados($id_periodo_lectivo)
	{
		$cadena = "";
		$consulta = parent::consulta("SELECT id_feriado, DATE_FORMAT(fe_fecha,'%Y-%m-%d') AS feriado FROM sw_feriado WHERE id_periodo_lectivo = $id_periodo_lectivo");
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
			$contador = 0;
			while($feriado = parent::fetch_assoc($consulta))
			{
				$contador++;
				$cadena .= "<tr>\n";
                $code = $feriado["id_feriado"];
                $fecha = $feriado["feriado"];
                $cadena .= "<td>$contador</td>\n";
				$cadena .= "<td>$code</td>\n";	
				$cadena .= "<td>$fecha</td>\n";
                $cadena .= "<td><div class=\"btn-group\">\n";
                $cadena .= "<a href=\"javascript:;\" class=\"btn btn-warning item-edit\" data=\"".$code."\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
                $cadena .= "<a href=\"javascript:;\" class=\"btn btn-danger item-delete\" data=\"".$code."\" title=\"Eliminar\"><span class=\"fa fa-remove\"></span></a>\n";
                $cadena .= "</div></td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan=\"5\" align=\"center\">No se han definido feriados para este periodo lectivo...</td>\n";
			$cadena .= "</tr>\n";	
		}	
		return $cadena;
    }

	function storeFeriado()
	{
		$consulta = parent::consulta("SELECT fe_fecha FROM sw_feriado WHERE fe_fecha = '".$this->fe_fecha."'");
		$num_reg = parent::num_rows($consulta);

		if($num_reg > 0){
			$data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "La fecha del dia feriado ya se encuentra ingresada en la base de datos...",
                "tipo_mensaje" => "error"
            );
            return json_encode($data);
		}else{
            $qry = "INSERT INTO sw_feriado (id_periodo_lectivo, fe_fecha) VALUES (";
            $qry .= $this->id_periodo_lectivo . ",";
            $qry .= "'" . $this->fe_fecha . "')";
            $consulta = parent::consulta($qry);
            if ($consulta){
                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "El feriado fue insertado exitosamente...",
                    "tipo_mensaje" => "success"
                );
                return json_encode($data);
            }else{
                $data = array(
                    "titulo"       => "Ocurrió un error inesperado.",
                    "mensaje"      => "El feriado no fue insertado...Error: ".mysqli_error($this->conexion),
                    "tipo_mensaje" => "error"
                );
                return json_encode($data);
            }
		}
    }
    
    function updateFeriado()
	{
		$consulta = parent::consulta("SELECT fe_fecha FROM sw_feriado WHERE fe_fecha = '".$this->fe_fecha."'");
		$num_reg = parent::num_rows($consulta);
		if($num_reg > 0){
			$data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "La fecha del dia feriado ya se encuentra ingresada en la base de datos...",
                "tipo_mensaje" => "error"
            );
            return json_encode($data);
		}else{
			$qry = "UPDATE sw_feriado SET fe_fecha = '".$this->fe_fecha."' WHERE id_feriado = ".$this->code;
			$consulta = parent::consulta($qry);
			if ($consulta){
                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "El feriado fue actualizado exitosamente...",
                    "tipo_mensaje" => "success"
                );
                return json_encode($data);
            }else{
                $data = array(
                    "titulo"       => "Ocurrió un error inesperado.",
                    "mensaje"      => "El feriado no fue actualizado...Error: ".mysqli_error($this->conexion),
                    "tipo_mensaje" => "error"
                );
                return json_encode($data);
            }
		}
	}

    function deleteFeriado($id_feriado){
        $qry = "DELETE FROM sw_feriado WHERE id_feriado = $id_feriado";
        $res = parent::consulta($qry);
        if($res){
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El feriado fue eliminado exitosamente...",
                "tipo_mensaje" => "success"
            );
            return json_encode($data);
        }else{
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "El feriado no fue eliminado...Error: ".mysqli_error($this->conexion),
                "tipo_mensaje" => "error"
            );
            return json_encode($data);
        }
    }
}