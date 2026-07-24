<?php
class MySQL
{
	var $conexion;
	function __construct()
	{
		if (!isset($this->conexion)) {
			$this->conexion = (mysqli_connect("localhost", "colegion_1", "AQSWDE123")) or die(mysqli_connect_error());
			mysqli_query($this->conexion, "SET NAMES 'utf8'");
			mysqli_select_db($this->conexion, "colegion_1") or die(mysqli_error($this->conexion));
			// $this->conexion->query("SET time_zone = 'America/Guayaquil';");
		}
	}

	function consulta($consulta)
	{
		$resultado = mysqli_query($this->conexion, $consulta);
		if (!$resultado) {
			echo 'MySQL Error: ' . mysqli_error($this->conexion) . '. Consulta: ' . $consulta;
			exit;
		}
		return $resultado;
	}

	function fetch_array($consulta)
	{
		return mysqli_fetch_array($consulta);
	}

	function num_rows($consulta)
	{
		return mysqli_num_rows($consulta);
	}

	function fetch_row($consulta)
	{
		return mysqli_fetch_row($consulta);
	}
	function fetch_assoc($consulta)
	{
		return mysqli_fetch_assoc($consulta);
	}
	function fetch_object($consulta)
	{
		return mysqli_fetch_object($consulta);
	}

	function filtrar($datos)
	{
		return mysqli_real_escape_string($this->conexion, trim($datos));
	}
}
