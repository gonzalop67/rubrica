<?php

class areas extends MySQL
{

	var $code = "";
	var $ar_nombre = "";

	function existeArea($nombre)
	{
		$consulta = parent::consulta("SELECT * FROM sw_area WHERE ar_nombre = '$nombre'");
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			return true;
		} else {
			return false;
		}
	}

	function obtenerIdArea($nombre)
	{
		$consulta = parent::consulta("SELECT id_area FROM sw_area WHERE ar_nombre = '$nombre'");
		$area = parent::fetch_object($consulta);
		return $area->id_area;
	}

	function obtenerArea($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_area WHERE id_area = $id");
		return parent::fetch_object($consulta);
	}

	function obtenerAreas()
	{
		// Funcion que retorna todas las areas ingresadas en la base de datos
		return parent::consulta("SELECT *
								   FROM sw_area
								 ORDER BY ar_nombre");
	}

	function obtenerDatosArea()
	{
		$consulta = parent::consulta("SELECT * FROM sw_area WHERE id_area = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function cargarAreas()
	{
		// Funcion que retorna todas las areas ingresadas en la base de datos
		$cadena = "";
		$consulta = parent::consulta("SELECT * FROM sw_area ORDER BY ar_nombre");
		if (parent::num_rows($consulta) > 0) {
			$contador = 0;
			while ($area = parent::fetch_assoc($consulta)) {
				// Aquí formo las filas que contendrá el tbody
				$contador++;
				$cadena .= "<tr>";
				$nombre = $area["ar_nombre"];
				$id = $area["id_area"];
				$cadena .= "<td>" . $contador . "</td>";
				$cadena .= "<td>" . $id . "</td>";
				$cadena .= "<td>" . $nombre . "</td>";
				$cadena .= "<td>\n";
				$cadena .= "<div class='btn-group'>\n";
				$cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $id . ")\" data-toggle=\"modal\" data-target=\"#editarAreaModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
				$cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarArea(" . $id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
				$cadena .= "</div>\n";
				$cadena .= "</td>\n";
				$cadena .= "</tr>";
			}
		} else {
			$cadena = "<tr><td colspan='4' align='center'>No se han ingresado areas todavia...</td></tr>";
		}
		return $cadena;
	}

	function insertarArea()
	{
		$qry = "INSERT INTO sw_area (ar_nombre) VALUES (";
		$qry .= "'" . $this->ar_nombre . "')";

		if ($this->existeArea($this->ar_nombre)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un área con ese nombre en la Base de Datos.",
				'estado' => 'error'
			];
		} else {
			try {
				$consulta = parent::consulta($qry);

				//Mensaje de operación exitosa
				$datos = [
					'titulo' => "¡Insertado con éxito!",
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

	function actualizarArea()
	{
		$qry = "UPDATE sw_area SET ";
		$qry .= "ar_nombre = '" . $this->ar_nombre . "'";
		$qry .= " WHERE id_area = " . $this->code;

		$consulta = parent::consulta("SELECT * FROM sw_area WHERE id_area = $this->code");
		$registro = parent::fetch_assoc($consulta);
		$nombreActual = $registro['ar_nombre'];

		if ($nombreActual != $this->ar_nombre && $this->existeArea($this->ar_nombre)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un área con ese nombre en la Base de Datos.",
				'estado' => 'error'
			];
		} else {
			try {
				$consulta = parent::consulta($qry);

				//Mensaje de operación exitosa
				$datos = [
					'titulo' => "¡Actualizado con éxito!",
					'mensaje' => "Area " . $this->ar_nombre . " actualizada exitosamente...",
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

	function eliminarArea()
	{
		// Primero compruebo si existen asignaturas asociadas
		$qry = "SELECT * FROM sw_asignatura WHERE id_area = " . $this->code;
		$consulta = parent::consulta($qry);
		$num_rows = parent::num_rows($consulta);
		if ($num_rows > 0) {
			//Mensaje de operación fallida
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "No se puede eliminar esta Area, porque tiene Asignaturas asociadas.",
				'estado' => 'error'
			];
		} else {
			try {
				$qry = "DELETE FROM sw_area WHERE id_area = " . $this->code;
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
}
