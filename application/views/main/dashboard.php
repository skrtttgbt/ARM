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
                        <a href="index.html">
                            <img src="../assets/images/freedashDark.svg" alt="" class="img-fluid">
                        </a>
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
                                        <span class="hide-menu"> Upcoming</span>
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
                        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Dashboard</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard" class="text-muted">Home</a></li>
                                    <li class="breadcrumb-item text-muted active" aria-current="page">Dashboard</li>
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
                <?php if(isset($this->session) && $this->session->flashdata('message')): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="dripicons-checkmark me-2"></i> 
                        <?php echo $this->session->flashdata('message'); ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-muted">Total Patients</h6>
                                <h2 class="mb-0"><?php echo (int) $total_patients; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-muted">Total Incidents</h6>
                                <h2 class="mb-0"><?php echo (int) $total_incidents; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-muted">Schedules Today</h6>
                                <h2 class="mb-0"><?php echo (int) $total_schedules_today; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-muted">Available Vaccines</h6>
                                <h2 class="mb-0"><?php echo (int) $total_vaccines; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card border-start border-warning border-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">Nearest Vaccine Expiry</h5>
                                        <p class="text-muted mb-0">Upcoming stock expirations based on patient-capacity batches</p>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Vaccine</th>
                                                <th>Patients Left</th>
                                                <th>Expiration Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($expiring_batches)): ?>
                                                <?php foreach ($expiring_batches as $batch): ?>
                                                    <?php
                                                    $batch_label = trim((string) $batch['vaccine_name']);
                                                    if (!empty($batch['vaccine_barcode'])) {
                                                        $batch_label .= ' (' . trim((string) $batch['vaccine_barcode']) . ')';
                                                    }
                                                    $days_to_expiry = (int) floor((strtotime($batch['expiration_date']) - strtotime(date('Y-m-d'))) / 86400);
                                                    $expiry_status = 'OK';
                                                    $expiry_class = 'success';

                                                    if ($days_to_expiry < 0) {
                                                        $expiry_status = 'Expired';
                                                        $expiry_class = 'danger';
                                                    } elseif ($days_to_expiry <= 30) {
                                                        $expiry_status = 'Expiring soon';
                                                        $expiry_class = 'warning';
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($batch_label); ?></td>
                                                        <td><?php echo (int) $batch['quantity_remaining']; ?></td>
                                                        <td><?php echo date('M j, Y', strtotime($batch['expiration_date'])); ?></td>
                                                        <td><span class="badge bg-<?php echo $expiry_class; ?>"><?php echo $expiry_status; ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No dated vaccine batches available yet.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Vaccine Patient Progress</h5>
                                <p class="text-muted mb-3">Stock is tracked per box. Each box has 3 vials, and each vial can serve 3 patients, for a total of 9 patients per box.</p>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Vaccine</th>
                                                <th>Vaccine Left</th>
                                                <th>Current Vial Progress</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($vaccines)): ?>
                                                <?php foreach ($vaccines as $vaccine): ?>
                                                    <?php
                                                    $vaccine_label = trim((string) $vaccine['name']);
                                                    if (!empty($vaccine['barcode'])) {
                                                        $vaccine_label .= ' (' . trim((string) $vaccine['barcode']) . ')';
                                                    }
                                                    $patients_per_box = 9;
                                                    $patients_left = (int) $vaccine['quantity'];
                                                    $current_patient_progress = ((int) $vaccine['used_count']) % $patients_per_box;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($vaccine_label); ?></td>
                                                        <td><?php echo $patients_left; ?></td>
                                                        <td><span class="badge bg-info text-dark"><?php echo $current_patient_progress . '/' . $patients_per_box; ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">No vaccine stock available.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Overdue Follow-Up Patients</h5>
                                <p class="text-muted mb-3">Patients appear here when they still need another dose and have not returned within 4 days after their last completed vaccination.</p>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Patient</th>
                                                <th>Mobile</th>
                                                <th>Completed Doses</th>
                                                <th>Remaining Doses</th>
                                                <th>Last Vaccination</th>
                                                <th>Days Since Last Dose</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($overdue_followups)): ?>
                                                <?php foreach ($overdue_followups as $followup): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($followup['patient_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($followup['mobile']); ?></td>
                                                        <td><?php echo (int) $followup['completed_doses'] . '/' . (int) $followup['required_doses']; ?></td>
                                                        <td><?php echo (int) $followup['remaining_doses']; ?></td>
                                                        <td><?php echo htmlspecialchars($followup['last_completed_schedule']); ?></td>
                                                        <td><span class="badge bg-warning text-dark"><?php echo (int) $followup['days_since_last_dose']; ?> days</span></td>
                                                        <td>
                                                            <form action="<?php echo base_url('dashboard/remind_overdue/' . (int) $followup['incident_id']); ?>" method="post" class="d-inline">
                                                                <button type="submit" class="btn btn-sm btn-warning">Remind</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No overdue follow-up patients right now.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">Incident Trend and Prediction</h5>
                                        <p class="text-muted mb-0">Actual data from 2021 to present, with prediction starting in 2022</p>
                                    </div>
                                </div>
                                <canvas id="monthlyTrendsChart" height="120"></canvas>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">Vaccine Prediction for Next Month</h5>
                                        <p class="text-muted mb-0">Total forecast numbers only for the upcoming month.</p>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold"><?php echo (int) $vaccine_forecast_data['predicted_next_month']; ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($vaccine_forecast_data['next_month_label']); ?></small>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100">
                                            <small class="text-muted d-block mb-1">Predicted Patients</small>
                                            <h2 class="mb-1"><?php echo (int) $vaccine_forecast_data['predicted_next_month']; ?></h2>
                                            <small class="text-muted"><?php echo htmlspecialchars($vaccine_forecast_data['next_month_label']); ?></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100">
                                            <small class="text-muted d-block mb-1">Required Vials</small>
                                            <h2 class="mb-1"><?php echo (int) $vaccine_forecast_data['next_month_required_vials']; ?></h2>
                                            <small class="text-muted">Monthly forecast uses 1 vial per patient.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-light border mt-3 mb-0" role="alert">
                                    <h6 class="mb-2">Vial Formula</h6>
                                    <p class="mb-2 text-dark">
                                        Required Vials = Predicted Patients x <?php echo (int) $vaccine_forecast_data['patients_per_vial']; ?>
                                    </p>
                                    <p class="mb-0 text-muted">
                                        <?php echo (int) $vaccine_forecast_data['next_month_required_vials']; ?> = <?php echo (int) $vaccine_forecast_data['predicted_next_month']; ?> x <?php echo (int) $vaccine_forecast_data['patients_per_vial']; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">Next Month Forecast Summary</h5>
                                        <p class="text-muted mb-0">Breakdown of the predicted total numbers only.</p>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="border rounded p-2 h-100">
                                            <small class="text-muted d-block">Forecast Month</small>
                                            <strong><?php echo htmlspecialchars($vaccine_forecast_data['next_month_label']); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="border rounded p-2 h-100">
                                            <small class="text-muted d-block">Predicted Patients</small>
                                            <strong><?php echo (int) $vaccine_forecast_data['predicted_next_month']; ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="border rounded p-2 h-100">
                                            <small class="text-muted d-block">Vials Per Patient</small>
                                            <strong><?php echo (int) $vaccine_forecast_data['patients_per_vial']; ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="border rounded p-2 h-100 bg-light">
                                            <small class="text-muted d-block">Required Vials</small>
                                            <strong class="fs-4"><?php echo (int) $vaccine_forecast_data['next_month_required_vials']; ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="border rounded p-2 h-100">
                                            <small class="text-muted d-block">Formula Used</small>
                                            <strong>Predicted Patients x Vials Per Patient</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Vaccine Archive Summary</h5>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Used</span>
                                        <strong><?php echo (int) $vaccine_archive_summary['used_total']; ?></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Damaged</span>
                                        <strong><?php echo (int) $vaccine_archive_summary['damaged_total']; ?></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Expired</span>
                                        <strong><?php echo (int) $vaccine_archive_summary['expired_total']; ?></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Recall</span>
                                        <strong><?php echo (int) $vaccine_archive_summary['recall_total']; ?></strong>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between">
                                        <span>Inventory Adjustment</span>
                                        <strong><?php echo (int) $vaccine_archive_summary['inventory_adjustment_total']; ?></strong>
                                    </div>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function () {
            $('[data-plugin="knob"]').knob();
        });

        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
            var months = <?php echo json_encode($chart_data['months']); ?>;
            var incidentCounts = <?php echo json_encode($chart_data['incident_counts']); ?>;
            var predictedIncidentCounts = <?php echo json_encode($chart_data['predicted_incident_counts']); ?>;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Actual Incidents',
                        data: incidentCounts,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.15)',
                        borderWidth: 3,
                        tension: 0.35,
                        spanGaps: false,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'Predicted Incidents',
                        data: predictedIncidentCounts,
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.15)',
                        borderWidth: 3,
                        borderDash: [8, 6],
                        tension: 0.35,
                        spanGaps: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Actual vs SARIMA Predicted Incidents - 2021 to Present'
                        },
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count'
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>

