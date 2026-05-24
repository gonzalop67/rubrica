<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <small>Lista de Asignaturas Asociadas</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <?php
            $qry = "SELECT c.id_curso, 
            d.id_paralelo,
            d.id_periodo_lectivo, 
            d.id_asignatura, 
            as_nombre, 
            es_figura, 
            cu_nombre, 
            pa_nombre 
            FROM sw_asignatura a, 
            sw_distributivo d,
            sw_periodo_lectivo pl, 
            sw_paralelo pa, 
            sw_curso c, 
            sw_especialidad e 
            WHERE a.id_asignatura = d.id_asignatura 
            AND d.id_paralelo = pa.id_paralelo 
            AND pa.id_curso = c.id_curso 
            AND c.id_especialidad = e.id_especialidad 
            AND d.id_usuario = $id_usuario
            AND pl.id_periodo_lectivo = d.id_periodo_lectivo
            AND pl.pe_estado = 'A'
            AND as_curricular = 1 
            ORDER BY c.id_curso, pa.id_paralelo, as_nombre ASC";
            $consulta = $db->consulta($qry);
            while ($row = $db->fetch_object($consulta)) {
                $id_curso = $row->id_curso;
                $id_paralelo = $row->id_paralelo;
                $id_asignatura = $row->id_asignatura;
                $id_periodo_lectivo = $row->id_periodo_lectivo;
                $nombre = $row->as_nombre;  // nombre de la asignatura
                $curso = $row->cu_nombre . " " . $row->es_figura . " " . $row->pa_nombre; // nombre del paralelo
            ?>
                <a href="admin2.php?id_usuario=<?php echo $_GET['id_usuario'] ?>&enlace=registrar_calificaciones.php&id_curso=<?php echo $id_curso ?>&id_paralelo=<?php echo $id_paralelo ?>&id_asignatura=<?php echo $id_asignatura ?>&nombre=<?php echo $nombre ?>&curso=<?php echo $curso ?>&id_periodo_lectivo=<?php echo $id_periodo_lectivo ?>">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="ion ion-ios-people-outline"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><?php echo $nombre ?></span>
                                <span class="info-box-number"><?php echo $curso ?></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                </a>
            <?php
            }
            ?>
        </div>
    </section>
</div>