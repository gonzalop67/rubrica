<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Aportes de Evaluación
            <small>Listado</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border">
                <button id="btn-new" class="btn btn-primary" data-toggle="modal" data-target="#nuevoAporteEvaluacionModal"><i class="fa fa-plus-circle"></i> Nuevo Registro</button>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <div class="form-group">
                            <!-- <label for="cboPeriodosEvaluacion">Modalidad:</label> -->
                            <select name="cboPeriodosEvaluacion" id="cboPeriodosEvaluacion" class="form-control">
                                <option value="0">Seleccione un periodo de evaluación...</option>
                            </select>
                        </div>
                        <div id="text_message" class="text-center"></div>
                        <!-- table -->
                        <table class="table fuente9">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Ponderación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="aportes_evaluacion">
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
        $("#btn-new").attr("disabled", "true");
        cargarPeriodosEvaluacion();
        cargarTiposAporte();
        $("#cboPeriodosEvaluacion").change(function(e) {
            // Código para recuperar los aportes de evaluación asociados al período de evaluación seleccionado
            listarAportesEvaluacion();
        });
        $("#new_ap_fecha_apertura").datepicker({
            dateFormat: 'yy-mm-dd',
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            firstDay: 1
        });
        $("#new_ap_fecha_cierre").datepicker({
            dateFormat: 'yy-mm-dd',
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            firstDay: 1
        });
        $("#edit_ap_fecha_apertura").datepicker({
            dateFormat: 'yy-mm-dd',
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            firstDay: 1
        });
        $("#edit_ap_fecha_cierre").datepicker({
            dateFormat: 'yy-mm-dd',
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            firstDay: 1
        });
        $("#aportes_evaluacion").html("<tr><td colspan='5' align='center'>Debes seleccionar un periodo de evaluacion...</td></tr>");

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

    function setearIndice(nombreCombo, indice) {
        for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
            if (document.getElementById(nombreCombo).options[i].value == indice) {
                document.getElementById(nombreCombo).options[i].selected = indice;
            }
    }

    function sel_texto(input) {
        $(input).select();
    }

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

    function cargarTiposAporte() {
        $.get("scripts/cargar_tipos_aporte_evaluacion.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error: No existen registros...");
                } else {
                    $("#new_ap_tipo").append(resultado);
                    $("#edit_ap_tipo").append(resultado);
                }
            }
        );
    }

    function listarAportesEvaluacion() {
        var id = document.getElementById("cboPeriodosEvaluacion").value;
        if (id == 0) {
            $("#aportes_evaluacion").html("<tr><td colspan='5' align='center'>Debes seleccionar un per&iacute;odo de evaluaci&oacute;n...</td></tr>");
            $("#btn-new").attr("disabled", true);
        } else {
            $.post("aportes_evaluacion/cargar_aportes_evaluacion.php", {
                    id_periodo_evaluacion: id
                },
                function(resultado) {
                    if (resultado == false) {
                        // alert("Error");
                        Swal.fire({
                            title: Error,
                            text: "No se han definido Periodos de Evaluación...",
                            icon: "error"
                        });
                    } else {
                        $("#btn-new").attr("disabled", false);
                        $("#aportes_evaluacion").html(resultado);
                    }
                }
            );
        }
    }

    function eliminarPeriodoEvaluacion(id) {
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
                    url: "aportes_evaluacion/eliminar_aporte_evaluacion.php",
                    data: {
                        id_aporte_evaluacion: id
                    },
                    dataType: "json",
                    success: function(r) {
                        if (r.estado == "success") {
                            toastr[r.estado](r.mensaje, r.titulo);
                            listarAportesEvaluacion();
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }

    function obtenerDatos(id) {
        //Obtengo los datos del aporte de evaluación seleccionado
        $("#text_message").html("<img src='./imagenes/ajax-loader.gif' alt='procesando'>");
        $.ajax({
            url: "aportes_evaluacion/obtener_aporte_evaluacion.php",
            method: "POST",
            type: "html",
            data: {
                id_aporte_evaluacion: id
            },
            success: function(response) {
                console.log(response);
                $("#text_message").html("");
                $("#id_aporte_evaluacion").val(id);
                var aporte = jQuery.parseJSON(response);
                $("#edit_ap_nombre").val(aporte.ap_nombre);
                $("#edit_ap_abreviatura").val(aporte.ap_abreviatura);
                $("#edit_ap_descripcion").val(aporte.ap_descripcion);
                setearIndice("edit_ap_tipo", aporte.id_tipo_aporte);
                $('#edit_ap_fecha_apertura').val(aporte.ap_fecha_apertura);
                $('#edit_ap_fecha_cierre').val(aporte.ap_fecha_cierre);
                $('#edit_ap_ponderacion').val(aporte.ap_ponderacion);
                $('#editarAporteEvaluacionModal').modal('show');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function actualizarAporteEvaluacion() {
        let cont_errores = 0;
        var id = $("#id_aporte_evaluacion").val();
        var ap_nombre = $("#edit_ap_nombre").val();
        var ap_abreviatura = $("#edit_ap_abreviatura").val();
        var ap_descripcion = $("#edit_ap_descripcion").val();
        var ap_tipo = $("#edit_ap_tipo").val();
        var ap_fecha_apertura = $("#edit_ap_fecha_apertura").val();
        var ap_fecha_cierre = $("#edit_ap_fecha_cierre").val();
        var ap_ponderacion = $("#edit_ap_ponderacion").val();
        var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();

        if (ap_nombre == "") {
            $("#mensaje8").html("Debe ingresar el nombre del aporte de evaluación...");
            $("#mensaje8").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje8").fadeOut();
        }

        if (ap_abreviatura == "") {
            $("#mensaje9").html("Debe ingresar la abreviatura del aporte de evaluación...");
            $("#mensaje9").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje9").fadeOut();
        }

        if (ap_descripcion == "") {
            $("#mensaje10").html("Debe ingresar la descripción del aporte de evaluación...");
            $("#mensaje10").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje10").fadeOut();
        }

        if (ap_tipo == "") {
            $("#mensaje11").html("Debe seleccionar el tipo del aporte de evaluación...");
            $("#mensaje11").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje11").fadeOut();
        }

        if (ap_ponderacion == "") {
            $("#mensaje12").html("Debe ingresar la ponderación del aporte de evaluación...");
            $("#mensaje12").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje12").fadeOut();
        }

        if (ap_fecha_apertura == "") {
            $("#mensaje13").html("Debe ingresar la fecha de apertura del aporte de evaluación...");
            $("#mensaje13").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje13").fadeOut();
        }

        if (ap_fecha_cierre == "") {
            $("#mensaje14").html("Debe ingresar la fecha de cierre del aporte de evaluación...");
            $("#mensaje14").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje14").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "aportes_evaluacion/actualizar_aporte_evaluacion.php",
                type: "POST",
                dataType: "json",
                data: {
                    id_aporte_evaluacion: id,
                    id_periodo_evaluacion: id_periodo_evaluacion,
                    ap_nombre: ap_nombre,
                    ap_abreviatura: ap_abreviatura,
                    ap_descripcion: ap_descripcion,
                    ap_tipo: ap_tipo,
                    ap_fecha_apertura: ap_fecha_apertura,
                    ap_fecha_cierre: ap_fecha_cierre,
                    ap_ponderacion: ap_ponderacion
                },
                success: function(r) {
                    listarAportesEvaluacion();
                    // swal(r.titulo, r.mensaje, r.estado);
                    // Swal.fire({
                    //     title: r.titulo,
                    //     text: r.mensaje,
                    //     icon: r.estado,
                    //     confirmButtonText: 'Cool'
                    // });
                    toastr[r.estado](r.mensaje, r.titulo);
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarAporteEvaluacionModal").modal('hide');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    function insertarAporteEvaluacion() {
        let cont_errores = 0;
        var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
        var ap_nombre = $("#new_ap_nombre").val().trim();
        var ap_abreviatura = $("#new_ap_abreviatura").val().trim();
        var ap_descripcion = $("#new_ap_descripcion").val().trim();
        var ap_tipo = $("#new_ap_tipo").val();
        var ap_ponderacion = $("#new_ap_ponderacion").val().trim();
        var ap_fecha_apertura = $("#new_ap_fecha_apertura").val().trim();
        var ap_fecha_cierre = $("#new_ap_fecha_cierre").val().trim();

        if (ap_nombre == "") {
            $("#mensaje1").html("Debe ingresar el nombre del aporte de evaluación...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (ap_abreviatura == "") {
            $("#mensaje2").html("Debe ingresar la abreviatura del aporte de evaluación...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (ap_descripcion == "") {
            $("#mensaje3").html("Debe ingresar la descripción del aporte de evaluación...");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (ap_tipo == "") {
            $("#mensaje4").html("Debe seleccionar el tipo del aporte de evaluación...");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (ap_ponderacion == "") {
            $("#mensaje5").html("Debe ingresar la ponderación del aporte de evaluación...");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje5").fadeOut();
        }

        if (ap_fecha_apertura == "") {
            $("#mensaje6").html("Debe ingresar la fecha de apertura del aporte de evaluación...");
            $("#mensaje6").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje6").fadeOut();
        }

        if (ap_fecha_cierre == "") {
            $("#mensaje7").html("Debe ingresar la fecha de cierre del aporte de evaluación...");
            $("#mensaje7").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje7").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "aportes_evaluacion/insertar_aporte_evaluacion.php",
                type: "POST",
                dataType: "json",
                data: {
                    id_periodo_evaluacion: id_periodo_evaluacion,
                    ap_nombre: ap_nombre,
                    ap_abreviatura: ap_abreviatura,
                    ap_descripcion: ap_descripcion,
                    ap_tipo: ap_tipo,
                    ap_ponderacion: ap_ponderacion,
                    ap_fecha_apertura: ap_fecha_apertura,
                    ap_fecha_cierre: ap_fecha_cierre
                },
                success: function(r) {
                    listarAportesEvaluacion();
                    // swal(r.titulo, r.mensaje, r.estado);
                    // Swal.fire({
                    //     title: r.titulo,
                    //     text: r.mensaje,
                    //     icon: r.estado,
                    //     confirmButtonText: 'Cool'
                    // });
                    toastr[r.estado](r.mensaje, r.titulo);
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevoAporteEvaluacionModal").modal('hide');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    function saveNewPositions() {
        var positions = [];
        $('.updated').each(function() {
            positions.push([$(this).attr('data-index'), $(this).attr('data-orden')]);
            $(this).removeClass('updated');
        });
        // console.log(positions);
        $.ajax({
            url: "aportes_evaluacion/saveNewPositions.php",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                //
                listarAportesEvaluacion();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
    }
</script>