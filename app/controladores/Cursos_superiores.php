<?php
class Cursos_superiores extends Controlador
{
    private $cursoModelo;
    private $cursoSuperiorModelo;
    private $asociarCursoSuperiorModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->cursoModelo = $this->modelo('Curso');
        $this->cursoSuperiorModelo = $this->modelo('Curso_superior');
        $this->asociarCursoSuperiorModelo = $this->modelo('Asociar_curso_superior');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $cursos = $this->cursoModelo->obtenerCursos($id_periodo_lectivo);
        $cursos_superiores = $this->cursoSuperiorModelo->obtenerCursosSuperiores();
        $datos = [
            'titulo' => 'Cursos Superiores',
            'cursos' => $cursos,
            'cursos_superiores' => $cursos_superiores,
            'nombreVista' => 'admin/cursos_superiores/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function listar()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        echo $this->asociarCursoSuperiorModelo->listarCursosSuperiores($id_periodo_lectivo);
    }

    public function insert()
    {
        $id_curso_inferior = $_POST['id_curso_inferior'];
        $id_curso_superior = $_POST['id_curso_superior'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        // Primero comprobar si ya existe la asociación en la bd
        if ($this->asociarCursoSuperiorModelo->existeAsociacion($id_curso_inferior, $id_curso_superior)) {
            $data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "Ya existe la asociacion entre los cursos inferior y superior seleccionados.",
				"tipo_mensaje" => "error"
			);

			echo json_encode($data);
        } else {
            $datos = [
                'id_curso_inferior' => $id_curso_inferior,
                'id_curso_superior' => $id_curso_superior,
                'id_periodo_lectivo' => $id_periodo_lectivo
            ];

            try {
                $this->asociarCursoSuperiorModelo->insertarAsociacion($datos);

                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "La Asociación Curso Inferior y Superior fue guardada correctamente.",
                    "tipo_mensaje" => "success"
                );
    
                echo json_encode($data);
            } catch (PDOException $ex) {
                $data = array(
                    "titulo"       => "Ocurrió un error inesperado.",
                    "mensaje"      => "La Asociación Curso Inferior y Superior NO fue guardada correctamente. Error: " . $ex->getMessage(),
                    "tipo_mensaje" => "error"
                );
    
                echo json_encode($data);
            }
        }
    }

    public function delete($id)
    {
        try {
            $this->asociarCursoSuperiorModelo->eliminarAsociacion($id);
            $_SESSION['mensaje_exito'] = "La Asociación Curso Inferior y Superior fue eliminada exitosamente de la base de datos.";
            redireccionar('/cursos_superiores');
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "La Asociación Curso Inferior y Superior NO fue eliminada exitosamente. Error: " . $ex->getMessage();
            redireccionar('/cursos_superiores');
        }
    }
}