<?php
// 1. Inclusión de las librerías requeridas
require_once('../fpdf186/fpdf.php');
require_once('../scripts/clases/class.mysql.php');
require_once('../scripts/clases/class.institucion.php');
require_once('../scripts/clases/class.asignaturas.php');
require_once('../scripts/clases/class.paralelos.php');
require_once('../scripts/clases/class.usuarios.php');
require_once('../scripts/clases/class.periodos_lectivos.php');

// 2. Control de Sesión activa
session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"] ?? 0;
$id_usuario         = $_SESSION["id_usuario"] ?? 0;

// 3. Capturar y desinfectar variables POST usando tu clase MySQL
$db = new MySQL();

$id_asignatura      = $db->filtrar($_POST["id_asignatura"] ?? 0);
$id_paralelo        = $db->filtrar($_POST["id_paralelo"] ?? 0);
$id_horario_detalle = $db->filtrar($_POST["id_horario_detalle"] ?? 0);
$ae_fecha           = $db->filtrar($_POST["ae_fecha"] ?? '');

// 4. Instanciar clases secundarias para textos informativos de la cabecera
$institucion = new institucion();
$nombreInstitucion = utf8_decode($institucion->obtenerNombreInstitucion());

$usuario = new usuarios();
$nombreUsuario = utf8_decode($usuario->obtenerNombreUsuario($id_usuario));

$asignatura = new asignaturas();
$nombreAsignatura = utf8_decode($asignatura->obtenerNombreAsignatura($id_asignatura));

$paralelo = new paralelos();
$nombreParalelo = utf8_decode($paralelo->obtenerNombreParalelo($id_paralelo));

$periodo_lectivo = new periodos_lectivos();
$nombrePeriodoLectivo = utf8_decode($periodo_lectivo->obtenerNombrePeriodoLectivo($id_periodo_lectivo));

// Obtener el nombre y rangos de tiempo de la hora dinámica directamente por SQL
$sqlHoraClase = "SELECT nombre, hora_inicio, hora_fin FROM sw_horario_detalles WHERE id_horario_detalle = '$id_horario_detalle' LIMIT 1";
$resHoraClase = $db->consulta($sqlHoraClase);
$nombreHoraClase = "N/A";
if ($db->num_rows($resHoraClase) > 0) {
	$rHora = $db->fetch_assoc($resHoraClase);
	$nombreHoraClase = $rHora['nombre'] . " (" . substr($rHora['hora_inicio'], 0, 5) . " - " . substr($rHora['hora_fin'], 0, 5) . ")";
}

// 5. INICIALIZAR GENERACIÓN DEL DOCUMENTO PDF (FPDF)
$pdf = new FPDF();
$pdf->AddPage();

// CORRECCIÓN: Definimos explícitamente Arial en el catálogo interno de FPDF
// Esto blinda el script contra el error 'Undefined font helvetica B' o 'arial B'
$pdf->SetFont('Arial', '', 10);

// Título 1: Institución
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, $nombreInstitucion, 0, 1, 'C');

// Título 2: Fecha
$pdf->SetFont('Arial', 'B', 12);
$title2 = "REPORTE DE ASISTENCIA DIARIA: " . $ae_fecha;
$pdf->Cell(0, 7, $title2, 0, 1, 'C');

// Título 3: Período Lectivo
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, $nombrePeriodoLectivo, 0, 1, 'C');
$pdf->Ln(4);

// Bloque Informativo de Metadatos
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(35, 6, "ASIGNATURA:", 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, $nombreAsignatura, 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(35, 6, "CURSO / PARALELO:", 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, $nombreParalelo, 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(35, 6, "BLOQUE HORARIO:", 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, $nombreHoraClase, 0, 1, 'L');
$pdf->Ln(6);

// ====================================================================
// CONSTRUCCIÓN DE LA LEYENDA DINÁMICA (Tipos de Inasistencia)
// ====================================================================
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, "Glosario de Novedades de Asistencia:", 0, 1, 'L');
$pdf->SetFont('Arial', '', 9);

$mensajeGlosario = "";
$sqlTipos = "SELECT id_tipo_inasistencia, ti_sigla, ti_nombre FROM sw_tipo_inasistencia ORDER BY id_tipo_inasistencia ASC";
$resTipos = $db->consulta($sqlTipos);

while ($tipo = $db->fetch_assoc($resTipos)) {
	$mensajeGlosario .= " [" . $tipo["ti_sigla"] . "]: " . $tipo["ti_nombre"] . "   ";
}
$pdf->MultiCell(0, 5, utf8_decode($mensajeGlosario), 0, 'L');
$pdf->Ln(4);

// ====================================================================
// DIBUJAR CABECERA DE LA TABLA DE CALIFICACIONES/ASISTENCIA
// ====================================================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(12, 7, "Nro.", 1, 0, 'C');
$pdf->Cell(12, 7, "ID", 1, 0, 'C');
$pdf->Cell(96, 7, "NOMINA DEL ESTUDIANTE", 1, 0, 'C');

$resTiposColumnas = $db->consulta($sqlTipos);
$totalTipos = $db->num_rows($resTiposColumnas);

$listaIdsTipos = array();
while ($tipoCol = $db->fetch_assoc($resTiposColumnas)) {
	$listaIdsTipos[] = $tipoCol;
	$pdf->Cell(14, 7, $tipoCol["ti_sigla"], 1, 0, 'C');
}
$pdf->Ln();

// ====================================================================
// DIBUJAR CUERPO DE LA TABLA (Estudiantes y su estado)
// ====================================================================
$pdf->SetFont('Arial', '', 10);

$sqlNomina = "SELECT m.id_estudiante, e.es_apellidos, e.es_nombres 
              FROM sw_estudiante_periodo_lectivo m
              INNER JOIN sw_estudiante e ON m.id_estudiante = e.id_estudiante 
              WHERE m.id_paralelo = '$id_paralelo' 
                AND m.activo = 1 
              ORDER BY e.es_apellidos ASC, e.es_nombres ASC";

$resNomina = $db->consulta($sqlNomina);
$contador = 0;

if ($db->num_rows($resNomina) > 0) {
	while ($alumno = $db->fetch_assoc($resNomina)) {
		$contador++;
		$id_est = $alumno["id_estudiante"];

		$pdf->Cell(12, 6, $contador, 1, 0, 'C');
		$pdf->Cell(12, 6, $id_est, 1, 0, 'C');

		$nombreCompleto = utf8_decode($alumno["es_apellidos"]) . " " . utf8_decode($alumno["es_nombres"]);
		$pdf->Cell(96, 6, $nombreCompleto, 1, 0, 'L');

		// Consulta unificada en singular apuntando a tu tabla real
		$sqlAsistEst = "SELECT id_tipo_inasistencia FROM sw_asistencia_estudiante 
                        WHERE id_estudiante = '$id_est' 
                          AND id_paralelo = '$id_paralelo' 
                          AND id_asignatura = '$id_asignatura' 
                          AND id_horario_detalle = '$id_horario_detalle' 
                          AND ae_fecha = '$ae_fecha' 
                        ORDER BY id_asistencia_estudiante DESC 
                        LIMIT 1";

		$resAsistEst = $db->consulta($sqlAsistEst);

		$idTipoGuardado = 1;
		if ($db->num_rows($resAsistEst) > 0) {
			$rAsist = $db->fetch_assoc($resAsistEst);
			$idTipoGuardado = intval($rAsist['id_tipo_inasistencia']);
		}

		// Convertimos el ID guardado del alumno a un entero limpio
		$idTipoGuardado = (int)$idTipoGuardado;

		// Recorremos las columnas dinámicas creadas en la cabecera
		foreach ($listaIdsTipos as $tipoColumna) {
			// CORREGIDO: Forzamos (int) en ambos lados para ganarle al tipado de PHP
			if ((int)$tipoColumna['id_tipo_inasistencia'] === $idTipoGuardado) {
				$pdf->Cell(14, 6, '*', 1, 0, 'C');
			} else {
				$pdf->Cell(14, 6, ' ', 1, 0, 'C');
			}
		}
		$pdf->Ln();
	}
} else {
	$pdf->Cell(120 + ($totalTipos * 14), 7, "No se encontraron alumnos registrados.", 1, 1, 'C');
}

// 6. PIE DE PÁGINA: Firmas de validación de asistencia
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(15, 6, "Prof.: ", 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(105, 6, $nombreUsuario, 0, 0, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "f.) ___________________________", 0, 1, 'L');

// 7. Renderizar salida PDF nativa al navegador
$pdf->Output();
