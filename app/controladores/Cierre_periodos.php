<?php
class Cierre_periodos extends Controlador
{
    private $paraleloModelo;
    private $cierrePeriodoModelo;
    private $aporteEvaluacionModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->cierrePeriodoModelo = $this->modelo('Cierre_periodo');
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->aporteEvaluacionModelo = $this->modelo('Aporte_evaluacion');
    }

    public function index()
    {
        $datos = [
            'titulo' => 'Cierre de Periodos',
            'nombreVista' => 'admin/cierre_periodos/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function listar()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        echo json_encode($this->cierrePeriodoModelo->obtenerCierresPeriodos($id_periodo_lectivo));
    }

    public function search()
    {
        $patron = trim($_POST['patron']);
        echo json_encode($this->cierrePeriodoModelo->buscarCierresPeriodos($patron));
    }

    public function create()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $paralelos = $this->paraleloModelo->obtenerParalelos($id_periodo_lectivo);
        $aportesEvaluacion = $this->aporteEvaluacionModelo->obtenerAportesEvaluacion($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Cierre Periodos Crear',
            'paralelos' => $paralelos,
            'aportes_evaluacion' => $aportesEvaluacion,
            'nombreVista' => 'admin/cierre_periodos/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        $id_paralelo = $_POST['id_paralelo'];
        $id_aporte_evaluacion = $_POST['id_aporte_evaluacion'];
        $ap_fecha_apertura = $_POST['ap_fecha_apertura'];
        $ap_fecha_cierre = $_POST['ap_fecha_cierre'];

        if ($this->cierrePeriodoModelo->existeAsociacion($id_paralelo, $id_aporte_evaluacion)) {
            $_SESSION['mensaje_error'] = "Ya existe la asociación entre Paralelo y Aporte de Evaluación en la base de datos.";
            redireccionar('/cierre_periodos/create');
        } else {
            $datos = [
                'id_paralelo' => $id_paralelo,
                'id_aporte_evaluacion' => $id_aporte_evaluacion,
                'ap_fecha_apertura' => $ap_fecha_apertura,
                'ap_fecha_cierre' => $ap_fecha_cierre
            ];
            try {
                $this->cierrePeriodoModelo->insertarAsociacion($datos);
                $_SESSION['mensaje_exito'] = "Cierre Paralelo Aporte de Evaluación insertado exitosamente en la base de datos.";
                redireccionar('/cierre_periodos');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "Cierre Paralelo Aporte de Evaluación no fue insertado exitosamente en la base de datos. Error: " . $ex->getMessage();
                redireccionar('/cierre_periodos');
            }
        }
    }

    public function obtenerParalelos()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        echo json_encode($this->paraleloModelo->obtenerParalelos($id_periodo_lectivo));
    }

    public function obtenerAportesEvaluacion()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        echo json_encode($this->aporteEvaluacionModelo->obtenerAportesEvaluacion($id_periodo_lectivo));
    }

    public function edit()
    {
        $id_aporte_paralelo_cierre = $_POST['id_aporte_paralelo_cierre'];
        echo json_encode($this->cierrePeriodoModelo->obtenerCierrePeriodo($id_aporte_paralelo_cierre));
    }

    public function update()
    {
        $datos = [
            'id_aporte_paralelo_cierre' => $_POST['id_aporte_paralelo_cierre'],
            'ap_fecha_apertura'         => $_POST['ap_fecha_apertura'],
            'ap_fecha_cierre'           => $_POST['ap_fecha_cierre']
        ];
        try {
            $this->cierrePeriodoModelo->actualizarCierrePeriodo($datos);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El Cierre de Periodo fue actualizado exitosamente.",
                "tipo_mensaje" => "success"
            );
            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "El Cierre de Periodo no se pudo actualizar. Error: " . $ex->getMessage(),
                "tipo_mensaje" => "error"
            );
            echo json_encode($data);
        }
    }

    public function update_fecha_apertura()
    {
        $datos = [
            'id_aporte_paralelo_cierre' => $_POST['id_aporte_paralelo_cierre'],
            'ap_fecha_apertura'         => $_POST['ap_fecha_apertura']
        ];
        try {
            $this->cierrePeriodoModelo->actualizarFechaApertura($datos);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El Cierre de Periodo fue actualizado exitosamente.",
                "tipo_mensaje" => "success"
            );
            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "El Cierre de Periodo no se pudo actualizar. Error: " . $ex->getMessage(),
                "tipo_mensaje" => "error"
            );
            echo json_encode($data);
        }
    }

    public function update_fecha_cierre()
    {
        $datos = [
            'id_aporte_paralelo_cierre' => $_POST['id_aporte_paralelo_cierre'],
            'ap_fecha_cierre'           => $_POST['ap_fecha_cierre']
        ];
        try {
            $this->cierrePeriodoModelo->actualizarFechaCierre($datos);
            $data = array(
                "titulo"       => "Operación exitosa.",
                "mensaje"      => "El Cierre de Periodo fue actualizado exitosamente.",
                "tipo_mensaje" => "success"
            );
            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = array(
                "titulo"       => "Ocurrió un error inesperado.",
                "mensaje"      => "El Cierre de Periodo no se pudo actualizar. Error: " . $ex->getMessage(),
                "tipo_mensaje" => "error"
            );
            echo json_encode($data);
        }
    }
}
