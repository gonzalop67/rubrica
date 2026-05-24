<link href="calendario/calendar-blue.css" rel="stylesheet" type="text/css" />
<div class="content-wrapper">
    <div id="cerrarPeriodosEvaluacion" class="col-sm-12">
        <br>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="text-center">Abrir / Cerrar Periodos de Evaluación</h4>
            </div>
            <div class="panel-body">
                <form id="form_malla" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Periodo:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboPeriodosEvaluacion">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Paralelo:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 2px;">
                            <select class="form-control fuente9" id="cboParalelo">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="row" id="botones_insercion">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button type="button" class="btn btn-block btn-primary hide" data-toggle="modal" data-target="#procesarCierresModal" name="process_all" id="process_all">
                                Procesar
                            </button>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <!-- message -->
                <div id="text_message" class="fuente9 text-center"></div>
                <!-- table -->
                <table id="tabla" class="table fuente9 hide">
                    <thead>
                        <tr>
                            <th>
                                <input id="check_all" name="main_checkbox" type="checkbox" style="vertical-align: middle;">
                            </th>
                            <th>Nro.</th>
                            <th>Id.</th>
                            <th>Nombre</th>
                            <th>Fecha de Apertura</th>
                            <th>Fecha de Cierre</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista_items">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Procesar Cierre Modal -->
<div class="modal fade" id="procesarCierresModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title text-center" id="myModalLabel2">Procesar Fecha de Cierre</h4>
			</div>
			<form id="form_process" action="" method="post" autocomplete="off">
				<input type="hidden" name="id_estudiante" id="id_estudiante">
				<div class="modal-body fuente9">
					<div class="form-group row">
						<label for="procesar_fecha_cierre" class="col-sm-2 col-form-label">Fecha de cierre:</label>
						<div class="col-sm-10">
							<input type="date" class="form-control" id="procesar_fecha_cierre" name="procesar_fecha_cierre" value="" placeholder="aaaa-mm-dd" maxlength="10">
							<span id="mensaje_error_fecha_cierre" style="color: #e73d4a"></span>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
					<button type="button" class="btn btn-success" onclick="procesarFechaCierre()"><span class="glyphicon glyphicon-save"></span> Procesar</a>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/calendar.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/lang/calendar-sp.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/calendar-setup.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        cargarPeriodosEvaluacion();
        cargarParalelos();
        //If check_all checked then check all table rows
		$("#check_all").on("click", function() {
			if ($("input:checkbox").prop("checked")) {
				$("input:checkbox[name='row-check']").prop("checked", true);
			} else {
				$("input:checkbox[name='row-check']").prop("checked", false);
			}
			toggleBtnState();
		});
		// Check each table row checkbox
		$(document).on('change', 'input[type="checkbox"][name="row-check"]', function() {
			if ($('input[type="checkbox"][name="row-check"]').length == $(
					'input[type="checkbox"][name="row-check"]:checked').length) {
				$('input[type="checkbox"][name="main_checkbox"]').prop('checked', true);
			} else {
				$('input[type="checkbox"][name="main_checkbox"]').prop('checked', false);
			}
			toggleBtnState();
		});
        $("#cboPeriodosEvaluacion").change(function(e) {
            e.preventDefault();
            $("#tabla tbody").html("");
            $('#cboParalelo').val(0);
            $("#mensaje").html("Debe seleccionar un Paralelo...");
            $("#cboParalelo").focus();
        });
        $("#cboParalelo").change(function(e) {
            e.preventDefault();
            var id_paralelo = $("#cboParalelo").val();
            if (id_paralelo == 0) {
                $("#tabla tbody").html("");
                $("#mensaje").html("Debe seleccionar un Paralelo...");
            } else {
                // Aquí se cargan los cierres definidos para el Paralelo y el periodo elegidos
                $("#tabla").removeClass("hide");
                cargarAportesEvaluacion(false);
            }
        });
    });

    function sel_texto(input) {
        $(input).select();
    }

    function toggleBtnState() {
		let selectedItems = $('input[type="checkbox"][name="row-check"]:checked').length;

		if (selectedItems > 0) {
			$('button#process_all').text('Procesar (' + selectedItems + ')').removeClass('hide');
		} else {
			$('button#process_all').addClass('hide');
		}
	}

    function procesarFechaCierre() {
		let fecha_cierre = $("#procesar_fecha_cierre").val();
		if (fecha_cierre.trim() == "") {
			Swal.fire({
				title: "Oops!",
				text: "Debes ingresar una fecha de cierre!",
				icon: "error"
			});
		} else {
			let checkbox = $('input[type="checkbox"][name="row-check"]:checked');
			if (checkbox.length > 0) {
				let checkbox_value = [];
                let texto = ""; 
				$(checkbox).each(function() {
					checkbox_value.push($(this).val());
				});

				$.ajax({
					url: "aportes_evaluacion/procesar_fecha_cierre.php",
					method: "POST",
					data: {
						checkbox_value: checkbox_value,
						fecha_cierre: fecha_cierre
					},
					dataType: 'json',
					success: function(response) {
                        if (response.contador === 1) {
                            texto = "Se procesó " + response.contador + " registro!";
                        } else {
                            texto = "Se procesaron " + response.contador + " registros!"
                        }
						cargarAportesEvaluacion(false);
						Swal.fire({
							title: "Muy bien!",
							text: texto,
							icon: "success"
						});
						$("#form_process")[0].reset();
						$('#procesarCierresModal').modal('hide');
						$('button#process_all').addClass('hide');
						$('input[type="checkbox"][name="main_checkbox"]').prop('checked', false);
					}
				});
			} else {
				alert("Selecciona al menos un registro");
			}
		}
	}

    function cargarPeriodosEvaluacion() {
        $.get("scripts/cargar_periodos_evaluacion.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboPeriodosEvaluacion").append(resultado);
                }
            }
        );
    }

    function cargarParalelos() {
        $.get("scripts/cargar_paralelos_especialidad.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#cboParalelo').append(resultado);
            }
        });
    }

    function cargarAportesEvaluacion(iDesplegar) {
        var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
        var id_paralelo = $("#cboParalelo").val();
        if (id_periodo_evaluacion == 0) {
            $("#mensaje").css("color", "red");
            $("#mensaje").html("Debe seleccionar un Per&iacute;odo de Evaluaci&oacute;n...");
            $("#lista_aportes_evaluacion").html("");
            $("#cboPeriodosEvaluacion").focus();
        } else if (id_paralelo == 0) {
            $("#mensaje").css("color", "red");
            $("#mensaje").html("Debe seleccionar un Paralelo...");
            $("#lista_aportes_evaluacion").html("");
            $("#cboParalelo").focus();

        } else {
            $.ajax({
                type: "POST",
                url: "scripts/listar_aportes_estados.php",
                data: "id_periodo_evaluacion=" + id_periodo_evaluacion + "&id_paralelo=" + id_paralelo,
                success: function(resultado) {
                    if (!iDesplegar) $("#mensaje").html("");
                    //$("#lista_aportes_evaluacion").html(resultado);
                    $("#tabla tbody").html(resultado);
                }
            });
        }
    }

    function actualizarAporteEvaluacion(id, nombre, fecha_apertura, fecha_cierre) {
        var txt_fecha_apertura = fecha_apertura.value;
        var txt_fecha_cierre = fecha_cierre.value;
        var id_paralelo = $("#cboParalelo").val();
        $("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
        $.ajax({
            type: "POST",
            url: "scripts/actualizar_fechas_aporte_evaluacion.php",
            data: "id_aporte_evaluacion=" + id + "&id_paralelo=" + id_paralelo + "&ap_nombre=" + nombre + "&fecha_apertura=" + txt_fecha_apertura + "&fecha_cierre=" + txt_fecha_cierre,
            success: function(resultado) {
                $("#mensaje").css("color", "blue");
                $("#mensaje").html(resultado);
                cargarAportesEvaluacion(true);
            }
        });
    }
</script>