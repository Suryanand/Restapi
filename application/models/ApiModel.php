<?php
class ApiModel extends CI_Model {
 	 function __construct(){
     parent::__construct();
	 $this->load->database();
	 $this->load->library('session');
	 $this->load->library('encrypt');
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
				   $hash = $this->encrypt->encode($password+ "_" +$last_login);
				   $this->db->trans_start();
				   $this->db->where('id',$id)->update('user',array('last_login' => $last_login , 'last_activity' => $last_login , 'token' => $hash));
				   if ($this->db->trans_status() === FALSE){
					  $this->db->trans_rollback();
					  return array('status' => 500,'message' => 'Internal server error.');
				   } else {
					  $this->db->trans_commit();
					  $this->session->set_userdata('userid', $id);
					  return array('status' => 200,'message' => "Successful Login",'id' => $id , 'token' => $hash);
				   }
				}else {
				  return array('status' => 204,'message' => 'Wrong password.', 'id' => $id);
				}
        }
    }
	public function emplist(){
		date_default_timezone_set('Asia/Kolkata');
		$now = new DateTime();
		$last_login = $now->format('Y-m-d H:i:s'); //date();
		$params = $_REQUEST;
		$userid = $params['userid']; 
		$token = $params['token'];
		$query1 = "select * from user where id = '". $userid ."' and token = '". $token ."'";
		$query = $this->db->query($query1);
		$check = $query->row();	
		$test = date($check->last_activity);
		$date = date_create_from_format('Y-m-d H:i:s', $check->last_activity);
		$timediff = round(abs( $date->getTimestamp() - (new \DateTime())->getTimestamp()) / 60 , 0);
		//$date1 = strtotime($check->last_activity);
		//$dateprint=date('Y-m-d H:i:s',$date1);
		//$test1 = $test->format('Y-m-d H:i:s');
		//$test_diff = date_diff($last_login , $check->last_activity);
		//$datetime1 = new DateTime();
		//$datetime2 = new DateTime($check->last_activity);
		//$interval = $datetime1->diff($datetime2);
		//$elapsed = $interval->format('%y years %m months %a days %h hours %i minutes %s seconds');
		//$timeinterval=$interval->format('%y-%m-%a %h:%i:%s');
		//$datetest=$datetime1-$datetime2;
		
        if($timediff > 30){
            return array('status' => 209,'message' => 'Session Expired.', 'c' => $last_login);
        }
		else{
			$q = $this->db->select('id, username, password, Firstname, Lastname, mobile, email, last_login')->from('user')->get()->result();
			if($q == ""){
				return array('status' => 204,'message' => 'Username not found.');
			}
			else{
				$this->db->where('id', $userid);
				$this->db->update('user',array('last_activity' => $last_login) );
				$affected_rows = $this->db->affected_rows();
				if($affected_rows){
					return array('status' => 200,'message' => "All Employees",'Employee List' => $q );
				}else{
					return array('status' => 209,'message' => 'Something Went Wrong.');
				}
			}
		}
	}
	
	public function saveemp(){
		date_default_timezone_set('Asia/Kolkata');
		$now = new DateTime();
		$last_login = $now->format('Y-m-d H:i:s'); //date();
		$params = $_REQUEST;
		$userid = $params['userid'];
		$data = array(
			'username' => $params['username'],
			'password' => $params['password'],
			'firstname' => $params['firstname'],
			'lastname' => $params['lastname'],
			'mobile' => $params['mobile'],
			'email' => $params['email']
		);
		$token = $params['token'];
		$query1 = "select * from user where id = '". $userid ."' and token = '". $token ."'";
		$query = $this->db->query($query1);
		$check = $query->row();	
		$test = date($check->last_activity);
		$date = date_create_from_format('Y-m-d H:i:s', $check->last_activity);
		$timediff = round(abs( $date->getTimestamp() - (new \DateTime())->getTimestamp()) / 60 , 0);
		if($timediff > 30){
            return array('status' => 209,'message' => 'Session Expired.', 'c' => $last_login);
        }
		else{
			$this->db->insert('user', $data);
			$insert_id = $this->db->insert_id();
			if($insert_id){
				$this->db->where('id', $userid);
				$this->db->update('user',array('last_activity' => $last_login) );
				$affected_rows = $this->db->affected_rows();
				if($affected_rows){
					return array('status' => 200,'message' => 'Employee Inserted.');
				}else{
					return array('status' => 209,'message' => 'Something Went Wrong.');
				}
			}else{
				return array('status' => 209,'message' => 'Something Went Wrong.');
			}
		}
	}
}