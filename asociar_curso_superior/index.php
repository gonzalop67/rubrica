<div class="content-wrapper">
    <!-- Header de la página (Opcional pero recomendado en AdminLTE) -->
    <section class="content-header">
        <h1>
            Configuración
            <small>Asociación de Cursos</small>
        </h1>
    </section>

    <!-- Contenido Principal -->
    <section class="content">
        <div class="row">
            <div id="asociarCursoSuperiorApp" class="col-xs-12">
                
                <!-- Box de AdminLTE (Reemplaza al panel) -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Asociar Cursos Superiores</h3>
                    </div>
                    
                    <div class="box-body">
                        <!-- Formulario Horizontal nativo -->
                        <form id="form_malla" action="" class="form-horizontal app-form">
                            
                            <!-- Campo: Curso Inferior -->
                            <div class="form-group">
                                <label for="cboCursos" class="col-sm-2 control-label">Curso Inferior:</label>
                                <div class="col-sm-10">
                                    <select class="form-control fuente9" id="cboCursos">
                                        <option value="0">Seleccione...</option>
                                    </select>
                                    <span class="help-block text-danger" id="mensaje1"></span>
                                </div>
                            </div>
                            
                            <!-- Campo: Curso Superior -->
                            <div class="form-group">
                                <label for="cboCursoSuperior" class="col-sm-2 control-label">Curso Superior:</label>
                                <div class="col-sm-10">
                                    <select class="form-control fuente9" id="cboCursoSuperior">
                                        <option value="0">Seleccione...</option>
                                    </select>
                                    <span class="help-block text-danger" id="mensaje2"></span>
                                </div>
                            </div>
                            
                            <!-- Botón de Acción -->
                            <div class="form-group" id="botones_insercion">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button id="btn-add-item" type="button" class="btn btn-primary" onclick="insertarAsociacion()">
                                        <i class="fa fa-plus"></i> Asociar
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <hr>
                        
                        <!-- Mensaje de estado -->
                        <div id="text_message" class="fuente9 text-center"></div>
                        
                        <!-- Tabla con estilo AdminLTE -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover fuente9">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">Id</th>
                                        <th>Curso Inferior</th>
                                        <th>Curso Superior</th>
                                        <th style="width: 120px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="lista_items">
                                    <!-- Aquí se despliega el contenido de la base de datos -->
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div><!-- /.box -->
                
            </div>
        </div>
    </section>
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