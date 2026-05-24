<?php
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

// Recibir parámetros enviado mediante POST
$niveles = $_POST['niveles'];
$sub_periodos = $_POST['sub_periodos'];

$qry = "UPDATE sw_periodo_lectivo SET pe_prefijo = '$_POST[pe_prefijo]', pe_anio_inicio = $_POST[pe_anio_inicio], pe_anio_fin = $_POST[pe_anio_fin], pe_fecha_inicio = '$_POST[pe_fecha_inicio]', pe_fecha_fin = '$_POST[pe_fecha_fin]', pe_nota_minima = $_POST[pe_nota_minima], pe_nota_aprobacion = $_POST[pe_nota_aprobacion], quien_inserta_comp_id = $_POST[quien_inserta_comp_id] WHERE id_periodo_lectivo = $_POST[id_periodo_lectivo]";

// echo $qry; die();

try {
	$db->consulta($qry);

	// Actualizar los niveles de educación asociados
	/*$qry = "DELETE FROM sw_periodo_nivel WHERE id_periodo_lectivo = $_POST[id_periodo_lectivo]";
	$db->consulta($qry);

	for ($i = 0; $i < count($niveles); $i++) {
		$qry = "INSERT INTO sw_periodo_nivel(id_periodo_lectivo, id_nivel_educacion) VALUES($_POST[id_periodo_lectivo], $niveles[$i])";
		$db->consulta($qry);
	}*/

	// Actualizar los subperiodos de evaluación asociados
	/*$qry = "DELETE FROM sw_periodo_lectivo_sub_periodo WHERE id_periodo_lectivo = $_POST[id_periodo_lectivo]";
	$db->consulta($qry);

	for ($i = 0; $i < count($sub_periodos); $i++) {
		$qry = "INSERT INTO sw_periodo_lectivo_sub_periodo(id_periodo_lectivo, id_sub_periodo_evaluacion ) VALUES($_POST[id_periodo_lectivo], $sub_periodos[$i])";
		$db->consulta($qry);
	}*/

	$data = array(
		"titulo"       => "Operación exitosa.",
		"mensaje"      => "El periodo lectivo se ha actualizado exitosamente.",
		"tipo_mensaje" => "success"
	);
} catch (Exception $e) {
	$data = array(
		"titulo"       => "Ocurrió un error inesperado.",
		"mensaje"      => "El periodo lectivo no se pudo actualizar exitosamente...Error: " . $e->getMessage(),
		"tipo_mensaje" => "error"
	);
}

echo json_encode($data);
