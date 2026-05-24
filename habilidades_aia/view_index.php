<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Habilidades
            <small>Acompañamiento Integral en el Aula</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <button id="btn-new" class="btn btn-primary" data-toggle="modal" data-target="#nuevaHabilidadModal"><i class="fa fa-plus-circle"></i> Nueva Habilidad</button>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="t_habilidades" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Id</th>
                                    <th>Categoría</th>
                                    <th>Nivel de Educación</th>
                                    <th>Nombre</th>
                                    <th>Abreviatura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="lista_habilidades">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.content-wrapper -->