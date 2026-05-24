<?php
include("../scripts/clases/class.mysql.php");
include("../scripts/clases/class.institucion.php");
function upload_image()
{
    if (isset($_FILES["in_logo"])) {
        if (file_exists('../public/uploads/' . $_FILES['in_logo']['name'])) {
            unlink('../public/uploads/' . $_FILES['in_logo']['name']);
        }
        $extension = explode('.', $_FILES['in_logo']['name']);
        $new_name = rand() . '.' . $extension[1];
        $destination = dirname(dirname(__FILE__)) . '/public/uploads/' . $new_name;
        move_uploaded_file($_FILES['in_logo']['tmp_name'], $destination);
        return $new_name;
    }
}
$institucion = new institucion();
$logoActual = $institucion->obtenerLogoInstitucion();
if ($_FILES['in_logo']['name'] != "") {
    $image = upload_image();
} else {
    $image = $logoActual;
}
$institucion->in_logo = $image;
echo $institucion->actualizarLogoInstitucion();
