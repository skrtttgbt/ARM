<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Forgot Password</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/old/vendors/feather/feather.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/old/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/old/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/old/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/old/vendors/mdi/css/materialdesignicons.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/old/css/style.css">

  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
          <div class="row w-100 mx-0">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                <h4>Reset Your Password</h4>
                <p class="mb-4">Enter your email address and we'll send you a link to reset your password.</p>
                <?php echo form_open('forgot_password', 'class="pt-3" id="forgotpasswordform"'); ?>
                
                  <?php if(isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                  <?php endif; ?>
                  
                  <?php if(isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                  <?php endif; ?>
                  
                  <div class="form-group">
                    <input type="email" name="email" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Email" value="<?php echo set_value('email'); ?>">
                    <?php echo form_error('email', '<div class="text-danger">', '</div>'); ?>
                  </div>
                  <div class="mt-3 d-grid gap-2">
                    <button name="resetBtn" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" type="submit">RESET PASSWORD</button>
                  </div>
                  <div class="text-center mt-4 fw-light">
                    <a href="<?php echo base_url(); ?>login" class="text-primary">Back to Login</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="<?php echo base_url(); ?>assets/old/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="<?php echo base_url(); ?>assets/old/js/off-canvas.js"></script>
    <script src="<?php echo base_url(); ?>assets/old/js/template.js"></script>
    <script src="<?php echo base_url(); ?>assets/old/js/settings.js"></script>
    <script src="<?php echo base_url(); ?>assets/old/js/todolist.js"></script>
    <!-- endinject -->
  </body>
</html>