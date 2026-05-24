<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Matriculación
            <small>Paralelos</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header">
                <a id="new_student" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newStudentModal"><i class="fa fa-plus-circle"></i> Nuevo Estudiante</a>

                <a id="inactive_students" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deletedStudentModal"><i class="fa fa-gear"></i> Estudiantes Inactivos</a>

                <div id="search_student" class="box-tools fuente9">
                    <form id="form-search" action="matriculacion/buscar_estudiantes_antiguos.php" method="POST">
                        <div class="input-group input-group-sm" style="width: 400px;">
                            <input type="text" name="input_search" id="input_search" class="form-control pull-right text-uppercase fuente9" placeholder="Ingrese Nombre para Histórico del Estudiante...">

                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="form_id" action="" class="form-horizontal">
                            <div class="form-group">
                                <label for="id_paralelo" class="col-sm-1 control-label text-right">Paralelo:</label>

                                <div class="col-sm-11">
                                    <select class="form-control fuente9" name="id_paralelo" id="id_paralelo">
                                        <option value="">Seleccione...</option>
                                    </select>
                                    <span id="mensaje1" style="color: #e73d4a"></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <div id="total_registros_estudiantes" class="paginacion">
                            <table class="fuente8" width="100%" cellspacing=4 cellpadding=0>
                                <tr>
                                    <td>
                                        <div id="contar_estudiantes_por_genero">
                                            <!-- Aqui va el conteo de estudiantes por genero -->
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="t_estudiantes" class="table fuente8">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Id</th>
                                    <th>Mat.</th>
                                    <th>Apellidos</th>
                                    <th>Nombres</th>
                                    <th>DNI</th>
                                    <th>Fec.Nacim.</th>
                                    <th>Edad</th>
                                    <th>Género</th>
                                    <th>Retirado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_estudiantes">
                                <!-- Aquí se pintarán los registros recuperados de la BD mediante AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <form id="formulario_certificado" action="reportes/certificado_matricula.php" method="post" target="_blank">
                    <div id="ver_reporte" style="text-align:left;margin-top:2px;display:none">
                        <input id="id_paralelo_certificado" name="id_paralelo_certificado" type="hidden" />
                        <input id="id_estudiante" name="id_estudiante" type="hidden" />
                        <input type="submit" value="Ver Reporte" />
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once "modalNuevoEstudiante.php" ?>
<?php require_once "modalEditarEstudiante.php" ?>

<!-- Estudiantes Des-matriculados Modal -->
<div class="modal fade" id="deletedStudentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel3">Estudiante Des-matriculados</h4>
            </div>
            <div class="col-md-12 table-responsive">
                <table id="t_deleted" class="table fuente8">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Id</th>
                            <th>Mat.</th>
                            <th>Apellidos</th>
                            <th>Nombres</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_deleted">
                        <!-- Aquí se pintarán los registros recuperados de la BD mediante AJAX -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Histórico de un estudiante Modal -->
<div class="modal fade" id="storyStudentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel4">Histórico del estudiante</h4>
                <h5 id="nombre_estudiante" class="modal-title text-center"></h5>
            </div>

            <div class="col-md-12 table-responsive">
                <table id="t_found" class="table table-striped table-hover fuente8">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Modalidad</th>
                            <th>Periodo Lectivo</th>
                            <th>Curso</th>
                            <th>Paralelo</th>
                            <th>Aprobado</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_found">

                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-success" onclick="seleccionarEstudiante()"><span class="glyphicon glyphicon-save"></span> Seleccionar</a>
            </div>
        </div>
    </div>
</div>

<script>
    var modo_actualizar = false;
    var reg_fecnac = /^([12]\d{3}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01]))$/i;

    $(document).ready(function() {
        cargar_paralelos();
        cargar_def_generos();
        cargar_tipos_documento();
        cargar_def_nacionalidades();

        // Determinar que opciones se tienen al momento de cargar...
        // determinarOpciones();

        // Obtener el rango de años para el datepicker
        var fecha = new Date();
        var year = fecha.getFullYear();
        var minYear = year - 70;
        var maxYear = year - 5;

        $("#id_paralelo").on('change', function() {
            let id_paralelo = $(this).val();

            if (id_paralelo == "") {
                $("#mensaje1").html("Debe seleccionar un paralelo...");
                $("#mensaje1").fadeIn("slow");
                $("#t_estudiantes tbody").html("");
                $("#t_deleted tbody").html("");
            } else {
                $("#mensaje1").fadeOut();
                contarEstudiantesParalelo(id_paralelo);
                // listarEstudiantesParalelo(id_paralelo);
                cargar_estudiantes_des_matriculados();
            }
        });

        $("#new_dni").blur(function() {
            //Aqui se va a determinar si el numero de cedula ya existe en la base de datos
            if (($(this).val() != "" && !modo_actualizar) || ($(this).val() != "" && modo_actualizar && $(this).val() != $("#cedula_anterior").val())) {
                $.post("matriculacion/determinar_cedula_repetida.php", {
                        es_cedula: $(this).val()
                    },
                    function(resultado) {
                        if (resultado != "") {
                            alert(resultado);
                        }
                    }
                );
            }
        });

        $("#new_fec_nac").blur(function() {
            //Aqui se va a calcular la edad a partir de la fecha de nacimiento
            if (reg_fecnac.test($(this).val())) {
                var hoy = new Date();
                var fec_nac = new Date($(this).val());
                var edad = hoy.getFullYear() - fec_nac.getFullYear();
                var m = hoy.getMonth() - fec_nac.getMonth();

                if (m < 0 || (m == 0 && hoy.getDate() < fec_nac.getDate())) {
                    edad--;
                }

                $("#new_edad").val(edad);
            }
        });

        $('#t_deleted tbody').on('click', '.item-deleted', function() {
            let id_estudiante_periodo_lectivo = $(this).attr('data');
            let id_paralelo = $("#id_paralelo").val();
            $.ajax({
                url: "matriculacion/volver_a_matricular.php",
                method: "post",
                data: {
                    id_estudiante_periodo_lectivo: id_estudiante_periodo_lectivo
                },
                dataType: "json",
                success: function(response) {
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    });
                    $('#deletedStudentModal').modal('hide');
                    contarEstudiantesParalelo(id_paralelo);
                    cargar_estudiantes_des_matriculados();
                },
                error: function(jqXHR, textStatus) {
                    console.log(jqXHR.responseText);
                }
            });
        });
    });

    function cargar_paralelos() {
        $.get("scripts/cargar_paralelos_especialidad.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#id_paralelo').append(resultado);
                $('#edit_id_paralelo').append(resultado);
            }
        });
    }

    function cargar_def_generos() {
        $.get("scripts/cargar_definicion_generos.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#new_genero').append(resultado);
                $('#edit_genero').append(resultado);
            }
        });
    }

    function cargar_tipos_documento() {
        $.get("scripts/cargar_tipos_documento.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#new_id_tipo_documento').append(resultado);
                $('#edit_id_tipo_documento').append(resultado);
            }
        });
    }

    function cargar_def_nacionalidades() {
        $.get("scripts/cargar_definicion_nacionalidades.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#new_nacionalidad').append(resultado);
                $('#edit_nacionalidad').append(resultado);
            }
        });
    }

    function cedulaValida(cedula) {
        var total = 0;
        var longitud = cedula.length;
        var longcheck = longitud - 1;

        if (longitud != 10) {
            return false;
        }

        if (cedula !== "" && longitud === 10) {
            for (i = 0; i < longcheck; i++) {
                if (i % 2 === 0) {
                    var aux = cedula.charAt(i) * 2;
                    if (aux > 9) aux -= 9;
                    total += aux;
                } else {
                    total += parseInt(cedula.charAt(i)); // parseInt o concatenará en lugar de sumar
                }
            }

            total = total % 10 ? 10 - total % 10 : 0;

            return cedula.charAt(longitud - 1) == total;
        }
    }

    function calcularEdad(es_fec_nacim) {
        //Aqui se va a calcular la edad a partir de la fecha de nacimiento
        var hoy = new Date();
        var fec_nac = new Date(es_fec_nacim);
        var edad = hoy.getFullYear() - fec_nac.getFullYear();
        var m = hoy.getMonth() - fec_nac.getMonth();

        if (m < 0 || (m == 0 && hoy.getDate() < fec_nac.getDate())) {
            edad--;
        }

        return edad;
    }

    function contarEstudiantesParalelo(id_paralelo) {
        $.post("matriculacion/contar_estudiantes_por_genero.php", {
                id_paralelo: id_paralelo
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#contar_estudiantes_por_genero").html(resultado);
                    listarEstudiantesParalelo(id_paralelo);
                }
            }
        );
    }

    function listarEstudiantesParalelo(id_paralelo) {
        var id_paralelo = document.getElementById("id_paralelo").value;
        if (id_paralelo == 0) {
            document.getElementById("lista_estudiantes").innerHTML = "Debe elegir un paralelo...";
        } else {
            $.post("matriculacion/listar_estudiantes.php", {
                    id_paralelo: id_paralelo
                },
                function(resultado) {
                    if (resultado == false) {
                        alert("Error");
                    } else {
                        $("#tbody_estudiantes").html(resultado);
                    }
                }
            );
        }
    }

    function pagination(partida, id_paralelo) {
        $("#pagina_actual").val(partida);
        var url = "matriculacion/paginar_estudiantes_paralelo.php";
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                partida: partida,
                id_paralelo: id_paralelo
            },
            success: function(data) {
                var array = eval(data);
                $("#tbody_estudiantes").html(array[0]);
                $("#pagination").html(array[1]);
            }
        });
        return false;
    }

    function insertarEstudiante() {
        let cont_errores = 0;
        let es_cedula = $("#new_dni").val().trim();
        let es_email = $("#new_email").val().trim();
        let es_sector = $("#new_sector").val().trim();
        let es_nombres = $("#new_nombres").val().trim();
        let id_paralelo = $("#id_paralelo").val().trim();
        let id_def_genero = $("#new_genero").val().trim();
        let es_telefono = $("#new_telefono").val().trim();
        let es_fec_nacim = $("#new_fec_nac").val().trim();
        let es_apellidos = $("#new_apellidos").val().trim();
        let es_direccion = $("#new_direccion").val().trim();
        let id_def_nacionalidad = $("#new_nacionalidad").val().trim();
        let id_tipo_documento = $("#new_id_tipo_documento").val().trim();

        var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
        var reg_nombres = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
        var reg_cedula = /^([A-Z0-9.]{4,10})$/i;
        var reg_email = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/i;

        if (id_paralelo == "") {
            $("#mensaje1").html("Debe seleccionar un paralelo...");
            $("#mensaje1").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (id_tipo_documento == "") {
            $("#mensaje2").html("Debe seleccionar un tipo de documento...");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (es_cedula == "") {
            $("#mensaje3").html("Debe ingresar el DNI...");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else if (es_cedula.length != 0 && id_tipo_documento == 1 && !reg_cedula.test(es_cedula)) {
            $("#mensaje3").html("El DNI del estudiante no tiene un formato válido.");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else if (id_tipo_documento == 1 && !cedulaValida(es_cedula)) {
            $("#mensaje3").html("La cédula ingresada no es válida.");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (es_apellidos == "") {
            $("#mensaje4").html("Debe ingresar los apellidos...");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else if (!reg_nombres.test(es_apellidos)) {
            $("#mensaje4").html("Los apellidos del estudiante deben contener al menos tres caracteres alfabéticos.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (es_nombres == "") {
            $("#mensaje5").html("Debe ingresar los nombres...");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else if (!reg_nombres.test(es_nombres)) {
            $("#mensaje5").html("Los nombres del estudiante deben contener al menos tres caracteres alfabéticos.");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje5").fadeOut();
        }

        if (es_fec_nacim == "") {
            $("#mensaje6").html("Debe ingresar la fecha de nacimiento...");
            $("#mensaje6").fadeIn();
            cont_errores++;
        } else if (!reg_fecnac.test(es_fec_nacim)) {
            $("#mensaje6").html("La fecha de nacimiento debe tener el formato aaaa-mm-dd");
            $("#mensaje6").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje6").fadeOut();
            $("#new_edad").val(calcularEdad(es_fec_nacim));
        }

        if (es_direccion == "") {
            $("#mensaje7").html("Debe ingresar la dirección de su domicilio...");
            $("#mensaje7").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje7").fadeOut();
        }

        if (es_sector == "") {
            $("#mensaje8").html("Debe ingresar el sector de su domicilio...");
            $("#mensaje8").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje8").fadeOut();
        }

        if (es_telefono == "") {
            $("#mensaje9").html("Debe ingresar el número de celular...");
            $("#mensaje9").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje9").fadeOut();
        }

        if (es_email.length != 0 && !reg_email.test(es_email)) {
            $("#mensaje10").html("Dirección de correo electrónico no válida.");
            $("#mensaje10").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje10").fadeOut();
        }

        if (id_def_genero == "") {
            $("#mensaje11").html("Debe seleccionar el género...");
            $("#mensaje11").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje11").fadeOut();
        }

        if (id_def_nacionalidad == "") {
            $("#mensaje12").html("Debe seleccionar la nacionalidad...");
            $("#mensaje12").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje12").fadeOut();
        }

        if (cont_errores == 0) {
            $("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
            $.ajax({
                type: "POST",
                url: "matriculacion/insertar_estudiante.php",
                data: {
                    id_tipo_documento: id_tipo_documento,
                    id_def_genero: id_def_genero,
                    id_def_nacionalidad: id_def_nacionalidad,
                    es_apellidos: es_apellidos.toUpperCase(),
                    es_nombres: es_nombres.toUpperCase(),
                    es_cedula: es_cedula.toUpperCase(),
                    es_email: es_email,
                    es_sector: es_sector.toUpperCase(),
                    es_direccion: es_direccion.toUpperCase(),
                    es_telefono: es_telefono,
                    es_fec_nacim: es_fec_nacim,
                    id_paralelo: id_paralelo
                },
                dataType: "json",
                success: function(r) {
                    console.log(r);
                    $("#img-loader").html("");
                    contarEstudiantesParalelo(id_paralelo);
                    if (r.estado === "success") {
                        $("#id_estudiante").val(r.id_estudiante);
                        $("#form_insert")[0].reset();
                        $('#newStudentModal').modal('hide');
                    }
                    alert(r.mensaje);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    }

    function editarEstudiante(id_estudiante) {
        $.ajax({
            url: "matriculacion/obtener_estudiante.php",
            method: "post",
            data: {
                id_estudiante: id_estudiante
            },
            dataType: "json",
            success: function(data) {
                $("#id_estudiante").val(id_estudiante);
                setearIndice("edit_id_tipo_documento", data.id_tipo_documento);
                $("#edit_dni").val(data.es_cedula);
                $("#edit_apellidos").val(data.es_apellidos);
                $("#edit_nombres").val(data.es_nombres);
                $("#edit_fec_nac").val(data.es_fec_nacim);
                $("#edit_edad").val(calcularEdad(data.es_fec_nacim));
                $("#edit_direccion").val(data.es_direccion);
                $("#edit_sector").val(data.es_sector);
                $("#edit_telefono").val(data.es_telefono);
                $("#edit_email").val(data.es_email);
                setearIndice("edit_genero", data.id_def_genero);
                setearIndice("edit_nacionalidad", data.id_def_nacionalidad);

                $('#editStudentModal').modal('show');
            },
            error: function(jqXHR, textStatus) {
                console.log(jqXHR.responseText);
            }
        });
    }

    function actualizarEstudiante() {
        let cont_errores = 0;
        let id_estudiante = $("#id_estudiante").val();
        let es_cedula = $("#edit_dni").val().trim();
        let es_email = $("#edit_email").val().trim();
        let es_sector = $("#edit_sector").val().trim();
        let es_nombres = $("#edit_nombres").val().trim();
        let id_paralelo = $("#id_paralelo").val().trim();
        let id_def_genero = $("#edit_genero").val().trim();
        let es_telefono = $("#edit_telefono").val().trim();
        let es_fec_nacim = $("#edit_fec_nac").val().trim();
        let es_apellidos = $("#edit_apellidos").val().trim();
        let es_direccion = $("#edit_direccion").val().trim();
        let edit_id_paralelo = $("#edit_id_paralelo").val().trim();
        let id_def_nacionalidad = $("#edit_nacionalidad").val().trim();
        let id_tipo_documento = $("#edit_id_tipo_documento").val().trim();

        var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
        var reg_nombres = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
        var reg_cedula = /^([A-Z0-9.]{4,10})$/i;
        var reg_email = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/i;
        var reg_fecnac = /^([12]\d{3}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01]))$/i;

        if (id_paralelo == "") {
            $("#mensaje1").html("Debe seleccionar un paralelo...");
            $("#mensaje1").fadeIn("slow");
            cont_errores++;
        }

        if (id_tipo_documento == "") {
            $("#mensaje13").html("Debe seleccionar un tipo de documento...");
            $("#mensaje13").fadeIn("slow");
            cont_errores++;
        }

        if (es_cedula == "") {
            $("#mensaje14").html("Debe ingresar el DNI...");
            $("#mensaje14").fadeIn("slow");
            cont_errores++;
        } else if (es_cedula.length != 0 && id_tipo_documento == 1 && !reg_cedula.test(es_cedula)) {
            $("#mensaje14").html("El DNI del estudiante no tiene un formato válido.", "error");
            $("#mensaje14").fadeIn("slow");
            cont_errores++;
        } else if (id_tipo_documento == 1 && !cedulaValida(es_cedula)) {
            $("#mensaje14").html("La cédula ingresada no es válida...", "error");
            $("#mensaje14").fadeIn("slow");
            cont_errores++;
        }

        if (es_apellidos == "") {
            $("#mensaje15").html("Debe ingresar los apellidos...", "error");
            $("#mensaje15").fadeIn("slow");
            cont_errores++;
        } else if (!reg_nombres.test(es_apellidos)) {
            $("#mensaje15").html("Los apellidos del estudiante deben contener al menos tres caracteres alfabéticos.", "error");
            $("#mensaje15").fadeIn("slow");
            cont_errores++;
        }

        if (es_nombres == "") {
            $("#mensaje16").html("Debe ingresar los nombres...", "error");
            $("#mensaje16").fadeIn("slow");
            cont_errores++;
        } else if (!reg_nombres.test(es_nombres)) {
            $("#mensaje16").html("Los nombres del estudiante deben contener al menos tres caracteres alfabéticos.", "error");
            $("#mensaje16").fadeIn("slow");
            cont_errores++;
        }

        if (es_fec_nacim == "") {
            $("#mensaje17").html("Debe ingresar la fecha de nacimiento...", "error");
            $("#mensaje17").fadeIn("slow");
            cont_errores++;
        } else if (!reg_fecnac.test(es_fec_nacim)) {
            $("#mensaje17").html("La fecha de nacimiento debe tener el formato aaaa-mm-dd", "error");
            $("#mensaje17").fadeIn("slow");
            cont_errores++;
        }

        if (es_direccion == "") {
            $("#mensaje18").html("Debe ingresar la dirección de su domicilio...", "error");
            $("#mensaje18").fadeIn("slow");
            cont_errores++;
        }

        if (es_sector == "") {
            $("#mensaje19").html("Debe ingresar el sector de su domicilio...", "error");
            $("#mensaje19").fadeIn("slow");
            cont_errores++;
        }

        if (es_telefono == "") {
            $("#mensaje20").html("Debe ingresar el número de celular...", "error");
            $("#mensaje20").fadeIn("slow");
            cont_errores++;
        }

        if (es_email.length != 0 && !reg_email.test(es_email)) {
            $("#mensaje21").html("Dirección de correo electrónico no válida.", "error");
            $("#mensaje21").fadeIn("slow");
            cont_errores++;
        }

        if (id_def_genero == "") {
            $("#mensaje22").html("Debe seleccionar el género...", "error");
            $("#mensaje22").fadeIn("slow");
            cont_errores++;
        }

        if (id_def_nacionalidad == "") {
            $("#mensaje23").html("Debe seleccionar la nacionalidad...", "error");
            $("#mensaje23").fadeIn("slow");
            cont_errores++;
        }

        if (cont_errores == 0) {
            // Aquí vamos a actualizar el estudiante mediante AJAX
            $.ajax({
                url: "matriculacion/actualizar_estudiante.php",
                method: "POST",
                data: {
                    id_estudiante: id_estudiante,
                    id_tipo_documento: id_tipo_documento,
                    id_def_genero: id_def_genero,
                    id_def_nacionalidad: id_def_nacionalidad,
                    es_apellidos: es_apellidos.toUpperCase(),
                    es_nombres: es_nombres.toUpperCase(),
                    es_cedula: es_cedula.toUpperCase(),
                    es_email: es_email,
                    es_sector: es_sector.toUpperCase(),
                    es_direccion: es_direccion.toUpperCase(),
                    es_telefono: es_telefono,
                    es_fec_nacim: es_fec_nacim,
                    id_paralelo: edit_id_paralelo
                },
                success: function(resultado) {
                    //alert(resultado);
                    id_paralelo = document.getElementById("id_paralelo").value;
                    contarEstudiantesParalelo(id_paralelo);
                    Swal.fire({
                        title: "Operación exitosa",
                        text: resultado,
                        icon: "success"
                    });
                    $("#form_edit")[0].reset();
                    $('#editStudentModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    }

    function cargar_estudiantes_des_matriculados() {
        // Aquí vamos a recuperar los estudiantes "des-matriculados"
        var id_paralelo = $("#id_paralelo").val();
        //console.log(id_paralelo);
        var request = $.ajax({
            url: "matriculacion/estudiantes_des_matriculados.php",
            method: "post",
            data: {
                id_paralelo: id_paralelo
            },
            dataType: "html"
        });

        request.done(function(response) {
            $("#t_deleted tbody").html(response);
        });

        request.fail(function(jqXHR, textStatus) {
            console.log("Requerimiento fallido: " + jqXHR.responseText);
        });
    }

    function quitarEstudiante(id_estudiante) {
        // Quitar al estudiante del paralelo

        var id_paralelo = $("#id_paralelo").val();

        if (id_estudiante == "") {
            $("#mensaje").html("No se ha pasado el parámetro de id_estudiante...");
            //salirEstudiante(false);
        } else {
            if (confirm("¿Desea quitar este estudiante de este periodo lectivo?")) {
                $("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");

                $.ajax({
                    type: "POST",
                    url: "matriculacion/quitar_estudiante.php",
                    data: "id_estudiante=" + id_estudiante + "&id_paralelo=" + id_paralelo,
                    success: function(resultado) {
                        alert(resultado);
                        contarEstudiantesParalelo(id_paralelo);
                        cargar_estudiantes_des_matriculados();
                    }
                });
            }
        }
    }

    function certificadoMatricula(id_estudiante) {
        var id_paralelo = document.getElementById("id_paralelo").value;
        document.getElementById("id_paralelo_certificado").value = id_paralelo;
        document.getElementById("id_estudiante").value = id_estudiante;
        document.getElementById("formulario_certificado").submit();
    }
</script>