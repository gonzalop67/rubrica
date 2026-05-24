<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Horario
            <small>Ver Horario General de Clases</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                        <!-- table -->
                        <?php
                        $qry = "SELECT id_periodo_lectivo FROM sw_periodo_lectivo WHERE pe_estado = 'A'";
                        $rs = $db->consulta($qry);
                        $periodos_lectivos = "";

                        while ($row = $db->fetch_object($rs)) {
                            $periodos_lectivos .= $row->id_periodo_lectivo . ",";
                        }
                        $periodos_lectivos = rtrim($periodos_lectivos, ",");
                        
                        $qry = "SELECT DISTINCT(d.id_dia_semana) 
                                FROM sw_distributivo di,
                                sw_periodo_lectivo pl, 
                                sw_horario h,
                                sw_dia_semana d
                                WHERE di.id_asignatura = h.id_asignatura
                                AND di.id_paralelo = h.id_paralelo
                                AND d.id_dia_semana = h.id_dia_semana
                                AND id_usuario = $id_usuario
                                AND pl.id_periodo_lectivo = di.id_periodo_lectivo 
                                AND pl.pe_estado = 'A'
                                ORDER BY ds_ordinal";
                        ?>
                        <!-- <table class="table table-bordered fuente9">
                            <thead id="horario_cabecera"> -->
                        <!-- Aqui desplegamos los dias de la semana de este paralelo -->
                        <!-- </thead>
                            <tbody id="horario_clases"> -->
                        <!-- Aqui desplegamos las horas clase con su asignatura y docente -->
                        <!-- </tbody>
                        </table> -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // Obtengo los dias de la semana
        // $.ajax({
        //     url: "../inspeccion/listar_dias_semana.php",
        //     data: {
        //         id_usuario: $(this).val()
        //     },
        //     method: "POST",
        //     type: "html",
        //     success: function(response) {
        //         $("#horario_cabecera").html(response);
        //     },
        //     error: function(xhr, status, error) {
        //         alert(xhr.responseText);
        //     }
        // });
    });
</script>