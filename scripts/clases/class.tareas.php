<?php

class tareas extends MySQL
{
	
    function cargarTareas(){
        // Funcion que retorna todas las tareas ingresadas en la base de datos
        $cadena = "";
        $consulta = parent::consulta("SELECT * FROM sw_por_hacer ORDER BY fecha DESC");
        if(parent::num_rows($consulta) > 0){
            while($tarea = parent::fetch_assoc($consulta)){
                // Aquí formo las filas que contendrá el tbody
                $cadena .= "<tr>";
                $task = $tarea["tarea"];
                $id = $tarea["id"];
                $checked = $tarea["hecho"] ? "checked" : "";
                $clase = $tarea["hecho"] ? "taskDone" : "";
                $cadena .= "<td><input type='checkbox' onclick='checkTask(this,".$id.")' $checked></td>";
                $cadena .= "<td><div class='".$clase."'>".$task."</div></td>";
                $cadena .= "<td><button onclick='editTask(".$id.")' class='btn btn-block btn-warning'>Editar</button></td>";
                $cadena .= "<td><button onclick='deleteTask(".$id.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
                $cadena .= "</tr>";
            }
        }else{
            $cadena = "<tr><td colspan='4' align='center'>No se han ingresado tareas todavia...</td></tr>";
        }
        return $cadena;
    }

    function consultarTareas($where){
        // Funcion que retorna las tareas ingresadas de acuerdo al filtro
        $cadena = "";
        $consulta = parent::consulta("SELECT * FROM sw_por_hacer" . $where . "ORDER BY fecha DESC");
        if(parent::num_rows($consulta) > 0){
            while($tarea = parent::fetch_assoc($consulta)){
                // Aquí formo las filas que contendrá el tbody
                $cadena .= "<tr>";
                $task = $tarea["tarea"];
                $id = $tarea["id"];
                $checked = $tarea["hecho"] ? "checked" : "";
                $clase = $tarea["hecho"] ? "taskDone" : "";
                $cadena .= "<td><input type='checkbox' onclick='checkTask(this,".$id.")' $checked></td>";
                $cadena .= "<td><div class='".$clase."'>".$task."</div></td>";
                $cadena .= "<td><button onclick='editTask(".$id.")' class='btn btn-block btn-warning'>Editar</button></td>";
                $cadena .= "<td><button onclick='deleteTask(".$id.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
                $cadena .= "</tr>";
            }
        }else{
            $cadena = "<tr><td colspan='4' align='center'>No se han ingresado tareas todavia...</td></tr>";
        }
        return $cadena;
    }

    function insertarTarea($tarea_descripcion){
        // Funcion que inserta una tarea en la base de datos
        // devuelve un mensaje de error o exito
        try {
            $consulta = parent::consulta("INSERT INTO sw_por_hacer (tarea, hecho) VALUES ('$tarea_descripcion', 0)");

            $datos = [
                'titulo' => "¡Insertado con éxito!",
                'mensaje' => "Inserción realizada exitosamente.",
                'tipo_mensaje' => 'success'
            ];
        } catch (\Exception $e) {
            $datos = [
                'titulo' => "¡Hubo Un Error!",
                'mensaje' => "Error: " . $e->getMessage(),
                'tipo_mensaje' => 'error'
            ];
        }
        
        return json_encode($datos);
    }

    function actualizarTarea($id, $tarea_descripcion){
        // Funcion que actualiza una tarea en la base de datos
        // devuelve un mensaje de error o exito
        $consulta = parent::consulta("UPDATE sw_por_hacer SET tarea = '$tarea_descripcion' WHERE id = $id");
        $mensaje = "Tarea actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo actualizar la tarea...Error: " . mysqli_error($this->conexion);
		return $mensaje;
    }

    function eliminarTarea($id){
        // Funcion que elimina una tarea en la base de datos
        // devuelve un mensaje de error o exito
        $consulta = parent::consulta("DELETE FROM sw_por_hacer WHERE id = $id");
        $mensaje = "Tarea eliminada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo eliminar la tarea...Error: " . mysqli_error($this->conexion);
		return $mensaje;
    }

    function obtenerTarea($id){
		$consulta = parent::consulta("SELECT * FROM sw_por_hacer WHERE id = $id");
		return json_encode(parent::fetch_assoc($consulta));
	}
    
    function actualizarCampoHecho($id, $estado_hecho){
		// Procedimiento para actualizar el estado de Hecho de una tarea
		$consulta = parent::consulta("UPDATE sw_por_hacer SET hecho = $estado_hecho WHERE id = " . $id);
		if($consulta) return "Tarea realizada actualizada correctamente.";
		else return "Tarea realizada no pudo actualizarse. Error: " . mysqli_error($this->conexion);
	}

}