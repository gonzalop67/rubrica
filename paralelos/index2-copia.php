<div class="content-wrapper">
    <div id="div_paralelos" class="col-sm-10 col-sm-offset-1">
        <h2>Paralelos</h2>
        <input type="hidden" id="id_paralelo">
        <input type="hidden" id="id_curso">
        <!-- panel -->
        <div class="panel panel-default">
            <form id="form_paralelos" action="" class="app-form">
                <button id="btn-new" type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#addnew">
                    Nuevo Paralelo
                </button>
            </form>
            <!-- message -->
            <div id="text_message" class="fuente9 text-center"></div>
            <!-- table -->
            <table class="table fuente9">
                <thead>
                    <tr>
                        <th>Nro</th>
                        <th>Especialidad</th>
                        <th>Curso</th>
                        <th>Nombre</th>
                        <th>Jornada</th>
                        <th><!-- Botón Editar --></th>
                        <th><!-- Botón Borrar --></th>
                        <th><!-- Botón Subir --></th>
                        <th><!-- Botón Bajar --></th>
                    </tr>
                </thead>
                <tbody id="lista_paralelos">
                    <!-- Aqui desplegamos el contenido de la base de datos -->
                </tbody>
            </table>
        </div>
    </div>
    <!-- New Paralelos Modal -->
    <div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title text-center" id="myModalLabel1">Nuevo Paralelo</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Curso:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="new_cbo_cursos">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Jornada:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="new_cboJornada">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="new_pa_nombre" value="">
                            <span class="help-desk error" id="mensaje3"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="addParalelo()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Paralelo Modal -->
    <div class="modal fade" id="editParalelo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title text-center" id="myModalLabel3">Editar Paralelo</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Curso:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="edit_cu_nombre" name="edit_cu_nombre" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Jornada:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="edit_cboJornada">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="edit_pa_nombre" name="edit_pa_nombre" value="">
                            <span class="help-desk error" id="mensaje3"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateParalelo()"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        cargar_cursos();
        cargar_jornadas();
        listarParalelos();
    });
    function cargar_cursos()
    {
        $.get("scripts/cargar_cursos.php", function(resultado){
            if(resultado == false)
            {
                alert("Error");
            }
            else
            {
                $('#new_cbo_cursos').append(resultado);
            }
        });
    }
    function cargar_jornadas()
	{
        $.get("scripts/cargar_jornadas.php", { },
            function(resultado)
            {
                if(resultado == false)
                {
                    alert("Error");
                }
                else
                {
                    $("#new_cboJornada").append(resultado);
                    $("#edit_cboJornada").append(resultado);
                }
            }
        );
	}
    function listarParalelos()
    {
        $.get("paralelos/cargar_paralelos.php", function(resultado){
            $('#lista_paralelos').html(resultado);
        });	
    }
    
    function addParalelo(){
        var id_curso = $("#new_cbo_cursos").val();
        var pa_nombre = $("#new_pa_nombre").val();
        var id_jornada = $("#new_cboJornada").val();

        // expresion regular para validar el ingreso del nombre
        var reg_nombre = /^([a-zA-Z. ]{1,16})$/i;
        
        // contador de errores
        var cont_errores = 0;

        if (id_curso == 0){
            $("#mensaje1").html("Debes seleccionar el Curso");
            $("#mensaje1").fadeIn("slow");
            cont_errores++;
        }else {
            $("#mensaje1").fadeOut();
        }

        if (id_jornada == 0){
            $("#mensaje2").html("Debes seleccionar la Jornada");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        }else {
            $("#mensaje2").fadeOut();
        }

        if(pa_nombre.trim()==""){
            $("#mensaje3").html("Debes ingresar el nombre del Paralelo");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        }else if(!reg_nombre.test(pa_nombre)){
            $("#mensaje3").html("Debes ingresar un nombre válido para el Paralelo");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        }else {
            $("#mensaje3").fadeOut();
        }

        if(cont_errores==0){
            $.ajax({
                url: "paralelos/insertar_paralelo.php",
                method: "POST",
                type: "html",
                data: {
                    id_curso: id_curso,
                    id_jornada: id_jornada,
                    pa_nombre: pa_nombre
                },
                success: function(response){
                    listarParalelos();
                    $('#addnew').modal('hide');
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }
    
    function updateParalelo(){
        var id_curso = $("#id_curso").val();
        var id_paralelo = $("#id_paralelo").val();
        var id_jornada = $("#edit_cboJornada").val();
        var pa_nombre = $("#edit_pa_nombre").val();

        // expresion regular para validar el ingreso del nombre
        var reg_nombre = /^([a-zA-Z.]{1,5})$/i;
        
        // contador de errores
        var cont_errores = 0;

        if(id_jornada==0){
            $("#mensaje2").html("Debes seleccionar la Jornada");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        }else {
            $("#mensaje3").fadeOut();
        }

        if(pa_nombre.trim()==""){
            $("#mensaje3").html("Debes ingresar el nombre del Paralelo");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        }else if(!reg_nombre.test(pa_nombre)){
            $("#mensaje3").html("Debes ingresar un nombre válido para el Paralelo");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        }else {
            $("#mensaje3").fadeOut();
        }

        if(cont_errores==0){
            console.log(id_jornada);
            $.ajax({
                url: "paralelos/actualizar_paralelo.php",
                method: "POST",
                type: "html",
                data: {
                    id_paralelo: id_paralelo,
                    id_curso: id_curso,
                    id_jornada: id_jornada,
                    pa_nombre: pa_nombre
                },
                success: function(response){
                    listarParalelos();
                    $('#editParalelo').modal('hide');
                    $("#text_message").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }
    
    function setearIndice(nombreCombo,indice)
	{
		for (var i=0;i<document.getElementById(nombreCombo).options.length;i++)
			if (document.getElementById(nombreCombo).options[i].value == indice) {
				document.getElementById(nombreCombo).options[i].selected = indice;
			}
	}
    function editParalelo(id)
    {
        $.ajax({
            type: "POST",
            url: "paralelos/obtener_paralelo.php",
            data: "id_paralelo="+id,
            success: function(resultado){
                var paralelo = eval('(' + resultado + ')');
                console.log(paralelo);
                $("#id_paralelo").val(id);
                $("#id_curso").val(paralelo.id_curso);
                $("#edit_cu_nombre").val("["+paralelo.es_figura+"] "+paralelo.cu_nombre);
                $("#edit_pa_nombre").val(paralelo.pa_nombre);
                setearIndice("edit_cboJornada",paralelo.id_jornada);
                $('#editParalelo').modal('show');
            }
        });
    }
    function deleteParalelo(id){
        //Elimino el paralelo mediante AJAX
        $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
        $.ajax({
            url: "paralelos/eliminar_paralelo.php",
            method: "POST",
            type: "html",
            data: {
                id_paralelo: id
            },
            success: function(response){
                $("#text_message").html(response);
                listarParalelos();
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
    function subirParalelo(id_paralelo)
    {
        if (id_paralelo == "") {
            document.getElementById("text_message").innerHTML = "No se ha pasado correctamente el par&aacute;metro id_paralelo...";
        } else {
            $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
            $.ajax({
                    type: "POST",
                    url: "paralelos/subir_paralelo.php",
                    data: "id_paralelo="+id_paralelo,
                    success: function(resultado){
                        $("#text_message").html(resultado);
                        listarParalelos();
                }
            });
        }	
    }
    function bajarParalelo(id_paralelo)
    {
        if (id_paralelo == "") {
            document.getElementById("text_message").innerHTML = "No se ha pasado correctamente el par&aacute;metro id_paralelo...";
        } else {
            $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
            $.ajax({
                    type: "POST",
                    url: "paralelos/bajar_paralelo.php",
                    data: "id_paralelo="+id_paralelo,
                    success: function(resultado){
                        $("#text_message").html(resultado);
                        listarParalelos();
                }
            });			
        }
    }
</script>