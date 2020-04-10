<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/4/8
 * Time: 10:27
 */
defined('BASEPATH') or exit('No direct script access allowed');

class User extends MY_Controller {
	function __construct() {
		parent::__construct();
		//管理员有访问该目录权限
		checkRightPage("normaladmin");
		$class = $this->router->fetch_class();
		$this->load->model("admin/{$class}_model", "model", true);
	}
	public function index()
	{
		$this->load->library('page');
		$page = new Page();
		$page->num = 50;
		$arrLimit = $page->getlimit();
		$arrWhere['ls'] = $arrLimit['ls'];
		$arrWhere['le'] = $arrLimit['le'];
		//根据不同等级用户获取不同用户列表
		$userinfo = checkLogin();
		$level = empty($userinfo['level']) ? 0 : $userinfo['level'];
		$arrRes = $this->model->getManageUserByWhere($arrWhere, $level);
		$all = $arrRes['num'];


		$arrLevel = getTableColumnInfo("user" ,'user_level' ,'colmunvalue');

		$data['arrlevel'] = array();
		foreach ($arrLevel as $key => $value) {
			$level > $key ? $data['arrlevel'][$key] = $value : "";
		}
		$data['list'] = $arrRes['list'];
		$data['page_view'] = $page->view(array(
			'all' => $all,
		));
		$this->load->load_template('admin/manage/user', $data);
	}
	//获取用户信息
	function getUserinfo()
	{
		$id = $this->input->get('id' , true);

		$arrUser = $this->model->getUserByUid($id);
		if (empty($arrUser['username'])) {
			json_code(-1, array(), "用户异常");
		}
		$userinfo = checkLogin();

		if (empty($arrUser) || $userinfo['level'] <= $arrUser['user_level']) {
			json_code(-2, array(), '用户不存在或没有操作权限');
		}
		json_code(1 , $arrUser , 'success');
	}
	// 添加用户
	function ajaxAddUser() {
		
		$userinfo = checkLogin();

		$uname = $this->input->post("uname", true);
		$pwd = $this->input->post("pwd", true);
		$nick_name = $this->input->post("nick_name", true);
		$user_level = $this->input->post("user_level", true);
		if ($uname == "" || $pwd == "" || $nick_name == "") {
			json_code(-1, null, "111");
		}
		empty($user_level) && $user_level = 1;
		
		$userlevel = c("table_desc")['user']['user_level'];
		$userlevel = getTableColumnInfo("user" ,'user_level' ,'colmunvalue');
		if (empty($userlevel[$user_level]) || $userinfo['level'] <= $user_level) {
			json_code(-2, null, "222");
		}
		$arr['uname'] = $uname;
		$arr['user_level'] = $user_level;
		$arr['pwd'] = $pwd;
		$arr['nick_name'] = $nick_name;
		if($user_level==2)
		{
			$arrRight = c("normaladmin");
			var_dump($arrRight);
			$strRight = "";
			if(!empty($arrRight))
			{
				foreach (s as $key => $value) {
					$strRight.=$value['action'].",";
				}
				$strRight = substr($strRight,0, -1);
			}
			$arr['user_right'] = $strRight;
		}
		$user = $this->model->addUser($arr);
		$data['uname'] = $uname;
//		wlog($this->GUSER."添加了用户:".$uname);
		json_code($user['code'], $data, $user['msg']);
	}

	//添加用户时，获取可分配的管理级别
	function ajaxManageRight() {
		$user = checkLogin();
		$level = $user['level'];
		//获取可分配级别
		$arrLevel = getTableColumnInfo("user" ,'user_level' ,'colmunvalue');

		$data['arrlevel'] = array();
		foreach ($arrLevel as $key => $value) {
			$level > $key ? $data['arrlevel'][$key] = $value : "";
		}
		$view = $this->load->view("/admin/manage/adduser", $data, true);

		json_code(1, array('addview' => $view), 'success');
	}
	//删除用户
	function deleteUser() {
		$id = $this->input->get("id", true);
		if (!$id) {
			json_code(-1, array(), "缺少参数");
		}
		$arrUser = $this->model->getUserByUid($id);
		if (empty($arrUser['username'])) {
			json_code(-1, array(), "用户异常");
		}

		$userinfo = checkLogin();
		if (empty($arrUser) || $userinfo['level'] <= $arrUser['user_level']) {
			json_code(-2, array(), '用户不存在或没有操作权限');
		}
		$bool = $this->model->deleteUserById($id);
		if (!$bool) {
			json_code(-1, array(), "error......");
		}
		json_code(1, array(), 'success');

	}

	// 管理员修改密码
	function ajaxAdminCPwd() {
		$uid = $this->input->post('id', true);
		$arrUser = $this->model->getUserByUid($uid);
		$loginuUser = checkLogin();
		if (empty($arrUser) || $loginuUser['level'] <= $arrUser['user_level']) {
//			errorpage('用户不存在或没有操作权限');
			json_code(-1, null, "用户不存在");
		}
	
		$uname = $arrUser['username'];
		$pwd = $this->input->post("pwd", true);
		if ($pwd == "") {
			json_code(-1, null, "参数错误");
		}
		$salt = getRand(16);
		$data['password'] = password($pwd, $salt);
		$where['id'] = $uid;
		$isS = $this->model->updateUser($data, $where);
		if (!$isS) {
			json_code(-3, null, "修改密码失败");
		}
		wlog($this->GUSER."修改".$uname."的密码");
		json_code(1, 1, "更新成功");
	}
	//权限编辑页
	function editRight() {
		
		$uid = $this->input->get('id', true);
		$arrUser = $this->model->getUserByUid($uid);
		$loginuUser = checkLogin();

		if (empty($arrUser) || $loginuUser['level'] <= $arrUser['user_level']) {
			errorpage('用户不存在或没有操作权限');
		}
		if ($arrUser['user_level'] == 8 || $arrUser['user_level'] == 4) {
			errorpage('该用户不允许权限分配');
		}
		$arrCurent = array(
			'mname' => "user right",
			'url' => '/admin/user/index',
			'parent' => 1,
		);
		$data['_current'] = $arrCurent;
		$this->load->load_template("/admin/manage/editright", $data);
	}
	
	
	

	// 用户自己修改密码
	function ajaxChangePwd() {

		checkRightPage();

		$user = checkLogin();
		if (!$user) {
			ajax(-1, null, "请先登录");
		}
		$uname = $user['username'];
		$opwd = $this->input->post("opwd", true);
		$npwd = $this->input->post("npwd", true);
		if ($opwd == "" || $npwd == "") {
			ajax(-1, null, "新密码或者老密码不能为空");
		}
		$user = $this->model->getUserByName($uname);
		if (!$user) {
			ajax(-2, null, "获取当前用户失败");
		}
		$salt = $user['salt'];

		if ($user['password'] != password($opwd, $salt)) {
			ajax(-3, null, "老密码不正确");
		}
		$data['password'] = password($npwd, $salt);
		$where['username'] = $uname;
		$isS = $this->model->updateUser($data, $where);
		if (!$isS) {
			ajax(-3, null, "修改密码失败");
		}
		ajax(1, null, "更新成功");
	}

	// 管理员修改用户权限后，用户刷新自己的权限
	function refreshRight() {
		$user = checkLogin();
		if (!$user) {
			ajax(-1, null, "请先登录");
		}
		$uname = $user['username'];

		$user = $this->model->getUserByName($uname);
		if (!$user) {
			ajax(-2, null, "获取当前用户失败");
		}
		$this->model->wsession($user);
		ajax(1, null, "OK");
	}


	/*
		        获取用户可分配权限
	*/
	function ajaxGetLoginUserRight() {
	
		//参数 group right 分别为 管理组id数组 right action 数组
		$arrPost = $this->input->post(null, true);
		$arrUser = $this->model->getUserByUid($arrPost['uid']);
		
		if (empty($arrUser['username'])) {
			json_code(-1, array(), "用户异常");
		}
		$userinfo = checkLogin();
		if (empty($arrUser) || $userinfo['level'] <= $arrUser['user_level']) {
			json_code(-2, array(), '用户不存在或没有操作权限');
		}
		if ($arrUser['user_level'] == 8 || $arrUser['user_level'] == 4) {
			json_code(-3, array(), '该用户不允许权限分配');
		}

		$this->load->model('admin/Manage_model');

		$arrWhere['system'] = 2;
		$arrAllMenuList = $this->Manage_model->getMenu($arrWhere);
		$arrAllMenuKv = array();
		$arrAllMenulink = array();
		//所有目录
		foreach ($arrAllMenuList as $arrMenu) {
			!isset($arrAllMenulink[$arrMenu['id']]) && $arrAllMenulink[$arrMenu['id']] = array();
			if (empty($arrMenu['parent'])) {
				$arrMenu['_list'] = &$arrAllMenulink[$arrMenu['id']];
				$arrAllMenuKv[] = $arrMenu;
			} else {
				$arrAllMenulink[$arrMenu['parent']][] = $arrMenu;
			}
		}
		
		//所有权限
		$arrAction = $this->Manage_model->getAction();
		$actionAllList = array();
		if(!empty($arrAction)) {
			foreach ($arrAction as $v) {
				$actionAllList[$v['parent']][] = $v;
			}
		}
		
		
		//当前管理员可分配管理员权限组
		$strGroup = "";
		//是否为管理员或超级管理员
		$isadmin = checkRight(); //true为超级管理员
		if (!$isadmin) {
			$strGroup = $userinfo['group'];
		}
		//超管获取所有用户组
		$arrGroup = $this->model->getUserGroup($strGroup);
		
		//当前管理员可分配的权限
		$arrRight = array();
		if ($isadmin) {
			$arrLinkRight = $this->Manage_model->getMenuByWhere();	//获取所有
			if (!empty($arrLinkRight)) {
				foreach ($arrLinkRight as $value) {
					!empty($value['action']) && $arrRight[] = $value['action'];
				}
			}
		} else {
			$arrRight = json_decode($userinfo['right'], true);
		}
		
		//当前正在分配的权限 或者 已拥有的权限
		if (!empty($arrPost['first']) && $arrPost['first'] == 1) {
			$userGroup = $arrUser['user_group'];	//用户组-超管都为空
			$userRigth = $arrUser['user_right'];	//用户单独权限配置
			$arrCurentGroup = empty($userGroup) ? array() : explode(",", $userGroup);
			$arrCurentRight = empty($userRigth) ? array() : explode(",", $userRigth);
			if (!empty($userGroup)) {
				$jsonRight = $this->model->getUserRight($userGroup, "", 2);	//获得用户组
				!empty($jsonRight) && $arrCurentRight = array_merge($arrCurentRight, json_decode($jsonRight, true));
			}
		} else {
			$arrCurentGroup = empty($arrPost['group']) ? array() : array_filter($arrPost['group'], function ($a) {return intval($a) >= 1;});
			$arrCurentRight = empty($arrPost['right']) ? array() : $arrPost['right'];
			if (!empty($arrCurentGroup)) {
				$strCurrentGroup = implode(",", $arrCurentGroup);
				$jsonRight = $this->model->getUserRight($strCurrentGroup, "", 2);
				$arrCurentRight = array_merge($arrCurentRight, json_decode($jsonRight, true));
			}
		}
		//当前用户权限
		$jdata['arrCurentGroup'] = $arrCurentGroup;
		$jdata['arrCurentRight'] = $arrCurentRight;

		//当前管理员可分配的权限组
		$jdata['arrGroup'] = $arrGroup;
		$jdata['arrRight'] = $arrRight;

		//所有目录和权限
		$jdata['actionAllList'] = $actionAllList;
		$jdata['arrAllMenuKv'] = $arrAllMenuKv;//目录
		
		$view = $this->load->view("admin/manage/ajaxeditrightpage", $jdata, true);

		$data['view'] = $view;

		json_code(1, $data, 'success');
	}
	/*
		保存用户权限
	*/
	function saveUserRight() {
		$uid = $this->input->post('uid', true);
		$postGroup = $this->input->post('group', true);
		$postRight = $this->input->post('right', true);

		$arrUser = $this->model->getUserByUid($uid);
		
		if (empty($arrUser['username'])) {
			json_code(-1, array(), "用户异常");
		}
		$userinfo = checkLogin();

		if (empty($arrUser) || $userinfo['level'] <= $arrUser['user_level']) {
			json_code(-2, array(), '用户不存在或没有操作权限');
		}
		if ($arrUser['user_level'] == 8 || $arrUser['user_level'] == 4) {
			json_code(-3, array(), '该用户不允许权限分配');
		}

		//是否为管理员或超级管理员
		$isadmin = checkRight();
		
		if (!$isadmin) {
			$strGroup = $userinfo['group'];
			$arrGroup = empty($strGroup) ? array() : explode(",", $strGroup);
			$arrRight = json_decode($userinfo['right'], true);
		} else {
			$arrResGroup = $this->model->getUserGroup();
			foreach ($arrResGroup as $key => $value) {
				$arrGroup[] = $value['id'];
			}
			$this->load->model('admin/Manage_model');
			$arrLinkRight = $this->Manage_model->getMenuByWhere();
			$arrRight = array();
			if (!empty($arrLinkRight)) {
				foreach ($arrLinkRight as $value) {
					!empty($value['action']) && $arrRight[] = $value['action'];
				}
			}
		}
		$strGroup = "";
		$strRight = "";
		$beyond = false;
		if (!empty($postGroup)) {
			foreach ($postGroup as $key => $value) {
				!in_array($value, $arrGroup) && $beyond = true;
			}
			$strGroup = implode(",", $postGroup);
		}

		if (!empty($postRight)) {
			foreach ($postRight as $key => $value) {
				!in_array($value, $arrRight) && $beyond = true;
			}
			$strRight = implode(",", $postRight);
		}

		if ($beyond) {
			json_code(-4, array(), '超出可分配权限范围');
		}

		if($arrUser['user_level'] == 2)
		{
			$arrRight = c("normaladmin");
			$strNormaladminRight = "";
			if(!empty($arrRight))
			{
				foreach ($arrRight as $key => $value) {
					$strNormaladminRight.=$value['action'].",";
				}
				$strNormaladminRight = substr($strNormaladminRight,0, -1);
			}
			$strRight?$strRight.=",".$strNormaladminRight:$strRight=$strNormaladminRight;
		}

		//如果当前用户为普通管理员 获取被编辑用户拥有但管理员么有的权限 

		if(!$isadmin && !empty($arrUser['user_right']))
		{
			$arrUserEditright = explode(",", $arrUser['user_right']);
			$arrRight = json_decode($userinfo['right'], true);
			foreach ($arrUserEditright as $key => $value) {
				if(!in_array($value, $arrRight) ){
					$strRight?$strRight.=(",".$value):$strRight.=$value;
				}
			}
			$arrUserEditGroup = explode(",", $arrUser['user_group']);
			$arrGroup = empty($userinfo['group']) ? array() : explode(",", $userinfo['group']);
			if(!empty($arrUserEditGroup))
			{
				foreach ($arrUserEditGroup as $key => $value) {
					if(!empty($value) && !in_array($value, $arrGroup) ){
						$strGroup?$strGroup.=(",".$value):$strGroup.=$value;
					}
				}
			}
		}

		$arrEdit = array(
			'user_group' => $strGroup,
			'user_right' => $strRight,
		);
		$arrWhere = array(
			'id' => $uid,
		);
		$bool = $this->model->updateUser($arrEdit, $arrWhere);
		if (!$bool) {
			json_code(-5, array(), '保存失败');
		}
		json_code(1, array(), 'success');
	}


	//更新用户信息
	function updatUserinfo(){
		$postUserInfo = $this->input->post(null , true);
		if(empty($postUserInfo['id']) ||empty($postUserInfo['nick_name'])||empty($postUserInfo['status']) )
		{
			json_code(-1 , array() , '参数错误');
		}
		$arrUser = $this->model->getUserByUid($postUserInfo['id']);
		if (empty($arrUser['username'])) {
			json_code(-1, array(), "用户异常");
		}
		$userinfo = checkLogin();

		if (empty($arrUser) || $userinfo['level'] <= $arrUser['user_level']) {
			json_code(-2, array(), '用户不存在或没有操作权限');
		}
		$arrEdit = array(
			'nick_name'=>$postUserInfo['nick_name'],
			'status'=>$postUserInfo['status']
		);
		$arrWhere = array(
			'id'=>$postUserInfo['id']
		);
		$bool = $this->model->updateUser($arrEdit , $arrWhere);
		if(!$bool)
		{
			json_code(-1 , array() , '修改失败');
		}
		json_code(1 , array() , 'success');
	}
}