<style>
    table {
        border: 1px solid black;

        border-collapse: separate;
        border-spacing: 0px;
        min-width: max-content;
    }

    th {
        position: sticky;
        top: 0px;
        color: white;
        background-color: teal;
    }

    .table-wrapper {
        max-height: 300px;
        overflow-y: scroll;

        margin: 20px;
    }

    th,
    td {
        border: 1px solid black;
        padding: 10px;
    }

    .outer-wrapper {
        border: 1px solid black;
        box-shadow: 0px 0px 3px black;

        margin: 20px;
        border-radius: 5px;
        max-width: fit-content;
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Períodos de Evaluación
            <small>Listado</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border pull-right">
                <span class="btn btn-primary" data-toggle="modal" data-target="#nuevoPeriodoEvaluacionModal"><i class="fa fa-plus-circle"></i> Nuevo Período de Evaluación</span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="outer-wrapper">
                            <div class="table-wrapper">
                                <table id="tabla">
                                    <!-- Acá se generará la tabla mediante AJAX -->
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once "modalInsertar.php" ?>
<?php require_once "modalActualizar.php" ?>

<script src="js/funciones.js"></script>

<script>
    $(document).ready(function() {
        // JQuery Listo para utilizar
        // cargarPeriodosEvaluacion();
        cargarTiposPeriodo();

        // cargarPeriodosEvaluacion();

        cargarCursos();

        cargar_tabla();

        $("#new_pe_principal").change(function() {
            if ($(this).val() === "1" || $(this).val() === "7" || $(this).val() === "8") {
                $("#div_rango").hide();
            } else {
                $("#div_rango").show();
            }
        });

        $("#edit_pe_principal").change(function() {
            if ($(this).val() === "1" || $(this).val() === "7" || $(this).val() === "8") {
                $("#div_rangou").hide();
            } else {
                $("#div_rangou").show();
            }
        });

        $('table tbody').sortable({
            update: function(event, ui) {
                $(this).children().each(function(index) {
                    if ($(this).attr('data-orden') != (index + 1)) {
                        $(this).attr('data-orden', (index + 1)).addClass('updated');
                    }
                });
                saveNewPositions();
            }
        });
    });

    function cargarCursos() {
        // Obtengo los cursos definidos en la base de datos
        $.ajax({
            url: "scripts/cargar_cursos.php",
            success: function(response) {
                $('#new_id_curso').append(response);
                $('#edit_id_curso').append(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function cargarTiposPeriodo() {
        // Obtengo los tipos de periodos de evaluación definidos en la base de datos
        $.ajax({
            url: "periodos_evaluacion/cargar_tipos_periodos_evaluacion.php",
            success: function(response) {
                $('#new_pe_principal').append(response);
                $('#edit_pe_principal').append(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function cargar_tabla() {
        // Generar los títulos de la cabecera de la tabla
        $.ajax({
            url: "periodos_evaluacion/cargar_tabla.php",
            success: function(response) {
                console.log(response);
                $('#tabla').html(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function insertarPeriodoEvaluacion() {
        var cont_errores = 0;
        var pe_nombre = $("#new_pe_nombre").val().trim();
        var pe_abreviatura = $("#new_pe_abreviatura").val().trim();
        var pe_principal = $("#new_pe_principal").val();
        var pe_ponderacion = $("#new_pe_ponderacion").val().trim();
        var nota_desde = $("#new_nota_desde").val().trim();
        var nota_hasta = $("#new_nota_hasta").val().trim();

        if (pe_nombre == "") {
            $("#mensaje1").html("Debe ingresar el nombre del periodo de evaluación...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (pe_abreviatura == "") {
            $("#mensaje2").html("Debe ingresar la abreviatura del periodo de evaluación...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (pe_principal == "") {
            $("#mensaje3").html("Debe seleccionar el tipo de periodo de evaluación...");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if ((pe_principal == 1 || pe_principal == 7) && pe_ponderacion == "") {
            $("#mensaje4").html("Debe ingresar el valor de la ponderación...");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (pe_principal != 1 && pe_principal != 7 && nota_desde == "") {
            $("#mensaje6").html("Debe ingresar el valor de la nota desde...");
            $("#mensaje6").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje6").fadeOut();
        }

        if (pe_principal != 1 && pe_principal != 7 && nota_hasta == "") {
            $("#mensaje7").html("Debe ingresar el valor de la nota hasta...");
            $("#mensaje7").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje7").fadeOut();
        }

        if (cont_errores == 0) {
            // submit el formulario
            var data = new FormData($("#form_insert")[0]);

            $.ajax({
                url: "periodos_evaluacion/insertar_periodo_evaluacion.php",
                method: 'POST',
                data: data,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(r) {
                    // console.log(r);
                    toastr[r.estado](r.mensaje, r.titulo);
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevoPeriodoEvaluacionModal").modal('hide');
                    cargar_tabla();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    function obtenerDatos(id) {
        //Obtener los datos del periodo de evaluación seleccionado
        $.ajax({
            url: "periodos_evaluacion/obtener_periodo_evaluacion.php",
            method: "POST",
            type: "html",
            data: {
                id_periodo_evaluacion: id
            },
            success: function(response) {
                $("#id_periodo_evaluacion").val(id);
                var periodo = jQuery.parseJSON(response);
                $("#edit_pe_nombre").val(periodo.pe_nombre);
                $("#edit_pe_abreviatura").val(periodo.pe_abreviatura);
                $("#edit_pe_ponderacion").val(periodo.pe_ponderacion);
                setearIndice("edit_pe_principal", periodo.id_tipo_periodo);
                if (periodo.id_tipo_periodo != 1 && periodo.id_tipo_periodo != 7 && periodo.id_tipo_periodo != 8) {
                    $.ajax({
                        url: "periodos_evaluacion/obtener_rango_supletorios.php",
                        method: "POST",
                        type: "html",
                        data: "id_periodo_evaluacion=" + id,
                        success: function(response) {
                            // console.log(response);
                            var rango = jQuery.parseJSON(response);
                            $("#nota_desdeu").val(rango.rango_desde);
                            $("#nota_hastau").val(rango.rango_hasta);
                        }
                    });
                }
                if (periodo.id_tipo_periodo != 1 && periodo.id_tipo_periodo != 7 && periodo.id_tipo_periodo != 8) {
                    $("#div_rangou").show();
                } else {
                    $("#div_rangou").hide();
                }
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });

        // Recuperamos los cursos asociados
        $.ajax({
            type: "POST",
            url: "periodos_evaluacion/obtener_cursos_asociados.php",
            data: "id_periodo_evaluacion=" + id,
            dataType: "json",
            success: function(r) {
                edit_id_curso = document.getElementById("edit_id_curso");
                // Limpiar los cursos seleccionados anteriormente
                for (let i = 0; i < edit_id_curso.length; i++) {
                    edit_id_curso.options[i].selected = '';
                }
                // Recuperar los cursos desde la base de datos
                for (let i = 0; i < r.length; i++) {
                    const id_periodo_curso = r[i];
                    for (let j = 0; j < edit_id_curso.length; j++) {
                        const edit_periodo_curso = edit_id_curso[j];
                        if (edit_periodo_curso.value === id_periodo_curso) {
                            edit_id_curso.options[j].selected = 'selected';
                        }
                    }
                }
            }
        });
    }

    function actualizarPeriodoEvaluacion() {
        var cont_errores = 0;
        var id = $("#id_periodo_evaluacion").val();
        var pe_nombre = $("#edit_pe_nombre").val();
        var pe_abreviatura = $("#edit_pe_abreviatura").val();
        var pe_principal = $("#edit_pe_principal").val();
        var pe_ponderacion = $("#edit_pe_ponderacion").val();
        var nota_desde = $("#nota_desdeu").val().trim();
        var nota_hasta = $("#nota_hastau").val().trim();

        if (pe_nombre == "") {
            $("#mensaje8").html("Debe ingresar el nombre del periodo de evaluación...");
            $("#mensaje8").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje8").fadeOut();
        }

        if (pe_abreviatura == "") {
            $("#mensaje9").html("Debe ingresar la abreviatura del periodo de evaluación...");
            $("#mensaje9").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje9").fadeOut();
        }

        if (pe_principal == "") {
            $("#mensaje10").html("Debe seleccionar el tipo de periodo de evaluación...");
            $("#mensaje10").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje10").fadeOut();
        }

        if (pe_principal != 1 && pe_principal != 7 && pe_principal != 8 && pe_ponderacion == "") {
            $("#mensaje11").html("Debe ingresar el valor de la ponderación...");
            $("#mensaje11").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje11").fadeOut();
        }

        if (pe_principal != 1 && pe_principal != 7 && pe_principal != 8 && nota_desde == "") {
            $("#mensaje13").html("Debe ingresar el valor de la nota desde...");
            $("#mensaje13").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje13").fadeOut();
        }

        if (pe_principal != 1 && pe_principal != 7 && pe_principal != 8 && nota_hasta == "") {
            $("#mensaje14").html("Debe ingresar el valor de la nota hasta...");
            $("#mensaje14").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje14").fadeOut();
        }

        if (cont_errores == 0) {
            // submit el formulario
            var data = new FormData($("#form_update")[0]);
            console.log(data);

            $.ajax({
                url: "periodos_evaluacion/actualizar_periodo_evaluacion.php",
                method: 'POST',
                data: data,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(r) {
                    cargarPeriodosEvaluacion($("#cboCursos").val());
                    toastr[r.estado](r.mensaje, r.titulo);
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarPeriodoEvaluacionModal").modal('hide');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    function eliminarPeriodoEvaluacion(id) {
        //Elimino el periodo de evaluacion mediante AJAX
        swal({
                title: "¿Está seguro que quiere eliminar el registro?",
                text: "No podrá recuperar el registro que va a ser eliminado!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Sí, elimínelo!"
            },
            function() {
                $.ajax({
                    url: "periodos_evaluacion/eliminar_periodo_evaluacion.php",
                    method: "POST",
                    data: "id_periodo_evaluacion=" + id,
                    dataType: "json",
                    success: function(r) {
                        // console.log(r);
                        cargarPeriodosEvaluacion($("#cboCursos").val());
                        toastr[r.estado](r.mensaje, r.titulo);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Otro manejador error
                        console.log(jqXHR.responseText);
                    }
                });
            });
    }

    function saveNewPositions() {
        var positions = [];
        $('.updated').each(function() {
            positions.push([$(this).attr('data-index'), $(this).attr('data-orden')]);
            $(this).removeClass('updated');
        });
        // console.log(positions);
        $.ajax({
            url: "periodos_evaluacion/saveNewPositions.php",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                //
                cargarPeriodosEvaluacion($("#cboCursos").val());
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
    }
</script>