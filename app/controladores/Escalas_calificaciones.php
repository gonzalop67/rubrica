<?php
class Escalas_calificaciones extends Controlador
{
    private $escalaCalificacionModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->escalaCalificacionModelo = $this->modelo('Escala_calificacion');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $escalas_calificaciones = $this->escalaCalificacionModelo->obtenerEscalasCalificaciones($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Escalas de Calificaciones',
            'escalas_calificaciones' => $escalas_calificaciones,
            'nombreVista' => 'admin/escalas_calificaciones/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $datos = [
            'titulo' => 'Escalas Calificaciones Crear',
            'nombreVista' => 'admin/escalas_calificaciones/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        $ec_cualitativa = trim($_POST['ec_cualitativa']);
        $ec_cuantitativa = trim($_POST['ec_cuantitativa']);
        $ec_nota_minima = trim($_POST['ec_nota_minima']);
        $ec_nota_maxima = trim($_POST['ec_nota_maxima']);
        $ec_equivalencia = trim($_POST['ec_equivalencia']);
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        if ($this->escalaCalificacionModelo->existeCualitativa($ec_cualitativa, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe la escala cualitativa en la base de datos.";
            redireccionar('/escalas_calificaciones/create');
        } else if ($this->escalaCalificacionModelo->existeCuantitativa($ec_cuantitativa, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe la escala cuantitativa en la base de datos.";
            redireccionar('/escalas_calificaciones/create');
        } else if ($this->escalaCalificacionModelo->existeNotaMinima($ec_nota_minima, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe la nota mínima en la base de datos.";
            redireccionar('/escalas_calificaciones/create');
        } else if ($this->escalaCalificacionModelo->existeNotaMaxima($ec_nota_maxima, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe la nota máxima en la base de datos.";
            redireccionar('/escalas_calificaciones/create');
        } else if ($this->escalaCalificacionModelo->existeEquivalencia($ec_equivalencia, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe la equivalencia en la base de datos.";
            redireccionar('/escalas_calificaciones/create');
        } else {
            $datos = [
                'ec_cualitativa' => $ec_cualitativa,
                'ec_cuantitativa' => $ec_cuantitativa,
                'ec_nota_minima' => $ec_nota_minima,
                'ec_nota_maxima' => $ec_nota_maxima,
                'ec_equivalencia' => $ec_equivalencia,
                'id_periodo_lectivo' => $id_periodo_lectivo
            ];
            try {
                $this->escalaCalificacionModelo->insertarEscalaCalificacion($datos);
                $_SESSION['mensaje_exito'] = "Escala de Calificaciones insertada exitosamente en la base de datos.";
                redireccionar('/escalas_calificaciones');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "La Escala de Calificaciones no fue insertada exitosamente. Error: " . $ex->getMessage();
                redireccionar('/escalas_calificaciones');
            }
        }
    }

    public function edit($id)
    {
        $escalaCalificacion = $this->escalaCalificacionModelo->obtenerEscalaCalificacion($id);
        $datos = [
            'titulo' => 'Escala de Calificaciones Editar',
            'escalaCalificacion' => $escalaCalificacion,
            'nombreVista' => 'admin/escalas_calificaciones/edit.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id_escala_calificaciones = $_POST['id_escala_calificaciones'];
        $ec_cualitativa = trim($_POST['ec_cualitativa']);
        $ec_cuantitativa = trim($_POST['ec_cuantitativa']);
        $ec_nota_minima = trim($_POST['ec_nota_minima']);
        $ec_nota_maxima = trim($_POST['ec_nota_maxima']);
        $ec_equivalencia = trim($_POST['ec_equivalencia']);
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        $escalaCalificacionActual = $this->escalaCalificacionModelo->obtenerEscalaCalificacion($id_escala_calificaciones);

        if ($escalaCalificacionActual->ec_cualitativa != $ec_cualitativa && $this->escalaCalificacionModelo->existeCualitativa($ec_cualitativa, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe la escala cualitativa en la base de datos.";
            redireccionar('/escalas_calificaciones/edit/' . $id_escala_calificaciones);
        } else if ($escalaCalificacionActual->ec_cuantitativa != $ec_cuantitativa && $this->escalaCalificacionModelo->existeCuantitativa($ec_cuantitativa, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe la escala cuantitativa en la base de datos.";
            redireccionar('/escalas_calificaciones/edit/' . $id_escala_calificaciones);
        } else if ($escalaCalificacionActual->ec_nota_minima != $ec_nota_minima && $this->escalaCalificacionModelo->existeNotaMinima($ec_nota_minima, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe la nota mínima en la base de datos.";
            redireccionar('/escalas_calificaciones/edit/' . $id_escala_calificaciones);
        } else if ($escalaCalificacionActual->ec_nota_maxima != $ec_nota_maxima && $this->escalaCalificacionModelo->existeNotaMaxima($ec_nota_maxima, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe la nota máxima en la base de datos.";
            redireccionar('/escalas_calificaciones/edit/' . $id_escala_calificaciones);
        } else if ($escalaCalificacionActual->ec_equivalencia != $ec_equivalencia && $this->escalaCalificacionModelo->existeEquivalencia($ec_equivalencia, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe la equivalencia en la base de datos.";
            redireccionar('/escalas_calificaciones/edit/' . $id_escala_calificaciones);
        } else {
            $datos = [
                'id_escala_calificaciones' => $id_escala_calificaciones,
                'ec_cualitativa' => $ec_cualitativa,
                'ec_cuantitativa' => $ec_cuantitativa,
                'ec_nota_minima' => $ec_nota_minima,
                'ec_nota_maxima' => $ec_nota_maxima,
                'ec_equivalencia' => $ec_equivalencia
            ];
            try {
                $this->escalaCalificacionModelo->actualizarEscalaCalificacion($datos);
                $_SESSION['mensaje_exito'] = "Escala de Calificaciones actualizada exitosamente en la base de datos.";
                redireccionar('/escalas_calificaciones');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "La Escala de Calificaciones no fue actualizada exitosamente. Error: " . $ex->getMessage();
                redireccionar('/escalas_calificaciones');
            }
        }
    }

    public function delete($id)
    {
        try {
            $this->escalaCalificacionModelo->eliminarEscalaCalificacion($id);
            $_SESSION['mensaje_exito'] = "Escala de Calificaciones eliminada exitosamente de la base de datos.";
            redireccionar('/escalas_calificaciones');
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "La Escala de Calificaciones no fue eliminada exitosamente. Error: " . $ex->getMessage();
            redireccionar('/escalas_calificaciones');
        }
    }

    public function saveNewPositions()
    {
        foreach($_POST['positions'] as $position) {
            $index = $position[0];
            $newPosition = $position[1];

            $this->escalaCalificacionModelo->actualizarOrden($index, $newPosition);
        }
    }
}
