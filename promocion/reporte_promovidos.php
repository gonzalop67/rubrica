<div class="container">
    <div id="promovidosApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Nómina de Promovidos</h4>
            </div>
            <div class="panel-body">
                <form id="form_promovidos" action="php_excel/reporte_nomina_promovidos.php" method="POST" class="app-form">
                    <input type="hidden" name="id_paralelo" id="id_paralelo">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Paralelo:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboParalelos" name="cboParalelos">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button type="button" class="btn btn-block btn-primary"
                                onclick="verReporte()">
                                Ver Reporte
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        //JQuery listo para usar
        cargarParalelos();
        $("#cboParalelos").change(function(e){
            e.preventDefault();
            var id_paralelo = $(this).val();
            $("#id_paralelo").val(id_paralelo);
            //alert(id_paralelo);
            if(id_paralelo == 0){
                swal("Ocurrió un error inesperado!", "Debe seleccionar un paralelo.", "error");
            }
        })
        /* $("#form_promovidos").submit(function(e){
            e.preventDefault();
            var url = $(this).attr("action");
            var id_paralelo = $("#cboParalelos").val();
            alert(id_paralelo);
            if(id_paralelo == 0){
                swal("Ocurrió un error inesperado!", "Debe seleccionar un paralelo.", "error");
            }else{
                //Lanzamos el submit
                location.href = url;
            }
        }); */
    });
    function verReporte()
    {
        var id_paralelo = $("#cboParalelos").val();
        if(id_paralelo == 0){
            swal("Ocurrió un error inesperado!", "Debe seleccionar un paralelo.", "error");
        } else {
            //Lanzamos el submit
            $("#form_promovidos").submit();
        }
    }
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
				}
			}
		);
	}
</script>