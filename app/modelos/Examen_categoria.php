<?php
class Examen_categoria
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function existeCategoria($category, $id_usuario)
    {
        $query = "SELECT id "
            . " FROM sw_exam_category "
            . "WHERE category = '" . $category . "' "
            . "  AND id_usuario = $id_usuario";

        $this->db->query($query);
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function insertarCategoria($datos)
    {
        $this->db->query('INSERT INTO sw_exam_category (id_usuario, category, exam_time_in_minutes) VALUES (:id_usuario, :category, :exam_time_in_minutes)');

        //Vincular valores
        $this->db->bind(':id_usuario', $datos['id_usuario']);
        $this->db->bind(':category', $datos['category']);
        $this->db->bind(':exam_time_in_minutes', $datos['exam_time_in_minutes']);

        //Ejecutar
        try {
            $this->db->execute();
            $datos = [
                'titulo' => "¡Agregado con éxito!",
                'mensaje' => "Inserción realizada exitosamente.",
                'estado' => 'success'
            ];
            //Enviar el objeto en formato json
            return json_encode($datos);
        } catch (PDOException $e) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "No se pudo realizar la inserción. Error: " . $e->getMessage(),
                'estado' => 'error'
            ];
            //Enviar el objeto en formato json
            return json_encode($datos);
        }
    }

    public function cargarCategoriasPorUsuario($id_usuario)
    {
        $this->db->query("SELECT * FROM sw_exam_category WHERE id_usuario = $id_usuario ORDER BY category ASC");
        return $this->db->registros();
    }

    public function obtenerCategoriasPorUsuario($id_usuario)
    {
        $this->db->query("SELECT * FROM sw_exam_category WHERE id_usuario = $id_usuario ORDER BY category ASC");
        $res = $this->db->registros();

        $lista = "";
        foreach ($res as $v) {
            $lista .= "<input type=\"button\" class=\"btn btn-success form-control\" value=\"" . $v->category . "\" style=\"margin-top: 10px; background-color: blue; color: white\" onclick=\"set_exam_type_session(" . $v->id . ")\">\n";
        }

        return $lista;
    }

    public function obtenerCategoriaPorNombre($exam_category)
    {
        $this->db->query("SELECT * FROM sw_exam_category WHERE category = '$exam_category'");
        return $this->db->registro();
    }

    public function obtenerCategoriaPorId($id)
    {
        $this->db->query("SELECT * FROM sw_exam_category WHERE id = $id");
        return $this->db->registro();
    }

    public function obtenerNombreCategoria($id)
    {
        $this->db->query("SELECT category FROM sw_exam_category WHERE id = $id");
        return $this->db->registro()->category;
    }

    public function actualizarCategoria($datos)
    {
        $this->db->query('UPDATE sw_exam_category SET category = :category, exam_time_in_minutes = :exam_time_in_minutes WHERE id = :id');

        //Vincular valores
        $this->db->bind(':id', $datos['id']);
        $this->db->bind(':category', $datos['category']);
        $this->db->bind(':exam_time_in_minutes', $datos['exam_time_in_minutes']);

        //Ejecutar
        try {
            $this->db->execute();
            $datos = [
                'titulo' => "¡Actualizado con éxito!",
                'mensaje' => "Actualización realizada exitosamente.",
                'estado' => 'success'
            ];
            //Enviar el objeto en formato json
            return json_encode($datos);
        } catch (PDOException $e) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "No se pudo realizar la actualización. Error: " . $e->getMessage(),
                'estado' => 'error'
            ];
            //Enviar el objeto en formato json
            return json_encode($datos);
        }
    }

    public function paginacion($paginaActual, $id_usuario)
    {
        $this->db->query("SELECT * FROM `sw_exam_category` WHERE id_usuario = $id_usuario");
        $consulta = $this->db->registros();
        $nroCategorias = $this->db->rowCount();

        $nroLotes = 5;
        $nroPaginas = ceil($nroCategorias / $nroLotes);

        $lista = '';
        $tabla = '';

        if ($nroCategorias > 0) {
            if ($paginaActual == 1) {
                $lista = $lista . '<li><a href="javascript:;" disabled>‹</a></li>';
            } else {
                $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual - 1) . ',' . $id_usuario . ');">‹</a></li>';
            }

            for ($i = 1; $i <= $nroPaginas; $i++) {
                if ($i == $paginaActual) {
                    $lista = $lista . '<li class="active"><a href="javascript:pagination(' . ',' . $id_usuario . ');">' . $i . '</a></li>';
                } else {
                    $lista = $lista . '<li><a href="javascript:pagination(' . $i . ',' . $id_usuario . ');">' . $i . '</a></li>';
                }
            }

            if ($paginaActual == $nroPaginas) {
                $lista = $lista . '<li><a href="javascript:;" disabled>›</a></li>';
            } else {
                $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual + 1) . ',' . $id_usuario . ');">›</a></li>';
            }

            if ($paginaActual <= 1) {
                $limit = 0;
            } else {
                $limit = $nroLotes * ($paginaActual - 1);
            }

            $this->db->query("SELECT * FROM `sw_exam_category` WHERE id_usuario = $id_usuario ORDER BY id DESC LIMIT $limit, $nroLotes");
            $consulta = $this->db->registros();
            $num_total_registros = $this->db->rowCount();

            if ($num_total_registros > 0) {
                $contador = $limit;
                foreach ($consulta as $v) {
                    $contador++;
                    $tabla .= "<tr>\n";
                    $code = $v->id;
                    $category = $v->category;
                    $exam_time_in_minutes = $v->exam_time_in_minutes;
                    $tabla .= "<td>$contador</td>";
                    $tabla .= "<td>$category</td>\n";
                    $tabla .= "<td>$exam_time_in_minutes</td>\n";
                    $tabla .= "<td>\n";
                    $tabla .= "<div class=\"btn-group\">\n";
                    $tabla .= "<a href=\"javascript:;\" class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $code . ")\" data-toggle=\"modal\" data-target=\"#editarCategoriaModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
                    $tabla .= "<a href=\"javascript:;\" class=\"btn btn-danger\" onclick=\"eliminarCategoria(" . $code . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></a>\n";
                    $tabla .= "</div>\n";
                    $tabla .= "</td>\n";
                    $tabla .= "</tr>\n";
                }
            }
        } else {
            $tabla .= "<tr>\n";
            $tabla .= "<td colspan=\"4\" align=\"center\">A&uacute;n no se han definido categorias...</td>\n";
            $tabla .= "</tr>\n";
        }

        $array = array(
            0 => $tabla,
            1 => $lista
        );

        return json_encode($array);
    }

    public function paginacion_select($paginaActual, $id_usuario)
    {
        $this->db->query("SELECT * FROM `sw_exam_category` WHERE id_usuario = $id_usuario");
        $consulta = $this->db->registros();
        $nroCategorias = $this->db->rowCount();

        $nroLotes = 5;
        $nroPaginas = ceil($nroCategorias / $nroLotes);

        $lista = '';
        $tabla = '';

        if ($nroCategorias > 0) {
            if ($paginaActual == 1) {
                $lista = $lista . '<li><a href="javascript:;" disabled>‹</a></li>';
            } else {
                $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual - 1) . ',' . $id_usuario . ');">‹</a></li>';
            }

            for ($i = 1; $i <= $nroPaginas; $i++) {
                if ($i == $paginaActual) {
                    $lista = $lista . '<li class="active"><a href="javascript:pagination(' . ',' . $id_usuario . ');">' . $i . '</a></li>';
                } else {
                    $lista = $lista . '<li><a href="javascript:pagination(' . $i . ',' . $id_usuario . ');">' . $i . '</a></li>';
                }
            }

            if ($paginaActual == $nroPaginas) {
                $lista = $lista . '<li><a href="javascript:;" disabled>›</a></li>';
            } else {
                $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual + 1) . ',' . $id_usuario . ');">›</a></li>';
            }

            if ($paginaActual <= 1) {
                $limit = 0;
            } else {
                $limit = $nroLotes * ($paginaActual - 1);
            }

            $this->db->query("SELECT * FROM `sw_exam_category` WHERE id_usuario = $id_usuario ORDER BY id DESC LIMIT $limit, $nroLotes");
            $consulta = $this->db->registros();
            $num_total_registros = $this->db->rowCount();

            if ($num_total_registros > 0) {
                $contador = $limit;
                foreach ($consulta as $v) {
                    $contador++;
                    $tabla .= "<tr>\n";
                    $code = $v->id;
                    $category = $v->category;
                    $exam_time_in_minutes = $v->exam_time_in_minutes;
                    $tabla .= "<td>$contador</td>";
                    $tabla .= "<td>$category</td>\n";
                    $tabla .= "<td>$exam_time_in_minutes</td>\n";
                    $tabla .= "<td>\n";
                    $tabla .= "<div class=\"btn-group\">\n";
                    $tabla .= "<a href=\"" . RUTA_URL . "/categorias/add_edit_questions/" . $code . "\" class=\"btn btn-primary\" title=\"Seleccionar\"><span class=\"fa fa-check\"></span></a>\n";
                    $tabla .= "</div>\n";
                    $tabla .= "</td>\n";
                    $tabla .= "</tr>\n";
                }
            }
        } else {
            $tabla .= "<tr>\n";
            $tabla .= "<td colspan=\"4\" align=\"center\">A&uacute;n no se han definido categorias...</td>\n";
            $tabla .= "</tr>\n";
        }

        $array = array(
            0 => $tabla,
            1 => $lista
        );

        return json_encode($array);
    }
}
