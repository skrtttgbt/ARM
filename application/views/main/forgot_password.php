<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PetVax Manager | Forgot Password</title>
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
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="brand-tag mb-2">PetVax Manager</div>
                    <h2 class="mb-1">Forgot password</h2>
                    <p class="text-muted mb-0">
                        <?php echo ($step ?? 'request') === 'verify'
                            ? 'Enter the SMS OTP and your new password.'
                            : 'Enter your account email to receive an OTP by SMS.'; ?>
                    </p>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (($step ?? 'request') === 'verify'): ?>
                    <?php echo form_open('forgot_password'); ?>
                        <input type="hidden" name="action" value="verify_otp">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo html_escape($email ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="otp" class="form-label">OTP Code</label>
                            <input type="text" name="otp" id="otp" class="form-control" inputmode="numeric" maxlength="6" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2">Verify OTP and Reset Password</button>
                    </form>

                    <?php echo form_open('forgot_password'); ?>
                        <input type="hidden" name="action" value="send_otp">
                        <input type="hidden" name="email" value="<?php echo html_escape($email ?? ''); ?>">
                        <button
                            type="submit"
                            class="btn btn-outline-secondary w-100 js-resend-otp-btn"
                            <?php echo !empty($resend_seconds_remaining) ? 'disabled' : ''; ?>
                            data-seconds-remaining="<?php echo (int) ($resend_seconds_remaining ?? 0); ?>">
                            <?php if (!empty($resend_seconds_remaining)): ?>
                                Resend OTP in <?php echo (int) $resend_seconds_remaining; ?>s
                            <?php else: ?>
                                Resend OTP
                            <?php endif; ?>
                        </button>
                    </form>
                <?php else: ?>
                    <?php echo form_open('forgot_password'); ?>
                        <input type="hidden" name="action" value="send_otp">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo html_escape($email ?? ''); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send OTP</button>
                    </form>
                <?php endif; ?>

                <div class="text-center mt-3">
                    <a href="<?php echo site_url('login'); ?>" class="small text-decoration-none">Back to login</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var resendButton = document.querySelector('.js-resend-otp-btn');
            if (!resendButton) {
                return;
            }

            var secondsRemaining = parseInt(resendButton.getAttribute('data-seconds-remaining'), 10) || 0;
            if (secondsRemaining <= 0) {
                return;
            }

            var timer = window.setInterval(function () {
                secondsRemaining -= 1;

                if (secondsRemaining <= 0) {
                    resendButton.disabled = false;
                    resendButton.textContent = 'Resend OTP';
                    resendButton.setAttribute('data-seconds-remaining', '0');
                    window.clearInterval(timer);
                    return;
                }

                resendButton.textContent = 'Resend OTP in ' + secondsRemaining + 's';
                resendButton.setAttribute('data-seconds-remaining', String(secondsRemaining));
            }, 1000);
        })();
    </script>
</body>
</html>
