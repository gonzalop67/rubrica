<?php
    include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.tutores.php");
    $id_comportamiento_tutor = $_POST["id_comportamiento_tutor"];
    $tutor = new tutores();
    echo $tutor->eliminarCalifComportamiento($id_comportamiento_tutor);
?>
