<div class="content-wrapper">
    <div id="perEvalApp" class="col-sm-9 col-sm-offset-1">
        <h2>Períodos de Evaluación</h2>
        <input type="hidden" id="id_periodo_evaluacion">
        <div class="panel panel-default">
            <!-- form -->
            <form id="form_per_eval" action="" class="app-form">
                <button id="btn-new" type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#addnew">
                    Nuevo Per&iacute;odo de Evaluaci&oacute;n
                </button>
            </form>
            <!-- message -->
            <div id="text_message" class="fuente9 text-center"></div>
            <!-- table -->
            <table class="table fuente9">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th colspan=2>Acciones</th>
                    </tr>
                </thead>
                <tbody id="periodos_evaluacion">
                    <!-- Aqui desplegamos el contenido de la base de datos -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- New Menu Modal -->
<div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Nuevo Per&iacute;odo de Evaluaci&oacute;n</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-3 text-right">
                        <label class="control-label">Nombre:</label>
                    </div>
                    <div class="col-lg-9">
                        <input type="text" class="form-control" id="new_pe_nombre" value="">
                    </div>
                </div>
                <div class="row" style="margin-top: 2px;">
                    <div class="col-lg-3 text-right">
                        <label class="control-label">Abreviatura:</label>
                    </div>
                    <div class="col-lg-9">
                        <input type="text" class="form-control" id="new_pe_abreviatura" value="">
                    </div>
                </div>
                <div class="row" style="margin-top: 2px;">
                    <div class="col-lg-3 text-right">
                        <label class="control-label">Tipo:</label>
                    </div>
                    <div class="col-lg-9">
                        <select class="form-control" id="new_pe_principal">
                            <option value="0"> Seleccione... </option>
                            <!-- Definido en la tabla sw_tipo_periodo
                            <option value="1"> 	QUIMESTRE </option>
                            <option value="2"> 	SUPLETORIO </option>
                            <option value="3"> 	REMEDIAL </option>
                            <option value="4"> 	DE GRACIA </option>-->
                        </select>
                    </div>
                </div>
                <div id="div_rango" style="display: none">
                    <fieldset>
                        <legend>Rango de obtención del examen:</legend>
                        <div class="row" style="margin-top: 2px;">
                            <div class="col-lg-3 text-right">
                                <label class="control-label">Nota desde:</label>
                            </div>
                            <div class="col-lg-9">
                                <input type="number" min="0.01" step="0.01" class="form-control" id="nota_desde" value="0.01" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                            </div>
                        </div>
                        <div class="row" style="margin-top: 2px;">
                            <div class="col-lg-3 text-right">
                                <label class="control-label">Nota hasta:</label>
                            </div>
                            <div class="col-lg-9">
                                <input type="number" min="0.01" step="0.01" class="form-control" id="nota_hasta" value="0.01" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="addPerEval()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
            </div>
        </div>
    </div>
</div>
<!-- Edit Menu Modal -->
<div class="modal fade" id="editPerEval" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Editar Per&iacute;odo de Evaluaci&oacute;n</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="edit_pe_nombre" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Abreviatura:</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="edit_pe_abreviatura" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label class="control-label" style="position:relative; top:7px;">Tipo:</label>
                    </div>
                    <div class="col-lg-10">
                        <select class="form-control" id="edit_pe_principal">
                            <option value="0">Seleccione...</option>
                            <!--<option value="1">QUIMESTRE</option>
                            <option value="2">SUPLETORIO</option>
                            <option value="3">REMEDIAL</option>
                            <option value="4">DE GRACIA</option>-->
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="updatePerEval()"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // JQuery Listo para utilizar
        cargarPerEval();
        cargarTiposPeriodo();
        $("#new_pe_principal").change(function(){
            if ($(this).val() === "1") {
                $("#div_rango").hide();
            } else {
                $("#div_rango").show();
            }
        });
    });

    function setearIndice(nombreCombo, indice) {
        for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
            if (document.getElementById(nombreCombo).options[i].value == indice) {
                document.getElementById(nombreCombo).options[i].selected = indice;
            }
    }

    function cargarPerEval() {
        // Obtengo todas los periodos de evaluacion ingresados en la base de datos
        $.ajax({
            url: "periodos_evaluacion/cargar_periodos_evaluacion.php",
            success: function(response) {
                $("#periodos_evaluacion").html("");
                $('#periodos_evaluacion').append(response);
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
                $('#new_pe_principal').append(response);
                $('#edit_pe_principal').append(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function editPerEval(id) {
        //Obtengo los datos del periodo de evaluación seleccionado
        $("#text_message").html("<img src='./imagenes/ajax-loader.gif' alt='procesando'>");
        $.ajax({
            url: "periodos_evaluacion/obtener_periodo_evaluacion.php",
            method: "POST",
            type: "html",
            data: {
                id_periodo_evaluacion: id
            },
            success: function(response) {
                $("#text_message").html("");
                $("#id_periodo_evaluacion").val(id);
                var periodo = jQuery.parseJSON(response);
                $("#edit_pe_nombre").val(periodo.pe_nombre);
                $("#edit_pe_abreviatura").val(periodo.pe_abreviatura);
                setearIndice("edit_pe_principal", periodo.pe_principal);
                $('#editPerEval').modal('show');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function updatePerEval() {
        var id = $("#id_periodo_evaluacion").val();
        var nombre = $("#edit_pe_nombre").val();
        var abreviatura = $("#edit_pe_abreviatura").val();
        var pe_principal = $("#edit_pe_principal").val();
        $.ajax({
            url: "periodos_evaluacion/actualizar_periodo_evaluacion.php",
            method: "POST",
            type: "html",
            data: {
                id_periodo_evaluacion: id,
                pe_nombre: nombre,
                pe_abreviatura: abreviatura,
                pe_tipo: pe_principal
            },
            success: function(response) {
                $("#text_message").html(response);
                cargarPerEval();
                $('#editPerEval').modal('hide');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function deletePerEval(id) {
        //Elimino el periodo de evaluacion mediante AJAX
        $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...'>");
        $.ajax({
            url: "periodos_evaluacion/eliminar_periodo_evaluacion.php",
            method: "POST",
            type: "html",
            data: {
                id_periodo_evaluacion: id
            },
            success: function(response) {
                $("#text_message").html(response);
                cargarPerEval();
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function addPerEval() {
        var pe_nombre = $("#new_pe_nombre").val();
        var pe_abreviatura = $("#new_pe_abreviatura").val();
        var pe_principal = $("#new_pe_principal").val();
        $.ajax({
            url: "periodos_evaluacion/insertar_periodo_evaluacion.php",
            method: "POST",
            type: "html",
            data: {
                pe_nombre: pe_nombre,
                pe_abreviatura: pe_abreviatura,
                pe_tipo: pe_principal
            },
            success: function(response) {
                cargarPerEval();
                $('#addnew').modal('hide');
                $("#text_message").html(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
</script>