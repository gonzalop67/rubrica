<?php
class Paralelos extends Controlador
{
    private $cursoModelo;
    private $jornadaModelo;
    private $paraleloModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->cursoModelo = $this->modelo('Curso');
        $this->jornadaModelo = $this->modelo('Jornada');
        $this->paraleloModelo = $this->modelo('Paralelo');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $paralelos = $this->paraleloModelo->obtenerParalelos($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Paralelos',
            'paralelos' => $paralelos,
            'nombreVista' => 'admin/paralelos/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $cursos = $this->cursoModelo->obtenerCursos($id_periodo_lectivo);
        $jornadas = $this->jornadaModelo->obtenerJornadas();

        $datos = [
            'titulo' => 'Paralelos Crear',
            'cursos' => $cursos,
            'jornadas' => $jornadas,
            'nombreVista' => 'admin/paralelos/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        $pa_nombre = $_POST['pa_nombre'];
        $id_curso = $_POST['id_curso'];
        $id_jornada = $_POST['id_jornada'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        if ($this->paraleloModelo->existeNombreParalelo($pa_nombre, $id_curso, $id_jornada)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre del paralelo en la base de datos.";
            redireccionar('/paralelos/create');
        } else {
            $datos = [
                'pa_nombre' => $pa_nombre,
                'id_curso' => $id_curso,
                'id_jornada' => $id_jornada,
                'id_periodo_lectivo' => $id_periodo_lectivo
            ];
            try {
                $this->paraleloModelo->insertarParalelo($datos);
                $_SESSION['mensaje_exito'] = "Paralelo insertado exitosamente en la base de datos.";
                redireccionar('/paralelos');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El Paralelo no fue insertado exitosamente. Error: " . $ex->getMessage();
                redireccionar('/paralelos');
            }
        }
    }

    public function edit($id)
    {
        $paralelo = $this->paraleloModelo->obtenerParalelo($id);

        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $cursos = $this->cursoModelo->obtenerCursos($id_periodo_lectivo);
        $jornadas = $this->jornadaModelo->obtenerJornadas();

        $datos = [
            'titulo' => 'Paralelos Editar',
            'paralelo' => $paralelo,
            'cursos' => $cursos,
            'jornadas' => $jornadas,
            'nombreVista' => 'admin/paralelos/edit.php'
        ];

        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id_paralelo = $_POST['id_paralelo'];
        $id_curso = $_POST['id_curso'];
        $id_jornada = $_POST['id_jornada'];
        $pa_nombre = $_POST['pa_nombre'];
        
        $paraleloActual = $this->paraleloModelo->obtenerParalelo($id_paralelo);

        if ($paraleloActual->pa_nombre != $pa_nombre && $this->paraleloModelo->existeNombreParalelo($pa_nombre, $id_curso, $id_jornada)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre del paralelo en la base de datos.";
            redireccionar('/paralelos/edit' . $id_paralelo);
        } else {
            $datos = [
                'id_paralelo' => $id_paralelo,
                'id_curso' => $id_curso,
                'id_jornada' => $id_jornada,
                'pa_nombre' => $pa_nombre
            ];

            try {
                $this->paraleloModelo->actualizarParalelo($datos);
                $_SESSION['mensaje_exito'] = "Paralelo actualizado exitosamente en la base de datos.";
                redireccionar('/paralelos');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El Paralelo no fue actualizado exitosamente. Error: " . $ex->getMessage();
                redireccionar('/paralelos');
            }
        }   
    }

    public function delete($id)
    {
        try {
            $this->paraleloModelo->eliminarParalelo($id);
            $_SESSION['mensaje_exito'] = "Paralelo eliminado exitosamente de la base de datos.";
            redireccionar('/paralelos');
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "El Paralelo no fue eliminado exitosamente. Error: " . $ex->getMessage();
            redireccionar('/paralelos');
        }
    }

    public function saveNewPositions()
    {
        foreach($_POST['positions'] as $position) {
            $index = $position[0];
            $newPosition = $position[1];

            $this->paraleloModelo->actualizarOrden($index, $newPosition);
        }
    }
}