<?php
class ApiModel extends CI_Model {
 	 function __construct(){
     parent::__construct();
	 $this->load->database();
	 date_default_timezone_set('Asia/Kolkata'); 
  	}
	var $auth_key = "Login_2578564534168463213546846";
	var $auth_key_list = "List_2578564534168463213546846";
    public function check_auth_client(){
        $auth_key  = $this->input->get_request_header('Authentication', TRUE);
        if($auth_key == $this->auth_key){
            return true;
        } else {
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        }
    }
	public function check_auth_client_List(){
        $auth_key  = $this->input->get_request_header('Authentication', TRUE);
        if($auth_key == $this->auth_key_list){
            return true;
        } else {
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.', 'auth' => $this->auth_key_list));
        }
    }
	public function login($username,$password)
    {
        $q = $this->db->select('password,id')->from('user')->where(array('username'=>$username,'password'=>$password))->get()->row();
        if($q == ""){
            return array('status' => 204,'message' => 'Username not found.' );
        }
		else{
			$id = $q->id;
				if ($id != null) {
				   $last_login = date('Y-m-d H:i:s');
				   $this->db->trans_start();
				   $this->db->where('id',$id)->update('user',array('last_login' => $last_login));
				   if ($this->db->trans_status() === FALSE){
					  $this->db->trans_rollback();
					  return array('status' => 500,'message' => 'Internal server error.');
				   } else {
					  $this->db->trans_commit();
					  return array('status' => 200,'message' => "Successful Login",'id' => $id);
				   }
				}else {
				  return array('status' => 204,'message' => 'Wrong password.', 'id' => $id);
				}
        }
    }
	public function emplist(){
		$q = $this->db->select('*')->from('user')->get()->result();
		if($q == ""){
            return array('status' => 204,'message' => 'Username not found.');
        }
		else{
			return array('message' => "All Employees",'Employee List' => $q);
		}
	}

}