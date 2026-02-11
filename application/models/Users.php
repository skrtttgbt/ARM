<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

    public function checkLogin() {

        $query = $this->db->where('email', $this->input->post('email'))
        ->where('password', md5($this->input->post('password')))
        ->get('users');

        return $query->row_array()['id'];

    }

    public function createUser() {

        $date = date("F j, Y");
        $mobile = normalize_ph_mobile($this->input->post('mobile'));

        $data = array(
        'level' => 1,
        'status' => 0,
        'image' => "default.png",
        'first_name' => $this->input->post('first_name'),
        'last_name' => $this->input->post('last_name'),
        'email' => $this->input->post('email'),
        'mobile' => $mobile,
        'password' => md5($this->input->post('password')),
        'created_at' => $date,
        'updated_at' => time()
        );

        $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
            
        $message = sprintf("You account is ready!\n\nUsername:\n%s\nPassword:\n%s", $this->input->post('email'), $this->input->post('email'));
            
        $data2 = [
            'api_token' => 'de58ea1dd508785da1e3c76551d1888e4994e7a6',
            'message' => $message,
            'phone_number' => $mobile
            ];
            
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        curl_close($ch);
            //echo $response;
        return $this->db->insert('users',$data);

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
                    'api_token' => 'de58ea1dd508785da1e3c76551d1888e4994e7a6',
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
            'api_token' => 'de58ea1dd508785da1e3c76551d1888e4994e7a6',
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
            'api_token' => 'de58ea1dd508785da1e3c76551d1888e4994e7a6',
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
            'api_token' => 'de58ea1dd508785da1e3c76551d1888e4994e7a6',
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
