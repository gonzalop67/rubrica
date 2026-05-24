<?php

class subniveles_educacion extends MySQL
{
    var $code = "";
    var $nivel_id = "";
	var $nombre = "";
	var $slug = "";
	var $es_bachillerato = 0;

    function existeSubNivelEducacion($campo, $valor, $nivel_id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_sub_nivel_educacion WHERE $campo = '$valor' AND nivel_id = $nivel_id");
		return (parent::num_rows($consulta) > 0);
	}

    function obtenerSubNivelEducacion($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_sub_nivel_educacion WHERE id = $id");
		return json_encode(parent::fetch_assoc($consulta));
	}

    function obtenerDatosSubNivelEducacion($id)
    {
        $result = parent::consulta("SELECT * FROM sw_sub_nivel_educacion WHERE id = $id");
        return parent::fetch_object($result);
    }

    function insertarSubNivelEducacion()
	{
		// Aqui se obtiene el máximo orden
		$consulta = parent::consulta("SELECT MAX(orden) AS secuencial FROM sw_sub_nivel_educacion");

		if (parent::num_rows($consulta) > 0) {
			$orden = parent::fetch_object($consulta)->secuencial + 1;
		} else {
			$orden = 1;
		}

		if ($this->existeSubNivelEducacion('nombre', $this->nombre, $this->nivel_id)) {
			//Mensaje de operación fallida
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un subnivel de educación con ese nombre en la base de datos.",
				'estado' => 'error'
			];
        } else if ($this->existeSubNivelEducacion('slug', $this->slug, $this->nivel_id)) {
            //Mensaje de operación fallida
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un subnivel de educación con ese slug en la base de datos.",
				'estado' => 'error'
			];
		} else {
			try {
				$qry = "INSERT INTO sw_sub_nivel_educacion (nivel_id, nombre, slug, es_bachillerato, orden) VALUES (";
                $qry .= $this->nivel_id . ",";
				$qry .= "'" . $this->nombre . "',";
				$qry .= "'" . $this->slug . "',";
				$qry .= $this->es_bachillerato . ",";
				$qry .= $orden . ")";

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

    function actualizarSubNivelEducacion()
	{
        $id = $_POST["id"];
        $nombre = $_POST["nombre"];
        $slug = $_POST["slug"];

		$registroActual = $this->obtenerDatosSubNivelEducacion($id);

		if ($registroActual->nombre != $nombre && $this->existeSubNivelEducacion('nombre', $this->nombre, $this->nivel_id)) {
			//Mensaje de operación fallida
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un subnivel de educación con ese nombre en la base de datos.",
				'estado' => 'error'
			];
        } else if ($registroActual->slug != $slug && $this->existeSubNivelEducacion('slug', $this->slug, $this->nivel_id)) {
            //Mensaje de operación fallida
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un subnivel de educación con ese slug en la base de datos.",
				'estado' => 'error'
			];
		} else {
			try {
				$qry = "UPDATE sw_sub_nivel_educacion SET nivel_id =";
                $qry .= $this->nivel_id . ",";
				$qry .= "nombre = '" . $this->nombre . "',";
				$qry .= "slug = '" . $this->slug . "',";
				$qry .= "es_bachillerato = " . $this->es_bachillerato;
				$qry .= " WHERE id = $this->code";

				parent::consulta($qry);

				//Mensaje de operación exitosa
				$datos = [
					'titulo' => "¡Agregado con éxito!",
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

    function eliminarSubNivelEducacion()
    {
        // Primero compruebo si no existen especialidades asociadas
		$qry = "SELECT id_especialidad FROM sw_especialidad WHERE subnivel_id = " . $this->code;
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
				$qry = "DELETE FROM sw_sub_nivel_educacion WHERE id=" . $this->code;
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