<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Users extends CI_Model {

	public function getUsers()
	{
        $query = $this->db->get('users');

        return $query->result_array();
	}
    
    public function getUser($id) 
    {
        $query = $this->db->where('id', $id)->get('users');

        return $query->row_array();
    }

    public function getUserByEmail($email) 
    {
        $query = $this->db->where('email', $email)->get('users');

        return $query->row_array();
    }

    public function getUserByCol($id, $col) 
    {
        $query = $this->db->where('id', $id)->get('users');

        return $query->row_array()[$col];
    }

    public function updateUserByCol($id, $col, $val) 
    {
        $this->db->set($col, $val, false);
        $this->db->where('id', $id);
        
        return $this->db->update('users');
    }

    public function updateUser($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    public function checkLogin() {

        $query = $this->db->where('email', $this->input->post('email'))
        ->where('password', md5($this->input->post('password')))
        ->get('users');

        return $query->row_array()['id'];

    }

    public function checkPassword($id, $password)
    {
        $query = $this->db->where('id', $id)
            ->where('password', md5($password))
            ->get('users');

        return (bool)$query->row_array();
    }

    public function updatePassword($id, $password)
    {
        $this->db->set('password', md5($password));
        $this->db->set('updated_at', time());
        $this->db->where('id', $id);
        return $this->db->update('users');
    }

    public function createUser() {

        $date = date("F j, Y");
        $mobile = normalize_ph_mobile($this->input->post('mobile'));
        $default_password = (string) rand(11111111, 99999999);
        $email = $this->input->post('email');

        $data = array(
        'level' => 1,
        'status' => 0,
        'image' => "default.png",
        'first_name' => $this->input->post('first_name'),
        'last_name' => $this->input->post('last_name'),
        'email' => $email,
        'mobile' => $mobile,
        'password' => md5($default_password),
        'created_at' => $date,
        'updated_at' => time()
        );

        $inserted = $this->db->insert('users', $data);
        if (!$inserted) {
            return false;
        }

        $message = sprintf("Your account is ready!\n\nUsername:\n%s\nPassword:\n%s", $email, $default_password);
        $sms_sent = $this->sendSmsMessage($mobile, $message);

        $subject = 'Your PetVax Admin Account';
        $email_message = $message . "\n\nPlease change your password after your first login.";
        $email_sent = $this->sendEmailMessage($email, $subject, $email_message);

        if (!$sms_sent) {
            log_message('error', 'Admin account SMS notification failed for: ' . $mobile);
        }

        if (!$email_sent) {
            log_message('error', 'Admin account email notification failed for: ' . $email);
            log_message('error', 'PHPMailer error: ' . $this->config->item('last_email_error'));
        }

        return true;

    }

    private function sendSmsMessage($mobile, $message)
    {
        if (empty($mobile)) {
            return false;
        }

        $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
        $data = [
            'api_token' => 'b36d92616e742c58bd0899a60a3fd23f250c2c0f',
            'message' => $message,
            'phone_number' => $mobile
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response !== false;
    }

    private function sendEmailMessage($email, $subject, $message)
    {
        if (empty($email)) {
            return false;
        }

        // Load SMTP credentials from application/config/email.php
        $this->config->load('email', true, true);

        $autoload = FCPATH . 'vendor/autoload.php';
        if (!file_exists($autoload)) {
            $this->config->set_item('last_email_error', 'Missing Composer autoload file at: ' . $autoload);
            return false;
        }

        require_once $autoload;

        if (!class_exists(PHPMailer::class)) {
            $this->config->set_item('last_email_error', 'PHPMailer class not found after autoload.');
            return false;
        }

        $smtp_host = $this->config->item('smtp_host');
        $smtp_port = (int) ($this->config->item('smtp_port') ?: 587);
        $smtp_user = $this->config->item('smtp_user');
        $smtp_pass = $this->config->item('smtp_pass');
        $smtp_secure = $this->config->item('smtp_crypto') ?: $this->config->item('smtp_secure');
        $from_email = $this->config->item('from_email') ?: $smtp_user;
        $from_name = $this->config->item('from_name') ?: 'PetVax Manager';

        if (empty($smtp_host) || empty($smtp_user) || empty($smtp_pass) || empty($from_email)) {
            $this->config->set_item('last_email_error', 'SMTP config missing (smtp_host/smtp_user/smtp_pass/from_email).');
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_user;
            $mail->Password = $smtp_pass;
            $mail->Port = $smtp_port;
            $mail->CharSet = 'UTF-8';
            $mail->isHTML(false);

            if (!empty($smtp_secure)) {
                $mail->SMTPSecure = $smtp_secure;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $sent = $mail->send();
            $this->config->set_item('last_email_error', '');
            return (bool) $sent;
        } catch (PHPMailerException $e) {
            $this->config->set_item('last_email_error', $e->getMessage());
            return false;
        }
    }

    public function getAdmins() {

        $query = $this->db->where('level', 1)->where('deleted', 0)->get('users');

        return $query->result_array();
    }

    public function getArchives() {

        $query = $this->db->where('level', 1)->where('deleted', 1)->get('users');

        return $query->result_array();
    }

    public function generateResetToken($user_id) {
        // Generate a unique token for password reset
        $token = bin2hex(random_bytes(50));
        
        // Store the reset token in the database
        $data = array(
            'reset_token' => $token,
            'reset_token_expires' => time() + 3600 // Token expires in 1 hour
        );
        
        $this->db->where('id', $user_id);
        $this->db->update('users', $data);
        
        return $token;
    }

    public function validateResetToken($token) {
        $current_time = time();
        
        // Check if token exists and hasn't expired
        $query = $this->db->where('reset_token', $token)
                          ->where('reset_token_expires >', $current_time)
                          ->get('users');
                          
        return $query->row_array();
    }

    public function resetPasswordWithToken($token, $new_password) {
        // Validate token first
        $user = $this->validateResetToken($token);
        
        if ($user) {
            // Update password and clear reset token
            $data = array(
                'password' => md5($new_password),
                'reset_token' => NULL,
                'reset_token_expires' => NULL
            );
            
            $this->db->where('id', $user['id']);
            $result = $this->db->update('users', $data);
            
            return $result ? $user : false;
        }
        
        return false;
    }

    public function resetPassword($email) {
        // Get user details
        $user = $this->getUserByEmail($email);
        
        if ($user) {
            // Generate reset token
            $reset_token = $this->generateResetToken($user['id']);
            
            // Only send SMS if user has a mobile number
            if (!empty($user['mobile'])) {
                // Send SMS with reset token
                $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
                    
                // Create a short reset link
                $reset_link = base_url() . 'reset_password?token=' . $reset_token . '&email=' . urlencode($email);
                
                $message = sprintf("Click this link to reset your password: %s", $reset_link);
                $phone = normalize_ph_mobile($user['mobile']);
                if ($phone === '') {
                    $phone = $user['mobile'];
                }
                    
                $data2 = [
                    'api_token' => 'b36d92616e742c58bd0899a60a3fd23f250c2c0f',
                    'message' => $message,
                    'phone_number' => $phone
                    ];
                    
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/x-www-form-urlencoded']);
                $response = curl_exec($ch);
                curl_close($ch);
            }
            
            return true;
        }
        
        return false;
    }

    public function actionReset($id) {

        $user = $this->getUser($id);
        $phone = normalize_ph_mobile($user['mobile']);
        if ($phone === '') {
            $phone = $user['mobile'];
        }

        $rand = rand(11111111,99999999);

        $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
            
        $message = sprintf("Your password is reset. Use the default password below.\n\nPassword:\n%s", $rand);
            
        $data2 = [
            'api_token' => 'b36d92616e742c58bd0899a60a3fd23f250c2c0f',
            'message' => $message,
            'phone_number' => $phone
            ];
            
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        curl_close($ch);


        $this->db->set('password', md5($rand));
        $this->db->where('id', $id);

        return $this->db->update('users');

    }

    public function actionSuspend($id) {

        $user = $this->getUser($id);
        $phone = normalize_ph_mobile($user['mobile']);
        if ($phone === '') {
            $phone = $user['mobile'];
        }

        $rand = rand(11111111,99999999);

        $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
            
        $message = sprintf("Your account is suspended.");
            
        $data2 = [
            'api_token' => 'b36d92616e742c58bd0899a60a3fd23f250c2c0f',
            'message' => $message,
            'phone_number' => $phone
            ];
            
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        curl_close($ch);

        $this->db->set('deleted', 1);
        $this->db->where('id', $id);

        return $this->db->update('users');
    }

    public function actionActivate($id) {

        $user = $this->getUser($id);
        $phone = normalize_ph_mobile($user['mobile']);
        if ($phone === '') {
            $phone = $user['mobile'];
        }

        $rand = rand(11111111,99999999);

        $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
            
        $message = sprintf("Your account is re-activated.");
            
        $data2 = [
            'api_token' => 'b36d92616e742c58bd0899a60a3fd23f250c2c0f',
            'message' => $message,
            'phone_number' => $phone
            ];
            
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        curl_close($ch);

        $this->db->set('deleted', 0);
        $this->db->where('id', $id);

        return $this->db->update('users');
    }
}

