<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/4/15
 * Time: 14:49
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage extends MY_Controller{
	public function __construct()
	{
		
		parent::__construct();
		checkRightPage();
		

		$class= $this->router->fetch_class();
		$this->load->model("admin/{$class}_model" , "model" , true);
	}
	public function index(){
		//获取所有目录
		$arrAllMenuList = $this->model->getMenuByWhere(array('type'=>self::TYPE_1));
		$menulist = $this->list_to_tree($arrAllMenuList);
		//获取所有action
		$arrAction = $this->model->  ();
		$actionAllList = array();
		if (!empty($arrAction)) {
			foreach ($arrAction as $value) {
				$actionAllList[$value['parent']][] = $value;
			}
		}
		
		$data['menulist'] = $menulist;
		$data['$actionAllList'] = $actionAllList;

		$this->load->load_template("/admin/manage/manage", $data);
		
	}
}