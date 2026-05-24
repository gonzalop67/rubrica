<div class="content-wrapper">
    <br>
    <div id="horarioApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>LISTA DE DOCENTES</h4>
            </div>
            <div class="panel-body">
                <form id="form_horario" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Paralelo:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboParalelos">
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
                <table class="table fuente9">
                    <thead>
                        <tr>
                            <th>Nro.</th>
                            <th>Asignatura</th>
                            <th>Docente</th>
                        </tr>
                    </thead>
                    <tbody id="lista_docentes">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        cargarParalelos();
        $("#cboParalelos").change(function(e){
            e.preventDefault();
            if ($(this).val()==0) {
                $("#message").html("Debe seleccionar un paralelo...");
                $("#lista_docentes").html("");
            } else {
                $("#message").html("");
                // Obtengo la lista de docentes del paralelo
                $.ajax({
                    url: "tutores/listar_docentes_paralelo.php",
                    data: {
                        id_paralelo: $(this).val()
                    },
                    method: "POST",
                    type: "html",
                    success: function(response){
                        console.log(response);
                        $("#lista_docentes").html(response);
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            }
        });
    });
    function cargarParalelos()
	{
		$.get("scripts/cargar_paralelos_especialidad.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboParalelos").append(resultado);
                    $("#message").html("Debe seleccionar un paralelo...");
				}
			}
		);
	}
</script>