<?php
session_start();
require_once("../scripts/clases/class.mysql.php");
require_once("../scripts/clases/class.encrypter.php");

//Instancia de la clase MySQL
$db = new mysql();

if (!isset($_SESSION['usuario_logueado']) or !$_SESSION['usuario_logueado'])
    header("Location: index.php");
else {
    //Primero obtener el id_usuario para obtener los datos correspondientes
    $id_usuario = encrypter::decrypt($_GET['id_usuario']);

    $db = new mysql();

    //Obtengo los nombres del usuario
    $consulta = $db->consulta("
    SELECT SUBSTRING_INDEX(us_apellidos, ' ', 1) AS primer_apellido, 
    SUBSTRING_INDEX(us_nombres, ' ', 1) AS primer_nombre,
    us_foto 
    FROM sw_usuario 
    WHERE id_usuario = $id_usuario
    ");
    $usuario = $db->fetch_assoc($consulta);
    $nombreUsuario = $usuario["primer_nombre"] . " " . $usuario["primer_apellido"];
    $userImage = "../public/uploads/" . $usuario["us_foto"];

    //Obtengo el periodo actual
    $consulta = $db->consulta("SELECT * FROM sw_periodo_lectivo WHERE pe_estado = 'A' ORDER BY pe_fecha_inicio DESC LIMIT 1");
    $periodo_lectivo = $db->fetch_object($consulta);

    $nombrePeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;

    if (isset($_GET["enlace"])) {
        $enlace = $_GET["enlace"];
    } else {
        $enlace = "dashboard.php";
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>SIAE-WEB Registro de Calificaciones</title>
    <link rel="shortcut icon" type="image/x-icon" href="./../favicon.ico" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <!-- jquery-ui -->
    <link rel="stylesheet" href="../assets/template/jquery-ui/jquery-ui.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/template/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="../assets/template/Ionicons/css/ionicons.min.css">

    <!-- sweetalert 2 -->
	<link rel="stylesheet" href="../assets/plugins/node_modules/sweetalert2/dist/sweetalert2.min.css">
	<script src="../assets/plugins/node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>

    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/template/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect. -->
    <link rel="stylesheet" href="../assets/template/dist/css/skins/skin-blue.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <!-- jquery-ui -->
    <script src="../assets/template/jquery-ui/jquery-ui.js"></script>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <link href="estilos.css" rel="stylesheet" type="text/css" />

</head>

<body class="hold-transition skin-blue sidebar-mini">
    <!-- -->
    <div class="wrapper">
        <!-- Main Header -->
        <header class="main-header">
            <!-- Logo -->
            <a href="admin2.php?id_usuario=<?php echo $_GET['id_usuario'] ?>&enlace=dashboard.php" class="logo">
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
                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                <img src="<?php echo $userImage ?>" class="user-image" alt="User Image">
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs"><?php echo $nombreUsuario ?></span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-right">
                                        <a href="logout.php" class="btn btn-default btn-flat"><i class="fa fa-sign-out" aria-hidden="true"></i> Salir</a>
                                    </div>
                                </li>
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
                        <img src="<?php echo $userImage ?>" class="img-circle" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p><?php echo $nombreUsuario ?></p>
                        <!-- Perfil -->
                        <small>Docente</small>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <ul class="sidebar-menu" data-widget="tree">
                    <!-- Optionally, you can add icons to the links -->
                    <?php
                    $active1 = '';
                    $active2 = '';
                    if (!isset($_GET['enlace'])) {
                        $active1 = 'active';
                    } else {
                        switch ($_GET['enlace']) {
                            case 'dashboard.php':
                                $active1 = 'active';
                                break;

                            case 'ver_horario.php':
                                $active2 = 'active';
                                break;

                            default:
                                # code...
                                break;
                        }
                    }

                    ?>
                    <li class="<?php echo $active1 ?>"><a href="admin2.php?id_usuario=<?php echo $_GET['id_usuario'] ?>&enlace=dashboard.php"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>

                    <!-- <li class="<?php echo $active1 ?>"><a href="admin2.php?id_usuario=<?php echo $id_usuario ?>&enlace=dashboard.php"><i class="fa fa-calendar-check-o"></i> <span>Registrar Asistencia</span></a></li> -->
                    
                    <!-- <li class="<?php echo $active2 ?>"><a href="admin2.php?id_usuario=<?php echo $id_usuario ?>&enlace=ver_horario.php"><i class="fa fa-eye"></i> <span>Ver Horario</span></a></li> -->
                </ul>
            </section>
        </aside>

        <?php include($enlace); ?>

        <!-- Main Footer -->
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 2.4.0
            </div>
            <?php
            $consulta = $db->consulta("SELECT in_nombre,
                                      in_url
                                 FROM sw_institucion
                                WHERE id_institucion = 1");
            $result = $db->fetch_object($consulta);
            $nom_institucion = $result->in_nombre;
            $url = $result->in_url;
            ?>
            <strong><?php echo date("Y"); ?> &copy; <a href="<?php echo $url; ?>" target="_blank"><?php echo $nom_institucion; ?></a>.</strong> Todos los derechos reservados.
        </footer>
    </div>

    <!-- ./wrapper -->

    <!-- REQUIRED JS SCRIPTS -->

    <!-- AdminLTE App -->
    <script src="../assets/template/dist/js/adminlte.min.js"></script>
    <script src="../assets/template/dist/js/scripts.js"></script>
</body>

</html>