<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Ingreso de Calificaciones de <?php echo $_GET['nombre'] ?> - <?php echo $_GET['curso'] ?>
                </h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <div class="box-body">
                <input type="hidden" id="id_usuario" value="<?php echo $id_usuario; ?>">
                <!-- <input type="hidden" id="id_paralelo" value="<?php echo $_GET['id_paralelo']; ?>">
                <input type="hidden" id="id_asignatura" value="<?php echo $_GET['id_asignatura']; ?>">
                <input type="hidden" id="id_periodo_lectivo" value="<?php echo $_GET['id_periodo_lectivo']; ?>"> -->
                <input type="hidden" id="id_periodo_evaluacion">
                <input type="hidden" id="id_aporte_evaluacion">
                <input type="hidden" id="nota_minima">
                <input type="hidden" id="nota_maxima">
                <input type="hidden" id="rows">
                <input type="hidden" id="cols">
                <div class="row mb-3">
                    <div class="col-sm-6 col-md-2 col-lg-1 control-label text-right">
                        <label for="">Paralelo:</label>
                    </div>
                    <div class="col-sm-6 col-md-10 col-lg-10">
                        <select id="cboParalelos" class="form-control">
                            <option value="0">Seleccione...</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6 col-md-2 col-lg-1 control-label text-right">
                        <label for="">Asignatura:</label>
                    </div>
                    <div class="col-sm-6 col-md-10 col-lg-10">
                        <select id="cboAsignaturas" class="form-control">
                            <option value="0">Seleccione...</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6 col-md-2 col-lg-1 control-label text-right">
                        <label for="">Aporte:</label>
                    </div>
                    <div class="col-sm-6 col-md-10 col-lg-10">
                        <select id="cboAportesEvaluacion" class="form-control">
                            <option value="0">Seleccione...</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div id="div_estado_rubrica" class="col-sm-12 col-md-12 col-lg-12 text-center">
                        ...
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div id="tituloNomina" class="header2">NOMINA DE ESTUDIANTES</div>
                        <div id="divTabla" class="table-responsive">

                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>
<script>
    $(document).ready(function() {
        //
        cargarParalelosDocente();

        $("#cboParalelos").change(function(e) {
            cargarAportesEvaluacion();
            listarAsignaturasDocente();

            // $("#div_estado_rubrica").html("");
            // $("#div_fecha_cierre").html("");
            // $("#mensaje_rubrica").html("");
            // $("#ver_reporte").hide();
            // $("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados:&nbsp;");
            // $("#paginacion_estudiantes").html("");
            // $("#tituloNomina").html("NOMINA DE ESTUDIANTES");
        });
    });

    function cargarParalelosDocente() {
        $('#cboParalelos option').remove();
        $.post("calificaciones/cargar_paralelos_docente.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    // console.log(resultado);
                    $("#cboParalelos").append(resultado);
                    // $("#lista_estudiantes_paralelo").addClass("text-danger");
                    // $("#lista_estudiantes_paralelo").html("Debe elegir un paralelo...");
                }
            }
        );
    }

    function listarAsignaturasDocente() {
        var id_paralelo = $("#cboParalelos").val();

        $("#cboAsignaturas").empty();

        $.ajax({
            type: "POST",
            url: "calificaciones/listar_asignaturas_paralelo_docente.php",
            data: {
                id_paralelo: id_paralelo
            },
            dataType: "json",
            success: function(response) {
                if (response.ok) {
                    $("#cboAsignaturas").append(response.cadena);
                } else {
                    Swal.fire({
                        icon: "info",
                        title: "Mensaje",
                        text: "Ocurrió un error inesperado. Error: " + response.cadena,
                    });
                }
            }
        });
    }

    function cargarAportesEvaluacion() {
        var id_paralelo = document.getElementById("cboParalelos").value;
        $('#cboAportesEvaluacion option').remove();
        $('#cboAportesEvaluacion optgroup').remove();
        $.post("calificaciones/cargar_aportes_evaluacion_paralelo.php", {
                id_paralelo: id_paralelo
            },
            function(resultado) {
                // console.log(resultado);
                if (resultado == false) {
                    // $("#lista_estudiantes_paralelo").addClass("text-danger");
                    // $("#lista_estudiantes_paralelo").html("No existen aportes de evaluaci&oacute;n asociados a este paralelo...");
                } else {
                    $("#cboAportesEvaluacion").append(resultado);
                    // $("#lista_estudiantes_paralelo").addClass("text-danger");
                    // $("#lista_estudiantes_paralelo").html("Debe elegir un aporte de evaluaci&oacute;n...");
                }
            }
        );
    }
</script>