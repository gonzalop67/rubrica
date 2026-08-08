<!-- Modal Papelera de Menús -->
<div class="modal fade" id="papeleraMenuModal" tabindex="-1" role="dialog" aria-labelledby="papeleraMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="papeleraMenuModalLabel">
                    <i class="fas fa-trash-restore mr-2"></i> Papelera de Reciclaje (Menús Eliminados)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <!-- Tabla responsiva para listar los eliminados -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 8%;">Ícono</th>
                                <th>Nombre / Texto</th>
                                <th>URL / Enlace</th>
                                <th>Fecha Eliminación</th>
                                <th class="text-center" style="width: 15%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-papelera-body">
                            <!-- Los registros eliminados se cargarán aquí dinámicamente vía AJAX -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Contenedor de carga vacío -->
                <div id="papelera-vacia-msg" class="text-center text-muted py-5 d-none">
                    <i class="fas fa-folder-open fa-2x mb-2 text-secondary"></i>
                    <p class="mb-0">La papelera está vacía. No hay menús eliminados.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
