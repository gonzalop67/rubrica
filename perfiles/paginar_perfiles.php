<?php
    include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.encrypter.php");

    session_start();
    $id_usuario = encrypter::encrypt($_SESSION['id_usuario']);
    $id_perfil = $_SESSION['id_perfil'];
    
    $paginaActual = $_POST['partida'];
    
    $db = new MySQL();
    
    $query = "SELECT * FROM `sw_perfil`";
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
    
    $consulta = $db->consulta("SELECT * FROM `sw_perfil` ORDER BY pe_nombre LIMIT $limit, $nroLotes");
    $num_total_registros = $db->num_rows($consulta);
    if($num_total_registros>0)
    {
        while($perfil = $db->fetch_assoc($consulta))
        {
            $tabla .= "<tr>";
            $nombre = $perfil["pe_nombre"];
            $id = $perfil["id_perfil"];
            $tabla .= "<td>".$id."</td>\n";
            $tabla .= "<td>".$nombre."</td>\n";
            $tabla .= "<td class='text-center'><a href='admin.php?id_usuario=$id_usuario&id_perfil=$id_perfil&enlace=perfiles/view_permisos_asociados.php&perfil_id=$id&nivel=0' class='btn btn-sm btn-primary'><i class='fa fa-shield'></i></a></td>\n";
            $tabla .= "<td><div class='btn-group'><a href='javascript:;' class='btn btn-warning item-edit' data='".$id."' title='Editar'><span class='fa fa-pencil'></span></a>\n";
            $tabla .= "<a href='javascript:;' class='btn btn-danger item-delete' data='".$id."' title='Eliminar'><span class='fa fa-trash'></span></a></div></td>\n";
            $tabla .= "</tr>\n";	
        }
    }
    else {
        $tabla .= "<tr>\n";	
        $tabla .= "<tr><td colspan='3' align='center'>No se han ingresado perfiles todavia...</td></tr>\n";
        $tabla .= "</tr>\n";	
    }

    $array = array(
        0 => $tabla,
        1 => $lista
    );

    echo json_encode($array);
?>
