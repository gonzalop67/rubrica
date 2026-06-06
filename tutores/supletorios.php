<style>
	.thead-dark {
        background-color: black;
        color: white;
    }
</style>	
	
	<div class="content-wrapper">
		<!-- Main content -->
		<section class="content">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Reporte de Calificaciones Supletorios</h3>
				</div>
				<!-- /.box-header -->
				<!-- Main content -->
				<div class="box-body">
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
						<form id="formulario_reporte" action="reporte/reporte_supletorios.php" method="post" target="_blank">
							&nbsp;<button class="btn btn-info">Ver Consolidado</button>
							<input id="id_paralelo" name="id_paralelo" type="hidden" value="" />
						</form>
					</div>
					<hr>
					<div id="nom_docente" class="text-center"></div>
					<div id="img_loader" class="text-center">
						<!-- <img src="imagenes/ajax-loader.gif" alt="Procesando..."> -->
					</div>
					<div id="tituloNomina" class="header2" style="font-size: 9pt;"> RESUMEN SUPLETORIOS </div>
					<div id="tbl_notas" class="table-responsive">
						<!-- Aqui van las calificaciones del estudiante -->
					</div>
				</div>
			</div>
		</section>
	</div>

	<script type="text/javascript" src="js/funciones.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			cargarAsignaturas();
			// mostrarTitulosPeriodos();
			$("#cboAsignaturas").change(function(e) {
				e.preventDefault();
				listarEstudiantesParalelo();
			});
		});

		function mostrarTitulosPeriodos() {
			$.post("calificaciones/mostrar_titulos_periodos.php", {
					pe_principal: 2, // 2 indica que son examenes supletorios
					alineacion: "center"
				},
				function(resultado) {
					console.log(resultado);
					if (resultado == false) {
						alert("Error");
					} else {
						$("#txt_rubricas").html(resultado);
					}
				}
			);
		}

		function cargarAsignaturas() {
			var id_paralelo_tutor = $("#id_paralelo_tutor").val();
			$("#id_paralelo").val(id_paralelo_tutor);
			// Luego obtengo el nombre del paralelo
			$.post("tutores/obtener_nombre_paralelo.php", {
					id_paralelo: $("#id_paralelo").val()
				},
				function(resultado) {
					if (resultado == false) {
						alert("Error");
					} else {
						$("#titulo_pagina").html("SUPLETORIOS DE " + resultado);
					}
				}
			);
			// Ahora obtengo el numero de estudiantes del paralelo
			$.post("calificaciones/contar_estudiantes_paralelo.php", {
					id_paralelo: $("#id_paralelo").val()
				},
				function(resultado) {
					if (resultado == false) {
						alert("Error");
					} else {
						var JSONNumRegistrosEstudiantes = eval('(' + resultado + ')');
						var total_registros = JSONNumRegistrosEstudiantes.num_registros;
						$("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados: " + total_registros);
					}
				}
			);
			// Luego obtengo las asignaturas asociadas al paralelo
			$.post("scripts/cargar_asignaturas_supletorios.php", {
					id_paralelo: $("#id_paralelo").val()
				},
				function(resultado) {
					if (resultado == false) {
						alert("Error");
					} else {
						document.getElementById("cboAsignaturas").length = 1;
						$("#cboAsignaturas").append(resultado);
					}
				}
			);
		}

		function listarEstudiantesParalelo() {
			// Aqui va el codigo para presentar las calificaciones por aporte de evaluacion, asignatura y paralelo
			var id_paralelo = document.getElementById("id_paralelo").value;
			var id_asignatura = document.getElementById("cboAsignaturas").value;
			if (id_asignatura == 0) {
				$("#lista_estudiantes_paralelo").html("Debe seleccionar una asignatura...");
				$("#cboAsignaturas").focus();
			} else {
				// Aqui va la llamada con AJAX al procedimiento que desplegará las calificaciones
				cargarEstudiantesParalelo(id_paralelo, id_asignatura);
			}
		}

		function cargarEstudiantesParalelo(id_paralelo, id_asignatura) {
			$("#tbl_notas").html("<div class='text-center'><img src=\"imagenes/ajax-loader-blue.GIF\" alt=\"Procesando...\"></div>");
			$.post("tutores/listar_supletorios.php", {
					id_paralelo: id_paralelo,
					id_asignatura: id_asignatura
				},
				function(resultado) {
					console.log(resultado);
					$("#tbl_notas").html(resultado);
				}
			);
		}

		function imprimirReporte() {
			var id_paralelo = document.getElementById("id_paralelo").value;
			if (id_paralelo == 0) {
				$("#lista_estudiantes_paralelo").addClass("error");
				$("#lista_estudiantes_paralelo").html("No se pudo obtener el id del paralelo asociado...");
			} else {
				$("#lista_estudiantes_paralelo").removeClass("error");
				document.getElementById("formulario_reporte").submit();
			}
		}
	</script>