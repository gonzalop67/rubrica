<?php
class Pregunta
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function existePregunta($question, $id_category)
    {
        $query = "SELECT * "
                . " FROM sw_questions "
                . "WHERE question = '$question' "
                . "  AND id_category = $id_category";

        $this->db->query($query);
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function insertarPregunta($datos)
    {
        $loop = 0;
        $count = 0;
        $this->db->query("SELECT * FROM sw_questions WHERE category = '$datos[category]' ORDER BY id ASC");
        $res = $this->db->registros();
        $count = $this->db->rowCount();

        if ($count > 0) {
            foreach ($res as $v) {
                $loop++;
                $this->db->query("UPDATE sw_questions SET question_no = :loop WHERE id = :id");
                //Vincular valores
                $this->db->bind(':loop', $loop);
                $this->db->bind(':id', $v->id);
                //Ejecutar la sentencia
                $this->db->execute();
            }
        }

        $loop++;
        $this->db->query('INSERT INTO sw_questions (id_category, question_no, question, opt1, opt2, opt3, answer, category) VALUES (:id_category, :question_no, :question, :opt1, :opt2, :opt3, :answer, :category)');

        //Vincular valores
        $this->db->bind(':id_category', $datos['id_category']);
        $this->db->bind(':question_no', $loop);
        $this->db->bind(':question', $datos['question']);
        $this->db->bind(':opt1', $datos['opt1']);
        $this->db->bind(':opt2', $datos['opt2']);
        $this->db->bind(':opt3', $datos['opt3']);
        $this->db->bind(':answer', $datos['answer']);
        $this->db->bind(':category', $datos['category']);

        $this->db->execute();
    }

    public function actualizarPregunta($datos)
    {
        $this->db->query('UPDATE sw_questions SET question = :question, opt1 = :opt1, opt2 = :opt2, opt3 = :opt3, answer = :answer WHERE id = :id');

        //Vincular valores
        $this->db->bind(':id', $datos['id']);
        $this->db->bind(':question', $datos['question']);
        $this->db->bind(':opt1', $datos['opt1']);
        $this->db->bind(':opt2', $datos['opt2']);
        $this->db->bind(':opt3', $datos['opt3']);
        $this->db->bind(':answer', $datos['answer']);

        $this->db->execute();
    }

    public function obtenerPregunta($id)
    {
        $query = "SELECT * FROM sw_questions WHERE id = $id";

        $this->db->query($query);
        return $this->db->registro();
    }

    public function obtenerPreguntaPorNumeroPregunta($id_category, $question_no)
    {
        $query = "SELECT * FROM sw_questions WHERE id_category = $id_category && question_no = $question_no ORDER BY id ASC";
        $this->db->query($query);

        return $this->db->registro();
    }

    public function obtenerNumeroPreguntasPorCategoria($id_category)
    {
        $query = "SELECT * FROM sw_questions WHERE id_category = $id_category";
        $this->db->query($query);

        $this->db->registros();
        return $this->db->rowCount();
    }

    public function obtenerAlternativas($id_question)
    {
        $query = "SELECT * FROM sw_choices WHERE id_question = $id_question";
        $this->db->query($query);

        return $this->db->registros();
    }

    public function paginacion($paginaActual, $id_category)
    {
        $this->db->query("SELECT * FROM `sw_questions` WHERE id_category = $id_category");
        $consulta = $this->db->registros();
        $nroPreguntas = $this->db->rowCount();

        $nroLotes = 5;
        $nroPaginas = ceil($nroPreguntas / $nroLotes);

        $lista = '';
        $tabla = '';

        if ($nroPreguntas > 0) {
            if ($paginaActual == 1) {
                $lista = $lista . '<li><a href="javascript:;" disabled>‹</a></li>';
            } else {
                $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual - 1) . ',' . $id_category . ');">‹</a></li>';
            }

            for ($i = 1; $i <= $nroPaginas; $i++) {
                if ($i == $paginaActual) {
                    $lista = $lista . '<li class="active"><a href="javascript:pagination(' . ',' . $id_category . ');">' . $i . '</a></li>';
                } else {
                    $lista = $lista . '<li><a href="javascript:pagination(' . $i . ',' . $id_category . ');">' . $i . '</a></li>';
                }
            }

            if ($paginaActual == $nroPaginas) {
                $lista = $lista . '<li><a href="javascript:;" disabled>›</a></li>';
            } else {
                $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual + 1) . ',' . $id_category . ');">›</a></li>';
            }

            if ($paginaActual <= 1) {
                $limit = 0;
            } else {
                $limit = $nroLotes * ($paginaActual - 1);
            }

            $this->db->query("SELECT * FROM `sw_questions` WHERE id_category = $id_category ORDER BY id ASC LIMIT $limit, $nroLotes");
            $consulta = $this->db->registros();
            $num_total_registros = $this->db->rowCount();

            if ($num_total_registros > 0) {
                $contador = $limit;
                foreach ($consulta as $v) {
                    $contador++;
                    $tabla .= "<tr>\n";
                    $code = $v->id;
                    $question_no = $v->question_no;
                    $question = $v->question;
                    $opt1 = $v->opt1;
                    $opt2 = $v->opt2;
                    $opt3 = $v->opt3;
                    $tabla .= "<td>$question_no</td>";
                    $tabla .= "<td>$question</td>\n";
                    $tabla .= "<td>$opt1</td>\n";
                    $tabla .= "<td>$opt2</td>\n";
                    $tabla .= "<td>$opt3</td>\n";
                    $tabla .= "<td>\n";
                    $tabla .= "<div class=\"btn-group\">\n";
                    $tabla .= "<a href=\"" . RUTA_URL . "/preguntas/editar/" . $code . "\" class=\"btn btn-warning\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
                    $tabla .= "<a href=\"" . RUTA_URL . "/preguntas/eliminar/" . $code . "\" class=\"btn btn-danger\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></a>\n";
                    $tabla .= "</div>\n";
                    $tabla .= "</td>\n";
                    $tabla .= "</tr>\n";
                }
            }
        } else {
            $tabla .= "<tr>\n";
            $tabla .= "<td colspan=\"6\" align=\"center\">A&uacute;n no se han insertado preguntas...</td>\n";
            $tabla .= "</tr>\n";
        }

        $array = array(
            0 => $tabla,
            1 => $lista
        );

        return json_encode($array);
    }
}
