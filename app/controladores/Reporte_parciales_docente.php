<?php
class PDF extends FPDF
{
    var $nombreParalelo = "";
    var $nombreInstitucion = "";
    var $nomNivelEducacion = "";
    var $logoInstitucion = "";
    var $nombrePeriodoLectivo = "";
    var $nombreAporteEvaluacion = "";
    var $id_periodo_evaluacion = "";
    var $nombreAsignatura = "";
    var $nombresInsumos;
    var $nombreRegimen;

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
        $title = 'REPORTE DEL ' . $this->nombreAporteEvaluacion;
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

        //Cabeceras de las columnas
        $this->SetFont('Arial', 'B', 9);
        $y = $this->GetY();
        $this->Cell(14, 8, utf8_decode("N°"), 1, 0, 'C');
        $this->Multicell(80, 4, utf8_decode("NÓMINA DE ESTUDIANTES") . "\n(apellidos y nombres)", 1, 'C');

        //Cabeceras de los insumos de evaluación
        $this->SetXY(104, $y);
        foreach ($this->nombresInsumos as $insumo) {
            $this->Cell(24, 8, $insumo->ru_abreviatura, 1, 0, 'C');
        }

        if (count($this->nombresInsumos) > 1) {
            $this->Cell(24, 8, "PROMEDIO", 1, 0, 'C');
        }

        $this->Ln();
    }

    //Pie de pagina
    function Footer()
    {
        //Posicion: a 1,5 cm del final
        $this->SetY(-15);
        //Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        //Numero de pagina
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }
}

class Reporte_parciales_docente extends Controlador
{
    private $db;
    private $cursoModelo;
    private $jornadaModelo;
    private $paraleloModelo;
    private $asignaturaModelo;
    private $institucionModelo;
    private $periodoLectivoModelo;
    private $aporteEvaluacionModelo;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->db = new Base;
        $this->cursoModelo = $this->modelo('Curso');
        $this->jornadaModelo = $this->modelo('Jornada');
        $this->paraleloModelo = $this->modelo('Paralelo');
        $this->asignaturaModelo = $this->modelo('Asignatura');
        $this->institucionModelo = $this->modelo('Institucion');
        $this->periodoLectivoModelo = $this->modelo('PeriodoLectivo');
        $this->aporteEvaluacionModelo = $this->modelo('Aporte_evaluacion');
    }

    public function reporte()
    {
        // Variables enviadas mediante POST
        $id_paralelo = $_POST["id_paralelo"];
        $id_asignatura = $_POST["id_asignatura"];
        $id_aporte_evaluacion = $_POST["id_aporte_evaluacion"];

        $id_usuario = $_SESSION["id_usuario"];
        $id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

        // Obtengo el tutor del grado/curso
        $this->db->query("SELECT us_shortname FROM sw_usuario u, sw_paralelo_tutor p WHERE u.id_usuario = p.id_usuario AND p.id_paralelo = $id_paralelo AND p.id_periodo_lectivo = $id_periodo_lectivo");
        $resultado = $this->db->registro();
        $nombreTutor = utf8_decode($resultado->us_shortname);

        // Obtengo el nombre del docente
        $this->db->query("SELECT us_shortname FROM sw_usuario WHERE id_usuario = $id_usuario");
        $resultado = $this->db->registro();
        $nombreDocente = utf8_decode($resultado->us_shortname);

        $nombreParalelo = utf8_decode($this->paraleloModelo->obtenerNombreParalelo($id_paralelo));

        // Aqui va el codigo para imprimir el nombre de la asignatura
        $this->db->query("SELECT as_nombre FROM sw_asignatura WHERE id_asignatura = $id_asignatura");
        $resultado = $this->db->registro();
        $nombreAsignatura = utf8_decode($resultado->as_nombre);

        $datosInstitucion = $this->institucionModelo->obtenerDatosInstitucion();

        $nombreInstitucion = utf8_decode($datosInstitucion->in_nombre);
        $nombreRegimen = utf8_decode($datosInstitucion->in_regimen);
        $direccionInstitucion = utf8_decode($datosInstitucion->in_direccion);
        $telefonoInstitucion = utf8_decode($datosInstitucion->in_telefono);
        $AMIEInstitucion = $datosInstitucion->in_amie;

        $in_logo = $datosInstitucion->in_logo == '' ? 'no-disponible.png' : $datosInstitucion->in_logo;
        $img_logo = RUTA_URL . '/public/uploads/' . $in_logo;

        $periodo_lectivo = $this->periodoLectivoModelo->obtenerPeriodoLectivo($id_periodo_lectivo);
        $nombrePeriodoLectivo = $periodo_lectivo->pe_fecha_inicio . " / " . $periodo_lectivo->pe_fecha_fin;

        $nombreAporteEvaluacion = $this->aporteEvaluacionModelo->obtenerNombreAporteEvaluacion($id_aporte_evaluacion);

        // Obtengo el recordset de nombres de insumos
        $nombresInsumos = $this->aporteEvaluacionModelo->obtenerNombresInsumos($id_aporte_evaluacion, $id_asignatura);

        //Creación del objeto de la clase heredada
        $pdf = new PDF('P');

        $pdf->logoInstitucion = $img_logo;
        $pdf->nombreRegimen = $nombreRegimen;
        $pdf->AMIEInstitucion = $AMIEInstitucion;
        $pdf->nombreAsignatura = $nombreAsignatura;
        $pdf->nombreInstitucion = $nombreInstitucion;
        $pdf->telefonoInstitucion = $telefonoInstitucion;
        $pdf->direccionInstitucion = $direccionInstitucion;

        $pdf->nombreParalelo = $nombreParalelo;
        $pdf->nombrePeriodoLectivo = $nombrePeriodoLectivo;
        $pdf->nombreAporteEvaluacion = $nombreAporteEvaluacion;

        $pdf->nombresInsumos = $nombresInsumos;

        $pdf->AliasNbPages();
        $pdf->SetTopMargin(4);

        $pdf->AddPage();

        $this->db->query("SELECT e.id_estudiante,
                                 es_apellidos, 
                                 es_nombres
                            FROM sw_estudiante e, 
                                 sw_estudiante_periodo_lectivo ep 
                           WHERE e.id_estudiante = ep.id_estudiante
                             AND ep.id_paralelo = $id_paralelo   
                             AND ep.id_periodo_lectivo = $id_periodo_lectivo 
                             AND activo = 1
                           ORDER BY es_apellidos, es_nombres");
        $registros = $this->db->registros();
        $contador = 0;
        $pdf->SetFont('Times', '', 9);
        foreach ($registros as $row) {
            $contador++;
            if ($contador % 32 == 0) $pdf->AddPage();
            $id_estudiante = $row->id_estudiante;
            $nombreEstudiante = $row->es_apellidos . " " . $row->es_nombres;
            $pdf->Cell(14, 6, $contador, 1, 0, 'C');
            if (strlen($nombreEstudiante) > 40) $pdf->SetFont('Times', '', 8);
            $pdf->Cell(80, 6, utf8_decode($nombreEstudiante), 1, 0, 'L');
            $pdf->SetFont('Times', '', 9);
            //Obtención de las calificaciones del estudiante
            $this->db->query("SELECT * FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion ORDER BY id_rubrica_evaluacion ASC");
            $rubricas = $this->db->registros();
            $num_registros = $this->db->rowCount();
            $contInsumo = 0; $suma = 0;
            foreach ($rubricas as $rubrica) {
                $id_rubrica_evaluacion = $rubrica->id_rubrica_evaluacion;
                $this->db->query("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_rubrica_personalizada = $id_rubrica_evaluacion AND id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura");
                $registro = $this->db->registro();
                if (!empty($registro) > 0) {
                    $calificacion = $registro->re_calificacion;
                    $suma += $calificacion;
                } else {
                    $calificacion = " ";
                }
                $pdf->Cell(24, 6, $calificacion, 1, 0, 'C');
                $contInsumo++;
            }
            if ($num_registros > 1) {
                $promedio = $suma / $contInsumo;
                $promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
                $pdf->Cell(24, 6, $promedio, 1, 0, 'C');
            }
            $pdf->Ln();
        }

        //Firmas de la Autoridad y/o del Secretario (a)
        $y = $pdf->GetY() + 10;
        $pdf->Line(24, $y, 94, $y);
        $pdf->Line(114, $y, 184, $y);
        $pdf->SetY($y + 1);
        $pdf->Cell(14, 6, "", 0, 0, 'C');
        $pdf->Cell(70, 6, $nombreDocente, 0, 0, 'C');
        $pdf->Cell(20, 6, "", 0, 0, 'C');
        $pdf->Cell(70, 6, $nombreTutor, 0, 0, 'C');
        $pdf->Ln(5);
        $pdf->Cell(14, 6, "", 0, 0, 'C');
        $pdf->Cell(70, 6, "DOCENTE", 0, 0, 'C');
        $pdf->Cell(20, 6, "", 0, 0, 'C');
        $pdf->Cell(70, 6, "TUTOR", 0, 0, 'C');

        $pdf->Output();
    }
}
