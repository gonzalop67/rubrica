<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- <section class="content-header">
        <h1>
            Preguntas
            <small>Seleccione la categoría para añadir y editar preguntas</small>
        </h1>
    </section> -->

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-body">
                <div class="row">
                    <form class="form-horizontal">
                        <div class="row">
                            <label for="cboCategorias" class="col-sm-4 col-md-2 col-lg-1 control-label text-right">Categorias:</label>
                            <div class="form-group">
                                <div class="col-sm-4 col-md-4 col-lg-3">
                                    <select id="cboCategorias" class="form-control fuente9">
                                        <option value=""> Seleccione... </option>
                                    </select>
                                    <span id="mensaje1" style="color: #e73d4a"></span>
                                </div>
                                <div class="col-sm-4 col-md-4 col-lg-3">
                                    <span id="btn_nueva_pregunta" class="btn btn-primary fuente10" style="display: none" data-toggle="modal" data-target="#nuevaPreguntaModal"><i class="fa fa-plus-circle"></i> Nueva Pregunta</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-sm-6 col-md-6 col-lg-5">
                                    <label class="col-sm-6 col-md-6 col-lg-6 text-right">&nbsp;N&uacute;mero de Preguntas encontradas:&nbsp;</label>
                                    <label id="numero_preguntas" class="col-sm-6 col-md-6 col-lg-6 text-left"></label>
                                    <input type="hidden" id="numero_asignaturas" value="">
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-6 fuente8" style="margin-top: -20px;">
                                    <div id="paginacion_preguntas">
                                        <ul id="pagination" class="pagination">
                                            <!-- Aqui va la paginacion de preguntas -->
                                        </ul>
                                    </div>
                                    <input type="hidden" id="pagina_actual" value="1">
                                </div>
                            </div>
                        </div>
                        <div class="row col-sm-12 col-md-12 col-lg-12" style="margin-left: 0px; margin-top: -20px">
                            <div class="header2" style="font-size: 9pt;"> LISTA DE PREGUNTAS ASOCIADAS </div>
                            <div class="table-responsive">
                                <table class="table fuente9">
                                    <thead class="thead-dark fuente9">
                                        <th>Nro.</th>
                                        <th>Pregunta</th>
                                        <th>Acciones</th>
                                    </thead>
                                    <tbody id="tbody_questions">
                                        <!-- Aquí irán los datos poblados desde la base de datos mediante AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="box box-solid">
            <div id="pag_nomina_estudiantes">
                <!-- Aqui va la paginacion de las alternativas encontradas -->
                <div id="total_registros_alternativas" class="paginacion" style="height:25px;">
                    <table class="fuente10" width="100%" cellspacing=4 cellpadding=0 style="border:none">
                        <tr>
                            <td>
                                <div id="pregunta" style="margin-left: 5px;"></div>
                            </td>
                            <td>
                                <div style="text-align: right; margin-right: 2px;">
                                    <button id="btn_nueva_alternativa" type="button" id="btn-new-choice" class="btn btn-success btn-xs" style="display: none;" data-toggle="modal" data-target="#nuevaAlternativaModal" title="Nueva Alternativa">Nueva Alternativa</button>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="tituloAlternativas" class="header2" style="font-size: 9pt;">LISTA DE ALTERNATIVAS</div>
                <div class="table-responsive">
                    <table class="table fuente9">
                        <thead class="thead-dark fuente9">
                            <th>Nro.</th>
                            <th>Alternativa</th>
                            <th>¿Es correcta?</th>
                            <th>Acciones</th>
                        </thead>
                        <tbody id="tbody_choices">
                            <!-- Aquí irán los datos poblados desde la base de datos mediante AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once "modalInsertarPregunta.php" ?>
<?php include_once "modalEditarPregunta.php" ?>

<?php include_once "modalInsertarAlternativa.php" ?>
<?php include_once "modalEditarAlternativa.php" ?>

<script>
    $(document).ready(function() {
        cargarCategorias();

        $("#cboCategorias").change(function() {
            let id_categoria = $(this).val();
            if (id_categoria === "") {
                $("#btn_nueva_pregunta").hide();
                $("#btn_nueva_alternativa").hide();
            } else {
                $("#btn_nueva_pregunta").show();
                $("#btn_nueva_alternativa").hide();
                $("#tbody_choices").html("");
                $("#pregunta").html("");
                contarPreguntas()
            }
        });

        $('#tbody_questions').on('click', '.item-delete', function() {
            var id_question = $(this).attr('data');
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
                        url: "preguntas/eliminar_pregunta.php",
                        data: {
                            id_question: id_question,
                            id_category: $("#cboCategorias").val()
                        },
                        method: "post",
                        dataType: "json",
                        success: function(response) {
                            // console.log(data);
                            toastr[response.tipo_mensaje](response.mensaje, response.titulo);

                            contarPreguntas();
                        },
                        error: function(jqXHR, textStatus) {
                            alert(jqXHR.responseText);
                        }
                    });
                });
        });

        $('#tbody_choices').on('click', '.item-delete', function() {
            let id_choice = $(this).attr('data');
            let id_question = $("#id_question").val();

            Swal.fire({
                title: "¿Estás seguro de eliminar este registro?",
                text: "¡Una vez eliminado no podrá recuperarse!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, ¡elimínelo!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "preguntas/eliminar_alternativa.php",
                        data: {
                            id_choice: id_choice
                        },
                        method: "post",
                        dataType: "json",
                        success: function(r) {
                            // console.log(r);
                            listarAlternativas(id_question);
                            toastr[r.tipo_mensaje](r.mensaje, r.titulo);
                        },
                        error: function(jqXHR, textStatus) {
                            alert(jqXHR.responseText);
                        }
                    });
                }
            });

            /*swal({
                    title: "¿Está seguro que quiere eliminar el registro?",
                    text: "No podrá recuperar el registro que va a ser eliminado!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Sí, elimínelo!"
                },
                function() {
                    $.ajax({
                        url: "preguntas/eliminar_alternativa.php",
                        data: {
                            id_choice: id_choice
                        },
                        method: "post",
                        dataType: "json",
                        success: function(r) {
                            // console.log(r);
                            listarAlternativas(id_question);
                            toastr[r.tipo_mensaje](r.mensaje, r.titulo);
                        },
                        error: function(jqXHR, textStatus) {
                            alert(jqXHR.responseText);
                        }
                    });
                });*/
        });
    });

    function cargarCategorias() {
        var id_usuario = document.getElementById("id_usuario").value;
        document.getElementById("cboCategorias").options.length = 1;
        $.post("preguntas/obtener_categorias.php", {
                id_usuario: id_usuario
            },
            function(resultado) {
                if (resultado == false) {
                    alert(resultado);
                } else {
                    $("#cboCategorias").append(resultado);
                }
            }
        );
    }

    function contarPreguntas() {
        $.post("preguntas/contar_preguntas_categoria.php", {
                id_category: $("#cboCategorias").val()
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    var JSONNumPreguntas = eval('(' + resultado + ')');
                    var total_registros = JSONNumPreguntas.num_registros;
                    $("#numero_preguntas").html(total_registros);
                    //Listar las preguntas del cuestionario
                    let pagina = $("#pagina_actual").val();
                    paginarPreguntas(pagina);
                }
            }
        );
    }

    function paginarPreguntas(partida) {
        $("#pagina_actual").val(partida);
        $.ajax({
            type: 'POST',
            url: "preguntas/paginar_preguntas.php",
            data: {
                id_categoria: $("#cboCategorias").val(),
                partida: partida
            },
            success: function(data) {
                var array = eval(data);
                $("#tbody_questions").html(array[0]);
                $("#pagination").html(array[1]);
            }
        });
    }

    function insertarPregunta() {
        let cont_errores = 0;
        let question = $("#question").val().trim();
        let id_category = $("#cboCategorias").val();

        if (question == "") {
            $("#mensaje1").html("Debe ingresar el texto de la pregunta...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "preguntas/insertar_pregunta.php",
                data: {
                    question: question,
                    id_category: id_category
                },
                dataType: "json",
                success: function(r) {
                    contarPreguntas();
                    //swal(r.titulo, r.mensaje, r.estado);
                    Swal.fire({
                        icon: r.estado,
                        title: r.titulo,
                        text: r.mensaje
                    });
                    $('#form_insert_question')[0].reset(); //limpiar formulario
                    $("#nuevaPreguntaModal").modal('hide');
                    $("#pregunta").html("");
                    $("#tbody_choices").html("");
                }
            });
        }

        return false;
    }

    function htmlEntities(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function obtenerPregunta(id) {
        $.ajax({
            url: "preguntas/obtener_pregunta.php",
            type: "POST",
            data: "id=" + id,
            dataType: "json",
            success: function(r) {
                // console.log(r);
                $("#id_question").val(r.id);
                $("#question_u").html(htmlEntities(r.question));
            }
        });
    }

    function actualizarPregunta() {
        let cont_errores = 0;
        let id_question = $("#id_question").val();
        let question = document.getElementById("question_u").value;
        let id_category = $("#cboCategorias").val();

        question = question.trim();

        if (question == "") {
            $("#mensaje2").html("Debe ingresar el texto de la pregunta...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "preguntas/actualizar_pregunta.php",
                data: {
                    id_question: id_question,
                    question: question,
                    id_category: id_category
                },
                dataType: "json",
                success: function(r) {
                    console.log(r);
                    contarPreguntas();
                    // swal(r.titulo, r.mensaje, r.estado);
                    Swal.fire({
                        icon: r.estado,
                        title: r.titulo,
                        text: r.mensaje
                    });
                    $('#form_update_question')[0].reset(); //limpiar formulario
                    $("#editarPreguntaModal").modal('hide');
                }
            });
        }

        return false;
    }

    function seleccionarPregunta(id_pregunta, num_pregunta) {
        $("#btn_nueva_alternativa").show();
        $("#id_question").val(id_pregunta);
        // $("#pregunta").html("Pregunta " + num_pregunta + ": " + htmlEntities(pregunta));
        listarAlternativas(id_pregunta);
    }

    function listarAlternativas(id_pregunta) {
        $.ajax({
            type: 'POST',
            url: "preguntas/listar_alternativas.php",
            data: {
                id_pregunta: id_pregunta
            },
            success: function(data) {
                console.log(data);
                $("#tbody_choices").html(data);
            }
        });
    }

    function insertarAlternativa() {
        let cont_errores = 0;
        let choice = $("#choice").val().trim();
        let id_question = $("#id_question").val();

        if (choice == "") {
            $("#mensaje3").html("Debe ingresar el texto de la alternativa...");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "preguntas/insertar_alternativa.php",
                data: {
                    choice: choice,
                    id_question: id_question
                },
                dataType: "json",
                success: function(r) {
                    listarAlternativas(id_question);
                    // swal(r.titulo, r.mensaje, r.estado);
                    Swal.fire({
                        icon: r.estado,
                        title: r.titulo,
                        text: r.mensaje
                    });
                    $('#form_insert_choice')[0].reset(); //limpiar formulario
                    $("#nuevaAlternativaModal").modal('hide');
                }
            });
        }

        return false;
    }

    function actualizarAlternativa() {
        let cont_errores = 0;
        let id = $("#id_choice").val();
        let choice = document.getElementById("choice_u").value;
        let id_question = $("#id_question").val();

        choice = choice.trim();

        if (choice == "") {
            $("#mensaje4").html("Debe ingresar el texto de la alternativa...");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "preguntas/actualizar_alternativa.php",
                data: {
                    id: id,
                    choice: choice,
                    id_question: id_question
                },
                dataType: "json",
                success: function(r) {
                    console.log(r);
                    listarAlternativas(id_question);
                    swal(r.titulo, r.mensaje, r.estado);
                    $('#form_update_choice')[0].reset(); //limpiar formulario
                    $("#editarAlternativaModal").modal('hide');
                }
            });
        }

        return false;
    }

    function set_correct_answer(obj, id_alternativa, id_pregunta) {
        is_correct = (obj.checked) ? "1" : "0";
        $.ajax({
            type: "POST",
            url: "preguntas/set_correct_answer.php",
            data: {
                id_alternativa: id_alternativa,
                is_correct: is_correct,
                id_pregunta: id_pregunta
            },
            success: function(resultado) {
                console.log(resultado);
                // No desplega nada... esto es solo para ejecutar el codigo php
            }
        });
    }

    function obtenerAlternativa(id) {
        $.ajax({
            url: "preguntas/obtener_alternativa.php",
            type: "POST",
            data: "id=" + id,
            dataType: "json",
            success: function(r) {
                // console.log(r);
                $("#id_choice").val(r.id);
                $("#choice_u").html(r.text);
            }
        });
    }
</script>