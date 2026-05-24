<div class="content-wrapper">
    <br>
    <div id="anualesApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 style="font:11pt helvetica;">Calificaciones Anuales</h4>
            </div>
            <div class="panel-body">
                <form id="form_parciales" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="font:10pt helvetica;">Asignatura:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 2px;">
                            <select id="cboAsignaturas" class="form-control" style="font:10pt helvetica;">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk" id="mensaje"></span>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <div class="text-center">
                    <form id="formulario_reporte" action="php_excel/reporte_anual_excel_tutor.php" method="post" target="_self">
                        <!-- &nbsp;&nbsp;Impresi&oacute;n para juntas&nbsp; -->
                        <!-- <input type="checkbox" name="imprimir_para_juntas" id="imprimir_para_juntas"> -->
                        &nbsp;<button class="btn btn-info">Ver Consolidado</button>
                        <input id="id_paralelo" name="id_paralelo" type="hidden" value="" />
                        <!-- <input id="impresion_para_juntas" name="impresion_para_juntas" type="hidden" /> -->
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
        $("#id_paralelo").val($.trim($("#id_paralelo_tutor").val()));
        $("#img_loader").hide();
        cargarAsignaturas();
        $("#cboAsignaturas").change(function() {
            if($(this).val() != 0) {
                showProfessorName();
                listarEstudiantesParalelo();
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
            if (id_paralelo == 0) {
                $("#tbl_notas").html("<p class='text-center text-danger'>No se pudo obtener el id del paralelo asociado...</p>");
                return false;
            } 
            document.getElementById("formulario_reporte").submit();
        });
    });
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
		var id_paralelo = document.getElementById("id_paralelo").value;
		var id_asignatura = document.getElementById("cboAsignaturas").value;
		// Aqui va la llamada con AJAX al procedimiento que desplegara las calificaciones
		cargarEstudiantesParalelo(id_paralelo, id_asignatura);
	}
    function cargarEstudiantesParalelo(id_paralelo, id_asignatura)
	{
		$("#img_loader").show();
		$("#tbl_notas").html("");
		$.post("scripts/listar_calificaciones_periodo_lectivo.php", 
			{ 
				id_paralelo: id_paralelo,
				id_asignatura: id_asignatura
			},
			function(resultado)
			{
                //console.log(resultado);
				$("#img_loader").hide();
				$("#tbl_notas").html(resultado);
			}
		);
	}
</script>