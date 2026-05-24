<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/keypress.js"></script>
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
			<td width="5%" class="fuente9" align="right">Aporte:&nbsp; </td>
            <td width="5%"> 
            	<select id="cboAportesEvaluacion" class="fuente8"> 
                    <option value="0"> Seleccione... </option> 
                </select> 
            </td>            
            <td width="*"> 
				<div id="mensaje_rubrica" style="margin-left: 4px;"> 
					<!-- Aqui va el mensaje de la edición del comportamiento -->	
				</div> 
			</td>
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
    <form id="formulario_comportamiento" action="reportes/reporte_comportamiento_parciales.php" method="post" target="_blank">
		<!-- <div id="lista_estudiantes_paralelo" style="text-align:center"> Debe seleccionar un per&iacute;odo de evaluaci&oacute;n... </div> -->
        <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
            <input id="id_paralelo" name="id_paralelo" type="hidden" />
            <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
            <input type="submit" value="Ver Reporte" />
        </div>
    </form>
</div>
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
				var id_aporte_evaluacion = $("#cboAportesEvaluacion").val();
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
				if (id_aporte_evaluacion != 0) {
					listarEstudiantesParalelo(id_paralelo_tutor, id_aporte_evaluacion);
				}
			});
		}
		$("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			var id_periodo_evaluacion = $(this).find('option:selected').val();
			if(id_periodo_evaluacion==0) {
				alert("Debe seleccionar un periodo de evaluacion...");
				$("#ver_reporte").css("display","none");
				$("#cboPeriodosEvaluacion").focus();
			} else {
				cargarAportesEvaluacion();
			}
		});
		$("#cboAportesEvaluacion").change(function(e){
			e.preventDefault();
			var id_aporte_evaluacion = $(this).find('option:selected').val();
			document.getElementById("id_aporte_evaluacion").value = id_aporte_evaluacion;
			var id_paralelo = $("#id_paralelo").val();
			// Aqui va el codigo para desplegar las calificaciones del comportamiento
			listarEstudiantesParalelo(id_paralelo,id_aporte_evaluacion);
			$("#ver_reporte").css("display","block");
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

	function cargarAportesEvaluacion()
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("cboAportesEvaluacion").options.length=1;
		$.get("scripts/cargar_aportes_principales_evaluacion.php", { id_periodo_evaluacion: id_periodo_evaluacion },
			function(resultado)
			{
				if (resultado == false) 
				{
					$("#lista_estudiantes_paralelo").addClass("error");
					$("#lista_estudiantes_paralelo").html("No existen aportes de evaluaci&oacute;n asociados a este peri&oacute;do de evaluaci&oacute;n...");
				}
				else
				{
					$("#cboAportesEvaluacion").append(resultado);
					$("#lista_estudiantes_paralelo").addClass("error");
					$("#lista_estudiantes_paralelo").html("Debe elegir un aporte de evaluaci&oacute;n...");
				}
			}
		);
	}

	function sel_texto(input) {
		$(input).select();
	}
	
	function listarEstudiantesParalelo(id_paralelo,id_aporte_evaluacion)
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("id_paralelo").value = id_paralelo;
		if(id_periodo_evaluacion==0) {
			$("#lista_estudiantes_paralelo").html("Debe escoger un per&iacute;odo de evaluaci&oacute;n...");
		} else if(id_paralelo==0) {
			$("#lista_estudiantes_paralelo").html("No se ha pasado correctamente el id_paralelo...");
		} else if(id_aporte_evaluacion==0) {
			$("#lista_estudiantes_paralelo").html("No se ha pasado correctamente el id_aporte_evaluacion...");
		} else {
			$("#lista_estudiantes_paralelo").html("<img src='imagenes/ajax-loader-red-dog.GIF' alt='procesando...' />");
			$.post("inspeccion/listar_estudiantes_parciales_paralelo.php", 
				{ 
					id_paralelo: id_paralelo,
					id_aporte_evaluacion: id_aporte_evaluacion
				},
				function(resultado)
				{
					console.log(resultado);
                    //$("#lista_estudiantes_paralelo").removeClass("error");
					$("#tbl_estudiantes").html(resultado);
					$("#ver_reporte").css("display","block");
				}
			);
		}
	}

	function editarCalificacion(obj,id_estudiante,id_paralelo,id_aporte_evaluacion)
	{
		var str = obj.value;
		var id = obj.id;
		var fila = id.substr(id.indexOf("_")+1);

		//Validacion de la calificacion
		str = eliminaEspacios(str);
		var permitidos = ['a', 'b', 'c', 'd', 'e', 'A', 'B', 'C', 'D', 'E'];
		var idx = permitidos.indexOf(str);
		if(str != '') { 
			if(idx == -1) {
				alert("La calificacion debe estar en el rango de A a E");
				obj.value = "";
			} else {
				$.post("inspeccion/obtener_escala_comportamiento.php",
					{
						co_calificacion: document.getElementById("puntaje_"+fila).value
					},
					function(resultado)
					{
						if(resultado==false) { // Si existe algun error
							alert(resultado);
						} else {
							//alert(resultado);
							var JSONEscalaComportamiento = eval('(' + resultado + ')');
							var id_escala_comportamiento = JSONEscalaComportamiento.id_escala_comportamiento;
							document.getElementById('equivalencia_'+fila).value = JSONEscalaComportamiento.ec_relacion;
							$.post("tutores/editar_calificacion.php",
								{
									id_estudiante: id_estudiante,
									id_paralelo: id_paralelo,
									id_escala_comportamiento: id_escala_comportamiento,
									id_aporte_evaluacion: id_aporte_evaluacion,
									co_calificacion: str.toUpperCase()
								},
								function(resultado)
								{
									if(resultado) { // Solo si existe resultado
										$("#mensaje_rubrica").html(resultado);
									}
								}
							);	
						}
					}
				);
			}
		} else {
			//Aqui va el codigo para eliminar la calificacion del comportamiento
			$.post("tutores/obtener_id_comportamiento_tutor.php",
				{
					id_paralelo: id_paralelo,
					id_estudiante: id_estudiante,
					id_aporte_evaluacion: id_aporte_evaluacion
				},
				function(resultado) {
					if(resultado==false) { // Si existe algun error
						alert(resultado);
					} else {
						//Aqui va el codigo para recuperar el id_comportamiento_tutor
						var JSONEscalaComportamiento = eval('(' + resultado + ')');
						var id_comportamiento_tutor = JSONEscalaComportamiento.id_comportamiento_tutor;
						if(id_comportamiento_tutor != 0) {
							$.post("tutores/eliminar_comportamiento_tutor.php",
								{
									id_comportamiento_tutor: id_comportamiento_tutor
								},
								function(resultado) {
									if(resultado==false) {
										alert(resultado);
									} else {
										//Aqui va el mensaje de exito o fracaso al eliminar el registro
										$("#mensaje_rubrica").html(resultado);
										document.getElementById('equivalencia_'+fila).value = '';
									}
								}
							);
						}
					}
				}
			);
		}
	}

</script>
</body>
</html>