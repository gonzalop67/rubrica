<?php

//Para redireccionar página
function redireccionar($pagina){
    header('Location: ' . RUTA_URL . $pagina);
}