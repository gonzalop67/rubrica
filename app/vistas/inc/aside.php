        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="<?php echo RUTA_URL ?>/assets/images/teacher-male-avatar.png" class="img-circle" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p><?php echo $datos['nombrePerfil'] ?></p>
                        <small><?php echo $datos['nombrePeriodoLectivo'] ?></small>
                    </div>
                </div>
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">MENU</li>
                    <li class="<?php echo "active" //service('request')->uri->getPath() == 'auth/dashboard' ? 'active' : '' ?>">
                        <a href="<?php echo RUTA_URL ?>/auth/dashboard">
                            <i class="fa fa-home"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-laptop"></i> <span><?php echo "Administración"; ?></span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="#"><i class="fa fa-circle-o"></i> Modalidades</a></li>
                            <li><a href="#"><i class="fa fa-circle-o"></i> Periodos Lectivos</a></li>
                            <li><a href="#"><i class="fa fa-circle-o"></i> Perfiles</a></li>
                            <li><a href="#"><i class="fa fa-circle-o"></i> Menús</a></li>
                            <li><a href="#"><i class="fa fa-circle-o"></i> Usuarios</a></li>
                        </ul>
                    </li>
                </ul>
            </section>
        </aside>