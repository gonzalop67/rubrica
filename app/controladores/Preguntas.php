<?php
class Preguntas extends Controlador
{
    private $preguntaModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->preguntaModelo = $this->modelo('Pregunta');
    }

    public function index()
    {
        $datos = [
            'titulo' => 'Preguntas',
            'nombreVista' => 'docente/preguntas/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        $id_category = $_POST['id_category'];
        $question = $_POST['question'];
        // Validaciones
        if ($this->preguntaModelo->existePregunta($question, $id_category)) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "Ya existe la pregunta en el presente cuestionario.",
                'estado' => 'error'
            ];
            //Enviar el objeto en formato json
            echo json_encode($datos);
            $_SESSION['mensaje_error'] = "Ya existe la pregunta en el presente cuestionario.";
            redireccionar('/categorias/add_edit_questions/' . $id_category);
        } else {
            $datos = [
                'id_category' => trim($_POST['id_category']),
                'question' => trim($_POST['question']),
                'opt1' => trim($_POST['opt1']),
                'opt2' => trim($_POST['opt2']),
                'opt3' => trim($_POST['opt3']),
                'answer' => trim($_POST['answer']),
                'category' => trim($_POST['exam_category'])
            ];

            try {
                $this->preguntaModelo->insertarPregunta($datos);
                $_SESSION['mensaje_exito'] = "Pregunta insertada exitosamente en la base de datos.";
                redireccionar('/categorias/add_edit_questions/' . $id_category);
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "La Pregunta no fue insertada exitosamente. Error: " . $ex->getMessage();
                redireccionar('/categorias/add_edit_questions/' . $id_category);
            }
        }
    }

    public function editar($id)
    {
        $pregunta = $this->preguntaModelo->obtenerPregunta($id);
        $datos = [
            'titulo' => 'Editar Pregunta',
            'pregunta' => $pregunta,
            'nombreVista' => 'docente/preguntas/edit_option.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id = $_POST['id_question'];
        $id_category = $_POST['id_category'];
        $question = trim($_POST['question']);

        $preguntaActual = $this->preguntaModelo->obtenerPregunta($id);
        //die(var_dump($question));

        if ($preguntaActual->question != $question && $this->preguntaModelo->existePregunta($question, $id_category)) {
            $_SESSION['mensaje_error'] = "Ya existe la pregunta en el presente cuestionario.";
            redireccionar('/categorias/add_edit_questions/' . $id_category);
        } else {
            $datos = [
                'id' => $id,
                'question' => $question,
                'opt1' => $_POST['opt1'],
                'opt2' => $_POST['opt2'],
                'opt3' => $_POST['opt3'],
                'answer' => $_POST['answer']
            ];
            try {
                $this->preguntaModelo->actualizarPregunta($datos);
                $_SESSION['mensaje_exito'] = "Pregunta actualizada exitosamente en la base de datos.";
                redireccionar('/categorias/add_edit_questions/' . $id_category);
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "La Pregunta no fue actualizada exitosamente. Error: " . $ex->getMessage();
                redireccionar('/categorias/add_edit_questions/' . $id_category);
            }
        }
    }

    public function paginar($id_category)
    {
        $paginaActual = $_POST['partida'];

        echo $this->preguntaModelo->paginacion($paginaActual, $id_category);
    }
}
