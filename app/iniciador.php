<?php

    //Cargamos librerias
    require_once 'config/Configurar.php';
    require_once 'helpers/url_helper.php';
    require_once 'fpdf16/fpdf.php';

    //Autoload php
    spl_autoload_register(function($nombreClase){
        require_once 'librerias/' . $nombreClase . '.php';
    });