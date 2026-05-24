<?php
class Asignaturas_cursos extends Controlador
{
    var $cursoModelo;
    var $asignaturaModelo;
    var $asignaturaCursoModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->cursoModelo = $this->modelo('Curso');
        $this->asignaturaModelo = $this->modelo('Asignatura');
        $this->asignaturaCursoModelo = $this->modelo('Asignatura_curso');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $cursos = $this->cursoModelo->obtenerCursos($id_periodo_lectivo);
        $asignaturas = $this->asignaturaModelo->obtenerAsignaturas();
        $datos = [
            'cursos' => $cursos,
            'asignaturas' => $asignaturas,
            'titulo' => 'Asignaturas Cursos',
            'nombreVista' => 'admin/asignaturas_cursos/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function getByCursoId()
    {
        $id_curso = $_POST['id_curso'];
        echo json_encode($this->asignaturaCursoModelo->listarAsignaturasAsociadas($id_curso));
    }

    public function getByParaleloId()
    {
        $id_paralelo = $_POST['id_paralelo'];
        echo json_encode($this->asignaturaCursoModelo->AsignaturasAsociadasParalelo($id_paralelo));
    }

    public function insert()
    {
        $id_curso = $_POST['id_curso'];
        $id_asignatura = $_POST['id_asignatura'];

        // Primero comprobar si ya existe la asociación en la bd
        if ($this->asignaturaCursoModelo->existeAsociacion($id_curso, $id_asignatura)) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "Ya existe la asociacion entre el curso y la asignatura seleccionados.",
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        } else {
            $datos = [
                'id_curso' => $id_curso,
                'id_asignatura' => $id_asignatura,
                'id_periodo_lectivo' => $_SESSION['id_periodo_lectivo']
            ];
            try {
                $this->asignaturaCursoModelo->insertarAsociacion($datos);
                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "La Asociación Asignatura Curso fue guardada correctamente.",
                    "tipo_mensaje" => "success"
                );

                echo json_encode($data);
            } catch (PDOException $ex) {
                $data = array(
                    "titulo"       => "Operación exitosa.",
                    "mensaje"      => "La Asociación Asignatura Curso NO fue guardada correctamente. Error: " . $ex->getMessage(),
                    "tipo_mensaje" => "error"
                );

                echo json_encode($data);
            }
        }
    }

    public function delete()
    {
        $id_asignatura_curso = $_POST['id'];
        try {
            $this->asignaturaCursoModelo->eliminarAsociacion($id_asignatura_curso);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "La Asociación Asignatura Curso fue eliminada correctamente.",
                "tipo_mensaje" => "success"
            );

            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "La Asociación Asignatura Curso NO fue eliminada correctamente. Error: " . $ex->getMessage(),
                "tipo_mensaje" => "error"
            );

            echo json_encode($data);
        }
    }

    public function saveNewPositions()
    {
        foreach ($_POST['positions'] as $position) {
            $index = $position[0];
            $newPosition = $position[1];

            $this->asignaturaCursoModelo->actualizarOrden($index, $newPosition);
        }
    }
}
