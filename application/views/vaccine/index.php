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
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/extra-libs/datatables.net-bs4/css/responsive.dataTables.min.css">
</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
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
                                    <li class="breadcrumb-item text-muted active" aria-current="page">Vaccine List</li>
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
                <?php if($CI->session->flashdata('barcode_error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="dripicons-warning me-2"></i>
                        <?php echo $CI->session->flashdata('barcode_error'); ?>
                    </div>
                <?php endif; ?>

                <div class="btn-group mb-3" role="group" aria-label="Vaccine Navigation">
                    <a href="<?php echo base_url(); ?>vaccine" class="btn btn-primary active">Vaccine List</a>
                    <a href="<?php echo base_url(); ?>vaccine/archive" class="btn btn-outline-primary">Archive</a>
                </div>

                <?php if ((int) $user_info['level'] === 0): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Create Vaccine Per Box</h4>

                        <?php echo form_open(base_url() . 'vaccine/create', 'form-horizontal'); ?>
                            <input type="hidden" name="user_id" value="<?php echo $user_info['id']; ?>">
                            <input type="hidden" name="capacity" value="1">
                            <input type="hidden" name="amount" value="0">

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="barcodeInput" class="col-form-label">Barcode</label>
                                    <input name="barcode" type="text" value="<?php echo set_value('barcode'); ?>" class="form-control <?php echo (form_error('barcode') ? "is-invalid" : ""); ?>" id="barcodeInput" placeholder="Enter barcode">
                                    <?php echo form_error('barcode', '<div class="invalid-feedback">', '</div>'); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12 mt-2">
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
                                <div class="form-group col-md-6 mt-2">
                                    <label class="col-form-label">Capacity</label>
                                    <input type="text" value="1" class="form-control" disabled>
                                    <small class="form-text text-muted">Capacity is fixed to 1 per box record.</small>
                                </div>

                                <div class="form-group col-md-6 mt-2">
                                    <label class="col-form-label">Initial Stock</label>
                                    <input type="text" value="3" class="form-control" disabled>
                                    <small class="form-text text-muted">Each box is stored as one inventory line with stock 3.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6 mt-2">
                                    <label class="col-form-label">Dose Amount</label>
                                    <input type="text" value="0" class="form-control" disabled>
                                    <small class="form-text text-muted">Dose amount is fixed to 0.</small>
                                </div>

                                <div class="form-group col-md-6 mt-2">
                                    <label for="manufactureDate" class="col-form-label">Manufacture Date</label>
                                    <input name="manufacture_date" type="date" value="<?php echo set_value('manufacture_date'); ?>" class="form-control <?php echo (form_error('manufacture_date') ? "is-invalid" : ""); ?>" id="manufactureDate">
                                    <?php echo form_error('manufacture_date', '<div class="invalid-feedback">', '</div>'); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6 mt-2">
                                    <label for="expirationDate" class="col-form-label">Expiration Date</label>
                                    <input name="expiration_date" type="date" value="<?php echo set_value('expiration_date'); ?>" class="form-control <?php echo (form_error('expiration_date') ? "is-invalid" : ""); ?>" id="expirationDate">
                                    <?php echo form_error('expiration_date', '<div class="invalid-feedback">', '</div>'); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Create Vaccine</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <h4 class="card-title">Vaccine Inventory Per Box</h4>
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="card">

                            <div class="card-body">
                                
                                <div class="table-responsive">
                                    <table id="default_order"class="table border table-striped table-bordered text-nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Barcode</th>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Patient Progress</th>
                                                <th>Expiration Date</th>
                                                <th>Expiry Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            if($vaccines) {
                                                foreach($vaccines as $vaccine){
                                                ?>
                                                <tr>
                                                    <?php
                                                    $nearest_expiration = !empty($vaccine['nearest_expiration_date']) ? $vaccine['nearest_expiration_date'] : '';
                                                    $days_to_expiry = $nearest_expiration !== '' ? (int) floor((strtotime($nearest_expiration) - strtotime(date('Y-m-d'))) / 86400) : null;
                                                    $patients_per_box = 9;
                                                    $current_patient_progress = ((int) $vaccine['used_count']) % $patients_per_box;
                                                    $expiry_label = 'No batch date';
                                                    $expiry_class = 'secondary';

                                                    if ($nearest_expiration !== '') {
                                                        if ($days_to_expiry < 0) {
                                                            $expiry_label = 'Expired';
                                                            $expiry_class = 'danger';
                                                        } elseif ($days_to_expiry <= 30) {
                                                            $expiry_label = 'Expiring soon';
                                                            $expiry_class = 'warning';
                                                        } else {
                                                            $expiry_label = 'OK';
                                                            $expiry_class = 'success';
                                                        }
                                                    }
                                                    ?>
                                                    <td><?php echo $vaccine['barcode'];?></td>
                                                    <td><?php echo $vaccine['name'];?></td>
                                                    <td><?php echo $vaccine['type'];?></td>
                                                    <td><span class="badge bg-info text-dark"><?php echo $current_patient_progress . '/' . $patients_per_box; ?></span></td>
                                                    <td>
                                                        <?php echo $nearest_expiration !== '' ? date('M j, Y', strtotime($nearest_expiration)) : '<span class="text-muted">Not set</span>';?>
                                                    </td>
                                                    <td><span class="badge bg-<?php echo $expiry_class; ?>"><?php echo $expiry_label; ?></span></td>
                                                    <td>
                                                        <?php if ((int) $user_info['level'] === 0): ?>
                                                            <?php if ((int) (isset($vaccine['quantity']) ? $vaccine['quantity'] : 0) > 0): ?>
                                                                <a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#archive-vaccine-modal-<?php echo $vaccine['id']; ?>">Archive</a>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">Admin only</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php if ((int) $user_info['level'] === 0 && (int) (isset($vaccine['quantity']) ? $vaccine['quantity'] : 0) > 0): ?>
                                                <div id="archive-vaccine-modal-<?php echo $vaccine['id']; ?>" class="modal fade" tabindex="-1" role="dialog"
                                                    aria-labelledby="archiveVaccineLabel-<?php echo $vaccine['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="archiveVaccineLabel-<?php echo $vaccine['id']; ?>">Archive Vaccine</h4>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-hidden="true"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php echo form_open(base_url() . "vaccine/action/archive/" . $vaccine['id']); ?>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Patients to archive</label>
                                                                        <input type="number" name="quantity" min="1" max="<?php echo (int) (isset($vaccine['quantity']) ? $vaccine['quantity'] : 0); ?>" value="<?php echo (int) (isset($vaccine['quantity']) ? $vaccine['quantity'] : 0); ?>" class="form-control" required>
                                                                        <small class="form-text text-muted">Available vaccine stock: <?php echo (int) (isset($vaccine['quantity']) ? $vaccine['quantity'] : 0); ?></small>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Archive Reason</label>
                                                                        <select name="archive_reason" class="form-control" required>
                                                                            <option value="">Select a reason</option>
                                                                            <option value="Damaged">Damaged</option>
                                                                            <option value="Expired">Expired</option>
                                                                            <option value="Recall">Recall</option>
                                                                            <option value="Inventory adjustment">Inventory adjustment</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Super admin password</label>
                                                                        <input type="password" name="password" class="form-control" required>
                                                                    </div>
                                                                    <div class="modal-footer px-0 pb-0">
                                                                        <button type="button" class="btn btn-light"
                                                                            data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-danger">Archive</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                                <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- column -->
                </div>

            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <?php include APPPATH . 'views/partials/footer.php'; ?>
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

    <script src="<?php echo base_url(); ?>assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/extra-libs/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
    <script>
        function hideVaccinePreloader() {
            var preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', hideVaccinePreloader);
        window.addEventListener('load', hideVaccinePreloader);

        var vaccineTable = $('#default_order').DataTable();

    </script>
</body>

</html>



