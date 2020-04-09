<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/2/18
 * Time: 13:17
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
	protected $rs;


	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$lcode = gconfig("login_code");
		$data['isDis'] = false;
		if ($lcode == 1) {
			$data['isDis'] = true;
		}
		$this->load->view("admin/login/login", $data);
	}
	//登录
	function login()
	{
		$this->load->library('session');
		$uname = $this->input->post("uname", true);
		$pwd = $this->input->post("pwd", true);
		$code = $this->input->post("code", true);
		if ($uname == "" || $pwd == "") {
			json_code(- 1, null, "用户名或密码不能为空");
		}
		$this->load->model("admin/User_model");
		// 此处可以用redis加入防刷机制
		$user = $this->User_model->getUserByName($uname);
		if (! $user || ! isset($user['salt']) || ! isset($user['password']) || password($pwd, $user['salt']) != $user['password']) {
			json_code(- 2, null, "用户名或者密码错误");
		}
		$this->load->library('Verifycode');
		$rs = $this->verifycode->checkVerifyCode($code);
		
		if($rs['success'] !== true) {
			json_code(- 2, null, '验证码错误');
		}
		$this->User_model->wsession($user);
		wlog($uname.date('Y-m-d H:i:s',time()) . "登录");
		json_code(1, null, "OK");
	}
	//获取验证码
	public function getVerifyCode()
	{
		$this->load->library('Verifycode');
		$cap = $this->verifycode->generateCode();
		if($cap) {
			$this->rs['image'] = $cap['image'];
			$this->rs['code'] = $cap['code'];
		} else {
			$this->rs['msg'] = '获取验证码失败';
		}
		json_code(1, 	$this->rs, "OK");
	}
	//退出登录
	function logout()
	{
		$this->load->library('session');
		$arrUserInfo = $this->session->userdata('user_info');
		$this->session->unset_userdata('user_info');
		session_destroy();
		redirect("/");
	}
}