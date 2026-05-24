<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1 id="titulo_principal">
			Cierre de Periodos
		</h1>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Default box -->
		<div class="box box-info">
			<div class="box-header with-border">
				<div class="box-tools pull-right" style="margin-top: 4px; margin-right: 20px">
					<form id="form-search" action="buscar_cierres.php" method="POST">
						<div class="input-group input-group-md" style="width: 300px;">
							<input type="text" name="patron" id="patron" class="form-control pull-right text-uppercase" placeholder="Buscar Aporte de Evaluación...">
							<div class="input-group-btn">
								<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
							</div>
						</div>
					</form>
				</div>
				<br><br>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-8 table-responsive">
						<table id="t_cierres_periodos" class="table table-striped table-hover">
							<thead>
								<tr>
									<th>Id</th>
									<th>Aporte</th>
									<th>Paralelo</th>
									<th>Figura</th>
									<th>Jornada</th>
									<th>Fecha de Apertura</th>
									<th>Fecha de Cierre</th>
									<th>Estado</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="tbody_cierres_periodos">
								<!-- Aquí se pintarán los registros recuperados de la BD mediante AJAX -->
							</tbody>
						</table>
					</div>
					<div class="col-md-4">
                        <div class="panel-body fuente9">
                            <!-- Horizontal Form -->
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 id="titulo" class="box-title">Nuevo Usuario</h3>
                                </div>
                                <!-- /.box-header -->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /.box -->
	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->