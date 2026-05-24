<?php
class Cursos extends Controlador
{
    private $cursoModelo;
    private $especialidadModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->cursoModelo = $this->modelo('Curso');
        $this->especialidadModelo = $this->modelo('Especialidad');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $cursos = $this->cursoModelo->obtenerCursos($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Cursos',
            'cursos' => $cursos,
            'nombreVista' => 'admin/cursos/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $especialidades = $this->especialidadModelo->obtenerEspecialidadesPorPeriodoId($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Cursos Crear',
            'especialidades' => $especialidades,
            'nombreVista' => 'admin/cursos/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        $cu_nombre          = $_POST['cu_nombre'];
        $cu_shortname       = $_POST['cu_shortname'];
        $cu_abreviatura     = $_POST['cu_abreviatura'];
        $es_bach_tecnico    = $_POST['es_bach_tecnico'];
        $es_intensivo       = $_POST['es_intensivo'];
        $id_especialidad    = $_POST['id_especialidad'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        if ($this->cursoModelo->existeNombreCurso($cu_nombre, $id_especialidad)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre del curso en la base de datos.";
            redireccionar('/cursos/create');
        } else if ($this->cursoModelo->existeNombreCortoCurso($cu_shortname, $id_especialidad)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre corto del curso en la base de datos.";
            redireccionar('/cursos/create');
        } else if ($this->cursoModelo->existeAbreviaturaCurso($cu_abreviatura, $id_especialidad)) {
            $_SESSION['mensaje_error'] = "Ya existe la abreviatura del curso en la base de datos.";
            redireccionar('/cursos/create');
        } else {
            $datos = [
                'cu_nombre'          => $cu_nombre,
                'cu_shortname'       => $cu_shortname,
                'cu_abreviatura'     => $cu_abreviatura,
                'es_bach_tecnico'    => $es_bach_tecnico,
                'es_intensivo'       => $es_intensivo,
                'id_especialidad'    => $id_especialidad,
                'id_periodo_lectivo' => $id_periodo_lectivo
            ];
            try {
                $this->cursoModelo->insertarCurso($datos);
                $_SESSION['mensaje_exito'] = "Curso insertado exitosamente en la base de datos.";
                redireccionar('/cursos');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El Curso no fue insertado exitosamente. Error: " . $ex->getMessage();
                redireccionar('/cursos');
            }
        }
    }

    public function edit($id)
    {
        $curso = $this->cursoModelo->obtenerCurso($id);
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $especialidades = $this->especialidadModelo->obtenerEspecialidadesPorPeriodoId($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Cursos Editar',
            'curso' => $curso,
            'especialidades' => $especialidades,
            'nombreVista' => 'admin/cursos/edit.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id_curso = $_POST['id_curso'];
        $cu_nombre = $_POST['cu_nombre'];
        $cu_shortname = $_POST['cu_shortname'];
        $cu_abreviatura = $_POST['cu_abreviatura'];
        $es_bach_tecnico = $_POST['es_bach_tecnico'];
        $es_intensivo = $_POST['es_intensivo'];
        $id_especialidad = $_POST['id_especialidad'];

        $cursoActual = $this->cursoModelo->obtenerCurso($id_curso);

        if ($cursoActual->cu_nombre != $cu_nombre && $this->cursoModelo->existeNombreCurso($cu_nombre, $id_especialidad)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre del curso en la base de datos.";
            redireccionar('/cursos/edit/' . $id_curso);
        } else if ($cursoActual->cu_shortname != $cu_shortname && $this->cursoModelo->existeNombreCortoCurso($cu_shortname, $id_especialidad)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre corto del curso en la base de datos.";
            redireccionar('/cursos/edit/' . $id_curso);
        } else if ($cursoActual->cu_abreviatura != $cu_abreviatura && $this->cursoModelo->existeAbreviaturaCurso($cu_abreviatura, $id_especialidad)) {
            $_SESSION['mensaje_error'] = "Ya existe la abreviatura del curso en la base de datos.";
            redireccionar('/cursos/edit/' . $id_curso);
        } else {
            $datos = [
                'id_curso'        => $id_curso,
                'cu_nombre'       => $cu_nombre,
                'cu_shortname'    => $cu_shortname,
                'cu_abreviatura'  => $cu_abreviatura,
                'es_intensivo'    => $es_intensivo,
                'es_bach_tecnico' => $es_bach_tecnico,
                'id_especialidad' => $id_especialidad
            ];
            try {
                $this->cursoModelo->actualizarCurso($datos);
                $_SESSION['mensaje_exito'] = "Curso actualizado exitosamente en la base de datos.";
                redireccionar('/cursos');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El Curso no fue actualizado exitosamente. Error: " . $ex->getMessage();
                redireccionar('/cursos');
            }
        }
    }

    public function delete($id)
    {
        try {
            $this->cursoModelo->eliminarCurso($id);
            $_SESSION['mensaje_exito'] = "Curso eliminado exitosamente de la base de datos.";
            redireccionar('/cursos');
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "El Curso no fue eliminado exitosamente. Error: " . $ex->getMessage();
            redireccionar('/cursos');
        }
    }

    public function saveNewPositions()
    {
        foreach($_POST['positions'] as $position) {
            $index = $position[0];
            $newPosition = $position[1];

            $this->cursoModelo->actualizarOrden($index, $newPosition);
        }
    }

}