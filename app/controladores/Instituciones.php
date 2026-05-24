<?php
class Instituciones extends Controlador
{
    private $institucionModelo;
    
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['usuario_logueado'])) {
            redireccionar('/auth');
        }
        $this->institucionModelo = $this->modelo('Institucion');
    }

    public function index()
    {
        $institucion = $this->institucionModelo->obtenerDatosInstitucion();
        $datos = [
            'titulo' => 'Institución CRUD',
            'institucion' => $institucion,
            'nombreVista' => 'admin/institucion/index.php'
        ];
        $this->vista('admin/index', $datos);
    }

    public function upload_image()
	{
		if(isset($_FILES["in_logo"]))
		{
			$extension = explode('.', $_FILES['in_logo']['name']);
			$new_name = rand() . '.' . $extension[1];
			$destination = dirname(dirname(dirname(__FILE__))) . '/public/uploads/' . $new_name;
			move_uploaded_file($_FILES['in_logo']['tmp_name'], $destination);
			return $new_name;
		}
	}

    public function actualizar()
    {
        sleep(1);

        $institucionActual = $this->institucionModelo->obtenerDatosInstitucion();

        if ($_FILES['in_logo']['name'] != "") {
            $image = $this->upload_image();
        } else {
            $image = $institucionActual->in_logo;
        }

        $datos = [
            'in_nombre' => trim($_POST['in_nombre']),
            'in_direccion' => trim($_POST['in_direccion']),
            'in_telefono' => trim($_POST['in_telefono']),
            'in_regimen' => trim($_POST['in_regimen']),
            'in_nom_rector' => trim($_POST['in_nom_rector']),
            'in_genero_rector' => trim($_POST['in_genero_rector']),
            'in_nom_secretario' => trim($_POST['in_nom_secretario']),
            'in_genero_secretario' => trim($_POST['in_genero_secretario']),
            'in_nom_vicerrector' => trim($_POST['in_nom_vicerrector']),
            'in_url' => trim($_POST['in_url']),
            'in_amie' => trim($_POST['in_amie']),
            'in_ciudad' => trim($_POST['in_ciudad']),
            'in_logo' => trim($image)
        ];

        return $this->institucionModelo->actualizarInstitucion($datos);
    }

    public function obtener_estado_copiar_y_pegar()
    {
        echo $this->institucionModelo->obtener_copiar_y_pegar();
    }

    public function actualizar_copiar_y_pegar()
    {
        $in_copiar_y_pegar = $_POST['in_copiar_y_pegar'];
        try {
            $this->institucionModelo->actualizar_copiar_y_pegar($in_copiar_y_pegar);
            $data = [
                "titulo"       => "Actualización exitosa.",
                "mensaje"      => "El estado copiar y pegar fue actualizado exitosamente.",
                "tipo_mensaje" => "success"
            ];

            echo json_encode($data);
        } catch (PDOException $ex) {
            $data = [
                "titulo"       => "PDOException!",
                "mensaje"      => "El usuario no fue actualizado exitosamente. Error: " . $ex->getMessage(),
                "tipo_mensaje" => "error"
            ];

            echo json_encode($data);
        }
    }
}
