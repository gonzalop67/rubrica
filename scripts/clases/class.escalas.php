<?php

class escalas extends MySQL
{

	var $code = "";
	var $id_periodo_lectivo = "";
	var $ec_cualitativa = "";
	var $ec_cuantitativa = "";
	var $ec_nota_minima = "";
	var $ec_nota_maxima = "";
	var $ec_equivalencia = "";
	var $ec_orden = "";

	function existeRecomendacionesQuimestrales($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_recomendaciones_quimestrales WHERE id_escala_calificaciones = $id");
		return (parent::num_rows($consulta) > 0);
	}

	function existeRecomendacionesAnuales($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_recomendaciones_anuales WHERE id_escala_calificaciones = $id");
		return (parent::num_rows($consulta) > 0);
	}

	function obtenerDatosEscala($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_escala_calificaciones WHERE id_escala_calificaciones = " . $id);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerEscalasCalificaciones()
	{
		$registros = parent::consulta("SELECT ec_nota_minima, ec_nota_maxima, ec_equivalencia FROM sw_escala_calificaciones WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY ec_orden");

		while ($reg = mysqli_fetch_array($registros)) {
			$vec[] = $reg;
		}

		require('../funciones/JSON.php');
		$json = new Services_JSON();
		$cad = $json->encode($vec);
		echo $cad;
	}

	function obtenerEscalasCalificacionesClub()
	{
		$registros = parent::consulta("SELECT ec_nota_minima, ec_nota_maxima, ec_abreviatura FROM sw_escala_proyectos ORDER BY id_escala_proyectos");

		while ($reg = mysqli_fetch_array($registros)) {
			$vec[] = $reg;
		}

		require('../funciones/JSON.php');
		$json = new Services_JSON();
		$cad = $json->encode($vec);
		echo $cad;
	}

	function listarEscalas()
	{
		$consulta = parent::consulta("SELECT * FROM sw_escala_calificaciones WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY ec_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($escala = parent::fetch_assoc($consulta)) {
				$contador += 1;
				$cadena .= '<tr data-index="'.$escala['id_escala_calificaciones'].'" data-orden="'.$escala['ec_orden'].'">\n';
				$code = $escala["id_escala_calificaciones"];
				$cualitativa = $escala["ec_cualitativa"];
				$cuantitativa = $escala["ec_cuantitativa"];
				$minima = $escala["ec_nota_minima"];
				$maxima = $escala["ec_nota_maxima"];
				$equivalencia = $escala["ec_equivalencia"];
				$cadena .= "<td>$contador</td>\n";
				$cadena .= "<td>$code</td>\n";
				$cadena .= "<td>$cualitativa</td>\n";
				$cadena .= "<td>$cuantitativa</td>\n";
				$cadena .= "<td>$minima</td>\n";
				$cadena .= "<td>$maxima</td>\n";
				$cadena .= "<td>$equivalencia</td>\n";
				$cadena .= "<td><div class='btn-group'>\n";
				$cadena .= "<a href='javascript:;' class='btn btn-warning item-edit' data='" . $code . "' title='Editar'><span class='fa fa-pencil'></span></a>\n";
				$cadena .= "<a href='javascript:;' class='btn btn-danger item-delete' data='" . $code . "' title='Eliminar'><span class='fa fa-trash'></span></a>\n";
				$cadena .= "</div></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='8' align='center'>No se han definido Escalas de Calificaci&oacute;n en este Per&iacute;odo Lectivo...</td>\n";
			$cadena .= "</tr>\n";
		}
		return $cadena;
	}

	function insertarEscala($id_periodo_lectivo, $cualitativa, $cuantitativa, $minima, $maxima, $equivalencia)
	{
		//Actualizar el orden al siguiente número secuencial que le toque...
		$qry = "SELECT MAX(ec_orden) AS secuencial FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo";
		$consulta = parent::consulta($qry);
		$registro = parent::fetch_object($consulta);
		$secuencial = $registro->secuencial + 1;

		$qry = "INSERT INTO sw_escala_calificaciones (id_periodo_lectivo, ec_cualitativa, ec_cuantitativa, ec_nota_minima, ec_nota_maxima, ec_equivalencia, ec_orden) VALUES (";
		$qry .= $id_periodo_lectivo . ",";
		$qry .= "'" . $cualitativa . "',";
		$qry .= "'" . $cuantitativa . "',";
		$qry .= $minima . ",";
		$qry .= $maxima . ",";
		$qry .= "'" . $equivalencia . "',";
		$qry .= $secuencial . ")";

		$consulta = parent::consulta($qry);
		if (!$consulta) {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "No se pudo insertar la escala de calificaciones...Error: " . mysqli_error($this->conexion) . "<br />Consulta: " . $qry,
				"tipo_mensaje" => "error"
			);
			return json_encode($data);
		} else {			
			$data = array(
				"titulo"       => "Operación exitosa.",
				"mensaje"      => "Escala de Calificaciones insertada exitosamente...",
				"tipo_mensaje" => "success"
			);
			return json_encode($data);
		}
	}

	function actualizarEscala($id, $cualitativa, $cuantitativa, $minima, $maxima, $equivalencia)
	{
		$qry = "UPDATE sw_escala_calificaciones SET ";
		$qry .= "ec_cualitativa = '" . $cualitativa . "',";
		$qry .= "ec_cuantitativa = '" . $cuantitativa . "',";
		$qry .= "ec_nota_minima = " . $minima . ",";
		$qry .= "ec_nota_maxima = " . $maxima . ",";
		$qry .= "ec_equivalencia = '" . $equivalencia . "'";
		$qry .= " WHERE id_escala_calificaciones = " . $id;
		$consulta = parent::consulta($qry);
		if (!$consulta) {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "No se pudo actualizar la escala de calificaciones...Error: " . mysqli_error($this->conexion) . "<br />Consulta: " . $qry,
				"tipo_mensaje" => "error"
			);
			return json_encode($data);
		} else {
			$data = array(
				"titulo"       => "Operación exitosa.",
				"mensaje"      => "Escala de Calificaciones [" . $cualitativa . "] actualizada exitosamente...",
				"tipo_mensaje" => "success"
			);
			return json_encode($data);
		}
	}

	function eliminarEscala()
	{
		$qry = "DELETE FROM sw_escala_calificaciones WHERE id_escala_calificaciones=" . $this->code;
		$consulta = parent::consulta($qry);
		if (!$consulta) {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "No se pudo eliminar la escala de calificaciones...Error: " . mysqli_error($this->conexion) . "<br />Consulta: " . $qry,
				"tipo_mensaje" => "error"
			);
			return json_encode($data);
		} else {
			$data = array(
				"titulo"       => "Operación exitosa.",
				"mensaje"      => "Escala de Calificaciones eliminada exitosamente...",
				"tipo_mensaje" => "success"
			);
			return json_encode($data);
		}
	}
}
