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
	
	const TYPE_1 = 1; //目录
	const TYPE_2 = 2; //权限
	
	
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
	//创建无限极树状结构
	function list_to_tree($list, $pk='id', $pid = 'parent', $child = '_list', $root = 0)
	{
		//创建Tree
		$tree = [];
		if (is_array($list)) {
			//创建基于主键的数组引用
			$refer = [];
			foreach ($list as $key => $data) {
				$refer[$data[$pk]] = &$list[$key];
			}
			foreach ($list as $key => $data) {
				//判断是否存在parent
				$parantId = $data[$pid];
				if ($root == $parantId) {
					$tree[] = &$list[$key];
				} else {
					if (isset($refer[$parantId])) {
						$parent = &$refer[$parantId];
						$parent[$child][] = &$list[$key];
					}
				}
			}
		}
		return $tree;
	}
}