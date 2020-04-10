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
		//����Ա�з��ʸ�Ŀ¼Ȩ��
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
		//���ݲ�ͬ�ȼ��û���ȡ��ͬ�û��б�
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
	//��ȡ�û���Ϣ
	function getUserinfo()
	{
		$id = $this->input->get('id' , true);

		$arrUser = $this->model->getUserByUid($id);
		if (empty($arrUser['username'])) {
			json_code(-1, array(), "�û��쳣");
		}
		$userinfo = checkLogin();

		if (empty($arrUser) || $userinfo['level'] <= $arrUser['user_level']) {
			json_code(-2, array(), '�û������ڻ�û�в���Ȩ��');
		}
		json_code(1 , $arrUser , 'success');
	}
	// ����û�
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
//		wlog($this->GUSER."������û�:".$uname);
		json_code($user['code'], $data, $user['msg']);
	}

	//����û�ʱ����ȡ�ɷ���Ĺ�����
	function ajaxManageRight() {
		$user = checkLogin();
		$level = $user['level'];
		//��ȡ�ɷ��伶��
		$arrLevel = getTableColumnInfo("user" ,'user_level' ,'colmunvalue');

		$data['arrlevel'] = array();
		foreach ($arrLevel as $key => $value) {
			$level > $key ? $data['arrlevel'][$key] = $value : "";
		}
		$view = $this->load->view("/admin/manage/adduser", $data, true);

		json_code(1, array('addview' => $view), 'success');
	}
	//ɾ���û�
	function deleteUser() {
		$id = $this->input->get("id", true);
		if (!$id) {
			json_code(-1, array(), "ȱ�ٲ���");
		}
		$arrUser = $this->model->getUserByUid($id);
		if (empty($arrUser['username'])) {
			json_code(-1, array(), "�û��쳣");
		}

		$userinfo = checkLogin();
		if (empty($arrUser) || $userinfo['level'] <= $arrUser['user_level']) {
			json_code(-2, array(), '�û������ڻ�û�в���Ȩ��');
		}
		$bool = $this->model->deleteUserById($id);
		if (!$bool) {
			json_code(-1, array(), "error......");
		}
		json_code(1, array(), 'success');

	}

	// ����Ա�޸�����
	function ajaxAdminCPwd() {
		$uid = $this->input->post('id', true);
		$arrUser = $this->model->getUserByUid($uid);
		$loginuUser = checkLogin();
		if (empty($arrUser) || $loginuUser['level'] <= $arrUser['user_level']) {
//			errorpage('�û������ڻ�û�в���Ȩ��');
			json_code(-1, null, "�û�������");
		}
	
		$uname = $arrUser['username'];
		$pwd = $this->input->post("pwd", true);
		if ($pwd == "") {
			json_code(-1, null, "��������");
		}
		$salt = getRand(16);
		$data['password'] = password($pwd, $salt);
		$where['id'] = $uid;
		$isS = $this->model->updateUser($data, $where);
		if (!$isS) {
			json_code(-3, null, "�޸�����ʧ��");
		}
		wlog($this->GUSER."�޸�".$uname."������");
		json_code(1, 1, "���³ɹ�");
	}
	//Ȩ�ޱ༭ҳ
	function editRight() {
		
		$uid = $this->input->get('id', true);
		$arrUser = $this->model->getUserByUid($uid);
		$loginuUser = checkLogin();

		if (empty($arrUser) || $loginuUser['level'] <= $arrUser['user_level']) {
			errorpage('�û������ڻ�û�в���Ȩ��');
		}
		if ($arrUser['user_level'] == 8 || $arrUser['user_level'] == 4) {
			errorpage('���û�������Ȩ�޷���');
		}
		$arrCurent = array(
			'mname' => "user right",
			'url' => '/admin/user/index',
			'parent' => 1,
		);
		$data['_current'] = $arrCurent;
		$this->load->load_template("/admin/manage/editright", $data);
	}
	
	
	

	// �û��Լ��޸�����
	function ajaxChangePwd() {

		checkRightPage();

		$user = checkLogin();
		if (!$user) {
			ajax(-1, null, "���ȵ�¼");
		}
		$uname = $user['username'];
		$opwd = $this->input->post("opwd", true);
		$npwd = $this->input->post("npwd", true);
		if ($opwd == "" || $npwd == "") {
			ajax(-1, null, "��������������벻��Ϊ��");
		}
		$user = $this->model->getUserByName($uname);
		if (!$user) {
			ajax(-2, null, "��ȡ��ǰ�û�ʧ��");
		}
		$salt = $user['salt'];

		if ($user['password'] != password($opwd, $salt)) {
			ajax(-3, null, "�����벻��ȷ");
		}
		$data['password'] = password($npwd, $salt);
		$where['username'] = $uname;
		$isS = $this->model->updateUser($data, $where);
		if (!$isS) {
			ajax(-3, null, "�޸�����ʧ��");
		}
		ajax(1, null, "���³ɹ�");
	}

	// ����Ա�޸��û�Ȩ�޺��û�ˢ���Լ���Ȩ��
	function refreshRight() {
		$user = checkLogin();
		if (!$user) {
			ajax(-1, null, "���ȵ�¼");
		}
		$uname = $user['username'];

		$user = $this->model->getUserByName($uname);
		if (!$user) {
			ajax(-2, null, "��ȡ��ǰ�û�ʧ��");
		}
		$this->model->wsession($user);
		ajax(1, null, "OK");
	}


	/*
		        ��ȡ�û��ɷ���Ȩ��
	*/
	function ajaxGetLoginUserRight() {
	
		//���� group right �ֱ�Ϊ ������id���� right action ����
		$arrPost = $this->input->post(null, true);
		$arrUser = $this->model->getUserByUid($arrPost['uid']);
		
		if (empty($arrUser['username'])) {
			json_code(-1, array(), "�û��쳣");
		}
		$userinfo = checkLogin();
		if (empty($arrUser) || $userinfo['level'] <= $arrUser['user_level']) {
			json_code(-2, array(), '�û������ڻ�û�в���Ȩ��');
		}
		if ($arrUser['user_level'] == 8 || $arrUser['user_level'] == 4) {
			json_code(-3, array(), '���û�������Ȩ�޷���');
		}

		$this->load->model('admin/Manage_model');

		$arrWhere['system'] = 2;
		$arrAllMenuList = $this->Manage_model->getMenu($arrWhere);
		$arrAllMenuKv = array();
		$arrAllMenulink = array();
		//����Ŀ¼
		foreach ($arrAllMenuList as $arrMenu) {
			!isset($arrAllMenulink[$arrMenu['id']]) && $arrAllMenulink[$arrMenu['id']] = array();
			if (empty($arrMenu['parent'])) {
				$arrMenu['_list'] = &$arrAllMenulink[$arrMenu['id']];
				$arrAllMenuKv[] = $arrMenu;
			} else {
				$arrAllMenulink[$arrMenu['parent']][] = $arrMenu;
			}
		}
		
		//����Ȩ��
		$arrAction = $this->Manage_model->getAction();
		$actionAllList = array();
		if(!empty($arrAction)) {
			foreach ($arrAction as $v) {
				$actionAllList[$v['parent']][] = $v;
			}
		}
		
		
		//��ǰ����Ա�ɷ������ԱȨ����
		$strGroup = "";
		//�Ƿ�Ϊ����Ա�򳬼�����Ա
		$isadmin = checkRight(); //trueΪ��������Ա
		if (!$isadmin) {
			$strGroup = $userinfo['group'];
		}
		//���ܻ�ȡ�����û���
		$arrGroup = $this->model->getUserGroup($strGroup);
		
		//��ǰ����Ա�ɷ����Ȩ��
		$arrRight = array();
		if ($isadmin) {
			$arrLinkRight = $this->Manage_model->getMenuByWhere();	//��ȡ����
			if (!empty($arrLinkRight)) {
				foreach ($arrLinkRight as $value) {
					!empty($value['action']) && $arrRight[] = $value['action'];
				}
			}
		} else {
			$arrRight = json_decode($userinfo['right'], true);
		}
		
		//��ǰ���ڷ����Ȩ�� ���� ��ӵ�е�Ȩ��
		if (!empty($arrPost['first']) && $arrPost['first'] == 1) {
			$userGroup = $arrUser['user_group'];	//�û���-���ܶ�Ϊ��
			$userRigth = $arrUser['user_right'];	//�û�����Ȩ������
			$arrCurentGroup = empty($userGroup) ? array() : explode(",", $userGroup);
			$arrCurentRight = empty($userRigth) ? array() : explode(",", $userRigth);
			if (!empty($userGroup)) {
				$jsonRight = $this->model->getUserRight($userGroup, "", 2);	//����û���
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
		//��ǰ�û�Ȩ��
		$jdata['arrCurentGroup'] = $arrCurentGroup;
		$jdata['arrCurentRight'] = $arrCurentRight;

		//��ǰ����Ա�ɷ����Ȩ����
		$jdata['arrGroup'] = $arrGroup;
		$jdata['arrRight'] = $arrRight;

		//����Ŀ¼��Ȩ��
		$jdata['actionAllList'] = $actionAllList;
		$jdata['arrAllMenuKv'] = $arrAllMenuKv;//Ŀ¼
		
		$view = $this->load->view("admin/manage/ajaxeditrightpage", $jdata, true);

		$data['view'] = $view;

		json_code(1, $data, 'success');
	}
	/*
		�����û�Ȩ��
	*/
	function saveUserRight() {
		$uid = $this->input->post('uid', true);
		$postGroup = $this->input->post('group', true);
		$postRight = $this->input->post('right', true);

		$arrUser = $this->model->getUserByUid($uid);
		
		if (empty($arrUser['username'])) {
			json_code(-1, array(), "�û��쳣");
		}
		$userinfo = checkLogin();

		if (empty($arrUser) || $userinfo['level'] <= $arrUser['user_level']) {
			json_code(-2, array(), '�û������ڻ�û�в���Ȩ��');
		}
		if ($arrUser['user_level'] == 8 || $arrUser['user_level'] == 4) {
			json_code(-3, array(), '���û�������Ȩ�޷���');
		}

		//�Ƿ�Ϊ����Ա�򳬼�����Ա
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
			json_code(-4, array(), '�����ɷ���Ȩ�޷�Χ');
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

		//�����ǰ�û�Ϊ��ͨ����Ա ��ȡ���༭�û�ӵ�е�����Աô�е�Ȩ�� 

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
			json_code(-5, array(), '����ʧ��');
		}
		json_code(1, array(), 'success');
	}


	//�����û���Ϣ
	function updatUserinfo(){
		$postUserInfo = $this->input->post(null , true);
		if(empty($postUserInfo['id']) ||empty($postUserInfo['nick_name'])||empty($postUserInfo['status']) )
		{
			json_code(-1 , array() , '��������');
		}
		$arrUser = $this->model->getUserByUid($postUserInfo['id']);
		if (empty($arrUser['username'])) {
			json_code(-1, array(), "�û��쳣");
		}
		$userinfo = checkLogin();

		if (empty($arrUser) || $userinfo['level'] <= $arrUser['user_level']) {
			json_code(-2, array(), '�û������ڻ�û�в���Ȩ��');
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
			json_code(-1 , array() , '�޸�ʧ��');
		}
		json_code(1 , array() , 'success');
	}
}