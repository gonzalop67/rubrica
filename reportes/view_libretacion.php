<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Libretación
            <small id="nombreParalelo"></small>
        </h1>
    </section>
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cboPeriodosEvaluacion">Período: </label>
                            <select name="cboPeriodosEvaluacion" id="cboPeriodosEvaluacion">
                                <option value="0">Seleccione...</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cboParalelos">Paralelo: </label>
                            <select name="cboParalelos" id="cboParalelos">
                                <option value="0">Seleccione...</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nro.</th>
                            <th>Id</th>
                            <th>Nómina</th>
                            <th>Promedio</th>
                            <th>Cualitativa</th>
                            <th>Libretación</th>
                        </tr>
                    </thead>
                    <tbody id="tbl_estudiantes">
                        <!-- Aquí va la lista de estudiantes obtenida de la base de datos -->
                    </tbody>
                </table>
            </div>
            <form id="formulario_libreta" action="reportes/reporte_libreta_sub_periodo.php" target="_blank" method="post">
                <input id="id_paralelo" name="id_paralelo" type="hidden" />
                <input id="id_estudiante" name="id_estudiante" type="hidden" />
                <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
            </form>
        </div>
    </section>
</div>
<!-- /.content-wrapper -->

<script>
    $(document).ready(function() {
        $("#tbl_estudiantes").html('<tr><td colspan="6" class="text-center text-danger">Debe seleccionar un per&iacute;odo de evaluaci&oacute;n...</td></tr>');
        $.ajaxSetup({
            error: function(xhr) {
                alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
            }
        });
        $("#cboPeriodosEvaluacion").change(function(e) {
            e.preventDefault();
            var id_paralelo = $("#cboParalelos").val();
            if ($(this).val() == 0) {
                $("#tbl_estudiantes").html("<tr><td class='text-center text-danger' colspan='6'>Debe seleccionar un periodo de evaluación...</td></tr>");
            } else if (id_paralelo == 0) {
                $("#tbl_estudiantes").html("<tr><td class='text-center text-danger' colspan='6'>Debe seleccionar un paralelo...</td></tr>");
            } else {
                listarEstudiantesParalelo(id_paralelo); //Esta funcion desencadena las demas funciones de paginacion
            }
        });
        $("#cboParalelos").change(function(e) {
            e.preventDefault();
            var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
            if ($(this).val() == 0) {
                $("#tbl_estudiantes").html("<tr><td class='text-center text-danger' colspan='6'>Debe seleccionar un paralelo...</td></tr>");
            } else if (id_periodo_evaluacion == 0) {
                $("#tbl_estudiantes").html("<tr><td class='text-center text-danger' colspan='6'>Debe seleccionar un periodo de evaluación...</td></tr>");
            } else {
                listarEstudiantesParalelo($(this).val()); //Esta funcion desencadena las demas funciones de paginacion
            }
        });
        cargarPeriodosEvaluacion();
        cargarParalelos();
    });

    function cargarPeriodosEvaluacion() {
        $.get("scripts/cargar_periodos_evaluacion_principales.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboPeriodosEvaluacion").append(resultado);
                }
            }
        );
    }

    function cargarParalelos() {
        $.get("scripts/cargar_paralelos.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboParalelos").append(resultado);
                }
            }
        );
    }

    function listarEstudiantesParalelo(id_paralelo) {
        var id_paralelo = document.getElementById("cboParalelos").value;
        var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
        $("#tbl_estudiantes").html('<tr><td class="text-center" colspan="6"><img src="imagenes/ajax-loader-blue.GIF" alt="procesando..." /></td></tr>');
        $.post("reportes/listar_estudiantes_libretacion.php", {
                id_paralelo: id_paralelo,
                id_periodo_evaluacion: id_periodo_evaluacion
            },
            function(resultado) {
                $("#tbl_estudiantes").html(resultado);
            }
        );
    }

    function seleccionarEstudiante(id_estudiante) {
        var id_paralelo = document.getElementById("cboParalelos").value;
        var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
        document.getElementById("id_paralelo").value = id_paralelo;
        document.getElementById("id_estudiante").value = id_estudiante;
        document.getElementById("id_periodo_evaluacion").value = id_periodo_evaluacion;
        document.getElementById("formulario_libreta").submit();
    }
</script>