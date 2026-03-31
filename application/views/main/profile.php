<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $CI =& get_instance(); ?>
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
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        <header class="topbar" data-navbarbg="skin6">
            <nav class="navbar top-navbar navbar-expand-lg">
                <div class="navbar-header" data-logobg="skin6">
                    <a class="nav-toggler waves-effect waves-light d-block d-lg-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                    <div class="navbar-brand"></div>
                    <a class="topbartoggler d-block d-lg-none waves-effect waves-light" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"><i class="ti-more"></i></a>
                </div>
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav float-left me-auto ms-3 ps-1"></ul>
                    <ul class="navbar-nav float-end">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="<?php echo base_url(); ?>assets/avatar/<?php echo $user_info['image']; ?>" alt="user" class="rounded-circle" width="40" height="40">
                                <span class="ms-2 d-none d-lg-inline-block"><span>Hello,</span> <span class="text-dark"><?php echo ucfirst($user_info['first_name']); ?></span> <i data-feather="chevron-down" class="svg-icon"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-right user-dd animated flipInY">
                                <a class="dropdown-item" href="<?php echo base_url(); ?>profile"><i data-feather="user" class="svg-icon me-2 ms-1"></i>My Profile</a>
                                <a class="dropdown-item" href="<?php echo base_url(); ?>settings"><i data-feather="settings" class="svg-icon me-2 ms-1"></i>Account Setting</a>
                                <a style="color:red" class="dropdown-item" href="<?php echo base_url(); ?>logout"><i data-feather="power" class="svg-icon me-2 ms-1"></i>Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar" data-sidebarbg="skin6">
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
                            <ul aria-expanded="false" class="collapse first-level base-level-line">
                                <li class="sidebar-item"><a href="<?php echo base_url(); ?>schedule" class="sidebar-link"><span class="hide-menu"> Today</span></a></li>
                                <li class="sidebar-item"><a href="<?php echo base_url(); ?>schedule/future" class="sidebar-link"><span class="hide-menu"> Upcoming</span></a></li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span class="hide-menu">Incident </span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level base-level-line">
                                <li class="sidebar-item"><a href="<?php echo base_url(); ?>incident" class="sidebar-link"><span class="hide-menu"> list</span></a></li>
                            </ul>
                        </li>
                        <li class="list-divider"></li>
                        <li class="nav-small-cap"><span class="hide-menu">File Management</span></li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <i class="fas fa-syringe"></i>
                                <span class="hide-menu">Vaccine </span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level base-level-line">
                                <li class="sidebar-item"><a href="<?php echo base_url(); ?>vaccine" class="sidebar-link"><span class="hide-menu"> List</span></a></li>
                                <li class="sidebar-item"><a href="<?php echo base_url(); ?>vaccine/archive" class="sidebar-link"><span class="hide-menu"> Archive</span></a></li>
                            </ul>
                        </li>
                        <li class="list-divider"></li>
                        <li class="nav-small-cap"><span class="hide-menu">Account Management</span></li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <i class="fas fa-users"></i>
                                <span class="hide-menu">Patient </span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level base-level-line">
                                <li class="sidebar-item"><a href="<?php echo base_url(); ?>patient" class="sidebar-link"><span class="hide-menu"> List</span></a></li>
                                <li class="sidebar-item"><a href="<?php echo base_url(); ?>patient/create" class="sidebar-link"><span class="hide-menu"> Create</span></a></li>
                                <li class="sidebar-item"><a href="<?php echo base_url(); ?>patient/archive" class="sidebar-link"><span class="hide-menu"> Archive</span></a></li>
                            </ul>
                        </li>
                        <?php if($user_info['level'] == 0) { ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <i class="fas fa-user-secret"></i>
                                <span class="hide-menu">Administrator </span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level base-level-line">
                                <li class="sidebar-item"><a href="<?php echo base_url(); ?>admin" class="sidebar-link"><span class="hide-menu"> List</span></a></li>
                                <li class="sidebar-item"><a href="<?php echo base_url(); ?>admin/create" class="sidebar-link"><span class="hide-menu"> Create</span></a></li>
                                <li class="sidebar-item"><a href="<?php echo base_url(); ?>admin/archive" class="sidebar-link"><span class="hide-menu"> Archive</span></a></li>
                            </ul>
                        </li>
                        <?php } ?>
                        <li class="list-divider"></li>
                        <li class="nav-small-cap"><span class="hide-menu">Audit Trail</span></li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <i class="far fa-chart-bar"></i>
                                <span class="hide-menu">Logs </span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level base-level-line">
                                <li class="sidebar-item"><a href="<?php echo base_url(); ?>log/transaction" class="sidebar-link"><span class="hide-menu"> Transactions</span></a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-7 align-self-center">
                        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Account</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard" class="text-muted">Home</a></li>
                                    <li class="breadcrumb-item text-muted active" aria-current="page">Profile</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <?php if(isset($CI->session) && $CI->session->flashdata('message')): ?>
                    <div class="alert alert-success" role="alert"><?php echo $CI->session->flashdata('message'); ?></div>
                <?php endif; ?>
                <?php if(isset($CI->session) && $CI->session->flashdata('error')): ?>
                    <div class="alert alert-danger" role="alert"><?php echo $CI->session->flashdata('error'); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <img src="<?php echo base_url(); ?>assets/avatar/<?php echo $user_info['image']; ?>" alt="Profile photo" class="rounded-circle mb-3" width="140" height="140">
                                <h4 class="card-title mb-1"><?php echo ucwords($user_info['first_name'] . ' ' . $user_info['last_name']); ?></h4>
                                <p class="text-muted mb-1"><?php echo $user_info['email']; ?></p>
                                <p class="text-muted"><?php echo $user_info['mobile']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h4 class="card-title">Profile Information</h4>
                                <?php echo form_open(base_url() . 'profile', 'class="form-horizontal"'); ?>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">First Name</label>
                                            <input name="first_name" type="text" class="form-control <?php echo form_error('first_name') ? 'is-invalid' : ''; ?>" value="<?php echo set_value('first_name', $user_info['first_name']); ?>">
                                            <?php echo form_error('first_name', '<div class="invalid-feedback">', '</div>'); ?>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">Last Name</label>
                                            <input name="last_name" type="text" class="form-control <?php echo form_error('last_name') ? 'is-invalid' : ''; ?>" value="<?php echo set_value('last_name', $user_info['last_name']); ?>">
                                            <?php echo form_error('last_name', '<div class="invalid-feedback">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6 mt-2">
                                            <label class="col-form-label">Email Address</label>
                                            <input type="email" class="form-control" value="<?php echo $user_info['email']; ?>" disabled>
                                        </div>
                                        <div class="form-group col-md-6 mt-2">
                                            <label class="col-form-label">Mobile Number</label>
                                            <input name="mobile" type="text" class="form-control <?php echo form_error('mobile') ? 'is-invalid' : ''; ?>" value="<?php echo set_value('mobile', $user_info['mobile']); ?>">
                                            <?php echo form_error('mobile', '<div class="invalid-feedback">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" name="updateProfile" value="1" class="btn btn-primary">Save Profile</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <h4 class="card-title">Change Password</h4>
                                <?php echo form_open(base_url() . 'profile', 'class="form-horizontal"'); ?>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label class="col-form-label">Current Password</label>
                                            <input name="current_password" type="password" class="form-control <?php echo form_error('current_password') ? 'is-invalid' : ''; ?>">
                                            <?php echo form_error('current_password', '<div class="invalid-feedback">', '</div>'); ?>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="col-form-label">New Password</label>
                                            <input name="new_password" type="password" class="form-control <?php echo form_error('new_password') ? 'is-invalid' : ''; ?>">
                                            <?php echo form_error('new_password', '<div class="invalid-feedback">', '</div>'); ?>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="col-form-label">Confirm Password</label>
                                            <input name="confirm_password" type="password" class="form-control <?php echo form_error('confirm_password') ? 'is-invalid' : ''; ?>">
                                            <?php echo form_error('confirm_password', '<div class="invalid-feedback">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" name="changePassword" value="1" class="btn btn-warning">Update Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Profile Picture</h4>
                                <?php echo form_open_multipart(base_url() . 'profile', 'class="form-horizontal"'); ?>
                                    <div class="row">
                                        <div class="form-group col-md-9">
                                            <label class="col-form-label">Upload New Photo</label>
                                            <input type="file" name="profile_image" class="form-control">
                                        </div>
                                        <div class="form-group col-md-3 d-flex align-items-end">
                                            <button type="submit" name="uploadPhoto" value="1" class="btn btn-secondary w-100">Upload Photo</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer text-center text-muted">Animal Rabies Management System</footer>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/dist/js/app-style-switcher.js"></script>
    <script src="<?php echo base_url(); ?>assets/dist/js/feather.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/extra-libs/sparkline/sparkline.js"></script>
    <script src="<?php echo base_url(); ?>assets/dist/js/sidebarmenu.js"></script>
    <script src="<?php echo base_url(); ?>assets/dist/js/custom.min.js"></script>
</body>

</html>
