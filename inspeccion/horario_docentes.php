<div class="container">
    <div id="horarioDocentesApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Horario de Docentes</h4>
            </div>
            <div class="panel-body">
                <form id="form_horario" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Docente:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboDocentes">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="error_message"></span>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <!-- message -->
                <div id="message" class="fuente9 text-center"></div>
                <!-- table -->
                <table class="table table-bordered fuente9">
                    <thead id="horario_cabecera">
                        <!-- Aqui desplegamos los dias de la semana de este paralelo -->
                    </thead>
                    <tbody id="horario_clases">
                        <!-- Aqui desplegamos las horas clase con su asignatura y docente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        cargarDocentes();
        $("#cboDocentes").change(function(e){
            e.preventDefault();
            if ($(this).val()==0) {
                $("#message").html("Debe seleccionar un docente...");
                $("#horario_clases").html("");
            } else {
                $("#message").html("");
                // Obtengo los dias de la semana
                $.ajax({
                    url: "inspeccion/listar_dias_semana.php",
                    data: {
                        id_usuario: $(this).val()
                    },
                    method: "POST",
                    type: "html",
                    success: function(response){
                        $("#horario_cabecera").html(response);
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
                // Luego obtengo el horario del docente elegido
                $.ajax({
                    url: "inspeccion/listar_horario_docente.php",
                    data: {
                        id_usuario: $(this).val()
                    },
                    method: "POST",
                    type: "html",
                    success: function(response){
                        console.log(response);
                        $("#horario_clases").html(response);
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            }
        });
    });
	function cargarDocentes()
	{
		$.get("scripts/cargar_docentes.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboDocentes").append(resultado);
				}
			}
		);
	}
</script>