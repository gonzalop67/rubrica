<?php

class niveles_educacion extends MySQL
{
    var $code = "";
    var $nombre = "";
    var $slug = "";

    function existeNivelEducacion($nombre)
	{
		$consulta = parent::consulta("SELECT * FROM sw_nivel_educacion WHERE nombre = '$nombre'");
		return (parent::num_rows($consulta) > 0);
	}

    function obtenerNivelEducacion($id) {
        $sql = "SELECT * FROM sw_nivel_educacion WHERE id = $id";
        $consulta = parent::consulta($sql);
        return parent::fetch_object($consulta);
    }

    function obtenerJsonNivelEducacion($id) {
        $sql = "SELECT * FROM sw_nivel_educacion WHERE id = $id";
        $consulta = parent::consulta($sql);
        return json_encode(parent::fetch_object($consulta));
    }

    function insertarNivelEducacion()
	{
        $sql = "SELECT MAX(orden) AS secuencial FROM sw_nivel_educacion";
        $result = parent::consulta($sql);
        $secuencial = parent::fetch_object($result)->secuencial;

		if ($this->existeNivelEducacion($this->nombre)) {
			//Mensaje de operación fallida
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un nivel de educación con ese nombre en la base de datos.",
				'estado' => 'error'
			];
		} else {
			try {
				$qry = "INSERT INTO sw_nivel_educacion (nombre, slug, orden) VALUES (";
				$qry .= "'" . $this->nombre . "',";
				$qry .= "'" . $this->slug . "',";
				$qry .= ($secuencial + 1) . ")";

				parent::consulta($qry);

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

    function actualizarNivelEducacion()
	{
        $id = $this->code;
        $registroActual = $this->obtenerNivelEducacion($id);

		if ($registroActual->nombre != $this->nombre && $this->existeNivelEducacion($this->nombre)) {
			//Mensaje de operación fallida
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un nivel de educación con ese nombre en la base de datos.",
				'estado' => 'error'
			];
		} else {
			try {
				$qry = "UPDATE sw_nivel_educacion SET nombre = ";
				$qry .= "'" . $this->nombre . "'";
				$qry .= ", slug = '" . $this->slug . "'";
				$qry .= " WHERE id = $this->code";

				parent::consulta($qry);

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

    function eliminarNivelEducacion()
    {
        // Primero compruebo si no existen subniveles de educación asociados
		$qry = "SELECT id FROM sw_sub_nivel_educacion WHERE nivel_id = " . $this->code;
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "No se puede eliminar porque tiene subniveles de educación asociados...",
				'estado' => 'error'
			];
		} else {
			try {
				$qry = "DELETE FROM sw_nivel_educacion WHERE id=" . $this->code;
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
