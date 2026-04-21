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

                <div class="btn-group mb-3" role="group" aria-label="Vaccine Navigation">
                    <a href="<?php echo base_url(); ?>vaccine" class="btn btn-primary active">Vaccine List</a>
                    <a href="<?php echo base_url(); ?>vaccine/archive" class="btn btn-outline-primary">Archive</a>
                </div>

                <?php if ((int) $user_info['level'] === 0): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h4 class="card-title mb-1">Add Vaccine Quantity</h4>
                                <p class="text-muted mb-0">Choose a vaccine type, then add new stock in one place.</p>
                            </div>
                        </div>

                        <form id="addStockForm" method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Vaccine Type</label>
                                    <select id="addStockVaccineSelect" class="form-control" required>
                                        <option value="">Select vaccine</option>
                                        <?php if ($vaccines): ?>
                                            <?php foreach ($vaccines as $vaccine): ?>
                                                <option
                                                    value="<?php echo (int) $vaccine['id']; ?>"
                                                    data-action="<?php echo base_url() . 'vaccine/action/add_quantity/' . $vaccine['id']; ?>"
                                                >
                                                    <?php echo htmlspecialchars($vaccine['name'] . ' - ' . $vaccine['type']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Boxes to add</label>
                                    <input type="number" name="quantity" min="1" value="1" class="form-control js-box-quantity" required>
                                    <small class="form-text text-muted">Each box has 3 vials, each vial serves 3 patients.</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Number of vials</label>
                                    <input type="number" value="3" class="form-control js-vial-preview" disabled>
                                    <small class="form-text text-muted">Preview only: boxes x 3 vials.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Manufacture Date</label>
                                    <input type="date" name="manufacture_date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Expiration Date</label>
                                    <input type="date" name="expiration_date" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">Add Quantity</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <h4 class="card-title">Vaccine List</h4>
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
                                                    $dose_amount = 3;
                                                    $current_patient_progress = ((int) $vaccine['used_count']) % $dose_amount;
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
                                                    <td><span class="badge bg-info text-dark"><?php echo $current_patient_progress . '/' . $dose_amount; ?></span></td>
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

                <h4 class="card-title mt-4">Vaccine Audit Trail</h4>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label for="auditTypeFilter" class="form-label">Filter Status</label>
                                        <select id="auditTypeFilter" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="IN STOCK">IN STOCK</option>
                                            <option value="USED">USED</option>
                                            <option value="DAMAGE">DAMAGE</option>
                                            <option value="EXPIRED">EXPIRED</option>
                                            <option value="RECALL">RECALL</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="auditVaccineFilter" class="form-label">Filter Vaccine</label>
                                        <select id="auditVaccineFilter" class="form-control">
                                            <option value="">All Vaccines</option>
                                            <?php
                                            $audit_vaccine_options = array();
                                            if (!empty($audit_trail_entries)) {
                                                foreach ($audit_trail_entries as $entry) {
                                                    if (!empty($entry['vaccine_name'])) {
                                                        $audit_vaccine_options[$entry['vaccine_name']] = true;
                                                    }
                                                }
                                            }
                                            ksort($audit_vaccine_options);
                                            ?>
                                            <?php if (!empty($audit_vaccine_options)): ?>
                                                <?php foreach (array_keys($audit_vaccine_options) as $audit_vaccine_name): ?>
                                                    <option value="<?php echo htmlspecialchars($audit_vaccine_name); ?>"><?php echo htmlspecialchars($audit_vaccine_name); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="auditDateFilter" class="form-label">Search Date</label>
                                        <input type="text" id="auditDateFilter" class="form-control" placeholder="Example: Apr 2026 or Apr 20">
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="vaccine_audit_table" class="table border table-striped table-bordered text-nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th>Vaccine</th>
                                                <th>Barcode</th>
                                                <th>Quantity</th>
                                                <th>Date</th>
                                                <th>Date Info</th>
                                                <th>Reference</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($audit_trail_entries)): ?>
                                                <?php foreach ($audit_trail_entries as $entry): ?>
                                                    <?php
                                                    $badge_class = 'secondary';
                                                    if ($entry['event_type'] === 'IN STOCK') {
                                                        $badge_class = 'success';
                                                    } elseif ($entry['event_type'] === 'USED') {
                                                        $badge_class = 'primary';
                                                    } elseif ($entry['event_type'] === 'DAMAGE') {
                                                        $badge_class = 'danger';
                                                    } elseif ($entry['event_type'] === 'EXPIRED') {
                                                        $badge_class = 'warning';
                                                    } elseif ($entry['event_type'] === 'RECALL') {
                                                        $badge_class = 'dark';
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><span class="badge bg-<?php echo $badge_class; ?>"><?php echo htmlspecialchars($entry['event_type']); ?></span></td>
                                                        <td><?php echo htmlspecialchars($entry['vaccine_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($entry['vaccine_barcode']); ?></td>
                                                        <td><?php echo (int) $entry['quantity']; ?></td>
                                                        <td><?php echo htmlspecialchars($entry['event_date']); ?></td>
                                                        <td><?php echo htmlspecialchars($entry['date_note']); ?></td>
                                                        <td>
                                                            <?php
                                                            if (!empty($entry['reference_label']) && !empty($entry['reference_date'])) {
                                                                echo htmlspecialchars($entry['reference_label'] . ': ' . $entry['reference_date']);
                                                            } else {
                                                                echo '<span class="text-muted">-</span>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($entry['details']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">No audit trail records found.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="card-title mt-4">Expiration List</h4>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table border table-striped table-bordered text-nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Vaccine</th>
                                                <th>Barcode</th>
                                                <th>Vaccine Remaining</th>
                                                <th>Manufacture Date</th>
                                                <th>Expiration Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($expiring_batches)): ?>
                                                <?php foreach ($expiring_batches as $batch): ?>
                                                    <?php
                                                    $days_to_expiry = (int) floor((strtotime($batch['expiration_date']) - strtotime(date('Y-m-d'))) / 86400);
                                                    $batch_status = 'OK';
                                                    $batch_class = 'success';

                                                    if ($days_to_expiry < 0) {
                                                        $batch_status = 'Expired';
                                                        $batch_class = 'danger';
                                                    } elseif ($days_to_expiry <= 30) {
                                                        $batch_status = 'Expiring soon';
                                                        $batch_class = 'warning';
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $batch['vaccine_name']; ?></td>
                                                        <td><?php echo $batch['vaccine_barcode']; ?></td>
                                                        <td><?php echo (int) $batch['quantity_remaining']; ?></td>
                                                        <td><?php echo date('M j, Y', strtotime($batch['manufacture_date'])); ?></td>
                                                        <td><?php echo date('M j, Y', strtotime($batch['expiration_date'])); ?></td>
                                                        <td><span class="badge bg-<?php echo $batch_class; ?>"><?php echo $batch_status; ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No expiration batches recorded yet.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
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
        var vaccineTable = $('#default_order').DataTable();
        var auditTable = $('#vaccine_audit_table').DataTable({
            order: [[4, 'desc']]
        });

        $.fn.dataTable.ext.search.push(function(settings, data) {
            if (settings.nTable.id !== 'vaccine_audit_table') {
                return true;
            }

            var selectedType = ($('#auditTypeFilter').val() || '').toLowerCase();
            var selectedVaccine = ($('#auditVaccineFilter').val() || '').toLowerCase();
            var dateKeyword = ($('#auditDateFilter').val() || '').toLowerCase();

            var rowType = (data[0] || '').toLowerCase();
            var rowVaccine = (data[1] || '').toLowerCase();
            var rowDate = (data[4] || '').toLowerCase();
            var rowDateInfo = (data[5] || '').toLowerCase();
            var rowReference = (data[6] || '').toLowerCase();

            if (selectedType && rowType.indexOf(selectedType) === -1) {
                return false;
            }

            if (selectedVaccine && rowVaccine !== selectedVaccine) {
                return false;
            }

            if (dateKeyword && rowDate.indexOf(dateKeyword) === -1 && rowDateInfo.indexOf(dateKeyword) === -1 && rowReference.indexOf(dateKeyword) === -1) {
                return false;
            }

            return true;
        });

        $('#auditTypeFilter, #auditVaccineFilter').on('change', function() {
            auditTable.draw();
        });

        $('#auditDateFilter').on('keyup change', function() {
            auditTable.draw();
        });

        var addStockForm = document.getElementById('addStockForm');
        var addStockVaccineSelect = document.getElementById('addStockVaccineSelect');

        if (addStockForm && addStockVaccineSelect) {
            function syncAddStockAction() {
                var selectedOption = addStockVaccineSelect.options[addStockVaccineSelect.selectedIndex];
                var action = selectedOption ? selectedOption.getAttribute('data-action') : '';
                addStockForm.action = action || '';
            }

            addStockVaccineSelect.addEventListener('change', syncAddStockAction);
            syncAddStockAction();

            addStockForm.addEventListener('submit', function(event) {
                if (!addStockVaccineSelect.value || !addStockForm.action) {
                    event.preventDefault();
                    addStockVaccineSelect.focus();
                }
            });
        }

        function updateVialPreview(input) {
            var boxes = parseInt(input.value, 10);
            if (isNaN(boxes) || boxes < 1) {
                boxes = 1;
            }

            var form = input.closest('form');
            if (!form) {
                return;
            }

            var vialPreview = form.querySelector('.js-vial-preview');
            if (vialPreview) {
                vialPreview.value = boxes * 3;
            }
        }

        document.querySelectorAll('.js-box-quantity').forEach(function(input) {
            updateVialPreview(input);

            input.addEventListener('input', function() {
                updateVialPreview(input);
            });
        });
    </script>
</body>

</html>



