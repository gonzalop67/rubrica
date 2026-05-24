<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Periodos Lectivos
            <small>Crear</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-success">
                            <div id="titulo" class="panel-heading">Nuevo Periodo Lectivo</div>
                        </div>
                        <div class="panel-body">
                            <form id="frm-periodo-lectivo" action="periodos_lectivos/insertar_periodo_lectivo.php" method="post">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="pe_anio_inicio">Año Inicial:</label>
                                            <input type="text" name="pe_anio_inicio" id="pe_anio_inicio" value="" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="pe_anio_fin">Año Final:</label>
                                            <input type="text" name="pe_anio_fin" id="pe_anio_fin" value="" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div id="div_pe_fecha_inicio" class="form-group">
                                            <label for="pe_fecha_inicio">Fecha de inicio:</label>
                                            <div class="controls">
                                                <div class="input-group">
                                                    <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#pe_fecha_inicio').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label>
                                                    <input type="text" name="pe_fecha_inicio" id="pe_fecha_inicio" class="form-control date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div id="div_pe_fecha_fin" class="form-group">
                                            <label for="pe_fecha_fin">Fecha de fin:</label>
                                            <div class="controls">
                                                <div class="input-group date">
                                                    <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#pe_fecha_fin').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label>
                                                    <input type="text" name="pe_fecha_fin" id="pe_fecha_fin" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="pe_nota_minima" class="form-label fw-bold">Nota mínima:</label>
                                            <input type="number" min="0.01" step="0.01" class="form-control" name="pe_nota_minima" id="pe_nota_minima" value="0.01" required>
                                            <span id="span_pe_nota_minima" class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="pe_nota_aprobacion" class="form-label fw-bold">Nota aprobación:</label>
                                            <input name="pe_nota_aprobacion" type="number" min="7" max="10" step="0.01" class="form-control" id="pe_nota_aprobacion" value="7" required>
                                            <span id="span_pe_nota_aprobacion" class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="id_modalidad">Modalidad:</label>
                                            <select name="id_modalidad" id="id_modalidad" class="form-control" required>
                                                <option value="">Seleccione...</option>
                                                <!-- Aqui vamos a poblar las modalidades ingresadas en la BDD mediante AJAX  -->
                                            </select>
                                            <span id="span_id_modalidad" class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="quien_inserta_comp_id ">¿Quién inserta el comportamiento?:</label>
                                            <select name="quien_inserta_comp_id" id="quien_inserta_comp_id" class="form-control" required>
                                                <option value="">Seleccione...</option>
                                                <!-- Aqui vamos a poblar quien inserta el comportamiento mediante AJAX  -->
                                            </select>
                                            <span id="span_quien_inserta_comp_id" class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div id="subniveles" class="col-lg-6">
                                        <label for="niveles">Asociar Nivel de Educación:</label>
                                        <!-- Aquí vamos a poblar los subniveles de educación -->
                                    </div>
                                    <div id="subperiodos" class="col-lg-6">
                                        <label for="sub_periodos_evaluacion">Asociar Sub Periodos de Evaluación:</label>
                                        <!-- <?php foreach ($datos['sub_periodos_evaluacion'] as $v) : ?>
                                            <div>
                                                <input type="checkbox" name="sub_periodos[]" value="<?= $v->id_sub_periodo_evaluacion ?>">
                                                <?= $v->pe_nombre ?>
                                            </div>
                                        <?php endforeach ?> -->
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top: 4px;">
                                    <button id="btn-save" type="submit" class="btn btn-success">Guardar</button>
                                    <button class="btn btn-default" onclick="javascript:history.back()">Regresar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>

<script>
    $(function() {
        //Datemask yyyy/mm/dd
        $('#pe_fecha_inicio').inputmask('yyyy/mm/dd', {
            'placeholder': 'aaaa/mm/dd'
        });
        
        $('#pe_fecha_fin').inputmask('yyyy/mm/dd', {
            'placeholder': 'aaaa/mm/dd'
        });

        $("#pe_fecha_inicio").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
        });

        $("#pe_fecha_fin").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
        });

        cargarModalidades();
        cargarQuienInsertaComportamiento();
        cargarSubnivelesEducacion();
        cargarSubperiodosEvaluacion();
    });

    function cargarModalidades() {
        $.ajax({
            url: "periodos_lectivos/cargar_modalidades.php",
            dataType: "html",
            success: function(data) {
                $("#id_modalidad").append(data);
            },
            error: function(jqXHR, textStatus) {
                alert(jqXHR.responseText);
            }
        });
    }

    function cargarQuienInsertaComportamiento() {
        $.ajax({
            url: "periodos_lectivos/cargar_quien_inserta_comportamiento.php",
            dataType: "html",
            success: function(data) {
                $("#quien_inserta_comp_id").append(data);
            },
            error: function(jqXHR, textStatus) {
                alert(jqXHR.responseText);
            }
        });
    }

    function cargarSubnivelesEducacion() {
        $.ajax({
            url: "periodos_lectivos/cargar_sub_niveles_educacion.php",
            dataType: "html",
            success: function(data) {
                $("#subniveles").append(data);
            },
            error: function(jqXHR, textStatus) {
                alert(jqXHR.responseText);
            }
        });
    }

    function cargarSubperiodosEvaluacion(){
        $.ajax({
            url: "periodos_lectivos/cargar_sub_periodos_evaluacion.php",
            dataType: "html",
            success: function(data) {
                $("#subperiodos").append(data);
            },
            error: function(jqXHR, textStatus) {
                alert(jqXHR.responseText);
            }
        });
    }
</script>