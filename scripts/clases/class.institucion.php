<?php

class institucion extends MySQL
{
	var $in_logo = "";
	var $in_amie = "";
	var $in_nombre = "";
	var $in_ciudad = "";
	var $in_regimen = "";
	var $in_distrito = "";
	var $in_direccion = "";
	var $in_telefono = "";
	var $in_nom_rector = "";
	var $in_genero_rector = "";
	// Campos añadidos recientemente (1-sep-2025)
	var $in_nom_vicerrector = "";
	var $in_genero_vicerrector = "";
	// -----------------------------------------
	var $in_copiar_y_pegar = 0;
	var $in_nom_secretario = "";
	var $in_genero_secretario = "";

	function actualizarDatosInstitucion()
	{
		$qry = "UPDATE sw_institucion SET ";
		$qry .= "in_nombre = '" . $this->in_nombre . "', ";
		$qry .= "in_direccion = '" . $this->in_direccion . "', ";
		$qry .= "in_telefono = '" . $this->in_telefono . "', ";
		$qry .= "in_regimen = '" . strtoupper($this->in_regimen) . "', ";
		$qry .= "in_nom_rector = '" . $this->in_nom_rector . "', ";
		$qry .= "in_nom_vicerrector = '" . $this->in_nom_vicerrector . "', ";
		$qry .= "in_nom_secretario = '" . $this->in_nom_secretario . "', ";
		$qry .= "in_genero_rector = '" . $this->in_genero_rector . "', ";
		$qry .= "in_genero_vicerrector = '" . $this->in_genero_vicerrector . "', ";
		$qry .= "in_genero_secretario = '" . $this->in_genero_secretario . "', ";
		$qry .= "in_amie = '" . $this->in_amie . "', ";
		$qry .= "in_ciudad = '" . $this->in_ciudad . "', ";
		$qry .= "in_copiar_y_pegar = " . $this->in_copiar_y_pegar;
		$qry .= " WHERE id_institucion = 1";
		// return $qry;
		$consulta = parent::consulta($qry);
		if ($consulta)
			return "Actualizaci&oacute;n realizada exitosamente.";
		else
			return "No se pudo realizar la actualizaci&oacute;n. Error: " . mysqli_error($this->conexion);
	}

	function actualizarLogoInstitucion()
	{
		$qry = "UPDATE sw_institucion SET ";
		$qry .= "in_logo = '" . $this->in_logo . "' ";
		$qry .= "WHERE id_institucion = 1";
		$consulta = parent::consulta($qry);
		if ($consulta)
			return "Actualización del logo realizada exitosamente.";
		else
			return "No se pudo realizar la actualización del logo. Error: " . mysqli_error($this->conexion);
	}

	function obtenerDatosInstitucion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_institucion WHERE id_institucion = 1");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerJornada($id_paralelo)
	{
		$qry = parent::consulta("SELECT jo_nombre, 
                                 cu_nombre,
                                 pa_nombre
                            FROM sw_jornada j, 
                                 sw_curso c, 
                                 sw_paralelo p
                           WHERE c.id_curso = p.id_curso 
                             AND j.id_jornada = p.id_jornada 
                             AND id_paralelo = $id_paralelo");
		$res = parent::fetch_assoc($qry);
		return $res["jo_nombre"];
	}

	function obtenerNombreInstitucion()
	{
		$consulta = parent::consulta("SELECT in_nombre FROM sw_institucion");
		return parent::fetch_object($consulta)->in_nombre;
	}

	function obtenerDireccionInstitucion()
	{
		$consulta = parent::consulta("SELECT in_direccion FROM sw_institucion");
		return parent::fetch_object($consulta)->in_direccion;
	}

	function obtenerTelefonoInstitucion()
	{
		$consulta = parent::consulta("SELECT in_telefono FROM sw_institucion");
		return parent::fetch_object($consulta)->in_telefono;
	}

	function obtenerRegimenInstitucion()
	{
		$consulta = parent::consulta("SELECT in_regimen FROM sw_institucion");
		return parent::fetch_object($consulta)->in_regimen;
	}

	function obtenerAMIEInstitucion()
	{
		$consulta = parent::consulta("SELECT in_amie FROM sw_institucion");
		return parent::fetch_object($consulta)->in_amie;
	}

	function obtenerDistritoInstitucion()
	{
		$consulta = parent::consulta("SELECT in_distrito FROM sw_institucion");
		return parent::fetch_object($consulta)->in_distrito;
	}

	function obtenerCiudadInstitucion()
	{
		$consulta = parent::consulta("SELECT in_ciudad FROM sw_institucion");
		return parent::fetch_object($consulta)->in_ciudad;
	}

	function obtenerLogoInstitucion()
	{
		$consulta = parent::consulta("SELECT in_logo FROM sw_institucion");
		return parent::fetch_object($consulta)->in_logo;
	}

	function obtenerNombreRector()
	{
		$consulta = parent::consulta("SELECT in_nom_rector FROM sw_institucion");
		return parent::fetch_object($consulta)->in_nom_rector;
	}

	function obtenerNombreVicerrector()
	{
		$consulta = parent::consulta("SELECT in_nom_vicerrector FROM sw_institucion");
		return parent::fetch_object($consulta)->in_nom_vicerrector;
	}

	function obtenerNombreSecretario()
	{
		$consulta = parent::consulta("SELECT in_nom_secretario FROM sw_institucion");
		return parent::fetch_object($consulta)->in_nom_secretario;
	}
}
