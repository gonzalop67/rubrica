<?php
class Cuestionarios extends Controlador
{
    private $paraleloModelo;
    private $categoriaModelo;
    private $asignaturaModelo;
    private $cuestionarioModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->categoriaModelo = $this->modelo('Categoria');
        $this->asignaturaModelo = $this->modelo('Asignatura');
        $this->cuestionarioModelo = $this->modelo('Cuestionario');
    }

    public function index()
    {
        //
    }

    public function asociar_paralelo()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        $categorias = $this->categoriaModelo->cargarCategoriasPorUsuario($id_usuario);
        $paralelos = $this->paraleloModelo->obtenerParalelosDocente($id_usuario, $id_periodo_lectivo);
        $asignaturas = $this->asignaturaModelo->obtenerAsignaturasDocente($id_usuario, $id_periodo_lectivo);
        $datos = [
            'categorias' => $categorias,
            'paralelos' => $paralelos,
            'asignaturas' => $asignaturas,
            'titulo' => 'Asociar Paralelo',
            'nombreVista' => 'docente/cuestionarios/asociar_paralelo.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function asociar_paralelo_insert()
    {
        $id_categoria = $_POST['id_categoria'];
        $id_paralelo = $_POST['id_paralelo'];
        $id_asignatura = $_POST['id_asignatura'];

        // Primero comprobar si ya existe la asociación en la bd
        if ($this->cuestionarioModelo->existeAsociacion($id_categoria, $id_paralelo, $id_asignatura)) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "Ya existe la asociacion entre el cuestionario, el paralelo y la asignatura seleccionados.",
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        } else {
            $datos = [
                'id_usuario' => $_SESSION['id_usuario'],
                'id_category' => $id_categoria,
                'id_paralelo' => $id_paralelo,
                'id_asignatura' => $id_asignatura
            ];
            try {
                $this->cuestionarioModelo->insertarAsociacion($datos);
                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "La Asociación Cuestionario Paralelo fue guardada correctamente.",
                    "tipo_mensaje" => "success"
                );

                echo json_encode($data);
            } catch (PDOException $ex) {
                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "La Asociación Cuestionario Paralelo NO fue guardada correctamente. Error: " . $ex->getMessage(),
                    "tipo_mensaje" => "error"
                );

                echo json_encode($data);
            }
        }
    }

    public function getByCategoryId()
    {
        $id_categoria = $_POST['id_categoria'];
        echo json_encode($this->cuestionarioModelo->listarCuestionariosAsociados($id_categoria));
    }

    public function eliminarAsociacion()
    {
        $id_cuestionario_paralelo = $_POST['id'];
        try {
            $this->cuestionarioModelo->eliminarAsociacion($id_cuestionario_paralelo);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "La Asociación Cuestionario Paralelo fue eliminada correctamente.",
                "tipo_mensaje" => "success"
            );

            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "La Asociación Cuestionario Paralelo NO fue eliminada correctamente. Error: " . $ex->getMessage(),
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        }
    }

    public function preliminar()
    {
        $datos = [
            'titulo' => 'Vista Preliminar',
            'nombreVista' => 'docente/cuestionarios/preliminar.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function obtenerNombreCategoria()
    {
        $id_category = $_POST["id_category"];
        $categoriaModelo = $this->modelo('Categoria');
        echo $categoriaModelo->obtenerNombreCategoria($id_category);
    }

    public function load_timer()
    {
        if (!isset($_SESSION["end_time"])) {
            echo "00:00:00";
        } else {
            $time1 = gmdate("H:i:s", strtotime($_SESSION["end_time"]) - strtotime(date("Y-m-d H:i:s")));
            if (strtotime($_SESSION["end_time"]) < strtotime(date("Y-m-d H:i:s"))) {
                echo "00:00:00";
            } else {
                echo $time1;
            }
        }
    }

    public function set_exam_type_session()
    {
        $id_category = $_POST['id_category'];
        $_SESSION["id_category"] = $id_category;

        $categoriaModelo = $this->modelo('Categoria');
        $res = $categoriaModelo->obtenerCategoriaPorId($id_category);

        $_SESSION["exam_time"] = $res->exam_time_in_minutes;

        $date = date("Y-m-d H:i:s");
        $_SESSION["end_time"] = date("Y-m-d H:i:s", strtotime($date . "+$_SESSION[exam_time] minutes"));
        $_SESSION["exam_start"] = "yes";
    }

    public function load_question()
    {
        $question_no = $_POST['questionno'];
        $id_category = $_POST['id_category'];

        $preguntaModelo = $this->modelo('Pregunta');
        $pregunta = $preguntaModelo->obtenerPreguntaPorNumeroPregunta($id_category, $question_no);

        $question_no = "";
        $question = "";
        $ans = "";

        $cadena = "";

        if (empty($pregunta)) {
            $cadena = "over";
        } else {
            $id_question = $pregunta->id;
            $question_no = $pregunta->question_no;
            $question = htmlentities($pregunta->question);

            if (isset($_SESSION["answer"][$question_no])) {
                $ans = $_SESSION["answer"][$question_no];
            }

            $cadena .= "<br>\n";
            $cadena .= "<div class=\"row\">\n"; // begin div.row (1)
            $cadena .= "<br>\n";
            $cadena .= "<div class=\"col-lg-2 col-lg-push-10\">\n"; // begin div.col-lg-2 col-lg-push-10 (1)

            $cadena .= "<div id=\"current_que\" style=\"float: left;\">$question_no</div>\n";
            $cadena .= "<div style=\"float: left;\">&nbsp;/&nbsp;</div>\n";

            $numeroPreguntas = $preguntaModelo->obtenerNumeroPreguntasPorCategoria($id_category);
            $cadena .= "<div id=\"total_que\" style=\"float: left;\">$numeroPreguntas</div>\n";
            $cadena .= "</div>\n"; // end div.col-lg-2 col-lg-push-10 (1)

            $cadena .= "<div class=\"row\">\n"; // begin div.row (2)
            $cadena .= "<div class=\"row\">\n"; // begin div.row (3)
            $cadena .= "<div class=\"col-lg-10 col-lg-push-1\" style=\"min-height: 300px; background-color: white;\" id=\"load_questions\">\n";

            // Aqui van los campos de la pregunta desde la base de datos
            $cadena .= "<br>\n";
            $cadena .= "<table>\n";
            $cadena .= "<tr>\n";
            $cadena .= "<td style=\"font-weight: bold; font-size: 18px; padding-left: 5px;\" colspan=\"2\">\n";
            $cadena .= "( " . $question_no . " ) " . $question . "\n";
            $cadena .= "</td>\n";
            $cadena .= "</tr>\n";
            $cadena .= "</table>\n";

            $cadena .= "<table style=\"margin-left: 10px;\">\n";

            $choices = $preguntaModelo->obtenerAlternativas($id_question);

            $checked = "";

            foreach ($choices as $choice) {
                if ($ans == $choice->id) {
                    $checked = "checked";
                }

                $cadena .= "<tr>\n";
                $cadena .= "<td>\n";
                $cadena .= "<input type=\"radio\" name=\"r1\" id=\"r1\" value=\"" . htmlentities($choice->text) . "\" onclick=\"radioclick($choice->id, $question_no)\" $checked>\n";
                $cadena .= "<td style=\"padding-left: 10px;\">\n";
                $cadena .= "$choice->text\n";
                $cadena .= "</td>\n";
                $cadena .= "</td>\n";
                $cadena .= "</tr>\n";
            }          

            $cadena .= "</table>\n";

            $cadena .= "</div>\n"; // end div.col-lg-10 col-lg-push-1 (1)
            $cadena .= "</div>\n"; // end div.row (3)

            $cadena .= "<div class=\"row\" style=\"margin-top: 10px\">\n"; // begin div.row (4)
            $cadena .= "<div class=\"col-lg-6 col-lg-push-3\" style=\"min-height: 50px;\">\n"; // end div.col-lg-6 col-lg-push-3 (1)
            $cadena .= "<div class=\"col-lg-12 text-center\">\n"; // begin div.col-lg-12 text-center (1)
            $cadena .= "<input type=\"button\" class=\"btn btn-warning\" value=\"Anterior\" onclick=\"load_previous($question_no);\">\n";
            $cadena .= "<input type=\"button\" class=\"btn btn-success\" value=\"Siguiente\" onclick=\"load_next($question_no);\">\n";
            $cadena .= "</div>\n"; // end div.col-lg-12 text-center (1)
            $cadena .= "</div>\n"; // end div.col-lg-6 col-lg-push-3 (1)
            $cadena .= "</div>\n"; // end div.row (4)

            $cadena .= "</div>\n"; // end div.row (2)

            $cadena .= "</div>\n"; // end div.row (1)
        }

        echo $cadena;
    }

    public function save_answer_in_session()
    {
        $questionno = $_POST["questionno"];
        $value1 = $_POST["value1"];
        $_SESSION["answer"]["$questionno"] = $value1;
    }

    public function resultados()
    {
        $datos = [
            'titulo' => 'Resultados del Cuestionario',
            'nombreVista' => 'docente/cuestionarios/resultados.php'
        ];
        $this->vista('admin/index', $datos);
    }
}
