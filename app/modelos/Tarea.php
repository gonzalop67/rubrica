<?php
class Tarea
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerTareas()
    {
        $this->db->query("SELECT *
                            FROM sw_tarea
                        ORDER BY fecha DESC");
        return $this->db->registros();
    }

    public function insertar($tarea)
    {
        $this->db->query('INSERT INTO sw_tarea (tarea) VALUES (:tarea)');

        //Vincular valores
        $this->db->bind(':tarea', $tarea);

        //Ejecutar
        try {
            $this->db->execute();
            $datos = [
                'titulo' => "¡Agregado con éxito!",
                'mensaje' => "Inserción realizada exitosamente.",
                'estado' => 'success'
            ];
        } catch (PDOException $e) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "No se pudo realizar la inserción. Error: " . $e->getMessage(),
                'estado' => 'error'
            ];
        }

        //Enviar el objeto en formato json
        return json_encode($datos);
    }

    public function obtenerTareaPorId($id)
    {
        $this->db->query("SELECT * FROM sw_tarea WHERE id = $id");
        return $this->db->registro();
    }

    public function actualizarTarea($datos)
    {
        $this->db->query('UPDATE sw_tarea SET tarea = :tarea WHERE id = :id_tarea');

        //Vincular valores
        $this->db->bind(':tarea', $datos['tarea']);
        $this->db->bind(':id_tarea', $datos['id_tarea']);

        //Ejecutar
        try {
            $this->db->execute();
            $datos = [
                'titulo' => "¡Actualizado con éxito!",
                'mensaje' => "Actualización realizada exitosamente.",
                'estado' => 'success'
            ];
        } catch (PDOException $e) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "No se pudo realizar la actualización. Error: " . $e->getMessage(),
                'estado' => 'error'
            ];
        }

        //Enviar el objeto en formato json
        return json_encode($datos);
    }

    public function actualizarHecho($id, $hecho)
    {
        $this->db->query('UPDATE sw_tarea SET hecho = :hecho WHERE id = :id');

        //Vincular valores
        $this->db->bind(':hecho', $hecho);
        $this->db->bind(':id', $id);

        //Ejecutar
        try {
            $this->db->execute();
            $mensaje = "Campo hecho actualizado con éxito...";
        } catch (PDOException $e) {
            $mensaje = "No se pudo actualizar el campo hecho..." . $e->getMessage();
        }

        //Enviar el objeto en formato json
        return $mensaje;
    }

    public function paginacion($paginaActual, $num_registros, $filtro)
    {
        $tabla = "";
        $lista = "";
        $limit = 0;

        $strQuery = "SELECT * FROM `sw_tarea` " . $filtro . " ORDER BY fecha DESC";

        $this->db->query($strQuery);
        $consulta = $this->db->registros();
        $nroRegistros = $this->db->rowCount();

        $nroPaginas = ceil($nroRegistros / $num_registros);

        if ($nroRegistros > 0) {
            if ($paginaActual == 1) {
                $lista = $lista . "<li class='disabled'><a href='javascript:;'>‹</a></li>";
            } else {
                $pagina = $paginaActual - 1;
                $lista = $lista . "<li><a href=\"javascript:paginarTareas($pagina, $num_registros, '$filtro');\">‹</a></li>";
            }

            for ($i = 1; $i <= $nroPaginas; $i++) {
                if ($i == $paginaActual) {
                    $lista = $lista . "<li class='active'><a href=\"javascript:paginarTareas($i, $num_registros, '$filtro');\">$i</a></li>";
                } else {
                    $lista = $lista . "<li><a href=\"javascript:paginarTareas($i, $num_registros, '$filtro');\">$i</a></li>";
                }
            }

            if ($paginaActual == $nroPaginas) {
                $lista = $lista . "<li class='disabled'><a href='javascript:;'>›</a></li>";
            } else {
                $pagina = $paginaActual + 1;
                $lista = $lista . "<li><a href=\"javascript:paginarTareas($pagina, $num_registros, '$filtro');\">›</a></li>";
            }
            if ($paginaActual <= 1) {
                $limit = 0;
            } else {
                $limit = $num_registros * ($paginaActual - 1);
            }

            $strQuery = "SELECT * FROM sw_tarea " . $filtro . " ORDER BY fecha DESC LIMIT $limit, $num_registros";
            $this->db->query($strQuery);
            $consulta = $this->db->registros();

            $num_total_registros = $this->db->rowCount();

            if ($num_total_registros > 0) {
                $contador = $limit;
                foreach ($consulta as $tarea) {
                    $contador++;
                    $tabla .= "<tr>";
                    $id = $tarea->id;
                    $task = $tarea->tarea;
                    $checked = $tarea->hecho ? "checked" : "";
                    $clase = $tarea->hecho ? "taskDone" : "";
                    $tabla .= "<td><input type='checkbox' onclick='checkTask(this," . $id . ")' $checked></td>";
                    $tabla .= "<td><div class='" . $clase . "'>" . $task . "</div></td>";
                    $tabla .= "<td>";
                    $tabla .= "<div class='btn-group'>";
                    $tabla .= "<a href=\"#\" class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $tarea->id . ")\" data-toggle=\"modal\" data-target=\"#editarTareaModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
                    $tabla .= '<a href="#" class="btn btn-danger item-delete" data="' . $id . '" title="Eliminar"><span class="fa fa-trash"></span></a>';
                    $tabla .= "</div>";
                    $tabla .= "</td>";
                    $tabla .= "</tr>";
                }
            }
        } else {
            $tabla = "<tr><td colspan='4' align='center'>No se han ingresado tareas todavia...</td></tr>";
        }

        $array = array(
            0 => $tabla,
            1 => $lista,
            2 => $nroRegistros
        );

        return json_encode($array);
    }
}
