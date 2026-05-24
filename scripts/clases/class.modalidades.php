<?php

class modalidades extends MySQL
{
	var $code = "";
	var $id_periodo_lectivo = 0;
	var $mo_nombre = "";
	var $mo_activo = "";

	function obtenerModalidad()
	{
		$consulta = parent::consulta("SELECT * FROM sw_modalidad WHERE id_modalidad = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function getModalidadByIdPeriodoLectivo($id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT mo_nombre FROM sw_modalidad m, sw_periodo_lectivo p WHERE m.id_modalidad = p.id_modalidad AND p.id_periodo_lectivo = $id_periodo_lectivo");
		$modalidad = parent::fetch_object($consulta);
		return $modalidad->mo_nombre;
	}

	function eliminarModalidad()
	{
		$qry = "SELECT * FROM sw_periodo_lectivo WHERE id_modalidad = $this->code";
		$consulta = parent::consulta($qry);
		
		if (parent::num_rows($consulta) > 0) {
			$data = array(
				"titulo"       => "Error",
				"mensaje"      => "No se puede eliminar la modalidad ya que tiene periodos lectivos asociados.",
				"tipo_mensaje" => "error"
			);
		} else {
			$qry = "DELETE FROM sw_modalidad WHERE id_modalidad = " . $this->code;
			$consulta = parent::consulta($qry);
			$data = array(
				"titulo"       => "Operación exitosa.",
				"mensaje"      => "La Modalidad se ha eliminado exitosamente...",
				"tipo_mensaje" => "success"
			);
		}

		return json_encode($data);
	}

	function insertarModalidad()
	{
		// Aqui primero llamo a la funcion almacenada secuencial_modalidad
		$consulta = parent::consulta("SELECT secuencial_modalidad() AS secuencial");
		$mo_orden = parent::fetch_object($consulta)->secuencial;

		$qry = "INSERT INTO sw_modalidad (mo_nombre, mo_activo, mo_orden) VALUES (";
		$qry .= "'" . $this->mo_nombre . "'," . $this->mo_activo . "," . $mo_orden . ")";
		$consulta = parent::consulta($qry);
		if ($consulta) {
			$data = array(
				"titulo"       => "Operación exitosa.",
				"mensaje"      => "La Modalidad se ha insertado exitosamente...",
				"tipo_mensaje" => "success"
			);
		} else {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "La Modalidad no se pudo insertar exitosamente...",
				"tipo_mensaje" => "error"
			);
		}

		return json_encode($data);
	}

	function actualizarModalidad()
	{
		$qry = "UPDATE sw_modalidad SET";
		$qry .= " mo_nombre = '" . $this->mo_nombre . "', ";
		$qry .= " mo_activo = " . $this->mo_activo;
		$qry .= " WHERE id_modalidad = " . $this->code;
		$consulta = parent::consulta($qry);
		if ($consulta) {
			$data = array(
				"titulo"       => "Operación exitosa.",
				"mensaje"      => "La Modalidad se ha actualizado exitosamente...",
				"tipo_mensaje" => "success"
			);
		} else {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "La Modalidad no se pudo actualizar exitosamente...",
				"tipo_mensaje" => "error"
			);
		}

		return json_encode($data);
	}

	function listarModalidades()
	{
		$consulta = parent::consulta("SELECT * FROM sw_modalidad ORDER BY id_modalidad ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($cursos = parent::fetch_assoc($consulta)) {
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $cursos["id_modalidad"];
				$name = $cursos["mo_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarModalidad(" . $code . ")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarModalidad(" . $code . ",'" . $name . "')\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido modalidades...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}
}
