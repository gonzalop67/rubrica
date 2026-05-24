<div class="content-wrapper">
    <div id="asociarCursoSuperiorApp" class="col-sm-12">
        <br>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Asociar Cursos Superiores</h4>
            </div>
            <div class="panel-body">
                <form id="form_malla" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Curso Inferior:</label>
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
                            <label class="control-label" style="position:relative; top:7px;">Curso Superior:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 2px;">
                            <select class="form-control fuente9" id="cboCursoSuperior">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje2"></span>
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
                            <th>Curso Inferior</th>
                            <th>Curso Superior</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista_items">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
		cargar_cursos();
        cargar_cursos_superiores();
        cargar_cursos_asociados();
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
                $('#cboCursos').append(resultado);
            }
        });	
    }

    function cargar_cursos_asociados()
	{
		$.get("asociar_curso_superior/cargar_cursos_asociados.php", 
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#lista_items").html(resultado);
				}
			}
		);
	}

    function cargar_cursos_superiores()
	{
		$.get("asociar_curso_superior/cargar_cursos_superiores.php", 
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboCursoSuperior").append(resultado);
				}
			}
		);
	}

    function insertarAsociacion()
	{
		var id_curso = document.getElementById("cboCursos").value;
		var id_curso_superior = document.getElementById("cboCursoSuperior").value;
        var cont_errores = 0;

		if (id_curso == 0) {
			$("#mensaje1").html("Debe seleccionar un curso...");
            $("#mensaje1").fadeIn();
            cont_errores++;
		} else {
            $("#mensaje1").fadeOut();
        }
        
        if (id_curso_superior == 0) {
			$("#mensaje2").html("Debe elegir un curso superior...");
            $("#mensaje2").fadeIn();
            cont_errores++;
		} else {
            $("#mensaje2").fadeOut();
        }

        if (cont_errores == 0) {
			$("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
                type: "POST",
                url: "asociar_curso_superior/insertar_asociacion.php",
                data: "id_curso="+id_curso+"&id_curso_superior="+id_curso_superior,
                success: function(resultado){
                    $("#text_message").html(resultado);
                    cargar_cursos_asociados();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
			});			
		}	
	}

    function eliminarAsociacion(id)
	{
		if (id == "") {
			document.getElementById("mensaje").innerHTML = "No se ha pasado correctamente el par&aacute;metro id_asociar_curso_superior...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
                type: "POST",
                url: "asociar_curso_superior/eliminar_asociacion.php",
                data: "id="+id,
                success: function(resultado){
                    $("#text_message").html(resultado);
                    cargar_cursos_asociados();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
			});
		}
	}

    function subirAsociacion(id_asignatura_curso, id_curso)
	{
		if (id_asignatura_curso == "" || id_curso == "") {
			document.getElementById("mensaje").innerHTML = "No se han pasado correctamente los par&aacute;metros id_curso_asignatura e id_curso...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas_cursos/subir_asociacion.php",
					data: "id_asignatura_curso="+id_asignatura_curso+"&id_curso="+id_curso,
					success: function(resultado){
						$("#text_message").html(resultado);
						cargar_asignaturas_asociadas(true);
				  }
			});			
		}	
	}

	function bajarAsociacion(id_asignatura_curso, id_curso)
	{
		if (id_asignatura_curso == "" || id_curso == "") {
			document.getElementById("mensaje").innerHTML = "No se han pasado correctamente los par&aacute;metros id_curso_asignatura e id_curso...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas_cursos/bajar_asociacion.php",
					data: "id_asignatura_curso="+id_asignatura_curso+"&id_curso="+id_curso,
					success: function(resultado){
						$("#text_message").html(resultado);
						cargar_asignaturas_asociadas(true);
				  }
			});			
		}	
	}
</script>