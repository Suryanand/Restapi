<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
	
	public function __construct()
        {
			parent::__construct();
			$this->load->helper('json_output_helper');
			$this->load->helper('form');
			$this->load->helper('url');
			$this->load->model('ApiModel');
		}
	public function index()
	{
		json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}
	public function login()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		}
		else{
			$check_auth_client = $this->ApiModel->check_auth_client();
			if($check_auth_client == true){
				$params = $_REQUEST;
		        $username = $params['username'];
		        $password = $params['password'];
		        $response = $this->ApiModel->login($username,$password);
			    json_output(400,array('status' => 400,'message' =>$response));
			} 
		}
	}
	public function emplist()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		}
		else{
			$check_auth_client = $this->ApiModel->check_auth_client_List();
			if($check_auth_client == true){
				$params = $_REQUEST;
				$response = $this->ApiModel->emplist();
				json_output(400,array('status' => 400,'message' =>$response));
			}
		}
	}
}
