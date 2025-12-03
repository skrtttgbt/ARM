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

        $data = array(
        'level' => 1,
        'status' => 0,
        'image' => "default.png",
        'first_name' => $this->input->post('first_name'),
        'last_name' => $this->input->post('last_name'),
        'email' => $this->input->post('email'),
        'mobile' => $this->input->post('mobile'),
        'password' => md5($this->input->post('password')),
        'created_at' => $date,
        'updated_at' => time()
        );

        $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
            
        $message = sprintf("You account is ready!\n\nUsername:\n%s\nPassword:\n%s", $this->input->post('email'), $this->input->post('email'));
            
        $data2 = [
            'api_token' => 'de58ea1dd508785da1e3c76551d1888e4994e7a6',
            'message' => $message,
            'phone_number' => $this->input->post('mobile')
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

    public function actionReset($id) {

        $user = $this->getUser($id);

        $rand = rand(11111111,99999999);

        $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
            
        $message = sprintf("Your password is reset. Use the default password below.\n\nPassword:\n%s", $rand);
            
        $data2 = [
            'api_token' => 'de58ea1dd508785da1e3c76551d1888e4994e7a6',
            'message' => $message,
            'phone_number' => $user['mobile']
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

        $rand = rand(11111111,99999999);

        $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
            
        $message = sprintf("Your account is suspended.");
            
        $data2 = [
            'api_token' => 'de58ea1dd508785da1e3c76551d1888e4994e7a6',
            'message' => $message,
            'phone_number' => $user['mobile']
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

        $rand = rand(11111111,99999999);

        $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
            
        $message = sprintf("Your account is re-activated.");
            
        $data2 = [
            'api_token' => 'de58ea1dd508785da1e3c76551d1888e4994e7a6',
            'message' => $message,
            'phone_number' => $user['mobile']
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
