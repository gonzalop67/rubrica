<?php
class Periodos_lectivos extends Controlador
{
    private $modalidadModelo;
    private $subPeriodoModelo;
    private $periodoNivelModelo;
    private $nivelEducacionModelo;
    private $periodoLectivoModelo;
    private $subperiodoPeriodoModel;
    private $quienInsertaComportamiento;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->modalidadModelo = $this->modelo('Modalidad');
        $this->periodoNivelModelo = $this->modelo('Periodo_nivel');
        $this->periodoLectivoModelo = $this->modelo('PeriodoLectivo');
        $this->nivelEducacionModelo = $this->modelo('Tipo_educacion');
        $this->subPeriodoModelo = $this->modelo('Periodo_evaluacion');
        $this->subperiodoPeriodoModel = $this->modelo('Subperiodo_periodo');
        $this->quienInsertaComportamiento = $this->modelo('Quien_inserta_comportamiento');
    }

    public function index()
    {
        $periodos_lectivos = $this->periodoLectivoModelo->obtenerPeriodosLectivos();
        $datos = [
            'titulo' => 'Periodos Lectivos',
            'periodos_lectivos' => $periodos_lectivos,
            'nombreVista' => 'admin/periodos_lectivos/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $modalidades = $this->modalidadModelo->obtenerModalidades();
        $niveles_educacion = $this->nivelEducacionModelo->obtenerNivelesEducacion();
        $quien_inserta_comportamiento = $this->quienInsertaComportamiento->obtenerTodos();
        $sub_periodos_evaluacion = $this->subPeriodoModelo->obtenerSubPeriodosEvaluacion();
        $datos = [
            'titulo' => 'Crear Periodo Lectivo',
            'modalidades' => $modalidades,
            'niveles_educacion' => $niveles_educacion,
            'sub_periodos_evaluacion' => $sub_periodos_evaluacion,
            'quien_inserta_comportamiento' => $quien_inserta_comportamiento,
            'nombreVista' => 'admin/periodos_lectivos/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function store()
    {
        // Comprobar que se han pasado los niveles de educación a asociar al nuevo periodo lectivo
        // die(var_dump($_POST));
        // $niveles = $_POST['niveles'];

        $datos = [
            'id_modalidad' => $_POST['id_modalidad'],
            'pe_anio_inicio' => trim($_POST['pe_anio_inicio']),
            'pe_anio_fin' => trim($_POST['pe_anio_fin']),
            'pe_fecha_inicio' => trim($_POST['pe_fecha_inicio']),
            'pe_fecha_fin' => trim($_POST['pe_fecha_fin']),
            'pe_nota_minima' => trim($_POST['pe_nota_minima']),
            'pe_nota_aprobacion' => trim($_POST['pe_nota_aprobacion']),
            'pe_nota_aprobacion' => trim($_POST['pe_nota_aprobacion']),
            'quien_inserta_comp_id' => trim($_POST['quien_inserta_comp_id']),
            'niveles' => $_POST['niveles'],
            'sub_periodos' => $_POST['sub_periodos']
        ];

        // die(var_dump($_POST));

        try {
            $this->periodoLectivoModelo->insertarPeriodoLectivo($datos);
            $_SESSION['mensaje_exito'] = "Periodo Lectivo insertado exitosamente en la base de datos.";
            redireccionar('/periodos_lectivos');
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "El Periodo Lectivo no fue insertado exitosamente. Error: " . $ex->getMessage();
            redireccionar('/periodos_lectivos');
        }
    }

    public function edit($id)
    {
        $modalidades = $this->modalidadModelo->obtenerModalidades();
        $niveles_periodo = $this->periodoNivelModelo->obtenerNivelesPeriodo($id);
        $periodo_lectivo = $this->periodoLectivoModelo->obtenerPeriodoLectivo($id);
        $niveles_educacion = $this->nivelEducacionModelo->obtenerNivelesEducacion();
        $quien_inserta_comportamiento = $this->quienInsertaComportamiento->obtenerTodos();
        $sub_periodos_evaluacion = $this->subPeriodoModelo->obtenerSubPeriodosEvaluacion();
        $sub_periodos_periodo = $this->subperiodoPeriodoModel->obtenerSubPeriodosPeriodo($id);

        $datos = [
            'modalidades' => $modalidades,
            'titulo' => 'Editar Periodo Lectivo',
            'niveles_periodo' => $niveles_periodo,
            'periodo_lectivo' => $periodo_lectivo,
            'niveles_educacion' => $niveles_educacion,
            'nombreVista' => 'admin/periodos_lectivos/edit.php',
            'sub_periodos_evaluacion' => $sub_periodos_evaluacion,
            'sub_periodos_periodo' => $sub_periodos_periodo,
            'quien_inserta_comportamiento' => $quien_inserta_comportamiento
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $datos = [
            'id_periodo_lectivo' => $_POST['id_periodo_lectivo'],
            'pe_anio_inicio' => trim($_POST['pe_anio_inicio']),
            'pe_anio_fin' => trim($_POST['pe_anio_fin']),
            'pe_fecha_inicio' => trim($_POST['pe_fecha_inicio']),
            'pe_fecha_fin' => trim($_POST['pe_fecha_fin']),
            'pe_nota_minima' => trim($_POST['pe_nota_minima']),
            'pe_nota_aprobacion' => trim($_POST['pe_nota_aprobacion']),
            'quien_inserta_comp_id' => trim($_POST['quien_inserta_comp_id']),
            'niveles' => $_POST['niveles'],
            'sub_periodos' => $_POST['sub_periodos']
        ];

        // print_r("<pre>");
        // print_r($datos);
        // print_r("</pre>");
        // die();

        try {
            $this->periodoLectivoModelo->actualizarPeriodoLectivo($datos);
            // Actualizar los niveles de educación asociados
            $this->periodoNivelModelo->eliminarNivelesPeriodoLectivo($_POST['id_periodo_lectivo']);
            $array_niveles = $_POST["niveles"];
            for ($i=0; $i < count($array_niveles); $i++) { 
                $this->periodoNivelModelo->insertarNivelPeriodo($_POST['id_periodo_lectivo'], $array_niveles[$i]);
            }
            // Actualizar los subperiodos de evaluación asociados
            $this->subperiodoPeriodoModel->eliminarSubPeriodosPeriodoLectivo($_POST['id_periodo_lectivo']);
            $array_sub_periodos = $_POST["sub_periodos"];
            for ($i=0; $i < count($array_sub_periodos); $i++) { 
                $this->subperiodoPeriodoModel->insertarSubPeriodoPeriodo($_POST['id_periodo_lectivo'], $array_sub_periodos[$i]);
            }
            // Mensaje de salida
            $_SESSION['mensaje_exito'] = "Periodo Lectivo actualizado exitosamente en la base de datos.";
            $msg = $_SESSION['mensaje_exito'];
            $ok = true;
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "El Periodo Lectivo no fue actualizado exitosamente. Error: " . $ex->getMessage();
            $msg = $_SESSION['mensaje_error'];
            $ok = false;
        }

        $data = [
            'ok' => $ok,
            'msg' => $msg
        ];

        echo json_encode($data);
    }

    public function paginar()
    {
        $paginaActual = $_POST['partida'];
        $id_modalidad = $_POST['id_modalidad'];

        echo $this->periodoLectivoModelo->paginacion($paginaActual, $id_modalidad);
    }
}
