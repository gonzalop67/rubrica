<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
</head>

<body>
<div id="pagina">
	<div id="titulo_pagina">
    	<!-- Aqui va el nombre del curso que corresponde al tutor -->
    </div>
	<?php
		if($num_registros > 1) {
	?>
		<div class="barra_principal" style="padding-top: 4px;">
		<table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="5%" class="fuente9" align="right">Paralelo:&nbsp; </td>
				<td width="95%"> 
					<select id="cboParalelosTutor" class="fuente8">  
						<?php
							$paralelos = $db->consulta("
							SELECT p.id_paralelo, 
								pa_nombre, 
								cu_shortname  
							FROM sw_paralelo_tutor pt, 
								sw_paralelo p, 
								sw_curso c
							WHERE p.id_paralelo = pt.id_paralelo 
							AND c.id_curso = p.id_curso
							AND id_usuario = $id_usuario 
							AND id_periodo_lectivo = $id_periodo_lectivo");
							while($paralelo=$db->fetch_assoc($paralelos)) {
						?>
							<option value="<?php echo $paralelo['id_paralelo']; ?>">
								<?php echo $paralelo['cu_shortname'] . " " . $paralelo['pa_nombre']; ?>
							</option>
						<?php
							}
						?>
					</select> 
				</td>            
			</tr>
		</table>
		</div>
	<?php
		}
	?>
    <div id="barra_principal">
      <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
         <tr>
			<td width="5%" class="fuente9" align="right">Per&iacute;odo:&nbsp; </td>
            <td width="5%"> 
            	<select id="cboPeriodosEvaluacion" class="fuente8"> 
                    <option value="0"> Seleccione... </option> 
                </select> 
            </td>            
            <td width="*"> <div id="img-loader-principal" class="boton"> </div> </td>
         </tr>
      </table>
      <input id="numero_pagina" type="hidden" />
    </div>
    <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Nro.</th>
				<th>Id</th>
				<th>N&oacute;mina</th>
				<th>COMPORTAMIENTO</th>
			</tr>
		</thead>
		<tbody id="tbl_estudiantes">
			<!-- Aquí va la lista de estudiantes obtenida de la base de datos -->
		</tbody>
	</table>
    <form id="formulario_comportamiento" action="reportes/reporte_comportamiento.php" method="post" target="_blank">
		<!-- <div id="lista_estudiantes_paralelo" style="text-align:center"> Debe seleccionar un per&iacute;odo de evaluaci&oacute;n... </div> -->
        <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
            <input id="id_paralelo" name="id_paralelo" type="hidden" />
            <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
            <input type="submit" value="Ver Reporte" />
        </div>
    </form>
</div>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/keypress.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		var num_paralelos = $("#num_paralelos").val();
		console.log("Numero de paralelos: "+num_paralelos);
		$("#tbl_estudiantes").html('<tr><td colspan="4" class="text-center text-danger">Debe seleccionar un per&iacute;odo de evaluaci&oacute;n...</td></tr>');
        if(num_paralelos == 1) {
			$.post("scripts/obtener_id_paralelo_tutor.php", 
				function(resultado) {
					var JSONIdParalelo = eval('(' + resultado + ')');
					$("#id_paralelo").val(JSONIdParalelo.id_paralelo);
					// Luego obtengo el nombre del paralelo
					$.post("tutores/obtener_nombre_paralelo.php",
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
								$("#titulo_pagina").html("COMPORTAMIENTO DE PARCIALES DE " + resultado);
							}
						}
					);
				}
			);
		}else{
			var id_paralelo_tutor = $("#cboParalelosTutor").val();
			// Luego obtengo el nombre del paralelo
			$.post("tutores/obtener_nombre_paralelo.php",
				{
					id_paralelo: id_paralelo_tutor
				},
				function(resultado)
				{
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						$("#titulo_pagina").html("COMPORTAMIENTO DE PARCIALES DE " + resultado);
					}
				}
			);
			$("#id_paralelo").val($("#cboParalelosTutor").val());
		}
		cargarPeriodosEvaluacion();
		if ($("#cboParalelosTutor").length) {
			$("#cboParalelosTutor").change(function(e){
				e.preventDefault();
				var id_paralelo_tutor = $("#cboParalelosTutor").val();
				var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
				// Luego obtengo el nombre del paralelo
				$.post("tutores/obtener_nombre_paralelo.php",
					{
						id_paralelo: id_paralelo_tutor
					},
					function(resultado)
					{
						if(resultado == false)
						{
							alert("Error");
						}
						else
						{
							$("#titulo_pagina").html("COMPORTAMIENTO DE PARCIALES DE " + resultado);
						}
					}
				);
				$("#id_paralelo").val($("#cboParalelosTutor").val());
				if (id_periodo_evaluacion != 0) {
					listarEstudiantesParalelo(id_paralelo_tutor);
				}
			});
		}
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			var id_periodo_evaluacion = $(this).find('option:selected').val();
			var id_paralelo = $("#id_paralelo").val();
			if(id_periodo_evaluacion==0) {
				alert("Debe seleccionar un periodo de evaluacion...");
				$("#ver_reporte").css("display","none");
				$("#cboPeriodosEvaluacion").focus();
			} else {
				// Aqui va el codigo para desplegar las calificaciones del comportamiento
				listarEstudiantesParalelo(id_paralelo);
				$("#ver_reporte").css("display","block");
			}
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

	function listarEstudiantesParalelo(id_paralelo)
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("id_paralelo").value = id_paralelo;
		document.getElementById("id_periodo_evaluacion").value = id_periodo_evaluacion;
		if(id_periodo_evaluacion==0) {
			$("#tbl_estudiantes").html("<tr><td colspan='4' class='text-center'>Debe escoger un per&iacute;odo de evaluaci&oacute;n...</td></tr>");
		} else if(id_paralelo==0) {
			$("#tbl_estudiantes").html("<tr><td colspan='4' class='text-center'>Debe escoger un paralelo...</td></tr>");
		} else {
			$("#tbl_estudiantes").html("<tr><td colspan='4' class='text-center'><img src='imagenes/ajax-loader-blue.GIF' alt='procesando...' /></td></tr>");
			$.post("inspeccion/listar_estudiantes_paralelo.php", 
				{ 
					id_paralelo: id_paralelo,
					id_periodo_evaluacion: id_periodo_evaluacion
				},
				function(resultado)
				{
					//console.log(resultado);
					$("#tbl_estudiantes").html(resultado);
					$("#ver_reporte").css("display","block");
				}
			);
		}
	}
</script>
</body>
</html>