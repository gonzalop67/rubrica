<?php
class Insumos_evaluacion extends Controlador
{
    private $tipoAsignaturaModelo;
    private $aporteEvaluacionModelo;
    private $insumoEvaluacionModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->tipoAsignaturaModelo = $this->modelo('Tipo_asignatura');
        $this->aporteEvaluacionModelo = $this->modelo('Aporte_evaluacion');
        $this->insumoEvaluacionModelo = $this->modelo('Insumo_evaluacion');
    }

    public function index()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $insumos_evaluacion = $this->insumoEvaluacionModelo->obtenerInsumosEvaluacion($id_periodo_lectivo);
        $datos = [
            'titulo' => 'Insumos de Evaluación',
            'insumos_evaluacion' => $insumos_evaluacion,
            'nombreVista' => 'admin/insumos_evaluacion/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function create()
    {
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $aportes_evaluacion = $this->aporteEvaluacionModelo->obtenerAportesEvaluacion($id_periodo_lectivo);
        $tipos_asignatura = $this->tipoAsignaturaModelo->obtenerTiposAsignatura();
        $datos = [
            'titulo' => 'Insumos Evaluación Crear',
            'aportes_evaluacion' => $aportes_evaluacion,
            'tipos_asignatura' => $tipos_asignatura,
            'nombreVista' => 'admin/insumos_evaluacion/create.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function insert()
    {
        $id_aporte_evaluacion = $_POST['id_aporte_evaluacion'];
        $ru_nombre = trim($_POST['ru_nombre']);
        $ru_abreviatura = trim($_POST['ru_abreviatura']);

        if ($this->insumoEvaluacionModelo->existeNombreInsumo($ru_nombre, $id_aporte_evaluacion)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre del insumo de evaluación en la base de datos.";
            redireccionar('/insumos_evaluacion/create');
        } else if ($this->insumoEvaluacionModelo->existeAbreviaturaInsumo($ru_abreviatura, $id_aporte_evaluacion)) {
                $_SESSION['mensaje_error'] = "Ya existe la abreviatura del insumo de evaluación en la base de datos.";
                redireccionar('/insumos_evaluacion/create');
        } else {
            $datos = [
                'id_aporte_evaluacion' => $id_aporte_evaluacion,
                'id_tipo_asignatura' => $_POST['id_tipo_asignatura'],
                'ru_nombre' => $_POST['ru_nombre'],
                'ru_abreviatura' => $_POST['ru_abreviatura']
            ];
            try {
                $this->insumoEvaluacionModelo->insertarInsumoEvaluacion($datos);
                $_SESSION['mensaje_exito'] = "Insumo de Evaluación insertado exitosamente en la base de datos.";
                redireccionar('/insumos_evaluacion');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El Insumo de Evaluación no fue insertado exitosamente. Error: " . $ex->getMessage();
                redireccionar('/insumos_evaluacion');
            }
        }
    }

    public function edit($id)
    {
        $insumo_evaluacion = $this->insumoEvaluacionModelo->obtenerInsumoEvaluacion($id);
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
        $aportes_evaluacion = $this->aporteEvaluacionModelo->obtenerAportesEvaluacion($id_periodo_lectivo);
        $tipos_asignatura = $this->tipoAsignaturaModelo->obtenerTiposAsignatura();
        $datos = [
            'titulo' => 'Insumos Evaluación Editar',
            'insumo_evaluacion' => $insumo_evaluacion,
            'aportes_evaluacion' => $aportes_evaluacion,
            'tipos_asignatura' => $tipos_asignatura,
            'nombreVista' => 'admin/insumos_evaluacion/edit.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function update()
    {
        $id_rubrica_evaluacion = $_POST['id_rubrica_evaluacion'];
        $id_aporte_evaluacion = $_POST['id_aporte_evaluacion'];
        $ru_nombre = trim($_POST['ru_nombre']);
        $ru_abreviatura = trim($_POST['ru_abreviatura']);

        $insumoEvaluacionActual = $this->insumoEvaluacionModelo->obtenerInsumoEvaluacion($id_rubrica_evaluacion);

        if ($insumoEvaluacionActual->ru_nombre != $ru_nombre && $this->insumoEvaluacionModelo->existeNombreInsumo($ru_nombre, $id_aporte_evaluacion)) {
            $_SESSION['mensaje_error'] = "Ya existe el nombre del insumo de evaluación en la base de datos.";
            redireccionar('/insumos_evaluacion/edit/' . $id_rubrica_evaluacion);
        } else if ($insumoEvaluacionActual->ru_abreviatura != $ru_abreviatura && $this->insumoEvaluacionModelo->existeAbreviaturaInsumo($ru_abreviatura, $id_aporte_evaluacion)) {
                $_SESSION['mensaje_error'] = "Ya existe la abreviatura del insumo de evaluación en la base de datos.";
                redireccionar('/insumos_evaluacion/edit/' . $id_rubrica_evaluacion);
        } else {
            $datos = [
                'id_rubrica_evaluacion' => $id_rubrica_evaluacion,
                'id_aporte_evaluacion' => $id_aporte_evaluacion,
                'id_tipo_asignatura' => $_POST['id_tipo_asignatura'],
                'ru_nombre' => $_POST['ru_nombre'],
                'ru_abreviatura' => $_POST['ru_abreviatura']
            ];
            try {
                $this->insumoEvaluacionModelo->actualizarInsumoEvaluacion($datos);
                $_SESSION['mensaje_exito'] = "Insumo de Evaluación actualizado exitosamente en la base de datos.";
                redireccionar('/insumos_evaluacion');
            } catch (PDOException $ex) {
                $_SESSION['mensaje_error'] = "El Insumo de Evaluación no fue actualizado exitosamente. Error: " . $ex->getMessage();
                redireccionar('/insumos_evaluacion');
            }
        }
    }

    public function delete($id)
    {
        try {
            $this->insumoEvaluacionModelo->eliminarInsumoEvaluacion($id);
            $_SESSION['mensaje_exito'] = "Insumo de Evaluación eliminado exitosamente de la base de datos.";
            redireccionar('/insumos_evaluacion');
        } catch (PDOException $ex) {
            $_SESSION['mensaje_error'] = "El Insumo de Evaluación no fue eliminado exitosamente. Error: " . $ex->getMessage();
            redireccionar('/insumos_evaluacion');
        }
    }
}