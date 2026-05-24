<?php
$perfilModelo = $this->modelo('Perfil');
$usuarioModelo = $this->modelo('Usuario');
$jornadaModelo = $this->modelo('Jornada');
$modalidadModelo = $this->modelo('Modalidad');
$institucionModelo = $this->modelo('Institucion');
$periodoLectivoModelo = $this->modelo('PeriodoLectivo');

$id_usuario = $_SESSION["id_usuario"];
$nombreUsuario = $usuarioModelo->obtenerNombreUsuario($id_usuario);
$us_foto = $usuarioModelo->obtenerFotoUsuario($id_usuario) == "" ? RUTA_URL . "/public/assets/images/teacher-male-avatar.png" : RUTA_URL . "/public/uploads/" . $usuarioModelo->obtenerFotoUsuario($id_usuario);

$id_perfil = $_SESSION["id_perfil"];
$nombrePerfil = $perfilModelo->obtenerNombrePerfil($id_perfil);

$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
$periodo_lectivo = $periodoLectivoModelo->obtenerPeriodoLectivo($id_periodo_lectivo);
$nombrePeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;

$urlInstitucion = $institucionModelo->obtenerUrlInstitucion();
$nombreInstitucion = $institucionModelo->obtenerNombreInstitucion();

//Obtengo el nombre de la modalidad asociada
$nombreModalidad = $modalidadModelo->obtenerNombreModalidadPorIdPeriodoLectivo($id_periodo_lectivo);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo NOMBRESITIO . $datos['titulo'] ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo RUTA_URL ?>/img/favicon.ico" />
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/assets/template/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/assets/template/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/assets/template/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/assets/template/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/assets/template/dist/css/skins/skin-blue.min.css">
    <!-- jquery-ui -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/assets/template/jquery-ui/jquery-ui.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/assets/template/select2/select2.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/assets/template/toastr/toastr.min.css">
    <!-- DataTable -->
    <!-- <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/assets/template/datatables/dataTables.bootstrap.css"> -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap.css" rel="stylesheet">

    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/assets/template/sweetalert2/dist/sweetalert2.min.css">

    <!-- Estilos propios de esta pagina -->
    <style type="text/css">
        .ocultar {
            display: none;
        }

        .blanco {
            color: #FFF;
        }

        .centered {
            text-align: center !important;
            vertical-align: middle !important;
        }

        .mb-3 {
            margin-bottom: 3px;
        }

        label.requerido::after {
            content: " *";
            color: #e73d4a;
        }
    </style>

    <!-- jQuery 3 -->
    <script src="<?php echo RUTA_URL ?>/public/assets/template/jquery/dist/jquery.min.js"></script>
    <!-- <script src="https://cdn.datatables.net/v/bs/dt-2.1.8/datatables.min.js"></script> -->
    <!-- <script type="text/javascript" src="<?php echo RUTA_URL ?>/public/js/scripts.js"></script> -->

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <!-- Main Header -->
        <header class="main-header">

            <!-- Logo -->
            <a href="<?php echo RUTA_URL ?>/auth/dashboard" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>SIAE</b></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><b>SIAE <?php echo $nombrePeriodoLectivo ?></b></span>
            </a>

            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->
                        <li class="dropdown messages-menu">
                            <!-- Menu toggle button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-comments-o"></i>
                                <span class="label label-danger">72</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 4 messages</li>
                                <li>
                                    <!-- inner menu: contains the messages -->
                                    <ul class="menu">
                                        <li>
                                            <!-- start message -->
                                            <a href="#">
                                                <div class="pull-left">
                                                    <!-- User Image -->
                                                    <img src="<?php echo RUTA_URL ?>/public/assets/images/student-male.png" class="img-circle" alt="User Image">
                                                </div>
                                                <!-- Message title and timestamp -->
                                                <h4>
                                                    Support Team
                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                </h4>
                                                <!-- The message -->
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <!-- end message -->
                                    </ul>
                                    <!-- /.menu -->
                                </li>
                                <li class="footer"><a href="#">See All Messages</a></li>
                            </ul>
                        </li>
                        <!-- /.messages-menu -->

                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                <img src="<?php echo $us_foto ?>" class="user-image" alt="User Image">
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs"><?php echo $nombreUsuario ?></span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="#"><i class="fa fa-check-square-o" aria-hidden="true"></i> Cambiar Clave</a></li>
                                <li><a href="#"><i class="fa fa-comments-o" aria-hidden="true"></i> Comentarios</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo RUTA_URL ?>/auth/logout"><i class="fa fa-sign-out" aria-hidden="true"></i> Salir</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">

            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">

                <!-- Sidebar user panel (optional) -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="<?php echo $us_foto ?>" class="img-circle" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p><?php echo $nombrePerfil ?></p>
                        <small><?php echo $nombreModalidad ?></small>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header"><?php echo strtoupper($nombrePerfil) ?></li>
                    <!-- Optionally, you can add icons to the links -->
                    <?= $active = $_GET['url'] == 'auth/dashboard' ? 'active' : '' ?>
                    <li class="<?= $active ?>"><a href="<?php echo RUTA_URL ?>/auth/dashboard"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
                    <?php
                    $menuModelo = $this->modelo('Menu');
                    $menusNivel1 = $menuModelo->listarMenusNivel1($_SESSION['id_perfil']);
                    foreach ($menusNivel1 as $menu) {
                    ?>
                        <?php if (count($menuModelo->listarMenusHijos($menu->id_menu)) > 0) { ?>
                            <li class="treeview">
                                <a href=""><i class="fa fa-link"></i> <span><?php echo $menu->mnu_texto; ?></span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <?php
                                    foreach ($menuModelo->listarMenusHijos($menu->id_menu) as $menu2) {
                                        $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
                                        $active = ($uriSegments[2] == $menu2->mnu_link) ? 'active' : '';
                                        $icono = "";
                                        if ($menu2->mnu_icono != "") {
                                            $icono = "<i class='fa fa-fw $menu2->mnu_icono'></i> ";
                                        }
                                    ?>
                                        <li class="<?= $active ?>"><a href="<?php echo RUTA_URL . '/' . $menu2->mnu_link; ?>"><?php echo $icono; ?><?php echo $menu2->mnu_texto; ?></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } else {
                            $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
                            $active = ($uriSegments[2] == $menu->mnu_link) ? 'active' : '';
                        ?>
                            <li class="<?= $active ?>">
                                <a href="<?php echo RUTA_URL . '/' . $menu->mnu_link; ?>">
                                    <span>
                                        <?php echo $menu->mnu_texto; ?>
                                    </span>
                                </a>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
                <!-- /.sidebar-menu -->
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <?php require RUTA_APP . "/vistas/" . $datos['nombreVista'] ?>
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Versión</b> 2.5.0
            </div>
            <strong><?php echo date("Y"); ?> &copy; <a href="<?php echo $urlInstitucion ?>" target="_blank"><?php echo $nombreInstitucion ?></a>.</strong> Todos los derechos reservados.
        </footer>

    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED JS SCRIPTS -->

    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo RUTA_URL ?>/public/assets/template/bootstrap/js/bootstrap.min.js"></script>

    <!-- Sweetalert2 -->
    <script src="<?php echo RUTA_URL ?>/public/assets/template/sweetalert2/dist/sweetalert2.all.min.js"></script>

    <!-- plotly -->
    <script src="<?php echo RUTA_URL ?>/public/js/plotly-latest.min.js"></script>
    <!-- jquery-ui -->
    <script src="<?php echo RUTA_URL ?>/public/assets/template/jquery-ui/jquery-ui.js"></script>
    <!-- jquery-ui-validation -->
    <script src="<?php echo RUTA_URL ?>/public/js/jquery-validation/jquery.validate.min.js"></script>
    <script src="<?php echo RUTA_URL ?>/public/js/jquery-validation/localization/messages_es.min.js"></script>
    <!-- Select2 -->
    <script src="<?php echo RUTA_URL ?>/public/assets/template/select2/select2.min.js"></script>
    <!-- Toastr -->
    <script src="<?php echo RUTA_URL ?>/public/assets/template/toastr/toastr.min.js"></script>
    <!-- DataTable -->
    <script src="<?php echo RUTA_URL ?>/public/assets/template/datatables/dataTables.js"></script>
    <script src="<?php echo RUTA_URL ?>/public/assets/template/datatables/dataTables.bootstrap.js"></script>

    <!-- AdminLTE App -->
    <script src="<?php echo RUTA_URL ?>/public/assets/template/dist/js/adminlte.min.js"></script>
    <script src="<?php echo RUTA_URL ?>/public/assets/template/dist/js/scripts.js"></script>

    <script type="text/javascript" src="<?php echo RUTA_URL ?>/public/js/funciones.js"></script>
</body>

</html>