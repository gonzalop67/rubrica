<?php
    // Recoger los datos de ingreso
    $fecha = $_POST["fecha"];
    $day = date("d");
    $month = date("m");
    $year = date("Y");
    $birthday = date("d",strtotime($fecha));
    $birthmonth = date("m",strtotime($fecha));
    $birthyear = date("Y",strtotime($fecha));

    if ($birthyear > 0) {
        /*----------------------------------------------------------------------------------*/
        // Calcular solo los anios cumplidos.
        //si el mes es el mismo pero el día inferior aun no ha cumplido años, le quitaremos un año al actual
        if (($birthmonth == $month) && ($birthday > $day)) {
            $year = $year - 1; 
        }

        //si el mes es superior al actual tampoco habrá cumplido años, por eso le quitamos un año al actual

        if ($birthmonth > $month) {
            $year = $year - 1;
        }

        //ya no habría mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad

        $edad = $year - $birthyear;
    } else {
        $edad = "";
    }
    
    echo $edad;
?>