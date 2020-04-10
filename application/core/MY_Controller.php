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
	const param_error = '参数错误';
	const group_already_exist = '分组已存在';
	const add_success = '添加成功';
	const delete_success = '删除成功';

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