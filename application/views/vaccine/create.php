<?php 
// Get CodeIgniter instance to access session
$CI =& get_instance();
$CI->load->library('session');

?>


<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="<?php echo base_url(); ?>assets/dist/css/style.min.css" rel="stylesheet">
</head>

<body>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin6">
            <nav class="navbar top-navbar navbar-expand-lg">
                <div class="navbar-header" data-logobg="skin6">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-lg-none" href="javascript:void(0)"><i
                            class="ti-menu ti-close"></i></a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <div class="navbar-brand">
                        <!-- Logo icon -->
                        <!-- <a href="index.html">
                            <img src="../assets/images/freedashDark.svg" alt="" class="img-fluid">
                        </a> -->
                    </div>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-lg-none waves-effect waves-light" href="javascript:void(0)"
                        data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i
                            class="ti-more"></i></a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-left me-auto ms-3 ps-1">
                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-end">
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <img src="<?php echo base_url(); ?>assets/avatar/<?php echo $user_info['image']; ?>" alt="user" class="rounded-circle"
                                    width="40" height="40">
                                <span class="ms-2 d-none d-lg-inline-block"><span>Hello,</span> <span
                                        class="text-dark"><?php echo ucfirst($user_info['first_name']); ?></span> <i data-feather="chevron-down"
                                        class="svg-icon"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-right user-dd animated flipInY">
                                <a class="dropdown-item" href="<?php echo base_url(); ?>profile"><i data-feather="user"
                                        class="svg-icon me-2 ms-1"></i>
                                    My Profile</a>
                                <a class="dropdown-item" href="<?php echo base_url(); ?>settings"><i data-feather="settings"
                                        class="svg-icon me-2 ms-1"></i>
                                    Account Setting</a>
                                <a style="color:red" class="dropdown-item" href="<?php echo base_url(); ?>logout"><i data-feather="power"
                                        class="svg-icon me-2 ms-1"></i>
                                    Logout</a>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="nav-small-cap"><span class="hide-menu">PetVax Manager</span></li>
                        <li class="sidebar-item"> 
                            <a class="sidebar-link sidebar-link" href="<?php echo base_url(); ?>dashboard" aria-expanded="false">
                                <i data-feather="home" class="feather-icon"></i>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <i class="fas fa-clock"></i>
                                <span class="hide-menu">Schedule </span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>schedule" class="sidebar-link">
                                        <span class="hide-menu"> Today</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>schedule/future" class="sidebar-link">
                                        <span class="hide-menu"> Up Comming</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span class="hide-menu">Incident </span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>incident" class="sidebar-link">
                                        <span class="hide-menu"> list</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="list-divider"></li>
                        <li class="nav-small-cap"><span class="hide-menu">File Management</span></li>
                        <li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <i class="fas fa-syringe"></i>
                                <span class="hide-menu">Vaccine </span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>vaccine" class="sidebar-link">
                                        <span class="hide-menu"> List</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>vaccine/archive" class="sidebar-link">
                                        <span class="hide-menu"> Archive</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="list-divider"></li>
                        <li class="nav-small-cap"><span class="hide-menu">Account Management</span></li>
                        <li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <i class="fas fa-users"></i>
                                <span class="hide-menu">Patient </span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>patient" class="sidebar-link">
                                        <span class="hide-menu"> List</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>patient/create" class="sidebar-link">
                                        <span class="hide-menu"> Create</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>patient/archive" class="sidebar-link">
                                        <span class="hide-menu"> Archive</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php 
                        if($user_info['level'] == 0) {
                        ?>
                        <li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <i class="fas fa-user-secret"></i>
                                <span class="hide-menu">Administrator </span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>admin" class="sidebar-link">
                                        <span class="hide-menu"> List</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>admin/create" class="sidebar-link">
                                        <span class="hide-menu"> Create</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>admin/archive" class="sidebar-link">
                                        <span class="hide-menu"> Archive</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php
                        }
                        ?>
                        <li class="list-divider"></li>
                        <li class="nav-small-cap"><span class="hide-menu">Audit Trail</span></li>
                        <li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <i class="far fa-chart-bar"></i>
                                <span class="hide-menu">Logs </span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>log/transaction" class="sidebar-link">
                                        <span class="hide-menu"> Transactions</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-7 align-self-center">
                        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Vaccine</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard" class="text-muted">Home</a></li>
                                    <li class="breadcrumb-item text-muted active" aria-current="page">Create Vaccine</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- <div class="col-5 align-self-center">
                        <div class="customize-input float-end">
                            <select class="custom-select custom-select-set form-control bg-white border-0 custom-shadow custom-radius">
                                <option selected>Aug 23</option>
                                <option value="1">July 23</option>
                                <option value="2">Jun 23</option>
                            </select>
                        </div>
                    </div> -->
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <?php 
                // Get CodeIgniter instance to access session
                $CI =& get_instance();
                $CI->load->library('session');
                if($CI->session->flashdata('message')): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="dripicons-checkmark me-2"></i> 
                        <?php echo $CI->session->flashdata('message'); ?>
                    </div>
                <?php endif; ?>

                <?php if($this->session->flashdata('barcode_error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="dripicons-warning me-2"></i>
                        <?php echo $this->session->flashdata('barcode_error'); ?>
                    </div>
                <?php endif; ?>

                <h4 class="card-title">Create Vaccine</h4>
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="card">
                            <div class="card-body">
                                <?php $barcode = $this->session->userdata('vaccine_barcode'); ?>

                                <div class="mb-4">
                                    <h5 class="card-title">Barcode</h5>
                                    <?php if (!$barcode): ?>
                                        <?php echo form_open(base_url() . 'vaccine/create', 'form-horizontal'); ?>
                                            <div class="row">
                                                <div class="form-group col-md-9">
                                                    <label for="barcodeInput" class="col-form-label">Scan or Enter Barcode</label>
                                                    <input name="barcode" type="text" class="form-control" id="barcodeInput" placeholder="Enter barcode">
                                                </div>
                                                <div class="form-group col-md-3 d-flex align-items-end">
                                                    <button type="submit" name="checkBarcode" value="1" class="btn btn-primary w-100">Check Barcode</button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <?php echo form_open(base_url() . 'vaccine/create', 'form-horizontal'); ?>
                                            <div class="row">
                                                <div class="form-group col-md-9">
                                                    <label class="col-form-label">Selected Barcode</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($barcode); ?>" disabled>
                                                </div>
                                                <div class="form-group col-md-3 d-flex align-items-end">
                                                    <button type="submit" name="removeBarcode" value="1" class="btn btn-outline-danger w-100">Remove Barcode</button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>

                                <?php echo form_open(base_url() . 'vaccine/create', 'form-horizontal'); ?>
                                    <input type="hidden" name="user_id" value="<?php echo $user_info['id']; ?>">
                                    <input type="hidden" name="barcode" value="<?php echo htmlspecialchars((string) $barcode); ?>">

                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="vaccineType" class="col-form-label">Vaccine Type</label>
                                            <select class="form-select <?php echo (form_error('type') ? "is-invalid" : ""); ?>" name="type" id="vaccineType">
                                                <option value="Cat and Dog" <?php echo set_select('type', 'Cat and Dog', TRUE); ?>>Cat and Dog</option>
                                                <option value="Dog" <?php echo set_select('type', 'Dog'); ?>>Dog</option>
                                                <option value="Cat" <?php echo set_select('type', 'Cat'); ?>>Cat</option>
                                            </select>
                                            <?php echo form_error('type', '<div class="invalid-feedback d-block">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-12 mt-2">
                                            <label for="vaccineName" class="col-form-label">Vaccine Name</label>
                                            <input name="name" type="text" value="<?php echo set_value('name'); ?>" class="form-control <?php echo (form_error('name') ? "is-invalid" : ""); ?>" id="vaccineName">
                                            <?php echo form_error('name', '<div class="invalid-feedback">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-12 mt-2">
                                            <label for="vaccineDescription" class="col-form-label">Vaccine Description</label>
                                            <textarea name="description" class="form-control <?php echo (form_error('description') ? "is-invalid" : ""); ?>" id="vaccineDescription"><?php echo set_value('description'); ?></textarea>
                                            <?php echo form_error('description', '<div class="invalid-feedback">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6 mt-2">
                                            <label for="vaccineCapacity" class="col-form-label">Capacity</label>
                                            <input name="capacity" type="number" value="<?php echo set_value('capacity'); ?>" class="form-control <?php echo (form_error('capacity') ? "is-invalid" : ""); ?>" id="vaccineCapacity">
                                            <?php echo form_error('capacity', '<div class="invalid-feedback">', '</div>'); ?>
                                        </div>

                                        <div class="form-group col-md-6 mt-2">
                                            <label for="vaccineQuantity" class="col-form-label">Quantity</label>
                                            <input name="quantity" type="number" min="0" value="<?php echo set_value('quantity', '0'); ?>" class="form-control <?php echo (form_error('quantity') ? "is-invalid" : ""); ?>" id="vaccineQuantity">
                                            <?php echo form_error('quantity', '<div class="invalid-feedback">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6 mt-2">
                                            <label for="doseAmount" class="col-form-label">Dose Amount</label>
                                            <select class="form-select <?php echo (form_error('amount') ? "is-invalid" : ""); ?>" name="amount" id="doseAmount">
                                                <option value="1" <?php echo set_select('amount', '1', TRUE); ?>>1</option>
                                                <option value="2" <?php echo set_select('amount', '2'); ?>>2</option>
                                                <option value="3" <?php echo set_select('amount', '3'); ?>>3</option>
                                                <option value="4" <?php echo set_select('amount', '4'); ?>>4</option>
                                                <option value="5" <?php echo set_select('amount', '5'); ?>>5</option>
                                            </select>
                                            <?php echo form_error('amount', '<div class="invalid-feedback d-block">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-12 mt-3">
                                            <button type="submit" class="btn btn-primary" <?php echo !$barcode ? 'disabled' : ''; ?>>Create Vaccine</button>
                                            <?php if (!$barcode): ?>
                                                <small class="text-muted ms-2">Check a barcode first before creating a vaccine.</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer text-center text-muted"> Footer here</a>.
            </footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="<?php echo base_url(); ?>assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="<?php echo base_url(); ?>assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- apps -->
    <!-- apps -->
    <script src="<?php echo base_url(); ?>assets/dist/js/app-style-switcher.js"></script>
    <script src="<?php echo base_url(); ?>assets/dist/js/feather.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="<?php echo base_url(); ?>assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/extra-libs/sparkline/sparkline.js"></script>
    <!--Wave Effects -->
    <!-- themejs -->
    <!--Menu sidebar -->
    <script src="<?php echo base_url(); ?>assets/dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="<?php echo base_url(); ?>assets/dist/js/custom.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/extra-libs/knob/jquery.knob.min.js"></script>
</body>

</html>



