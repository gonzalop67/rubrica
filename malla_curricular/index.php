<div class="content-wrapper">
    <div id="mallaApp" class="col-sm-12">
        <br>
        <input type="hidden" id="id_malla_curricular">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Malla Curricular</h4>
            </div>
            <div class="panel-body">
                <form id="form_malla" action="" class="app-form">
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
                            <label class="control-label" style="position:relative; top:7px;">Asignatura:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 2px;">
                            <select class="form-control fuente9" id="cboAsignaturas">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Presenciales:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="number" min="0" class="form-control fuente9" id="horas_presenciales" value="0" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                        </div>
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Autónomas:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="number" min="0" class="form-control fuente9" id="horas_autonomas" value="0" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                        </div>
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Tutorías:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="number" min="0" class="form-control fuente9" id="horas_tutorias" value="0" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 2px;">
                            <span class="help-desk error" id="mensaje3"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 2px;">
                            <span class="help-desk error" id="mensaje4"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 2px;">
                            <span class="help-desk error" id="mensaje5"></span>
                        </div>
                    </div>
                    <div class="row" id="botones_insercion">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="button" class="btn btn-block btn-primary" onclick="insertarItemMalla()">
                                Añadir
                            </button>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 4px;" id="botones_edicion">
                        <div class="col-sm-6">
                            <button id="btn-cancel" type="button" class="btn btn-block" onclick="cancelarEdicion()">
                                Cancelar
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <button id="btn-update" type="button" class="btn btn-block btn-primary" onclick="actualizarItemMalla()">
                                Actualizar
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
                            <th>#</th>
                            <th>Id</th>
                            <th>Asignatura</th>
                            <th>Curso</th>
                            <th>Presencial</th>
                            <th>Autónomo</th>
                            <th>Tutoría</th>
                            <th colspan="2" style="text-align:center">Acciones</th>
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
                        <label class="control-label" style="position:relative; top:7px;">Total Horas:</label>
                    </div>
                    <div class="col-sm-2" style="margin-top: 2px;">
                        <input type="text" class="form-control fuente9 text-right" id="total_horas" value="0" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "modalActualizar.php" ?>

<script>
    $(document).ready(function() {
        $("#botones_edicion").hide();
        cargarCursos();
        $("#cboCursos").change(function(e) {
            e.preventDefault();
            $("#text_message").html("");
            cargarAsignaturas($(this).val());
            listarMalla();
        })
    });

    function sel_texto(input) {
        $(input).select();
    }

    function cancelarEdicion() {
        $("#botones_edicion").hide();
        $("#botones_insercion").show();
        $("#cboCursos").attr("disabled", false);
        $("#cboAsignaturas").attr("disabled", false);
    }

    function editarMalla(id_malla) {
        $("#id_malla_curricular").val(id_malla);
        $("#cboCursos").attr("disabled", true);
        $("#cboAsignaturas").attr("disabled", true);
        $("#botones_insercion").hide();
        $("#botones_edicion").show();
        // Primero obtengo los datos del item elegido
        $.ajax({
            url: "malla_curricular/obtener_item_malla.php",
            method: "POST",
            type: "html",
            data: {
                id_malla_curricular: id_malla
            },
            success: function(response) {
                var malla = jQuery.parseJSON(response);
                $("#horas_presenciales").val(malla.ma_horas_presenciales);
                $("#horas_autonomas").val(malla.ma_horas_autonomas);
                $("#horas_tutorias").val(malla.ma_horas_tutorias);
                // Procedimiento para "setear" el índice de cboAsignaturas
                var id_asignatura = malla.id_asignatura;
                var sel = document.getElementById("cboAsignaturas");
                for (var i = 0; i < sel.length; i++) {
                    //console.log(sel[i].value+"\n");
                    if (sel[i].value == id_asignatura) {
                        document.getElementById("cboAsignaturas").selectedIndex = i;
                    }
                }
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function insertarItemMalla() {
        // Recolección de datos
        var cont_errores = 0;
        var id_curso = $("#cboCursos").val();
        var id_asignatura = $("#cboAsignaturas").val();
        var presenciales = $("#horas_presenciales").val();
        var autonomas = $("#horas_autonomas").val();
        var tutorias = $("#horas_tutorias").val();

        // Validación de ingreso de datos
        if (id_curso == 0) {
            $("#mensaje1").html("Debe elegir el curso...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut("slow");
        }

        if (id_asignatura == 0) {
            $("#mensaje2").html("Debe elegir la asignatura...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut("slow");
        }

        if (presenciales.trim() == "") {
            $("#mensaje3").html("Debe ingresar el número de horas presenciales.");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else if (parseInt(presenciales) < 0) {
            $("#mensaje3").html("Debe ingresar un valor entero mayor que cero! para el número de horas presenciales.");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (autonomas.trim() == "") {
            $("#mensaje4").html("Debe ingresar el número de horas autónomas.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else if (parseInt(autonomas) < 0) {
            $("#mensaje4").html("Debe ingresar un valor entero mayor o igual que cero! para el número de horas autónomas.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (tutorias.trim() == "") {
            $("#mensaje5").html("Debe ingresar el número de horas de tutorías.");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else if (parseInt(tutorias) < 0) {
            $("#mensaje5").html("Debe ingresar un valor entero mayor o igual que cero! para el número de horas de tutorías.");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje5").fadeOut();
        }

        if (cont_errores == 0) {
            // Se procede a la inserción del item de la malla
            $.ajax({
                url: "malla_curricular/insertar_item_malla.php",
                method: "POST",
                type: "html",
                data: {
                    id_curso: id_curso,
                    id_asignatura: id_asignatura,
                    ma_horas_presenciales: presenciales,
                    ma_horas_autonomas: autonomas,
                    ma_horas_tutorias: tutorias
                },
                success: function(response) {
                    listarMalla();
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    function obtenerDatos(id) {
        $("#id_malla_curricular").val(id);
        $.ajax({
            url: "malla_curricular/obtener_item_malla.php",
            type: "POST",
            data: "id_malla_curricular=" + id,
            success: function(result) {
                console.log(result);
                var r = JSON.parse(result);
                $("#ma_horas_presenciales").val(r.ma_horas_presenciales);
                $("#ma_horas_autonomas").val(r.ma_horas_autonomas);
                $("#ma_horas_tutorias").val(r.ma_horas_tutorias);
            }
        });
    }

    $('#form_update').submit(function(e) {
        e.preventDefault();

        let id_malla = $("#id_malla_curricular").val();
        let presenciales = $("#ma_horas_presenciales").val();
        let autonomas = $("#ma_horas_autonomas").val();
        let tutorias = $("#ma_horas_tutorias").val();

        $.ajax({
            url: "malla_curricular/actualizar_item_malla.php",
            data: {
                id_malla_curricular: id_malla,
                ma_horas_presenciales: presenciales,
                ma_horas_autonomas: autonomas,
                ma_horas_tutorias: tutorias
            },
            type: "POST",
            dataType: "html",
            success: function(response) {
                listarMalla();
                $('#form_update')[0].reset(); //limpiar formulario
                $("#editarItemMallaModal").modal('hide');
                $("#text_message").html(response);
            }
        });
    });

    function actualizarItemMalla() {
        // Recolección de datos
        var cont_errores = 0;
        var id_malla = $("#id_malla_curricular").val();
        var id_curso = $("#cboCursos").val();
        var id_asignatura = $("#cboAsignaturas").val();
        var presenciales = $("#horas_presenciales").val();
        var autonomas = $("#horas_autonomas").val();
        var tutorias = $("#horas_tutorias").val();

        // Validación de ingreso de datos
        if (id_curso == 0) {
            $("#mensaje1").html("Debe elegir el curso...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut("slow");
        }

        if (id_asignatura == 0) {
            $("#mensaje2").html("Debe elegir la asignatura...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut("slow");
        }

        if (presenciales.trim() == "") {
            $("#mensaje3").html("Debe ingresar el número de horas presenciales.");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else if (parseInt(presenciales) < 0) {
            $("#mensaje3").html("Debe ingresar un valor entero mayor que cero! para el número de horas presenciales.");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (autonomas.trim() == "") {
            $("#mensaje4").html("Debe ingresar el número de horas autónomas.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else if (parseInt(autonomas) < 0) {
            $("#mensaje4").html("Debe ingresar un valor entero mayor o igual que cero! para el número de horas autónomas.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (tutorias.trim() == "") {
            $("#mensaje5").html("Debe ingresar el número de horas de tutorías.");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else if (parseInt(tutorias) < 0) {
            $("#mensaje5").html("Debe ingresar un valor entero mayor o igual que cero! para el número de horas de tutorías.");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje5").fadeOut();
        }

        if (cont_errores == 0) {
            // Se procede a la inserción del item de la malla
            $.ajax({
                url: "malla_curricular/actualizar_item_malla.php",
                method: "POST",
                type: "html",
                data: {
                    id_malla_curricular: id_malla,
                    id_curso: id_curso,
                    id_asignatura: id_asignatura,
                    ma_horas_presenciales: presenciales,
                    ma_horas_autonomas: autonomas,
                    ma_horas_tutorias: tutorias
                },
                success: function(response) {
                    listarMalla();
                    cancelarEdicion();
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    function eliminarMalla(id_malla) {
        //Elimino el item de la malla mediante AJAX
        $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
        $.ajax({
            url: "malla_curricular/eliminar_item_malla.php",
            method: "POST",
            type: "html",
            data: {
                id_malla_curricular: id_malla
            },
            success: function(response) {
                $("#text_message").html(response);
                listarMalla();
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function listarMalla() {
        var id_curso = $("#cboCursos").val();
        if (id_curso == 0) {
            $("#lista_items").html("<tr><td colspan='8' align='center'>Debes seleccionar un curso...</td></tr>");
        } else {
            $.post("malla_curricular/listar_malla.php", {
                    id_curso: id_curso
                },
                function(resultado) {
                    if (resultado == false) {
                        alert("Error");
                    } else {
                        var datos = JSON.parse(resultado);
                        $("#lista_items").html(datos.cadena);
                        $("#total_horas").val(datos.total_horas);
                    }
                }
            );
        }
    }

    function cargarCursos() {
        $.get("scripts/cargar_cursos.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboCursos").append(resultado);
                }
            }
        );
    }

    function cargarAsignaturas(id_curso) {
        $.ajax({
            url: "scripts/cargar_asignaturas_por_curso.php",
            method: "POST",
            type: "html",
            data: {
                id_curso: id_curso
            },
            success: function(response) {
                document.getElementById("cboAsignaturas").length = 0;
                $("#cboAsignaturas").append("<option value='0'>Seleccione...</option>");
                $("#cboAsignaturas").append(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
</script>