<div class="content-wrapper">
    <div id="appAprobadosParalelo" class="col-sm-10 col-sm-offset-1">
        <h2>Aprobados por Paralelo</h2>
        <!-- panel -->
        <div class="panel panel-default">
            <h4 id="subtitulo" class="text-center">Selecciona un Paralelo</h4>
            <form id="form_aprobados_paralelo" action="" class="app-form">
                <select id="cboParalelos" class="form-control">
                    <option value="0">Seleccione ...</option>
                </select>
                <!-- <button id="submit" type="button" class="btn btn-block btn-primary" onclick="verAprobadosParalelo()">
                    Consultar
                </button> -->
            </form>
            <!-- message -->
            <div id="text_message" class="fuente9 text-center"></div>
            <div id="pag_nomina_estudiantes" style="margin-top:2px;">
                <input type="hidden" id="titulo" value="">
                <form id="formulario_periodo" action="php_excel/aprobados_por_paralelo.php" method="post">
                    <div id="aprobados_por_paralelo" style="text-align:center"> Debe elegir un paralelo... </div>
                    <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
                        <input id="id_paralelo" name="id_paralelo" type="hidden" />
                        <input type="submit" value="Generar Informe" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        cargarParalelos();
        $("#cboParalelos").change(function(e){
			e.preventDefault();
			var id_paralelo = $(this).val();
            if (id_paralelo == 0) {
                $("#aprobados_por_paralelo").html("Debe elegir un paralelo...");
                $("#ver_reporte").css("display","none");
            } else {
                $("#aprobados_por_paralelo").html("");
                //Aqui va la llamada a ajax para recuperar los porcentajes de aprobados y no aprobados
                cargarPorcentajesdeAprobados(id_paralelo);
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
				}
			}
		);
	}
    function cargarPorcentajesdeAprobados(id_paralelo)
    {
        // Procedimiento para recuperar los porcentajes de aprobados y no aprobados
        $("#aprobados_por_paralelo").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
        $.ajax({
            url: "estadisticas/obtener_aprobados_por_paralelo.php",
            type: "POST",
            data: {
				id_paralelo: id_paralelo
			},
            dataType: "json",
            success: function(data){
                console.log(data);
				$("#aprobados_por_paralelo").html("");
                var etiquetas = new Array();
                var porcentajes = new Array();
                $.each(data,function(key,value){
                    etiquetas.push(value.etiqueta);
                    porcentaje = Number(value.porcentaje);
                    porcentajes.push(porcentaje);
                });
                graficar(etiquetas, porcentajes, "aprobados_por_paralelo");
				$("#ver_reporte").css("display","block");
            }
        });
    }
    function graficar(etiquetas, porcentajes, idDiv)
	{
		var title = "APROBADOS POR PARALELO"
					+"<br>"
					+$("#cboParalelos option:selected").text();
		var data = [{
			values: porcentajes,
			labels: etiquetas,
			hoverinfo: "label",
			type: 'pie',
			sort: false,
			marker: {
				colors: ['rgb(51, 153, 102)', 'rgb(255, 80, 80)']
			}
		}];

		var layout = {
            title: title,
			"titlefont": {
				"size": 12
			},
        };

		Plotly.newPlot(idDiv, data, layout);
	}
</script>