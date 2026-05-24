<?php

class valores_mes extends MySQL
{
	var $code = "";
	var $vm_mes = "";
	var $vm_valor = "";
	var $id_periodo_lectivo = "";

    function listarValoresMes()
	{
        $meses = array(
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
		);
		$cadena = "";
		$consulta = parent::consulta("SELECT * FROM sw_valor_mes ORDER BY vm_mes ASC");
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0)
		{
			$contador = 0;
			while($valor_mes = parent::fetch_assoc($consulta))
			{
				$contador++;
				$cadena .= "<tr>\n";
                $code = $valor_mes["id_valor_mes"];
                $name = $valor_mes["vm_valor"];
                $i_mes = $valor_mes["vm_mes"] - 1;
                $mes = $meses[$i_mes];
                $cadena .= "<td>$contador</td>\n";
				$cadena .= "<td>$code</td>\n";	
				$cadena .= "<td>$mes</td>\n";
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td><button title='Editar' class='btn btn-block btn-warning glyphicon glyphicon-pencil' onclick=\"editarValor(".$code.")\"></button></td>";
				$cadena .= "<td><button title='Eliminar' class='btn btn-block btn-danger glyphicon glyphicon-remove' onclick=\"eliminarValor(".$code.")\"></button></td>";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td>No se han definido valores del mes...</td>\n";
			$cadena .= "</tr>\n";	
		}	
		return $cadena;
    }
	
	function obtenerValorMes()
	{
		$consulta = parent::consulta("SELECT vm_mes, vm_valor FROM sw_valor_mes WHERE id_valor_mes = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function insertarValorMes()
	{
		$consulta = parent::consulta("SELECT vm_mes FROM sw_valor_mes WHERE vm_mes = ".$this->vm_mes);
		$num_reg = parent::num_rows($consulta);

		if($num_reg > 0){
			$mensaje = "<div class='alert alert-danger' style='margin-left: 4px; margin-right: 4px; color: #000;' role='alert'>
			Ya se encuentra asociado un valor al mes seleccionado...</div>";	
		}else{
			$consulta = parent::consulta("SELECT vm_valor FROM sw_valor_mes WHERE vm_valor = '".$this->vm_valor."'");
			$num_reg = parent::num_rows($consulta);
			if($num_reg > 0){
				$mensaje = "<div class='alert alert-danger' style='margin-left: 4px; margin-right: 4px; color: #000;' role='alert'>
				Ya existe en la base de datos el valor tipeado...</div>";	
			}else{
				$qry = "INSERT INTO sw_valor_mes (id_periodo_lectivo, vm_mes, vm_valor) VALUES (";
				$qry .= $this->id_periodo_lectivo . ",";
				$qry .= $this->vm_mes . ",";
				$qry .= "'" . $this->vm_valor . "')";
				$consulta = parent::consulta($qry);
				$mensaje = "<div class='alert alert-success' style='margin-left: 4px; margin-right: 4px; color: #000;' role='alert'>
					Valor del mes insertado exitosamente...</div>";
				if (!$consulta)
					$mensaje = "<div class='alert alert-danger' style='margin-left: 4px; margin-right: 4px; color: #000;' role='alert'>
					No se pudo insertar el valor del mes...Error: " . mysqli_error($this->conexion) . "</div>";
			}
		}
		
		return $mensaje;
	}

	function actualizarValorMes()
	{
		$consulta = parent::consulta("SELECT vm_valor FROM sw_valor_mes WHERE vm_valor = '".$this->vm_valor."'");
		$num_reg = parent::num_rows($consulta);
		if($num_reg > 0){
			$mensaje = "<div class='alert alert-danger' style='margin-left: 4px; margin-right: 4px; color: #000;' role='alert'>
			Ya existe en la base de datos el valor tipeado...</div>";	
		}else{
			$qry = "UPDATE sw_valor_mes SET vm_valor = '".$this->vm_valor."' WHERE id_valor_mes = ".$this->code;
			$consulta = parent::consulta($qry);
			$mensaje = "<div class='alert alert-success' style='margin-left: 4px; margin-right: 4px; color: #000;' role='alert'>
				Valor del mes actualizado exitosamente...</div>";
			if (!$consulta)
				$mensaje = "<div class='alert alert-danger' style='margin-left: 4px; margin-right: 4px; color: #000;' role='alert'>
				No se pudo actualizar el valor del mes...Error: " . mysqli_error($this->conexion) . "</div>";
		}
		return $mensaje;
	}

	function eliminarValorMes($id){
		$qry = "DELETE FROM sw_valor_mes WHERE id_valor_mes = ".$this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "<div class='alert alert-success' style='margin-left: 4px; margin-right: 4px; color: #000;' role='alert'>
			Valor del mes eliminado exitosamente...</div>";
		if (!$consulta)
			$mensaje = "<div class='alert alert-danger' style='margin-left: 4px; margin-right: 4px; color: #000;' role='alert'>
			No se pudo eliminar el valor del mes...Error: " . mysqli_error($this->conexion) . "</div>";

		return $mensaje;
	}
}
?>