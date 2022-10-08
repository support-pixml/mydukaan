<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Font Awesome -->
<link rel="stylesheet" href="<?php echo base_url('admin_assets/plugins/fontawesome-free/css/all.min.css'); ?>">
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- Tempusdominus Bbootstrap 4 -->
<link rel="stylesheet" href="<?php echo base_url('admin_assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css'); ?>">
<!-- iCheck -->
<link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="<?php echo base_url('admin_assets/dist/css/adminlte.min.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('admin_assets/plugins/select2/css/select2.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('admin_assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css'); ?>">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="<?php echo base_url('admin_assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css'); ?>">

<link rel="stylesheet" href="<?php echo base_url('admin_assets/plugins/datatables-bs4/css/dataTables.bootstrap4.css'); ?>">

<link rel="stylesheet" href="<?php echo base_url('admin_assets/plugins/dropify/css/dropify.css'); ?>">
<!-- Daterange picker -->
<link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">

<link rel="stylesheet" href="<?php echo base_url('admin_assets/plugins/toastr/toastr.min.css'); ?>">
<!-- Google Font: Source Sans Pro -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
                <!-- <li class="nav-item d-none d-sm-inline-block">
                    <a href="index3.html" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link">Contact</a>
                </li> -->
            </ul>

        </nav>
        <?php $uri = service('uri'); ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/dashboard" class="brand-link">
                <!-- <img src="<?php echo base_url('assets/images/logo.png'); ?>" alt="Technofab Logo" class="brand-image img-circle elevation-3"
                    style="opacity: 1"> -->
                <span class="brand-text font-weight-light">D Store - Admin Panel</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                        <li class="nav-item">
                            <a href="/customers" class="nav-link <?= $uri->getSegment(1) == 'customers' || $uri->getSegment(1) == 'customer_plans' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Customers
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/signout" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>
                                    Log Out
                                </p>
                            </a>
                        </li>
                        <!-- <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-table"></i>
                                <p>
                                    Tables
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="pages/tables/simple.html" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Simple Tables</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="pages/tables/data.html" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>DataTables</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="pages/tables/jsgrid.html" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>jsGrid</p>
                                    </a>
                                </li>
                            </ul>
                        </li>                       -->
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>