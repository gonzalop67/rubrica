<div class="content-wrapper">
    <div id="appCursos" class="col-sm-10 col-sm-offset-1">
        <h2>Cursos</h2>
        <input type="hidden" id="id_curso">
        <!-- panel -->
        <div class="panel panel-default">
            <h4 id="subtitulo" class="text-center">Selecciona una Especialidad</h4>
            <form id="form_cursos" action="" class="app-form">
                <select id="cboEspecialidades" class="form-control">
                    <option value="0">Seleccione ...</option>
                </select>
                <button id="btn-new" type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#addnew">
                    Nuevo Curso
                </button>
            </form>
            <!-- message -->
            <div id="text_message" class="fuente9 text-center"></div>
            <!-- table -->
            <table class="table fuente9">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th colspan=2>Acciones</th>
                    </tr>
                </thead>
                <tbody id="lista_cursos">
                    <!-- Aqui desplegamos el contenido de la base de datos -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- New Curso Modal -->
<div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel">Nuevo Curso</h4>
            </div>
            <form action="" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Nombre:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="new_cu_nombre" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Abreviatura:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="new_cu_abreviatura" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Nombre Corto:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="new_cu_shortname" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Inserta Comportamiento:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="new_ins_comp">
                                <option value="0">Docente</option>
                                <option value="1">Tutor</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">¿Bachillerato Técnico?:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="new_es_bach_tecnico">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">¿Es Intensivo?:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="new_es_intensivo">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="addCurso()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Curso Modal -->
<div class="modal fade" id="editCurso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel">Editar Curso</h4>
            </div>
            <form action="" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Nombre:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="edit_cu_nombre" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Abreviatura:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="edit_cu_abreviatura" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Nombre Corto:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="edit_cu_shortname" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Inserta Comportamiento:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="edit_ins_comp">
                                <option value="0">Docente</option>
                                <option value="1">Tutor</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">¿Bachillerato Técnico?:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="edit_es_bach_tecnico">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">¿Es Intensivo?:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="edit_es_intensivo">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateCurso()"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // JQuery Listo para utilizar
        $("#btn-new").attr("disabled", "true");
        cargarEspecialidades();
        $("#cboEspecialidades").change(function(e) {
            // Código para recuperar los cursos asociados a la especialidad seleccionada
            listarCursos();
        });
        $("#lista_cursos").html("<tr><td colspan='4' align='center'>Debes seleccionar una especialidad...</td></tr>");
    });

    function setearIndice(nombreCombo, indice) {
        for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
            if (document.getElementById(nombreCombo).options[i].value == indice) {
                document.getElementById(nombreCombo).options[i].selected = indice;
            }
    }

    function cargarEspecialidades() {
        $.get("scripts/cargar_especialidades.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboEspecialidades").append(resultado);
                }
            }
        );
    }

    function listarCursos() {
        var id = document.getElementById("cboEspecialidades").value;
        if (id == 0) {
            $("#apo_eval").html("<tr><td colspan='4' align='center'>Debes seleccionar una especialidad...</td></tr>");
            $("#btn-new").attr("disabled", true);
        } else {
            $.post("cursos/cargar_cursos.php", {
                    id_especialidad: id
                },
                function(resultado) {
                    if (resultado == false) {
                        alert("Error");
                    } else {
                        $("#btn-new").attr("disabled", false);
                        $("#lista_cursos").html(resultado);
                    }
                }
            );
        }
    }

    function deleteCurso(id) {
        // Validación de la entrada de datos

        if (id == "") {
            $("#text_message").html("No se ha pasado el parámetro de id_curso...");
        } else {
            var eliminar = confirm("¿Seguro que desea eliminar este curso?")
            if (eliminar) {
                $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
                $.ajax({
                    type: "POST",
                    url: "cursos/eliminar_curso.php",
                    data: "id_curso=" + id,
                    success: function(resultado) {
                        $("#text_message").html(resultado);
                        listarCursos();
                    }
                });
            }
        }
    }

    function editCurso(id) {
        //Obtengo los datos del curso seleccionado
        $("#text_message").html("<img src='./imagenes/ajax-loader.gif' alt='procesando'>");
        $.ajax({
            url: "cursos/obtener_curso.php",
            method: "POST",
            type: "html",
            data: {
                id_curso: id
            },
            success: function(response) {
                $("#text_message").html("");
                $("#id_curso").val(id);
                var curso = jQuery.parseJSON(response);
                $("#edit_cu_nombre").val(curso.cu_nombre);
                $("#edit_cu_abreviatura").val(curso.cu_abreviatura);
                $("#edit_cu_shortname").val(curso.cu_shortname);
                setearIndice("edit_ins_comp", curso.quien_inserta_comp);
                setearIndice("edit_es_bach_tecnico", curso.es_bach_tecnico);
                setearIndice("edit_es_intensivo", curso.es_intensivo);
                $('#editCurso').modal('show');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function updateCurso() {
        var id = $("#id_curso").val();
        var id_esp = $("#cboEspecialidades").val();
        var nombre = $("#edit_cu_nombre").val();
        var abreviatura = $("#edit_cu_abreviatura").val();
        var shortname = $("#edit_cu_shortname").val();
        var ins_comp = $("#edit_ins_comp").val();
        var bach_tecnico = $("#edit_es_bach_tecnico").val();
        var es_intensivo = $("#edit_es_intensivo").val();
        $.ajax({
            url: "cursos/actualizar_curso.php",
            method: "POST",
            type: "html",
            data: {
                id_curso: id,
                id_especialidad: id_esp,
                cu_nombre: nombre,
                cu_abreviatura: abreviatura,
                cu_shortname: shortname,
                ins_comp: ins_comp,
                bach_tecnico: bach_tecnico,
                es_intensivo: es_intensivo
            },
            success: function(response) {
                $("#text_message").html(response);
                listarCursos();
                $('#editCurso').modal('hide');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function addCurso() {
        var id_especialidad = $("#cboEspecialidades").val();
        var cu_nombre = $("#new_cu_nombre").val();
        var cu_abreviatura = $("#new_cu_abreviatura").val();
        var cu_shortname = $("#new_cu_shortname").val();
        var ins_comp = $("#new_ins_comp").val();
        $.ajax({
            url: "cursos/insertar_curso.php",
            method: "POST",
            type: "html",
            data: {
                id_especialidad: id_especialidad,
                cu_nombre: cu_nombre,
                cu_abreviatura: cu_abreviatura,
                cu_shortname: cu_shortname,
                ins_comp: ins_comp
            },
            success: function(response) {
                listarCursos();
                $('#addnew').modal('hide');
                $("#text_message").html(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
</script>