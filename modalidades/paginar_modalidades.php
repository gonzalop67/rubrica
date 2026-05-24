<?php
    include("../scripts/clases/class.mysql.php");
    
    $paginaActual = $_POST['partida'];
    
    $db = new MySQL();
    
    $query = "SELECT * FROM `sw_modalidad`";
    $consulta = $db->consulta($query);
    $nroPeriodosLectivos = $db->num_rows($consulta);

    $nroLotes = 5;
    $nroPaginas = ceil($nroPeriodosLectivos / $nroLotes);

    $lista = '';
    $tabla = '';

    if ($paginaActual == 1) {
        $lista = $lista.'<li><a href="javascript:;" disabled>‹</a></li>';
    } else {
        $lista = $lista.'<li><a href="javascript:pagination('.($paginaActual - 1).');">‹</a></li>';
    }

    for ($i=1; $i<=$nroPaginas; $i++) { 
        if ($i == $paginaActual) {
            $lista = $lista.'<li class="active"><a href="javascript:pagination('.$i.');">'.$i.'</a></li>';
        } else {
            $lista = $lista.'<li><a href="javascript:pagination('.$i.');">'.$i.'</a></li>';
        }
    }

    if ($paginaActual == $nroPaginas) {
        $lista = $lista.'<li><a href="javascript:;" disabled>›</a></li>';
    } else {
        $lista = $lista.'<li><a href="javascript:pagination('.($paginaActual + 1).');">›</a></li>';
    }

    if ($paginaActual <= 1) {
        $limit = 0;
    } else {
        $limit = $nroLotes * ($paginaActual - 1);
    }
    
    $consulta = $db->consulta("SELECT * FROM `sw_modalidad` ORDER BY mo_nombre LIMIT $limit, $nroLotes");
    $num_total_registros = $db->num_rows($consulta);
    if($num_total_registros>0)
    {
        while($modalidad = $db->fetch_assoc($consulta))
        {
            $tabla .= "<tr>";
            $id = $modalidad["id_modalidad"];
            $nombre = $modalidad["mo_nombre"];
            $activo = $modalidad["mo_activo"] ? "Sí" : "No";
            $tabla .= "<td>".$id."</td>";
            $tabla .= "<td>".$nombre."</td>";
            $tabla .= "<td>".$activo."</td>";
            $tabla .= "<td><div class='btn-group'><a href='javascript:;' class='btn btn-warning item-edit' data='".$id."' title='Editar'><span class='fa fa-pencil'></span></a>";
            $tabla .= "<a href='javascript:;' class='btn btn-danger item-delete' data='".$id."' title='Eliminar'><span class='fa fa-remove'></span></a></div></td>";
            $tabla .= "</tr>";	
        }
    }
    else {
        $tabla .= "<tr>\n";	
        $tabla .= "<tr><td colspan='3' align='center'>No se han ingresado modalidades todavia...</td></tr>\n";
        $tabla .= "</tr>\n";	
    }

    $array = array(
        0 => $tabla,
        1 => $lista
    );

    echo json_encode($array);
?>
