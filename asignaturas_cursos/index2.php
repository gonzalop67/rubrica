<div class="content-wrapper">
    <div id="asociarAsignaturaCursoApp" class="col-sm-12">
        <br>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Asociar Asignaturas con Cursos</h4>
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
                        <th>Asignatura</th>
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
                        <label class="control-label" style="position:relative; top:7px;">Total Asignaturas:</label>
                    </div>
                    <div class="col-sm-2" style="margin-top: 2px;">
                        <input type="text" class="form-control fuente9 text-right" id="total_asignaturas" value="0" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
		cargar_cursos();
		cargar_asignaturas();
        $('#cboCursos').select2();
        $("#cboCursos").change(function(e){
			e.preventDefault();
			cargar_asignaturas_asociadas();
		});
        $('#cboAsignaturas').select2();

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

    function cargar_asignaturas()
    {
        $.get("scripts/cargar_asignaturas.php", {}, 
            function(resultado){
                if(resultado == false)
                {
                    alert("Error");
                }
                else
                {
                    $('#cboAsignaturas').append(resultado);
                }
            }
        );	
    }

    function cargar_asignaturas_asociadas()
	{
		var id_curso = document.getElementById("cboCursos").value;

        $.ajax({
            type: "POST",
            url: "asignaturas_cursos/cargar_asignaturas_asociadas.php",
            data: { 
                id_curso: id_curso 
            },
            dataType: "json",
            success: function (response) {
                $("#lista_items").html(response.cadena);
                $("#total_asignaturas").val(response.total_asignaturas);
            }
        });
	}

    function insertarAsociacion()
	{
		var id_curso = document.getElementById("cboCursos").value;
		var id_asignatura = document.getElementById("cboAsignaturas").value;
        var cont_errores = 0;

		if (id_curso == 0) {
			$("#mensaje1").html("Debe seleccionar un curso...");
            $("#mensaje1").fadeIn();
            cont_errores++;
		} else {
            $("#mensaje1").fadeOut();
        }
        
        if (id_asignatura == 0) {
			$("#mensaje2").html("Debe elegir una asignatura...");
            $("#mensaje2").fadeIn();
            cont_errores++;
		} else {
            $("#mensaje2").fadeOut();
        }

        if (cont_errores == 0) {
			// $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas_cursos/insertar_asociacion.php",
					data: "id_curso="+id_curso+"&id_asignatura="+id_asignatura,
                    dataType: "json",
					success: function(r){
                        toastr[r.estado](r.mensaje, r.titulo);
                        cargar_asignaturas_asociadas();
				  }
			});			
		}	
	}

    function eliminarAsociacion(id_asignatura_curso, id_curso)
	{
		if (id_asignatura_curso == "" || id_curso == "") {
			document.getElementById("mensaje").innerHTML = "No se han pasado correctamente los par&aacute;metros id_curso_asignatura e id_curso...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
					type: "POST",
					url: "asignaturas_cursos/eliminar_asociacion.php",
					data: "id_asignatura_curso="+id_asignatura_curso+"&id_curso="+id_curso,
					success: function(resultado){
						$("#text_message").html(resultado);
						cargar_asignaturas_asociadas();
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
            url: "asignaturas_cursos/saveNewPositions.php",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                //
                cargar_asignaturas_asociadas();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
    }
</script>