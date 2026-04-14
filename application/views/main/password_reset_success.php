<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PetVax Manager | Password Reset Success</title>
    <link href="<?php echo base_url(); ?>assets/dist/css/style.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #eef6f5 0%, #d9e9f2 100%);
        }
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .auth-card {
            width: 100%;
            max-width: 460px;
            border: 0;
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(27, 61, 89, 0.14);
        }
        .brand-tag {
            color: #4f6d7a;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="card auth-card">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="brand-tag mb-2">PetVax Manager</div>
                <h2 class="mb-3">Password updated</h2>
                <p class="text-muted mb-4">Your password has been reset successfully. You can now sign in with your new password.</p>
                <a href="<?php echo site_url('login'); ?>" class="btn btn-primary w-100">Back to login</a>
            </div>
        </div>
    </div>
</body>
</html>
