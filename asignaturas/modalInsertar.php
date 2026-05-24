<!-- New Asignatura Modal -->
<div class="modal fade" id="nuevaAsignaturaModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel1">Nueva Asignatura</h4>
            </div>
            <form id="form_insert" action="" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-lg-2">
                            <label for="new_cbo_areas" class="control-label">Area:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="new_cbo_areas">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-2">
                            <label for="new_as_nombre" class="control-label">Nombre:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="new_as_nombre" value="">
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-2">
                            <label for="new_as_abreviatura" class="control-label">Abreviatura:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="new_as_abreviatura" value="">
                            <span class="help-desk error" id="mensaje3"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-2">
                            <label for="new_cbo_tipos" class="control-label">Tipo:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="new_cbo_tipos">
                                <!-- Aqui se cargan los tipos de asignaturas dinamicamente -->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="insertarAsignatura()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
                </div>
            </form>
        </div>
    </div>
</div>