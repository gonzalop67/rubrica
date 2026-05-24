<?php
    include("../scripts/clases/class.mysql.php");
    $db = new MySQL();
    $consulta = $db->consulta("SELECT * FROM sw_modalidad ORDER BY mo_orden ASC");
    $num_registros = $db->num_rows($consulta);
    $cadena = "";
    if($num_registros > 0){
        for ($i=0; $i < $num_registros ; $i++) { 
            $registro = $db->fetch_assoc($consulta);
            $activo = $registro["mo_activo"] == 1 ? 'Sí' : 'No';
            $cadena .= '<tr data-index="'.$registro['id_modalidad'].'" data-orden="'.$registro['mo_orden'].'">\n';
            $cadena .= "<td>".$registro['id_modalidad']."</td>\n";
            $cadena .= "<td>".$registro['mo_nombre']."</td>\n";
            $cadena .= "<td>".$activo."</td>\n";
            $cadena .= "<td>\n";
            $cadena .= "<div class='btn-group'>\n";
            $cadena .= '<a href="javascript:;" class="btn btn-warning item-edit" data="' . $registro['id_modalidad'] . '" title="Editar"><span class="fa fa-pencil"></span></a>';
            $cadena .= '<a href="javascript:;" class="btn btn-danger item-delete" data="' . $registro['id_modalidad'] . '" title="Eliminar"><span class="fa fa-trash"></span></a>';
            $cadena .= "</div>";
            $cadena .= "</td>\n";
            $cadena .= "</tr>\n";
        }
    } else {
        $cadena .= "<tr><td colspan='4' align='center'>No se han ingresado modalidades todavía...</td></tr>";
    }
    
    echo $cadena;
?>