<?php
class Aportes_evaluacion extends Controlador
{
    private $tipoAporteModelo;
    private $aporteEvaluacionModelo;
    private $periodoEvaluacionModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->tipoAporteModelo = $this->modelo('Tipo_aporte');
        $this->aporteEvaluacionModelo = $this->modelo('Aporte_evaluacion');
        $this->periodoEvaluacionModelo = $this->modelo('Periodo_evaluacion');
    }

    public function obtenerAportesEvaluacion()
    {
        $id_periodo_evaluacion = $_POST['id_periodo_evaluacion'];
        echo $this->aporteEvaluacionModelo->obtenerAportesPorPeriodo($id_periodo_evaluacion);
    }

    public function index()
    {
        $aportesEvaluacion = $this->aporteEvaluacionModelo->obtenerAportesEvaluacion();
        $datos = [
            'titulo' => 'Aportes de Evaluación',
            'aportes_evaluacion' => $aportesEvaluacion,
            'nombreVista' => 'admin/aportes_evaluacion/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $tipos_aporte = $this->tipoAporteModelo->obtenerTiposAporteEvaluacion();
        $sub_periodos_evaluacion = $this->periodoEvaluacionModelo->obtenerPeriodosEvaluacion($_SESSION['id_periodo_lectivo']);
        $datos = [
            'titulo' => 'Aportes Evaluación Crear',
            'tipos_aporte' => $tipos_aporte,
            'sub_periodos_evaluacion' => $sub_periodos_evaluacion,
            'nombreVista' => 'admin/aportes_evaluacion/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        // print_r("<pre>");
        // print_r($_POST);
        // print_r("</pre>");
        // die();
        $id_sub_periodo_evaluacion = $_POST['id_sub_periodo_evaluacion'];
        $ap_nombre = trim($_POST['ap_nombre']);
        $ap_abreviatura = trim($_POST['ap_abreviatura']);
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        if ($this->aporteEvaluacionModelo->existeNombreAporte($ap_nombre, $id_sub_periodo_evaluacion)) {
            $_SESSION['mensaje'] = "Ya existe el nombre del aporte de evaluación en el sub periodo de evaluación seleccionado.";
            $_SESSION['tipo'] = "danger";
            $_SESSION['icono'] = "ban";
            redireccionar('/aportes_evaluacion/create');
        } else if ($this->aporteEvaluacionModelo->existeAbreviaturaAporte($ap_abreviatura, $id_sub_periodo_evaluacion)) {
            $_SESSION['mensaje'] = "Ya existe la abreviatura del aporte de evaluación en la base de datos.";
            $_SESSION['tipo'] = "danger";
            $_SESSION['icono'] = "ban";
            redireccionar('/aportes_evaluacion/create');
        } else {
            $datos = [
                'id_sub_periodo_evaluacion' => $id_sub_periodo_evaluacion,
                'id_tipo_aporte' => $_POST['id_tipo_aporte'],
                'ap_nombre' => strtoupper(trim($_POST['ap_nombre'])),
                'ap_abreviatura' => strtoupper(trim($_POST['ap_abreviatura'])),
                'ap_descripcion' => strtoupper(trim($_POST['ap_descripcion'])),
                'ap_ponderacion' => trim($_POST['ap_ponderacion']),
                'ap_fecha_apertura' => trim($_POST['ap_fecha_apertura']),
                'ap_fecha_cierre' => trim($_POST['ap_fecha_cierre']),
                'id_periodo_lectivo' => $id_periodo_lectivo
            ];
            try {
                $this->aporteEvaluacionModelo->insertarAporteEvaluacion($datos);
                // $_SESSION['mensaje_exito'] = "";
                $_SESSION['mensaje'] = "Aporte de Evaluación insertado exitosamente en la base de datos.";
                $_SESSION['tipo'] = "success";
                $_SESSION['icono'] = "check";
                redireccionar('/aportes_evaluacion');
            } catch (PDOException $ex) {
                $_SESSION['mensaje'] = "El Aporte de Evaluación no fue insertado exitosamente. Error: " . $ex->getMessage();
                $_SESSION['tipo'] = "danger";
                $_SESSION['icono'] = "ban";
                redireccionar('/aportes_evaluacion');
            }
        }
    }

    public function edit($id)
    {
        $aporte_evaluacion = $this->aporteEvaluacionModelo->obtenerAporteEvaluacion($id);
        $tipos_aporte = $this->tipoAporteModelo->obtenerTiposAporteEvaluacion();
        $periodos_evaluacion = $this->periodoEvaluacionModelo->obtenerPeriodosEvaluacion($_SESSION['id_periodo_lectivo']);
        $datos = [
            'titulo' => 'Aportes Evaluación Editar',
            'aporte_evaluacion' => $aporte_evaluacion,
            'tipos_aporte' => $tipos_aporte,
            'periodos_evaluacion' => $periodos_evaluacion,
            'nombreVista' => 'admin/aportes_evaluacion/edit.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id_aporte_evaluacion = $_POST['id_aporte_evaluacion'];
        $ap_nombre = trim($_POST['ap_nombre']);
        $ap_abreviatura = trim($_POST['ap_abreviatura']);
        $id_sub_periodo_evaluacion = $_POST['id_sub_periodo_evaluacion'];

        $aporteEvaluacionActual = $this->aporteEvaluacionModelo->obtenerAporteEvaluacion($id_aporte_evaluacion);

        if ($aporteEvaluacionActual->ap_nombre != $ap_nombre && $this->aporteEvaluacionModelo->existeNombreAporte($ap_nombre, $id_sub_periodo_evaluacion)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre del aporte de evaluación en la base de datos.";
            redireccionar('/aportes_evaluacion/edit/' . $id_aporte_evaluacion);
        } else if ($aporteEvaluacionActual->ap_abreviatura != $ap_abreviatura && $this->aporteEvaluacionModelo->existeAbreviaturaAporte($ap_abreviatura, $id_sub_periodo_evaluacion)) {
            $_SESSION['mensaje_error'] = "Ya existe la abreviatura del aporte de evaluación en la base de datos.";
            redireccionar('/aportes_evaluacion/edit/' . $id_aporte_evaluacion);
        } else {
            $datos = [
                'id_aporte_evaluacion' => $id_aporte_evaluacion,
                'id_sub_periodo_evaluacion' => $id_sub_periodo_evaluacion,
                'id_tipo_aporte' => $_POST['id_tipo_aporte'],
                'ap_nombre' => strtoupper(trim($_POST['ap_nombre'])),
                'ap_abreviatura' => strtoupper(trim($_POST['ap_abreviatura'])),
                'ap_descripcion' => strtoupper(trim($_POST['ap_descripcion'])),
                'ap_ponderacion' => trim($_POST['ap_ponderacion']),
                'ap_fecha_apertura' => trim($_POST['ap_fecha_apertura']),
                'ap_fecha_cierre' => trim($_POST['ap_fecha_cierre']),
            ];

            try {
                $this->aporteEvaluacionModelo->actualizarAporteEvaluacion($datos);
                $_SESSION['mensaje_exito'] = "Aporte de Evaluación actualizado exitosamente en la base de datos.";
                redireccionar('/aportes_evaluacion');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El Aporte de Evaluación no fue actualizado exitosamente. Error: " . $ex->getMessage();
                redireccionar('/aportes_evaluacion');
            }
        }
    }

    public function delete()
    {
        $id = $_POST['id'];
        try {
            $this->aporteEvaluacionModelo->eliminarAporteEvaluacion($id);
            $message = "Aporte de Evaluación eliminado exitosamente de la base de datos.";
            // redireccionar('/aportes_evaluacion');
            $response = [
                'title' => "¡Eliminado!",
                'text'  => $message,
                'icon'  => "success"
            ];
        } catch (PDOException $ex) {
            $message = "El Aporte de Evaluación no fue eliminado exitosamente. Error: " . $ex->getMessage();
            // redireccionar('/aportes_evaluacion');
            $response = [
                'title' => "¡Error!",
                'text'  => $message,
                'icon'  => "success"
            ];
        }
        echo json_encode($response);
    }
}
