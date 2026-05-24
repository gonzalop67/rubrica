<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Ingreso de Calificaciones de Proyectos</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
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
                </div>
                <div id="pag_nomina_estudiantes">
                    <!-- Aqui va la paginacion de los estudiantes encontrados -->
                    <div id="total_registros_estudiantes" class="paginacion" style="height:25px;">
                        <table class="fuente10" width="100%" cellspacing=4 cellpadding=0 style="border:none">
                            <tr>
                                <td>
                                    <div id="num_estudiantes" style="margin-left: 5px;"></div>
                                </td>
                                <td>
                                    <div id="leyendas_rubricas" class="fuente9">
                                        <!-- Aqui van las leyendas de las rubricas -->
                                    </div>
                                </td>
                                <td>
                                    <!-- <div id="btn-guardar" style="text-align: right; margin-right: 2px;">
                                        <button type="button" id="save_all" class="btn btn-success btn-xs" onclick="guardarTabla()" title="Guardar Todas las Calificaciones">Guardar</button>
                                    </div> -->
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
                    <form id="formulario_rubrica" action="reportes/reporte_proyectos.php" method="post" target="_blank">
                        <div id="img_loader_estudiantes" style="text-align:center"> </div>
                        <div id="lista_estudiantes_paralelo" class="text-center"></div>
                        <input id="id_asignatura" name="id_asignatura" type="hidden" />
                        <input id="id_paralelo" name="id_paralelo" type="hidden" />
                        <input id="id_curso" name="id_curso" type="hidden" />
                        <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
                        <div id="ver_reporte" style="text-align:center;display:none">
                            <input class="btn btn-primary btn-sm" type="submit" value="Ver Reporte" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.content-wrapper -->

<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/keypress.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        cargarPeriodosEvaluacion();
        $("#cboPeriodosEvaluacion").change(function(e) {
            e.preventDefault();
            if ($(this).val() == "0") {
                Swal.fire({
                    icon: "error",
                    text: "Debes elegir un periodo de evaluación...",
                    title: "Oops..."
                });
            } else {
                listarEstudiantesParalelo();
            }
        });
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

    function listarEstudiantesParalelo() {
        var id_paralelo_tutor = $("#id_paralelo_tutor").val().trim();
        var id_periodo_lectivo = $("#id_periodo_lectivo").val().trim();
        var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
        // Obtener el id_aporte_evaluacion correspondiente a los proyectos interdisciplinares
        $.ajax({
            type: "post",
            url: "scripts/obtener_id_aporte_proyecto.php",
            data: {
                id_periodo_lectivo: id_periodo_lectivo,
                id_periodo_evaluacion: id_periodo_evaluacion
            },
            dataType: "json",
            success: function(response) {
                if (response.errorno == 0) {
                    var id_aporte_evaluacion = response.id_aporte_evaluacion;
                    // Listar Estudiantes del Paralelo
                    $("#img_loader_estudiantes").html("<img src='imagenes/ajax-loader-blue.GIF' alt='procesando...' />");
                    $.ajax({
                        type: "post",
                        url: "tutores/listar_estudiantes_proyecto.php",
                        data: {
                            id_paralelo: id_paralelo_tutor,
                            id_periodo_lectivo: id_periodo_lectivo,
                            id_periodo_evaluacion: id_periodo_evaluacion,
                            id_aporte_evaluacion: id_aporte_evaluacion
                        },
                        dataType: "html",
                        success: function(response) {
                            // console.log(response);
                            $("#img_loader_estudiantes").html("");
                            $("#lista_estudiantes_paralelo").html(response);
                        }
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        text: response.error,
                        title: "Error"
                    });
                }
            }
        });
    }

    function replace(str) {
        let str_aux = "";
        for (let i = 0; i < str.length; i++) {
            const element = str[i];
            if (element === ",") {
                str_aux += ".";
            } else {
                str_aux += element;
            }
        }
        return str_aux;
    }

    function editarCalificacion(obj, id_estudiante, id_paralelo, id_aporte_evaluacion, calificacion_bd) {
        var calificacion = obj.value;
        var puntaje = 0;
        var id = obj.id;
        var fila = id.substr(id.lastIndexOf("_") + 1);
        var frmFormulario = document.forms["formulario_rubrica"];
        calificacion = replace(calificacion);
        //Validacion de la calificacion
        if (calificacion == "")
            calificacion = 0;
        if (calificacion < 0 || calificacion > 10) {
            Swal.fire({
                icon: "error",
                title: "La calificacion debe estar en el rango de 0 a 10",
                text: "Se ha producido un error en el ingreso de calificaciones"
            });
            if (typeof calificacion_bd !== 'undefined' && calificacion_bd !== "")
                document.getElementById(id).value = calificacion_bd;
            else
                document.getElementById(id).value = "";
        } else {
            $.post("tutores/editar_nota_proyecto.php", {
                    id_estudiante: id_estudiante,
                    id_paralelo: id_paralelo,
                    id_aporte_evaluacion: id_aporte_evaluacion,
                    calificacion: calificacion
                },
                function(resultado) {
                    if (resultado) { // Solo si existe resultado
                        console.log(resultado);
                    }
                }
            );
        }
    }
</script>