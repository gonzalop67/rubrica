<div class="content-wrapper">
    <br>
    <div id="parcialesApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 style="font:11pt helvetica;">Calificaciones de Sub Periodo</h4>
            </div>
            <div class="panel-body">
                <form id="form_parciales" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="font:10pt helvetica;">Sub Periodo:</label>
                        </div>
                        <div class="col-sm-10">
                            <select id="cboPeriodosEvaluacion" class="form-control" style="font:10pt helvetica;">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span id="error-cboPeriodosEvaluacion" class="help-desk"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="font:10pt helvetica;">Asignatura:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 2px;">
                            <select id="cboAsignaturas" class="form-control" style="font:10pt helvetica;">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span id="error-cboAsignaturas" class="help-desk"></span>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <div class="text-center">
                    <form id="formulario_reporte" action="php_excel/reporte_subperiodo_tutor.php" method="post" target="_self">
                        <!-- &nbsp;&nbsp;Impresi&oacute;n para juntas&nbsp;
                        <input type="checkbox" name="imprimir_para_juntas" id="imprimir_para_juntas"> -->
                        &nbsp;<button class="btn btn-info">Ver Consolidado</button>
                        <input id="id_paralelo" name="id_paralelo" type="hidden" value="" />
                        <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
                        <input id="impresion_para_juntas" name="impresion_para_juntas" type="hidden" />
                    </form>
                </div>
                <hr>
                <div id="nom_docente" class="text-center"></div>
                <div id="img_loader" class="text-center">
                    <img src="imagenes/ajax-loader.gif" alt="Procesando...">
                </div>
                <div id="tbl_notas" class="table-responsive">
                    <!-- Aqui van las calificaciones del estudiante -->
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $("#tbl_notas").html("<p class='text-center text-danger'>Debe seleccionar un Sub Periodo...</p>");
        $("#id_paralelo").val($.trim($("#id_paralelo_tutor").val()));
        $("#img_loader").hide();
        cargarPeriodosEvaluacion();
        cargarAsignaturas();
        $("#cboPeriodosEvaluacion").change(function() {
            $("#nom_docente").html("");
            $("#cboAsignaturas").val(0);
            if($(this).val() !== 0) {
                $("#tbl_notas").html("<p class='text-center text-danger'>Nota: Para obtener el Consolidado, dar click en \"Ver Consolidado\".</p>");
            } else {
                $("#tbl_notas").html("<p class='text-center text-danger'>Debe seleccionar un subperiodo de evaluación...</p>");
            }
        });
        $("#cboAsignaturas").change(function() {
            if($(this).val() != 0) {
                if($("#cboPeriodosEvaluacion").val() != 0) {
                    showProfessorName();
			        listarEstudiantesParalelo();
                } else {
                    $(this).val(0);
                    $("#nom_docente").html("");
                    $("#tbl_notas").html("<p class='text-center text-danger'>Nota: Para obtener el Consolidado, dar click en \"Ver Consolidado\".</p>");
                }
            } else {
                $("#nom_docente").html("");
                $("#tbl_notas").html("");
            }
		});
        $("#imprimir_para_juntas").click(function(){
			var chequeado = 0;
			if($(this).is(':checked'))
				chequeado = 1;
			else
				chequeado = 0;
            $("#impresion_para_juntas").val(chequeado);
		});
        $("#formulario_reporte").submit(function(e){
            e.preventDefault();
            
            var id_paralelo = document.getElementById("id_paralelo").value;
            var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
            document.getElementById("id_periodo_evaluacion").value = document.getElementById("cboPeriodosEvaluacion").value;
            if (id_paralelo == 0) {
                $("#tbl_notas").html("<p class='text-center text-danger'>No se pudo obtener el id del paralelo asociado...</p>");
                return false;
            } else if (id_periodo_evaluacion == 0) {
                $("#tbl_notas").html("<p class='text-center text-danger'>Debe seleccionar un quimestre...</p>");
                return false;
            } 
            document.getElementById("formulario_reporte").submit();
        });
    });
    function cargarPeriodosEvaluacion()
	{
		$.get("scripts/cargar_periodos_evaluacion_principales.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboPeriodosEvaluacion").append(resultado);
				}
			}
		);
	}
    function cargarAsignaturas()
	{
        // Luego obtengo las asignaturas asociadas al paralelo
        $.post("scripts/cargar_asignaturas_por_paralelo.php", 
            {
                id_paralelo: $("#id_paralelo").val()
            },
            function(resultado)
            {
                if(resultado == false)
                {
                    alert("Error");
                }
                else
                {
                    document.getElementById("cboAsignaturas").length = 1;
                    $("#cboAsignaturas").append(resultado);
                }
            }
        );
    }
    function showProfessorName()
	{
		// Aqui va el codigo para mostrar el nombre del docente de la asignatura elegida
		var id_asignatura = document.getElementById("cboAsignaturas").value;
		var id_paralelo = document.getElementById("id_paralelo").value;
		$.post("tutores/consultar_nombre_docente.php", 
			{ 
				id_asignatura: id_asignatura,
				id_paralelo: id_paralelo
			},
			function(resultado)
			{
				$("#nom_docente").html(resultado);
			}
		);
	}
    function listarEstudiantesParalelo()
	{
		// Aqui va el codigo para presentar las calificaciones por aporte de evaluacion, asignatura y paralelo
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		var id_paralelo = document.getElementById("id_paralelo").value;
		var id_asignatura = document.getElementById("cboAsignaturas").value;
		// Aqui va la llamada con AJAX al procedimiento que desplegara las calificaciones
		cargarEstudiantesParalelo(id_paralelo, id_asignatura, id_periodo_evaluacion);
	}
    function cargarEstudiantesParalelo(id_paralelo, id_asignatura, id_periodo_evaluacion)
	{
		$("#img_loader").show();
		$("#tbl_notas").html("");
		$.post("scripts/listar_calificaciones_periodo.php", 
			{ 
				id_paralelo: id_paralelo,
				id_asignatura: id_asignatura,
				id_periodo_evaluacion: id_periodo_evaluacion
			},
			function(resultado)
			{
				$("#img_loader").hide();
				$("#tbl_notas").html(resultado);
			}
		);
	}
</script>