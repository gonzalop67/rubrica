<?php

class tipos_educacion extends MySQL
{

	var $code = "";
	var $id_periodo_lectivo = "";
	var $te_nombre = "";
	var $te_bachillerato = 0;

	function existeTipoEducacion($nombre, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_educacion WHERE te_nombre = '$nombre' AND id_periodo_lectivo = $id_periodo_lectivo");
		return (parent::num_rows($consulta) > 0);
	}

	function obtenerNombreTipoEducacion($id_paralelo)
	{
		$consulta = parent::consulta("SELECT te_nombre FROM sw_tipo_educacion te, sw_especialidad es, sw_curso cu, sw_paralelo pa WHERE pa.id_curso = cu.id_curso AND cu.id_especialidad = es.id_especialidad AND es.id_tipo_educacion = te.id_tipo_educacion AND id_paralelo = $id_paralelo");
		$tipo_educacion = parent::fetch_object($consulta);
		return $tipo_educacion->te_nombre;
	}

	function obtenerIdTipoEducacion($nombre)
	{
		$consulta = parent::consulta("SELECT id_tipo_educacion FROM sw_tipo_educacion WHERE te_nombre = '$nombre'");
		$tipo_educacion = parent::fetch_object($consulta);
		return $tipo_educacion->id_tipo_educacion;
	}

	function obtenerTipoEducacion($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_educacion WHERE id_tipo_educacion = $id");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerDatosTipoEducacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_educacion WHERE id_tipo_educacion = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function listar_tipos_educacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_educacion WHERE id_periodo_lectivo = " . $this->code . " ORDER BY te_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($tipos_educacion = parent::fetch_assoc($consulta)) {
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $tipos_educacion["id_tipo_educacion"];
				$name = $tipos_educacion["te_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarTipoEducacion(" . $code . ")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarTipoEducacion(" . $code . ",'" . $name . "')\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido Tipos de Educaci&oacute;n en este Per&iacute;odo Lectivo...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function cargar_tipos_educacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_educacion WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY te_orden");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($tipo_educacion = parent::fetch_assoc($consulta)) {
				$contador++;
				$id = $tipo_educacion["id_tipo_educacion"];
				$name = $tipo_educacion["te_nombre"];
				$orden = $tipo_educacion["te_orden"];
				$es_bachillerato = $tipo_educacion["te_bachillerato"] == 1 ? 'Sí' : 'No';
				$cadena .= "<tr data-index='$id' data-orden='$orden'>\n";
				$cadena .= "<td>$id</td>\n";
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td>$es_bachillerato</td>";
				$cadena .= "<td>\n";
				$cadena .= "<div class='btn-group'>\n";
				$cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $id . ")\" data-toggle=\"modal\" data-target=\"#editarNivelEducacionModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
				$cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarNivelEducacion(" . $id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
				$cadena .= "</div>";
				$cadena .= "</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='5' align='center'>No se han definido Niveles de Educaci&oacute;n en este Per&iacute;odo Lectivo...</td>\n";
			$cadena .= "</tr>\n";
		}
		return $cadena;
	}

	function insertarNivelEducacion()
	{
		// Aqui primero llamo a la funcion almacenada secuencial_tipo_educacion
		$consulta = parent::consulta("SELECT MAX(te_orden) AS secuencial FROM sw_tipo_educacion");

		if (parent::num_rows($consulta) > 0) {
			$te_orden = parent::fetch_object($consulta)->secuencial + 1;
		} else {
			$te_orden = 1;
		}

		if ($this->existeTipoEducacion($this->te_nombre, $this->id_periodo_lectivo)) {
			//Mensaje de operación fallida
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un nivel de educación con ese nombre en el presente periodo lectivo.",
				'estado' => 'error'
			];
		} else {
			try {
				$qry = "INSERT INTO sw_tipo_educacion (id_periodo_lectivo, te_nombre, te_bachillerato, te_orden) VALUES (";
				$qry .= $this->id_periodo_lectivo . ",";
				$qry .= "'" . $this->te_nombre . "',";
				$qry .= $this->te_bachillerato . ",";
				$qry .= $te_orden . ")";

				$consulta = parent::consulta($qry);

				//Mensaje de operación exitosa
				$datos = [
					'titulo' => "¡Agregado con éxito!",
					'mensaje' => "Inserción realizada exitosamente.",
					'estado' => 'success'
				];
			} catch (Exception $e) {
				//Mensaje de operación fallida
				$datos = [
					'titulo' => "¡Error!",
					'mensaje' => "No se pudo realizar la inserción. Error: " . $e->getMessage(),
					'estado' => 'error'
				];
			}
		}
		return json_encode($datos);
	}

	// function actualizarTipoEducacion()
	// {
	// 	$qry = "UPDATE sw_tipo_educacion SET ";
	// 	$qry .= "te_nombre = '" . $this->te_nombre . "'";
	// 	$qry .= " WHERE id_tipo_educacion = " . $this->code;
	// 	$consulta = parent::consulta($qry);
	// 	$mensaje = "Tipo de Educaci&oacute;n [" . $this->te_nombre . "] actualizado exitosamente...";
	// 	if (!$consulta)
	// 		$mensaje = "No se pudo actualizar el Tipo de Educaci&oacute;n...Error: " . mysqli_error($this->conexion);
	// 	return $mensaje;
	// }

	function actualizarNivelEducacion()
	{
		$qry = "UPDATE sw_tipo_educacion SET ";
		$qry .= "te_nombre = '" . $this->te_nombre . "', ";
		$qry .= "te_bachillerato = " . $this->te_bachillerato;
		$qry .= " WHERE id_tipo_educacion = " . $this->code;

		$consulta = parent::consulta("SELECT * FROM sw_tipo_educacion WHERE id_tipo_educacion = $this->code");
		$registro = parent::fetch_object($consulta);
		$nombreActual = $registro->te_nombre;

		if ($nombreActual != $this->te_nombre && $this->existeTipoEducacion($this->te_nombre, $this->id_periodo_lectivo)) {
			//Mensaje de operación exitosa
			$datos = [
				'titulo' => "¡Ocurrió un error inesperado!",
				'mensaje' => "Ya existe un nivel de educación con este nombre.",
				'estado' => 'error'
			];
		} else {
			try {
				$consulta = parent::consulta($qry);

				//Mensaje de operación exitosa
				$datos = [
					'titulo' => "¡Actualizado con éxito!",
					'mensaje' => "Actualización realizada exitosamente.",
					'estado' => 'success'
				];
			} catch (Exception $e) {
				//Mensaje de operación fallida
				$datos = [
					'titulo' => "¡Error!",
					'mensaje' => "No se pudo realizar la actualización. Error: " . $e->getMessage(),
					'estado' => 'error'
				];
			}
		}
		return json_encode($datos);
	}

	function eliminarTipoEducacion()
	{
		// Primero compruebo si no existen especialidades asociadas
		$qry = "SELECT id_especialidad FROM sw_especialidad WHERE id_tipo_educacion = " . $this->code;
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "No se puede eliminar porque tiene especialidades asociadas...",
				'estado' => 'error'
			];
		} else {
			try {
				$qry = "DELETE FROM sw_tipo_educacion WHERE id_tipo_educacion=" . $this->code;
				$consulta = parent::consulta($qry);

				//Mensaje de operación exitosa
				$datos = [
					'titulo' => "¡Eliminado con éxito!",
					'mensaje' => "Eliminación realizada exitosamente.",
					'estado' => 'success'
				];
			} catch (Exception $e) {
				//Mensaje de operación fallida
				$datos = [
					'titulo' => "¡Error!",
					'mensaje' => "No se pudo realizar la eliminación. Error: " . $e->getMessage(),
					'estado' => 'error'
				];
			}
		}
		return json_encode($datos);
	}

	// function subirNivelEducacion()
	// {
	// 	// Primero obtengo el "orden" del tipo de educación actual
	// 	$qry = "SELECT te_orden AS orden FROM sw_tipo_educacion WHERE id_tipo_educacion = " . $this->code;
	// 	$orden = parent::fetch_object(parent::consulta($qry))->orden;

	// 	// Ahora obtengo el id del registro que tiene el orden anterior
	// 	$qry = "SELECT id_tipo_educacion AS id FROM sw_tipo_educacion WHERE te_orden = $orden - 1 AND id_periodo_lectivo = " . $this->id_periodo_lectivo;
	// 	$id = parent::fetch_object(parent::consulta($qry))->id;

	// 	// Se actualiza el orden (decrementar en uno) del registro actual...
	// 	$qry = "UPDATE sw_tipo_educacion SET te_orden = te_orden - 1 WHERE id_tipo_educacion = " . $this->code;
	// 	$consulta = parent::consulta($qry);

	// 	// Luego se actualiza el orden (incrementar en uno) del registro anterior al actual...
	// 	$qry = "UPDATE sw_tipo_educacion SET te_orden = te_orden + 1 WHERE id_tipo_educacion = $id";
	// 	$consulta = parent::consulta($qry);

	// 	$mensaje = "Nivel de educaci&oacute;n \"subido\" exitosamente...";

	// 	if (!$consulta)
	// 		$mensaje = "No se pudo \"subir\" el Nivel de educaci&oacute;n...Error: " . mysqli_error($this->conexion);

	// 	return $mensaje;
	// }

	// function bajarNivelEducacion()
	// {
	// 	// Primero obtengo el "orden" del tipo de educacion actual
	// 	$qry = "SELECT te_orden AS orden FROM sw_tipo_educacion WHERE id_tipo_educacion = " . $this->code;
	// 	$orden = parent::fetch_object(parent::consulta($qry))->orden;

	// 	// Ahora obtengo el id del registro que tiene el orden anterior
	// 	$qry = "SELECT id_tipo_educacion AS id FROM sw_tipo_educacion WHERE te_orden = $orden + 1 AND id_periodo_lectivo = " . $this->id_periodo_lectivo;
	// 	$id = parent::fetch_object(parent::consulta($qry))->id;

	// 	// Se actualiza el orden (incrementar en uno) del registro actual...
	// 	$qry = "UPDATE sw_tipo_educacion SET te_orden = te_orden + 1 WHERE id_tipo_educacion = " . $this->code;
	// 	$consulta = parent::consulta($qry);

	// 	// Luego se actualiza el orden (decrementar en uno) del registro anterior al actual...
	// 	$qry = "UPDATE sw_tipo_educacion SET te_orden = te_orden - 1 WHERE id_tipo_educacion = $id";
	// 	$consulta = parent::consulta($qry);

	// 	$mensaje = "Nivel de educaci&oacute;n \"bajado\" exitosamente...";

	// 	if (!$consulta)
	// 		$mensaje = "No se pudo \"bajar\" del Nivel de educaci&oacute;n...Error: " . mysqli_error($this->conexion);

	// 	return $mensaje;
	// }
}
