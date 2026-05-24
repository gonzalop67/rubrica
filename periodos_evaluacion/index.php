<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?php echo $_SESSION['titulo_pagina'] ?></title>
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
</head>

<body>
	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				Periodos de Evaluación
				<small>Listado</small>
			</h1>
		</section>
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box box-solid">
				<div class="box-body">
					<hr>
					<div class="row">
						<div class="col-md-8 table-responsive">
							<div class="form-group">
								<!-- <label for="id_modalidad">Modalidad:</label> -->
								<select name="cboCursos" id="cboCursos" class="form-control">
									<option value="0">Seleccione un grado/curso...</option>
								</select>
							</div>
							<table id="example1" class="table table-hover">
								<thead>
									<tr>
										<th>Id</th>
										<th>Nombre</th>
										<th>Ponderación</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody id="tbody_periodos_evaluacion">
									<!-- Aqui vamos a poblar los periodos de evaluación ingresados en la BDD mediante AJAX  -->
								</tbody>
							</table>
							<div class="text-center">
								<ul class="pagination" id="pagination"></ul>
							</div>
							<input type="hidden" id="pagina_actual">
						</div>
						<div class="col-md-4">
							<div class="panel panel-success">
								<div id="titulo" class="panel-heading">Nuevo Periodo de Evaluación</div>
							</div>
							<div class="panel-body">
								<form id="frm-periodo-evaluacion" action="" method="post">
									<input type="hidden" name="id_periodo_evaluacion" id="id_periodo_evaluacion" value="0">
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="pe_nombre">Nombre:</label>
												<input type="text" name="pe_nombre" id="pe_nombre" class="form-control" autofocus>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="pe_abreviatura">Abreviatura:</label>
												<input type="text" name="pe_abreviatura" id="pe_abreviatura" class="form-control">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="cboTipoPeriodo">Tipo de Periodo:</label>
												<select class="form-control fuente9" name="cboTipoPeriodo" id="cboTipoPeriodo">
													<option value="0">Seleccione el tipo de periodo...</option>
												</select>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="pe_ponderacion">Ponderación:</label>
												<input type="number" min="0" max="1.00" step="0.01" class="form-control" id="pe_ponderacion" name="pe_ponderacion" value="0" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
											</div>
										</div>
									</div>

									<div class="form-group">
										<button id="btn-save" type="submit" class="btn btn-success">Guardar</button>
										<button id="btn-cancel" type="button" class="btn btn-info">Cancelar</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</section>
		<!-- /.content -->
	</div>
	<!-- /.content-wrapper -->
	<!-- <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script> -->
	<script src="assets/template/jquery-ui/jquery-ui.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			cargarCursos();
			//pagination(1);

			cargarTiposPeriodo();

			$("#tbody_periodos_lectivos").html("<tr><td colspan='6' align='center'>Debe seleccionar una modalidad...</td></tr>");

			$("#id_modalidad").change(function() {
				let id_modalidad = $(this).val();
				if (id_modalidad == 0) {
					$("#tbody_periodos_lectivos").html("<tr><td colspan='6' align='center'>Debe seleccionar una modalidad...</td></tr>");
					$("#pagination").hide();
				} else {
					pagination(1, id_modalidad);
					$("#pagination").show();
				}
			});

			$("#pe_fecha_inicio").datepicker({
				dateFormat: 'yy-mm-dd',
				firstDay: 1
			});

			$("#pe_fecha_fin").datepicker({
				dateFormat: 'yy-mm-dd',
				firstDay: 1
			});

			$("#btn-cancel").click(function() {
				$("#frm-periodo-lectivo")[0].reset();
				$("#titulo").html("Nuevo Periodo Lectivo");
				$("#btn-save").html("Guardar");
				$("#btn-save").prop("disabled", false);
				$("#pe_anio_inicio").focus();
			});

			$('#tbody_periodos_lectivos').on('click', '.item-edit', function() {
				var id_periodo_lectivo = $(this).attr('data');
				$("#titulo").html("Editar Periodo Lectivo");
				$("#btn-save").html("Actualizar");
				$.ajax({
					url: "periodos_lectivos/obtener_periodo_lectivo.php",
					data: {
						id: id_periodo_lectivo
					},
					method: "POST",
					dataType: "json",
					success: function(data) {
						$("#id_periodo_lectivo").val(id_periodo_lectivo);
						$("#pe_anio_inicio").val(data.pe_anio_inicio);
						$("#pe_anio_fin").val(data.pe_anio_fin);
						$("#pe_fecha_inicio").val(data.pe_fecha_inicio);
						$("#pe_fecha_fin").val(data.pe_fecha_fin);
						$("#pe_nota_minima").val(data.pe_nota_minima);
						$("#pe_nota_aprobacion").val(data.pe_nota_aprobacion);

						setearIndice("id_modalidad", data.id_modalidad);
						setearIndice("quien_inserta_comp", data.quien_inserta_comp);

						if (data.id_periodo_estado == 1)
							$("#btn-save").prop("disabled", false);
						else
							$("#btn-save").prop("disabled", true);
					},
					error: function(jqXHR, textStatus) {
						alert(jqXHR.responseText);
					}
				});
			});

			$("#frm-periodo-lectivo").submit(function(e) {
				e.preventDefault();
				var url;
				var id_periodo_lectivo = $("#id_periodo_lectivo").val();
				var pe_anio_inicio = $.trim($("#pe_anio_inicio").val());
				var pe_anio_fin = $.trim($("#pe_anio_fin").val());
				var pe_fecha_inicio = $.trim($("#pe_fecha_inicio").val());
				var pe_fecha_fin = $.trim($("#pe_fecha_fin").val());
				var pe_nota_minima = $.trim($("#pe_nota_minima").val());
				var pe_nota_aprobacion = $.trim($("#pe_nota_aprobacion").val());
				var id_modalidad = $("#id_modalidad").val();
				var quien_inserta_comp = $("#quien_inserta_comp").val();

				if (pe_anio_inicio == "") {
					Swal.fire("Ocurrió un error inesperado!", "Debe ingresar el año inicial.", "error");
				} else if (pe_anio_fin == "") {
					Swal.fire("Ocurrió un error inesperado!", "Debe ingresar el año final.", "error");
				} else if (pe_fecha_inicio == "") {
					Swal.fire("Ocurrió un error inesperado!", "Debe ingresar la fecha de inicio.", "error");
				} else if (pe_anio_inicio.length != 4) {
					Swal.fire("Ocurrió un error inesperado!", "Debe ingresar los 4 dígitos para el año inicial.", "error");
				} else if (pe_anio_fin.length != 4) {
					Swal.fire("Ocurrió un error inesperado!", "Debe ingresar los 4 dígitos para el año final.", "error");
				} else if (parseInt(pe_anio_inicio) > parseInt(pe_anio_fin)) {
					Swal.fire("Ocurrió un error inesperado!", "El año inicial no puede ser mayor que el año final.", "error");
				} else if (id_modalidad == 0) {
					Swal.fire("Ocurrió un error inesperado!", "Debe seleccionar una modalidad.", "error");
				} else {

					if ($("#btn-save").html() == "Guardar")
						url = "periodos_lectivos/insertar_periodo_lectivo.php";
					else if ($("#btn-save").html() == "Actualizar")
						url = "periodos_lectivos/actualizar_periodo_lectivo.php";

					$.ajax({
						url: url,
						method: "post",
						data: {
							id_periodo_lectivo: id_periodo_lectivo,
							id_modalidad: id_modalidad,
							anio_inicial: pe_anio_inicio,
							anio_final: pe_anio_fin,
							fec_ini: pe_fecha_inicio,
							fec_fin: pe_fecha_fin,
							nota_minima: pe_nota_minima,
							nota_aprobacion: pe_nota_aprobacion,
							quien_inserta_comp: quien_inserta_comp
						},
						dataType: "json",
						success: function(response) {

							Swal.fire({
								title: response.titulo,
								text: response.mensaje,
								icon: response.tipo_mensaje,
								confirmButtonText: 'Aceptar'
							});

							var pagina_actual = $("#pagina_actual").val();
							pagination(pagina_actual, id_modalidad);

							$("#frm-periodo-lectivo")[0].reset();

							if ($("#btn-save").html() == "Actualizar") {
								$("#btn-save").html("Guardar");
								$("#titulo").html("Nuevo Periodo Lectivo");
							}

						},
						error: function(jqXHR, textStatus) {
							console.log(jqXHR.responseText);
						}
					});
				}

			});
		});

		function cargarCursos() {
			// Obtengo los cursos definidos en la base de datos
			$.ajax({
				url: "scripts/cargar_cursos.php",
				success: function(response) {
					$('#cboCursos').append(response);
				},
				error: function(xhr, status, error) {
					alert(xhr.responseText);
				}
			});
		}

		function cargarTiposPeriodo() {
			// Obtengo los tipos de periodos de evaluación definidos en la base de datos
			$.ajax({
				url: "periodos_evaluacion/cargar_tipos_periodos_evaluacion.php",
				success: function(response) {
					$('#cboTipoPeriodo').append(response);
				},
				error: function(xhr, status, error) {
					alert(xhr.responseText);
				}
			});
		}

		function pagination(partida, id_modalidad) {
			$("#pagina_actual").val(partida);
			var url = "periodos_lectivos/paginar_periodos_lectivos.php";
			$.ajax({
				type: 'POST',
				url: url,
				data: {
					partida: partida,
					id_modalidad: id_modalidad
				},
				success: function(data) {
					var array = eval(data);
					$("#tbody_periodos_lectivos").html(array[0]);
					$("#pagination").html(array[1]);
				}
			});
			return false;
		}

		function setearIndice(nombreCombo, indice) {
			for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
				if (document.getElementById(nombreCombo).options[i].value == indice) {
					document.getElementById(nombreCombo).options[i].selected = indice;
				}
		}
	</script>
</body>

</html>