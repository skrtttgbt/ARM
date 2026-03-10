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
                                    <a href="<?php echo base_url(); ?>vaccine/create" class="sidebar-link">
                                        <span class="hide-menu"> Create</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>vaccine/archive" class="sidebar-link">
                                        <span class="hide-menu"> Archive</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <i class="fas fa-vials"></i>
                                <span class="hide-menu">Vial </span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>vial" class="sidebar-link">
                                        <span class="hide-menu"> List</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>vial/create" class="sidebar-link">
                                        <span class="hide-menu"> Create</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="<?php echo base_url(); ?>vial/verify" class="sidebar-link">
                                        <span class="hide-menu"> Verify</span>
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
                        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Patient</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item"><a href="" class="text-muted">Home</a></li>
                                    <li class="breadcrumb-item text-muted active" aria-current="page">Create Account</li>
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
                <h4 class="card-title">Create Patient</h4>
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="card">
                            
                            <div class="card-body">
                                <h4 class="card-title">Personal Information</h4>
                                <?php echo form_open(base_url() . 'patient/create', 'form-horizontal'); ?>
                                <input type="hidden" name="user_id" id="<?php echo $user_info['id']; ?>">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="inputHorizontalSuccess" class="col-form-label">First Name</label>
                                            <input name="first_name" type="text" class="form-control <?php echo (form_error('first_name') ? "is-invalid" : ""); ?>" id="inputHorizontalSuccess">
                                            <?php echo form_error('first_name', '<div class="invalid-feedback">', '</div>'); ?>  
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="inputHorizontalSuccess" class="col-form-label">Last Name</label>
                                            <input name="last_name" type="text" class="form-control <?php echo (form_error('last_name') ? "is-invalid" : ""); ?>" id="inputHorizontalSuccess">
                                            <?php echo form_error('last_name', '<div class="invalid-feedback">', '</div>'); ?>  
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="inputHorizontalSuccess" class="col-form-label">Gender</label>
                                            <select name="gender" class="form-select" >
                                                <option value="" disabled selected>Select Gender</option>
                                                <option value="male"><i class="fas fa-mars"></i> MALE</option>
                                                <option value="female"><i class="fas fa-venus"></i> FEMALE</option>
                                                <option value="other"><i class="fas fa-genderless"></i> PREFER NOT TO SAY</option>
                                            </select>
                                            <?php echo form_error('gender', '<div class="invalid-feedback">', '</div>'); ?>  
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="inputHorizontalSuccess" class="col-form-label">Birthday</label>
                                            <input name="birthday" type="date" class="form-control <?php echo (form_error('birthday') ? "is-invalid" : ""); ?>" id="inputHorizontalSuccess">
                                            <?php echo form_error('birthday', '<div class="invalid-feedback">', '</div>'); ?>  
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="inputHorizontalSuccess" class="col-form-label">Height (cm)</label>
                                            <input name="height" type="text" class="form-control <?php echo (form_error('height') ? "is-invalid" : ""); ?>" id="inputHorizontalSuccess" placeholder="Enter height in cm">
                                            <?php echo form_error('height', '<div class="invalid-feedback">', '</div>'); ?>  
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="inputHorizontalSuccess" class="col-form-label">Weight (kg)</label>
                                            <input name="weight" type="text" class="form-control <?php echo (form_error('weight') ? "is-invalid" : ""); ?>" id="inputHorizontalSuccess" placeholder="Enter weight in kg">
                                            <?php echo form_error('weight', '<div class="invalid-feedback">', '</div>'); ?>  
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-12 mt-2">
                                            <label for="inputHorizontalSuccess" class="col-form-label">Mobile Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text">+639</span>
                                                <input name="mobile" type="text" inputmode="numeric" maxlength="9" pattern="[0-9]{9}" placeholder="XXXXXXXXX" class="form-control <?php echo (form_error('mobile') ? "is-invalid" : ""); ?>" id="inputHorizontalSuccess">
                                            </div>
                                            <?php echo form_error('mobile', '<div class="invalid-feedback">', '</div>'); ?>  
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-12 mt-2">
                                            <label for="inputHorizontalSuccess" class="col-form-label">Address</label>
                                            <input name="address" type="text" class="form-control <?php echo (form_error('address') ? "is-invalid" : ""); ?>" id="inputHorizontalSuccess">
                                            <?php echo form_error('address', '<div class="invalid-feedback">', '</div>'); ?>  
                                        </div>
                                    </div>

                                    <hr>
                                    <h4 class="card-title">PhilHealth Information</h4>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="inputHorizontalSuccess" class="col-form-label">Account Type</label>
                                            <select name="type" class="form-select">
                                                <option value="Member" <?= set_select('type', 'Member'); ?>>Member</option>
                                                <option value="Dependent" <?= set_select('type', 'Dependent'); ?>>Dependent</option>
                                            </select>
                                            <?php echo form_error('type', '<div class="invalid-feedback">', '</div>'); ?>  
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="inputHorizontalSuccess" class="col-form-label">Relationship</label>
                                            <select name="relationship" class="form-select <?php echo (form_error('relationship') ? "is-invalid" : ""); ?>" id="inputHorizontalSuccess">
                                                <option value="">Select Relationship</option>
                                                <option value="Spouse">Spouse</option>
                                                <option value="Child">Child</option>
                                                <option value="Parent">Parent</option>
                                                <option value="Grandparent">Grandparent</option>
                                                <option value="Grandchild">Grandchild</option>
                                                <option value="Sibling">Sibling</option>
                                                <option value="In-Law">In-Law</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            <?php echo form_error('relationship', '<div class="invalid-feedback">', '</div>'); ?>  
                                        </div>
                                    </div>
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function () {
                                        const typeSelect = document.querySelector('select[name="type"]');
                                        const relationshipSelect = document.querySelector('select[name="relationship"]');

                                        function toggleRelationship() {
                                            if (typeSelect.value === "Member") {
                                                relationshipSelect.value = "";
                                                relationshipSelect.disabled = true;
                                            } else {
                                                relationshipSelect.disabled = false;
                                            }
                                        }

                                        // run on page load + when selection changes
                                        toggleRelationship();
                                        typeSelect.addEventListener("change", toggleRelationship);
                                        });
                                    </script>

                                    <div class="row">
                                        <div class="form-group col-md-12 mt-2">
                                            <label for="inputHorizontalSuccess" class="col-form-label">PhilHealth Account Number</label>
                                            <input name="account_number" type="number" class="form-control <?php echo (form_error('account_number') ? "is-invalid" : ""); ?>" id="inputHorizontalSuccess">
                                            <?php echo form_error('account_number', '<div class="invalid-feedback">', '</div>'); ?>  
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="inputHorizontalSuccess" class="col-form-label">PhilHealth Member First Name</label>
                                            <input name="account_first_name" type="text" class="form-control <?php echo (form_error('account_first_name') ? "is-invalid" : ""); ?>" id="inputHorizontalSuccess">
                                            <?php echo form_error('account_first_name', '<div class="invalid-feedback">', '</div>'); ?>  
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="inputHorizontalSuccess" class="col-form-label">PhilHealth Member Last Name</label>
                                            <input name="account_last_name" type="text" class="form-control <?php echo (form_error('account_last_name') ? "is-invalid" : ""); ?>" id="inputHorizontalSuccess">
                                            <?php echo form_error('account_last_name', '<div class="invalid-feedback">', '</div>'); ?>  
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-12 mt-3">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>

                                </form>

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
    <script>
        $(function () {
            $('[data-plugin="knob"]').knob();
        });
    </script>
</body>

</html>

