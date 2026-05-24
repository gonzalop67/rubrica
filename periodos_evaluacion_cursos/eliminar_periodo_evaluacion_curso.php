<?php
	include("../scripts/clases/class.mysql.php");
    $db = new MySQL();

	$id_periodo_evaluacion_curso = $_POST["id_periodo_evaluacion_curso"];

	try {
        $qry = "DELETE FROM sw_periodo_evaluacion_curso WHERE id_periodo_evaluacion_curso = $id_periodo_evaluacion_curso";
        
        $consulta = $db->consulta($qry);

        //Mensaje de operación exitosa
        $datos = [
            'titulo' => "¡Eliminado con éxito!",
            'mensaje' => "Eliminación realizada exitosamente.",
            'estado' => 'success'
        ];
    } catch (Exception $e) {
        //Mensaje de operación fallida
        $datos = [
            'titulo' => "¡Error!",
            'mensaje' => "No se pudo realizar la actualización. Error: " . $e->getMessage(),
            'estado' => 'error'
        ];
    }

    echo json_encode($datos);
?>