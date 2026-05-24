<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Rúbricas de Evaluación
            <small>Listado</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border">
                <button id="btn-new" class="btn btn-primary" data-toggle="modal" data-target="#nuevoInsumoEvaluacionModal"><i class="fa fa-plus-circle"></i> Nuevo Registro</button>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <div class="form-group">
                            <label for="cboPeriodosEvaluacion">Periodo de Evaluación:</label>
                            <select name="cboPeriodosEvaluacion" id="cboPeriodosEvaluacion" class="form-control">
                                <option value="0">Seleccione un Periodo de Evaluación...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cboAportesEvaluacion">Aporte de Evaluación:</label>
                            <select name="cboAportesEvaluacion" id="cboAportesEvaluacion" class="form-control">
                                <option value="0">Seleccione un Aporte de Evaluación...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cboTipoAsignatura">Tipo de Asignatura:</label>
                            <select name="cboTipoAsignatura" id="cboTipoAsignatura" class="form-control">
                                <option value="0">Seleccione un Tipo de Asignatura...</option>
                            </select>
                        </div>
                        <div id="text_message" class="text-center"></div>
                        <!-- table -->
                        <table class="table fuente9">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Abreviatura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="insumos_evaluacion">
                                <!-- Aqui desplegamos el contenido de la base de datos -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once "modalInsertar.php" ?>
<?php require_once "modalActualizar.php" ?>

<script>
    $(document).ready(function() {
        // JQuery Listo para utilizar
        $("#btn-new").attr("disabled", true);
        $("#cboAportesEvaluacion").attr("disabled", true);
        $("#cboTipoAsignatura").attr("disabled", true);
        cargarPeriodosEvaluacion();
        cargarTiposAsignatura();

        $("#insumos_evaluacion").html("<tr><td colspan='5' align='center'>Debes seleccionar un periodo de evaluacion...</td></tr>");

        $("#cboPeriodosEvaluacion").change(function(e) {
            // Código para recuperar los aportes de evaluación asociados al período de evaluación seleccionado
            if ($(this).val() == 0) {
                $("#cboAportesEvaluacion").attr("disabled", true);
                $("#cboTipoAsignatura").attr("disabled", true);
            } else {
                cargarAportesEvaluacion();
                $("#cboAportesEvaluacion").attr("disabled", false);
                $("#cboTipoAsignatura").attr("disabled", false);
            }
        });

        $("#cboAportesEvaluacion").change(function(e) {
            if ($("#cboTipoAsignatura").val() != 0) {
                listarRubricasEvaluacion();
            }
        });

        $("#cboTipoAsignatura").change(function(e) {
            // Código para recuperar las rúbricas de evaluación asociadas al aporte de evaluación seleccionado
            listarRubricasEvaluacion();
        });
    });

    function cargarPeriodosEvaluacion() {
        $.get("scripts/cargar_periodos_evaluacion.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboPeriodosEvaluacion").append(resultado);
                }
            }
        );
    }

    function cargarTiposAsignatura() {
        $.get("scripts/cargar_tipos_asignatura.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboTipoAsignatura").append(resultado);
                }
            }
        );
    }

    function cargarAportesEvaluacion() {
        var id = $("#cboPeriodosEvaluacion").val();
        document.getElementById("cboAportesEvaluacion").length = 0;
        $('#cboAportesEvaluacion').append('<option value="0">Seleccione...</option>');
        $("#insumos_evaluacion").html("<tr><td colspan='5' align='center'>Debes seleccionar un aporte de evaluacion...</td></tr>");
        $("#btn-new").attr("disabled", true);
        if (id == 0) {
            $("#insumos_evaluacion").html("<tr><td colspan='5' align='center'>Debes seleccionar un periodo de evaluacion...</td></tr>");
        } else {
            $.get("scripts/cargar_aportes_evaluacion.php", {
                    id_periodo_evaluacion: id
                },
                function(resultado) {
                    if (resultado == false) {
                        alert("Error");
                    } else {
                        $("#cboAportesEvaluacion").append(resultado);
                    }
                }
            );
        }
    }

    function listarRubricasEvaluacion() {
        var id_aporte = $("#cboAportesEvaluacion").val();
        var id_tipo = $("#cboTipoAsignatura").val();
        if (id_aporte == 0) {
            $("#insumos_evaluacion").html("<tr><td colspan='5' align='center'>Debes seleccionar un aporte de evaluacion...</td></tr>");
            $("#btn-new").attr("disabled", true);
        } else if (id_tipo == 0) {
            $("#insumos_evaluacion").html("<tr><td colspan='5' align='center'>Debes seleccionar un tipo de asignatura...</td></tr>");
            $("#btn-new").attr("disabled", true);
        } else {
            $("#btn-new").attr("disabled", false);
            $.post("rubricas_evaluacion/cargar_rubricas.php", {
                    id_aporte_evaluacion: id_aporte,
                    id_tipo_asignatura: id_tipo
                },
                function(resultado) {
                    if (resultado == false) {
                        alert("Error");
                    } else {
                        $("#insumos_evaluacion").html(resultado);
                    }
                }
            );
        }
    }

    function insertarInsumoEvaluacion() {
        var id_aporte = $("#cboAportesEvaluacion").val();
        var id_tipo = $("#cboTipoAsignatura").val();
        var ru_nombre = $("#new_ru_nombre").val();
        var ru_abreviatura = $("#new_ru_abreviatura").val();
        var ru_descripcion = $("#new_ru_descripcion").val();
        var ru_ponderacion = $("#new_ru_ponderacion").val();
        var cont_errores = 0;

        if (ru_nombre.trim() == "") {
            $("#mensaje1").html("Debes ingresar el nombre de la rubrica de evaluacion...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (ru_abreviatura.trim() == "") {
            $("#mensaje2").html("Debes ingresar la abreviatura de la rubrica de evaluacion...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (ru_descripcion.trim() == "") {
            $("#mensaje3").html("Debes ingresar la descripcion de la rubrica de evaluacion...");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (ru_ponderacion.trim() == "") {
            $("#mensaje4").html("Debes ingresar la ponderacion de la rubrica de evaluacion...");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "rubricas_evaluacion/insertar_rubrica.php",
                type: "POST",
                data: {
                    id_aporte_evaluacion: id_aporte,
                    id_tipo_asignatura: id_tipo,
                    ru_nombre: ru_nombre,
                    ru_abreviatura: ru_abreviatura,
                    ru_ponderacion: ru_ponderacion,
                    ru_descripcion: ru_descripcion
                },
                dataType: "json",
                success: function(r) {
                    listarRubricasEvaluacion();
                    $('#nuevoInsumoEvaluacionModal').modal('hide');
                    $("#form_insert")[0].reset();
                    toastr[r.estado](r.mensaje, r.titulo);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    function obtenerDatos(id) {
        //Obtengo los datos de la rubrica de evaluación seleccionada
        $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando'>");
        $.ajax({
            url: "rubricas_evaluacion/obtener_rubrica.php",
            method: "POST",
            type: "html",
            data: {
                id_rubrica: id
            },
            success: function(response) {
                $("#text_message").html("");
                $("#id_rubrica_evaluacion").val(id);
                var rubrica = jQuery.parseJSON(response);
                $("#edit_ru_nombre").val(rubrica.ru_nombre);
                $("#edit_ru_abreviatura").val(rubrica.ru_abreviatura);
                $("#edit_ru_descripcion").val(rubrica.ru_descripcion);
                $("#edit_ru_ponderacion").val(rubrica.ru_ponderacion);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function actualizarInsumoEvaluacion() {
        var id = $("#id_rubrica_evaluacion").val();
        var id_aporte = $("#cboAportesEvaluacion").val();
        var nombre = $("#edit_ru_nombre").val();
        var abreviatura = $("#edit_ru_abreviatura").val();
        var ponderacion = $("#edit_ru_ponderacion").val();
        var descripcion = $("#edit_ru_descripcion").val();
        $.ajax({
            url: "rubricas_evaluacion/actualizar_rubrica.php",
            type: "POST",
            dataType: "json",
            data: {
                id_rubrica_evaluacion: id,
                id_aporte_evaluacion: id_aporte,
                ru_nombre: nombre,
                ru_abreviatura: abreviatura,
                ru_ponderacion: ponderacion,
                ru_descripcion: descripcion
            },
            success: function(r) {
                listarRubricasEvaluacion();
                $('#editarInsumoEvaluacionModal').modal('hide');
                $("#form_update")[0].reset();
                toastr[r.estado](r.mensaje, r.titulo);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function eliminarInsumoEvaluacion(id) {
        Swal.fire({
            title: "¿Está seguro que quiere eliminar el registro?",
            text: "No podrá recuperar el registro que va a ser eliminado!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, elimínelo!",
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "post",
                    url: "rubricas_evaluacion/eliminar_rubrica_evaluacion.php",
                    data: {
                        id_rubrica_evaluacion: id
                    },
                    dataType: "json",
                    success: function(data) {
                        Swal.fire({
                            title: data.titulo,
                            text: data.mensaje,
                            icon: data.estado,
                            confirmButtonText: 'Aceptar'
                        });

                        listarRubricasEvaluacion();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }
</script>