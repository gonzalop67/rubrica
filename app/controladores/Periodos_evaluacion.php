<?php
class Periodos_evaluacion extends Controlador
{
    private $tipoPeriodoEvaluacionModelo;
    private $periodoEvaluacionModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->tipoPeriodoEvaluacionModelo = $this->Modelo('Tipo_periodo');
        $this->periodoEvaluacionModelo = $this->modelo('Periodo_evaluacion');
    }

    public function index()
    {
        // $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $sub_periodos_evaluacion = $this->periodoEvaluacionModelo->obtenerSubPeriodosEvaluacion();
        $datos = [
            'titulo' => 'Periodos de Evaluación',
            'sub_periodos_evaluacion' => $sub_periodos_evaluacion,
            'nombreVista' => 'admin/periodos_evaluacion/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $tipos_periodo = $this->tipoPeriodoEvaluacionModelo->obtenerTiposPeriodoEvaluacion();
        $datos = [
            'titulo'        => 'Periodos Evaluación Crear',
            'tipos_periodo' => $tipos_periodo,
            'nombreVista'   => 'admin/periodos_evaluacion/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        $pe_nombre = trim($_POST['pe_nombre']);
        $pe_abreviatura = trim($_POST['pe_abreviatura']);
        $id_tipo_periodo = trim($_POST['id_tipo_periodo']);
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        if ($this->periodoEvaluacionModelo->existeNombrePeriodo($pe_nombre, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre del periodo de evaluación en la base de datos.";
            redireccionar('/periodos_evaluacion/create');
        } else if ($this->periodoEvaluacionModelo->existeAbreviaturaPeriodo($pe_abreviatura, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe la abreviatura del periodo de evaluación en la base de datos.";
            redireccionar('/periodos_evaluacion/create');
        } else {
            $datos = [
                'pe_nombre' => $pe_nombre,
                'pe_abreviatura' => $pe_abreviatura,
                'id_tipo_periodo' => $id_tipo_periodo,
                'id_periodo_lectivo' => $id_periodo_lectivo
            ];
            try {
                $this->periodoEvaluacionModelo->insertarPeriodoEvaluacion($datos);
                $_SESSION['mensaje_exito'] = "Periodo de Evaluación insertado exitosamente en la base de datos.";
                redireccionar('/periodos_evaluacion');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El Periodo de Evaluación no fue insertado exitosamente. Error: " . $ex->getMessage();
                redireccionar('/periodos_evaluacion');
            }
        }
    }

    public function edit($id)
    {
        $periodo_evaluacion = $this->periodoEvaluacionModelo->obtenerPeriodoEvaluacion($id);
        $tipos_periodo = $this->tipoPeriodoEvaluacionModelo->obtenerTiposPeriodoEvaluacion();
        $datos = [
            'titulo'             => 'Periodos Evaluación Editar',
            'periodo_evaluacion' => $periodo_evaluacion,
            'tipos_periodo'      => $tipos_periodo,
            'nombreVista'        => 'admin/periodos_evaluacion/edit.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id = $_POST['id_sub_periodo_evaluacion'];
        $pe_nombre = trim($_POST['pe_nombre']);
        $pe_abreviatura = trim($_POST['pe_abreviatura']);
        $id_tipo_periodo = trim($_POST['id_tipo_periodo']);
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        $periodoEvaluacionActual = $this->periodoEvaluacionModelo->obtenerPeriodoEvaluacion($id);

        if ($periodoEvaluacionActual->pe_nombre != $pe_nombre && $this->periodoEvaluacionModelo->existeNombrePeriodo($pe_nombre, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre del periodo de evaluación en la base de datos.";
            redireccionar('/periodos_evaluacion/create');
        } else if ($periodoEvaluacionActual->pe_abreviatura != $pe_abreviatura && $this->periodoEvaluacionModelo->existeAbreviaturaPeriodo($pe_abreviatura, $id_periodo_lectivo)) {
            $_SESSION['mensaje_error'] = "Ya existe la abreviatura del periodo de evaluación en la base de datos.";
            redireccionar('/periodos_evaluacion/create');
        } else {
            $datos = [
                'id_sub_periodo_evaluacion' => $id,
                'pe_nombre' => $pe_nombre,
                'pe_abreviatura' => $pe_abreviatura,
                'id_tipo_periodo' => $id_tipo_periodo
            ];
            try {
                $this->periodoEvaluacionModelo->actualizarPeriodoEvaluacion($datos);
                $_SESSION['mensaje_exito'] = "Periodo de Evaluación actualizado exitosamente en la base de datos.";
                redireccionar('/periodos_evaluacion');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El Periodo de Evaluación no fue actualizado exitosamente. Error: " . $ex->getMessage();
                redireccionar('/periodos_evaluacion');
            }
        }
    }

    public function delete($id)
    {
        try {
            $this->periodoEvaluacionModelo->eliminarPeriodoEvaluacion($id);
            $_SESSION['mensaje_exito'] = "Periodo de Evaluación eliminado exitosamente de la base de datos.";
            redireccionar('/periodos_evaluacion');
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "El Periodo de Evaluación no fue eliminado exitosamente. Error: " . $ex->getMessage();
            redireccionar('/periodos_evaluacion');
        }
    }

    public function saveNewPositions()
    {
        foreach($_POST['positions'] as $position) {
            $index = $position[0];
            $newPosition = $position[1];

            $this->periodoEvaluacionModelo->actualizarOrden($index, $newPosition);
        }
    }

}