<?php
class PDF extends FPDF
{
    var $nombresPeriodos;
    var $abreviaturaPeriodo;
    var $nombreParalelo = "";
    var $logoInstitucion = "";
    var $AMIEInstitucion = "";
    var $nombreAsignatura = "";
    var $nombreInstitucion = "";
    var $telefonoInstitucion = "";
    var $direccionInstitucion = "";
    var $nombrePeriodoLectivo = "";
    var $nombrePeriodoEvaluacion = "";

    //Cabecera de página
    function Header()
    {
        //Logo Izquierda
        $this->Image(RUTA_URL . '/public/img/ministerio.png', 10, 18, 33);

        //Logo Derecha
        $this->Image($this->logoInstitucion, 210 - 40, 5, 23);

        //Nombre de la IE
        $this->SetFont('Times', 'B', 14);
        $title = $this->nombreInstitucion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);

        //Direccion de la IE
        $this->SetFont('Arial', 'I', 12);
        $title = $this->direccionInstitucion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);

        //Telefono de la IE
        $this->SetFont('Arial', 'I', 11);
        $title = utf8_decode("Teléfono: ") . $this->telefonoInstitucion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);

        //AMIE de la IE
        $this->SetFont('Arial', 'I', 11);
        $title = "AMIE: " . $this->AMIEInstitucion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(10);

        //Linea de division
        $this->Line(10, 30, 210 - 10, 30); // 20mm from each edge

        //Titulo del Reporte
        $this->SetFont('Times', 'B', 14);
        $title = 'REPORTE DEL ' . $this->nombrePeriodoEvaluacion;
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);

        //Año Lectivo
        $this->SetFont('Times', '', 12);
        $title = utf8_decode("PERIODO LECTIVO: " . $this->nombrePeriodoLectivo);
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(5);

        //Nombre de la Asignatura
        $this->SetFont('Times', '', 12);
        $title = utf8_decode($this->nombreAsignatura);
        $w = $this->GetStringWidth($title);
        $this->SetX((210 - $w) / 2);
        $this->Cell($w, 10, $title, 0, 0, 'C');
        $this->Ln(7);

        //Nombre del Curso, Paralelo y Jornada
        $this->SetFont('Arial', 'B', 10);
        $this->SetX(15);
        $this->Cell(60, 10, $this->nombreParalelo, 0, 0, 'L');
        $this->Ln(7);

        // Impresion de los titulos de cabecera
        $this->SetFont('Arial', '', 8);
        $this->Cell(8, 6, "Nro.", 1, 0, 'C');
        $this->Cell(70, 6, "NOMINA", 1, 0, 'C');

        // Aqui imprimo las cabeceras de cada periodo de evaluacion
        foreach ($this->nombresPeriodos as $periodo) {
            $this->Cell(11, 6, $periodo->pe_abreviatura, 1, 0, 'C');
        }

        $this->Cell(11, 6, "SUMA", 1, 0, 'C');
        $this->Cell(11, 6, "PROM", 1, 0, 'C');

        // Aqui imprimo las abreviaturas de los periodos de evaluacion supletorios
        foreach ($this->abreviaturaPeriodo as $periodo) {
            $this->Cell(11, 6, $periodo->pe_abreviatura, 1, 0, 'C');
        }

        $this->Cell(11, 6, "P.F.", 1, 0, 'C');
        $this->Cell(24, 6, "OBSERVACION", 1, 0, 'C');
    }
}

class Reporte_supletorios_docente extends Controlador
{
    private $db;
    private $paraleloModelo;
    private $funcionesModelo;
    private $institucionModelo;
    private $periodoLectivoModelo;
    private $periodoEvaluacionModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->db = new Base;
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->funcionesModelo = $this->modelo('Funciones');
        $this->institucionModelo = $this->modelo('Institucion');
        $this->periodoLectivoModelo = $this->modelo('PeriodoLectivo');
        $this->periodoEvaluacionModelo = $this->modelo('Periodo_evaluacion');
    }

    public function reporte()
    {
        // Variables enviadas mediante POST
        $id_paralelo = $_POST["id_paralelo"];
        $id_asignatura = $_POST["id_asignatura"];
        $id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];

        $id_usuario = $_SESSION["id_usuario"];
        $id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

        // Obtengo el id_tipo_periodo del periodo de evaluacion supletorio
        $this->db->query("SELECT id_tipo_periodo FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion");
        $resultado = $this->db->registro();
        $id_tipo_periodo = $resultado->id_tipo_periodo;

        // Obtengo el tutor del grado/curso
        $this->db->query("SELECT us_shortname FROM sw_usuario u, sw_paralelo_tutor p WHERE u.id_usuario = p.id_usuario AND p.id_paralelo = $id_paralelo AND p.id_periodo_lectivo = $id_periodo_lectivo");
        $resultado = $this->db->registro();
        $nombreTutor = $resultado->us_shortname;

        // Obtengo el nombre del docente
        $this->db->query("SELECT us_shortname FROM sw_usuario WHERE id_usuario = $id_usuario");
        $resultado = $this->db->registro();
        $nombreDocente = $resultado->us_shortname;

        $nombreParalelo = utf8_decode($this->paraleloModelo->obtenerNombreParalelo($id_paralelo));

        // Aqui va el codigo para imprimir el nombre de la asignatura
        $this->db->query("SELECT as_nombre FROM sw_asignatura WHERE id_asignatura = $id_asignatura");
        $resultado = $this->db->registro();
        $nombreAsignatura = utf8_decode($resultado->as_nombre);

        // Aqui va el codigo para imprimir el nombre del periodo de evaluacion
        $this->db->query("SELECT pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion");
        $resultado = $this->db->registro();
        $nombrePeriodoEvaluacion = utf8_decode($resultado->pe_nombre);

        $datosInstitucion = $this->institucionModelo->obtenerDatosInstitucion();

        $nombreInstitucion = utf8_decode($datosInstitucion->in_nombre);
        // $nombreRegimen = utf8_decode($datosInstitucion->in_regimen);
        $direccionInstitucion = utf8_decode($datosInstitucion->in_direccion);
        $telefonoInstitucion = utf8_decode($datosInstitucion->in_telefono);
        $AMIEInstitucion = $datosInstitucion->in_amie;

        $in_logo = $datosInstitucion->in_logo == '' ? 'no-disponible.png' : $datosInstitucion->in_logo;
        $img_logo = RUTA_URL . '/public/uploads/' . $in_logo;

        $periodo_lectivo = $this->periodoLectivoModelo->obtenerPeriodoLectivo($id_periodo_lectivo);
        $nombrePeriodoLectivo = $periodo_lectivo->pe_fecha_inicio . " / " . $periodo_lectivo->pe_fecha_fin;

        // Obtengo las abreviaturas de los nombres de los periodos de evaluacion
        $nombresPeriodos = $this->periodoEvaluacionModelo->obtenerAbreviaturasPeriodos($id_periodo_lectivo);

        // Aqui va el codigo para imprimir el nombre del periodo de evaluacion
        $this->db->query("SELECT pe_abreviatura FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo > 1");
        $resultados = $this->db->registros();
        $abreviaturaPeriodoEvaluacion = $resultados;

        //Creación del objeto de la clase heredada
        $pdf = new PDF('P');

        $pdf->logoInstitucion = $img_logo;
        $pdf->AMIEInstitucion = $AMIEInstitucion;
        $pdf->nombreInstitucion = $nombreInstitucion;
        $pdf->telefonoInstitucion = $telefonoInstitucion;
        $pdf->direccionInstitucion = $direccionInstitucion;

        $pdf->nombreParalelo = $nombreParalelo;
        $pdf->nombreAsignatura = $nombreAsignatura;
        $pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;
        $pdf->nombrePeriodoEvaluacion = $nombrePeriodoEvaluacion;

        $pdf->nombresPeriodos = $nombresPeriodos;
        $pdf->abreviaturaPeriodo = $abreviaturaPeriodoEvaluacion;

        $pdf->SetTopMargin(4);
        $pdf->AddPage();

        $pdf->Ln();
        // Aqui imprimo las cabeceras de cada asignatura
        $this->db->query("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_supletorios_temp s WHERE e.id_estudiante = s.id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_tipo_periodo = $id_tipo_periodo ORDER BY es_apellidos, es_nombres ASC");
        $registros = $this->db->registros();

        $contador = 0;
        foreach ($registros as $registro) {
            $contador++;
            $id_estudiante = $registro->id_estudiante;

            $pdf->Cell(8, 6, $contador, 1, 0, 'C');
            $nombre_completo = utf8_decode($registro->es_apellidos) . " " . utf8_decode($registro->es_nombres);
            $pdf->Cell(70, 6, $nombre_completo, 1, 0, 'L');

            $this->db->query("SELECT id_periodo_evaluacion 
                                FROM sw_periodo_evaluacion 
                               WHERE id_periodo_lectivo = $id_periodo_lectivo 
                                 AND id_tipo_periodo = 1");

            $periodos_evaluacion = $this->db->registros();

            $suma_periodos = 0;
            $contador_periodos = 0;
            foreach ($periodos_evaluacion as $periodo_evaluacion) {
                $contador_periodos++;
                $id_periodo_evaluacion = $periodo_evaluacion->id_periodo_evaluacion;

                // Aqui voy a llamar a la funcion almacenada para obtener la calificacion quimestral correspondiente
                $qry = "SELECT calcular_promedio_quimestre($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
                $this->db->query($qry);
                $result = $this->db->registro();
                $calificacion_quimestral = $result->promedio;

                $suma_periodos += $calificacion_quimestral;

                $calificacion_quimestral = floor($calificacion_quimestral * 100) / 100;
                $pdf->Cell(11, 6, number_format($calificacion_quimestral, 2), 1, 0, 'C');
            }

            // Calculo la suma y el promedio de los dos quimestres
            $promedio_periodos = $suma_periodos / $contador_periodos;
            $promedio_periodos = floor($promedio_periodos * 100) / 100;

            $pdf->Cell(11, 6, number_format($suma_periodos, 2), 1, 0, 'C');
            $pdf->Cell(11, 6, number_format($promedio_periodos, 2), 1, 0, 'C');

            $promedio_final = $promedio_periodos;
            $supletorio = " ";
            $remedial = " ";
            $de_gracia = " ";
            $observacion = " ";

            if ($promedio_periodos >= 7 && $promedio_periodos <= 10) {
                $observacion = "APRUEBA";
            } else if ($promedio_periodos >= 5 && $promedio_periodos < 7) {
                $observacion = "SUPLETORIO";
            }
            if ($this->funcionesModelo->existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo)) {
                $supletorio = $this->funcionesModelo->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);
                if ($supletorio >= 7) {
                    $promedio_final = 7;
                    $observacion = "APRUEBA";
                } else {
                    if ($this->funcionesModelo->existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo)) {
                        $remedial = $this->funcionesModelo->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
                        if ($remedial >= 7) {
                            $promedio_final = 7;
                            $observacion = "APRUEBA";
                        } else {
                            $observacion = "NO APRUEBA";
                        }
                    } else {
                        $observacion = "REMEDIAL";
                    }
                }
            } else if ($promedio_periodos > 0 && $promedio_periodos < 5) {
                $observacion = "REMEDIAL";
                if ($this->funcionesModelo->existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo)) {
                    $remedial = $this->funcionesModelo->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
                    if ($remedial >= 7) {
                        $promedio_final = 7;
                        $observacion = "APRUEBA";
                    } else {
                        $observacion = "NO APRUEBA";
                    }
                }
            } else {
                $observacion = "SIN NOTAS";
            }

			//Aquí va lo del examen de gracia.........
			//........................................

            $pdf->Cell(11, 6, $supletorio, 1, 0, 'C');
            $pdf->Cell(11, 6, $remedial, 1, 0, 'C');
            $pdf->Cell(11, 6, $de_gracia, 1, 0, 'C');
            $pdf->Cell(11, 6, number_format($promedio_final, 2), 1, 0, 'C');
            $pdf->Cell(24, 6, $observacion, 1, 0, 'C');

            $pdf->Ln();
        }

        $pdf->Output();
    }
}
