<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr">
    <head>
        <title>E-PRAKERIN| Petrosida Gresik</title>
        <meta content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="keywords" content="praktek kerja industri, kimia, laboratorium, prakerin, magang, pestisida, pupuk, bio" />
        <meta name="description" content="praktek kerja industri">
        <meta name="author" content="petrosida gresik">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        
        <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>

        <link rel="icon" href="<?php echo base_url(); ?>gambar/favicon.png" type="image/x-icon" >

        <!-- Custom fonts for this template-->
        <link href="<?php echo base_url(); ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="//fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

        <!-- Custom styles for this template-->
        <link href="<?php echo base_url(); ?>assets/css/sb-admin-2.min.css" rel="stylesheet">

        <!-- Custom styles for this page -->
        <link href="<?php echo base_url(); ?>assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-6GR6909W07"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
        
          gtag('config', 'G-6GR6909W07');
        </script>
    </head>
    
    <body id="page-top">
        <!-- Page Wrapper -->
        <div id="wrapper">

        <?php
            $auth = $this->session->userdata("SES_PRIV");
            $name = $this->session->userdata("SES_USERNAME");
            $img  = $this->session->userdata("SES_PICT");
                if ($auth) {
            ?>
            
            <!--left-fixed -navigation-->
            <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

                <!-- Sidebar - Brand -->
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo base_url(); ?>">
                  <div class="sidebar-brand-icon">
                      <img src="<?php echo base_url(); ?>/gambar/logo3.png" width="100%">
                  </div>
                 <!-- <div class="sidebar-brand-text">PRAKERIN<br/><font size=2pt>Petrosida Gresik</font></div>-->
                </a>

                <!-- Divider -->
                <hr class="sidebar-divider my-0">

                <!-- Nav Item - Dashboard -->
                <li class="nav-item active">
                  <a class="nav-link" href="<?php echo base_url(); ?>">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Beranda</span></a>
                </li>
                <!-- Divider -->
                <hr class="sidebar-divider">

                <?php if ($auth == '1' || $auth == '2') { ?>
                <!-- Heading -->
                <div class="sidebar-heading">
                  Admin Only
                </div>
                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                  <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Setting</span>
                  </a>
                  <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                      <h6 class="collapse-header">Custom User Data:</h6>
                      <a class="collapse-item" href="<?php echo base_url(); ?>setting/data/list">General Setting</a>
                      <a class="collapse-item" href="<?php echo base_url(); ?>type_user/data/list">Type User</a>
                      <a class="collapse-item" href="<?php echo base_url(); ?>user/data/list">Data User</a>
                    </div>
                  </div>
                </li>

                <!-- Nav Item - Utilities Collapse Menu -->
                <li class="nav-item">
                  <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Master Data</span>
                  </a>
                  <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                      <h6 class="collapse-header">All Data Master:</h6>
                      <a class="collapse-item" href="<?php echo base_url(); ?>depag/data/list">Register Departemen</a>
                      <a class="collapse-item" href="<?php echo base_url(); ?>persyaratan/data/list">Persyaratan Khusus</a>
					  <a class="collapse-item" href="<?php echo base_url(); ?>testimoni/data/list">Testimoni</a>
                    </div>
                  </div>
                </li>

                <?php
                $analisis_aktif = false;
                try {
                    $this->config->load('analisis');
                    $analisis_aktif = $this->config->item('analisis_aktif');
                } catch (Exception $e) {
                    $analisis_aktif = false;
                }
                if ($analisis_aktif && ($auth == 1 || $auth == 2)) {
                ?>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo base_url(); ?>analisis/dashboard">
                    <i class="fas fa-fw fa-chart-line"></i>
                    <span>Analisis</span>
                  </a>
                </li>
                <?php } ?>

                <!-- Nav Item - Utilities Collapse Menu -->
                <li class="nav-item">
                  <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReport" aria-expanded="true" aria-controls="collapseReport">
                    <i class="fas fa-fw fa-book"></i>
                    <span>Laporan</span>
                  </a>
                  <div id="collapseReport" class="collapse" aria-labelledby="headingReport" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                      <h6 class="collapse-header">All Report:</h6>
                      <a class="collapse-item" href="<?php echo base_url(); ?>laporan/data/list">Data Rekapitulasi PKL</a>
                    </div>
                  </div>
                </li>
                <?php } ?>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <div class="sidebar-heading">
                  General
                </div>

                <!-- Nav Item - Charts -->
                <li class="nav-item">
                 <?php if($auth==3 || $auth==4){?> <a class="nav-link" href="<?php echo base_url(); ?>transaksi/data/list">
                <?php
                 }else{
                     ?>
                     <a class="nav-link" href="<?php echo base_url(); ?>transaksi/data/list_pending">
                     <?php
                 }
                ?>
                    <i class="fas fa-fw fa-tasks"></i>
                    <span>Register</span></a>
                </li>

                <!-- Nav Item - Charts -->
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo base_url(); ?>tutorial/data/list">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Tata cara & Persyaratan</span></a>
                </li>
				<li class="nav-item">
                  <a class="nav-link" href="<?php echo base_url().'home/index_login2'; ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Kuota KP</span></a>
                </li>
                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">

                <!-- Sidebar Toggler (Sidebar) -->
                <div class="text-center d-none d-md-inline">
                  <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>

            </ul>
            <!-- End of Sidebar -->
                <?php } ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
              

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
        
                <?php if ($auth == null) { ?>

                <div class="topbar-divider d-none d-sm-block"></div>

                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow">
                  <a class="nav-link" href="<?php echo base_url(); ?>login/sistem/login_sistem">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">Login</span>
                    <img class="img-profile rounded-circle" src='<?php echo base_url() . "gambar/profile.png" ?>'>
                  </a>
                </li>
                <?php } else if ($auth) { 
                    if($img <> "" && file_exists("gambar/" . $img)){
                        $gambar = "/gambar/" . $img;
                    }else{
                        $gambar = "/gambar/profile.png";
                    }
                    ?>

                <div class="topbar-divider d-none d-sm-block"></div>

                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow">
                  <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $name?></span>
                    <img class="img-profile rounded-circle" src='<?php echo base_url() . $gambar ?>'>
                  </a>
                  <!-- Dropdown - User Information -->
                  <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="<?php echo base_url()?>user/data/list">
                      <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                      Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                      <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                      Logout
                    </a>
                  </div>
                </li>
                    
                <?php } ?>

              </ul>
        </nav>
