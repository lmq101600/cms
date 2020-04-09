<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/2/18
 * Time: 11:31
 */
class MY_Controller extends CI_Controller
{

	public $GUSER = null;

	function __construct()
	{
		parent::__construct();
		$this->GUSER = $this->init();
	}

	function init()
	{
		$userInfo = checkLogin();
		if (!$userInfo) {
			redirect("/admin/auth/index");
		}
		return $userInfo['username'];
	}
}