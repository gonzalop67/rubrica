<?php
	sleep(1);
	session_start();
	require_once("clases/class.mysql.php");
	require_once("clases/class.usuarios.php");
	require_once("clases/class.periodos_lectivos.php");
	$usuarios = new usuarios();
	//recibo las variables de tipo post de la pagina login.php
	$login = $_POST['uname'];
	$clave = $_POST['passwd'];
	$id_periodo_lectivo = $_POST['cboPeriodo'];
	$periodo_lectivo = new periodos_lectivos();
	$periodo_lectivo = $periodo_lectivo->obtenerPeriodoLectivo($id_periodo_lectivo);
	$id_modalidad = $periodo_lectivo->id_modalidad;
	$id_perfil = $_POST['cboPerfil'];
	//consultar a la tabla usuario si existe un usuario en la tabla
	$_SESSION['usuario_logueado'] = false;
	if ($usuarios->existeUsuario($login, $clave, $id_perfil)) {
	    $id_usuario = $usuarios->obtenerIdUsuario($login, $clave, $id_perfil);
		//$nombreModalidad = $modalidades->getModalidadByIdPeriodoLectivo($id_periodo_lectivo);
		$_SESSION['usuario_logueado'] = true;
		$_SESSION['id_periodo_lectivo'] = $id_periodo_lectivo;
		$_SESSION['id_modalidad'] = $id_modalidad;
		$_SESSION['id_usuario'] = $id_usuario;
		$_SESSION['id_perfil'] = $id_perfil;
		$_SESSION['cambio_paralelo'] = 0;
		//echo json_encode(array('id_usuario' => $id_usuario));
		echo json_encode(array('id_usuario' => encrypter::encrypt($id_usuario)));
	} else {
		echo json_encode(array('error' => true));
	}
?>