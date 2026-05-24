<div class="content-wrapper">
    <div class="col-sm-12">
        <br>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Asociar Periodos de Evaluación con Cursos</h4>
            </div>
            <div class="panel-body">
                <form id="form_insert" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Curso:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboCursos">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Periodo:</label>
                        </div>
                        <div class="col-sm-4" style="margin-top: 2px;">
                            <select class="form-control fuente9" id="cboPeriodosEvaluacion">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Ponderación:</label>
                        </div>
                        <div class="col-sm-4" style="margin-top: 2px;">
                            <input type="number" min="0.01" max="1.00" step="0.01" class="form-control" id="pe_ponderacion" value="0.01" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                            <span id="mensaje3" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="row" id="botones_insercion">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="button" class="btn btn-block btn-primary" onclick="insertarAsociacion()">
                                Asociar
                            </button>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <!-- message -->
                <div id="text_message" class="fuente9 text-center"></div>
                <!-- table -->
                <table class="table fuente9">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Curso</th>
                            <th>Periodo</th>
                            <th>Ponderacion</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista_items">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-10 text-right">
                        <label class="control-label" style="position:relative; top:7px;">Total Periodos:</label>
                    </div>
                    <div class="col-sm-2" style="margin-top: 2px;">
                        <input type="text" class="form-control fuente9 text-right" id="total_periodos" value="0" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        cargar_cursos();
        cargarPeriodosEvaluacion();

        $("#cboCursos").change(function(e) {
            e.preventDefault();
            cargar_periodos_asociados();
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

    function sel_texto(input) {
		$(input).select();
	}

    function cargar_cursos() {
        $.get("scripts/cargar_cursos.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#cboCursos').append(resultado);
            }
        });
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

    function cargar_periodos_asociados() {
        var id_curso = document.getElementById("cboCursos").value;

        $.ajax({
            type: "POST",
            url: "periodos_evaluacion_cursos/cargar_periodos_asociados.php",
            data: "id_curso=" + id_curso,
            success: function(resultado) {
                var datos = JSON.parse(resultado);
                $("#lista_items").html(datos.cadena);
                $("#total_periodos").val(datos.total_periodos);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(errorThrown);
            }
        });
    }

    function insertarAsociacion() {
        var id_curso = document.getElementById("cboCursos").value;
        var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
        var pe_ponderacion = document.getElementById("pe_ponderacion").value;
        var cont_errores = 0;

        if (id_curso == 0) {
            $("#mensaje1").html("Debe seleccionar un curso...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (id_periodo_evaluacion == 0) {
            $("#mensaje2").html("Debe elegir un periodo de evaluación...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (cont_errores == 0) {
            $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
            $.ajax({
                type: "POST",
                url: "periodos_evaluacion_cursos/insertar_asociacion.php",
                data: {
                    id_curso: id_curso,
                    id_periodo_evaluacion: id_periodo_evaluacion,
                    pe_ponderacion: pe_ponderacion
                },
                success: function(resultado) {
                    $("#text_message").html(resultado);
                    cargar_periodos_asociados();
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
            url: "periodos_evaluacion_cursos/saveNewPositions.php",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                //
                cargar_periodos_asociados();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
    }

    function eliminarAsociacion(id_periodo_evaluacion_curso, id_curso) {
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
                    url: "periodos_evaluacion_cursos/eliminar_periodo_evaluacion_curso.php",
                    method: "POST",
                    data: "id_periodo_evaluacion_curso=" + id_periodo_evaluacion_curso,
                    dataType: "json",
                    success: function(r) {
                        // console.log(r);
                        cargar_periodos_asociados();
                        toastr[r.estado](r.mensaje, r.titulo);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Otro manejador error
                        console.log(jqXHR.responseText);
                    }
                });
            });
    }
</script>