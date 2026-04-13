<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PetVax Manager | Login</title>
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
            max-width: 420px;
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
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="brand-tag mb-2">PetVax Manager</div>
                    <h2 class="mb-1">Sign in</h2>
                    <p class="text-muted mb-0">Use your administrator account to continue.</p>
                </div>

                <?php if (!empty($login_error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $login_error; ?>
                    </div>
                <?php endif; ?>

                <?php echo form_open('login'); ?>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo set_value('email'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label mb-0">Password</label>
                            <a href="<?php echo site_url('forgot_password'); ?>" class="small text-decoration-none">Forgot password?</a>
                        </div>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <button type="submit" name="loginBtn" value="1" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
