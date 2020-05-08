<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/4/10
 * Time: 11:05
 */

defined('BASEPATH') or exit('No direct script access allowed');
class Group extends MY_Controller
{
	function __construct()
	{
		//todo 检查loginer
		
		
		parent::__construct();
		$this->load->model('admin/group_model','model');
	}
	//主页展示
	public function index(){
		$data['list'] = $this->model->getGroup();
		$this->load->load_template('admin/manage/group', $data);
	}
	//增加分组
	public function addGroup(){
		$gname = $this->input->post('gname');
		if(!$gname) {
			json_code('-1',null,self::param_error);
		}
		$data = $this->model->getGroup(array('gname'=>$gname));
		if($data) {
			json_code('-1',null,self::group_already_exist);
		}
		$data = array('gname'=>$gname,'ctime'=>date('Y-m-d H:i:s'),'mtime'=>date('Y-m-d H:i:s'));
		$this->model->addGroup($data);
		json_code(1 , null , self::add_success);
	}
	//删除分组
	public function deleteGroup(){
		$id = $this->input->post('id');
		if(!$id) {
			json_code('-1',null,self::param_error);
		}
		$this->model->deleteGroup($id);
		json_code(1 , null , self::delete_success);
	}
	//编辑
	public function editGroupRight(){
		$id = $this->input->get('id');
		
		$this->load->model('admin/Manage_model');
		
		$arrAllMenuList = $this->Manage_model->getMenu(array('system'=>2));


//		//所有目录
//		foreach ($arrAllMenuList as $arrMenu) {
//
//			!isset($arrAllMenulink[$arrMenu['id']]) && $arrAllMenulink[$arrMenu['id']] = array();
//			if (empty($arrMenu['parent'])) {
//				$arrMenu['_list'] = &$arrAllMenulink[$arrMenu['id']];
//				$arrAllMenuKv[] = $arrMenu;
//			} else {
//				$arrAllMenulink[$arrMenu['parent']][] = $arrMenu;
//			}
//		}
	
		//所有目录
		$arrAllMenuKv = $this->list_to_tree($arrAllMenuList);
//		var_dump($arrAllMenuKv);die;
		//所有权限
		$arrAction = $this->Manage_model->getAction();
		$actionAllList = array();
		if (!empty($arrAction)) {
			foreach ($arrAction as $value) {
				$actionAllList[$value['parent']][] = $value;
			}
		}
		
		//此分组现在所有的权限
		$arrRight = $this->model->getGroupRight($id);	
		
		$arrCurentRight = array();
		foreach ($arrRight as $key => $value) {
			$arrCurentRight[] = $value['pmid'];
		}
		
		
		$arrCurent = array('mname' => "权限编辑", 'url' => '/admin/group/index', 'parent' => 1,);
		$data['_current'] = $arrCurent;
		
		//该分组现在所有的权限
		$data['arrCurentRight'] = $arrCurentRight;
		//所有目录和权限
		$data['actionAllList'] = $actionAllList;
		$data['arrAllMenuKv'] = $arrAllMenuKv;
		
		
		
		$this->load->load_template('admin/manage/editgroupright',$data);
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

	function saveUserGroupRight() {

		$ugid = $this->input->post('ugid', true);
		$postRight = $this->input->post('right', true);

		$arrWhere = array(
			'id' => $ugid,
		);
		$arrUser = $this->model->getUserGroup($arrWhere);
		if (empty($arrUser)) {
			json_code(-1, array(), "权限组不存在");
		}
		$insert = array();

		if (!empty($postRight)) {
			foreach ($postRight as $key => $pmid) {
				if(intval( $pmid) <= 0)
				{
					continue;
				}
				$insert[] = array(
					'ugid'=>$ugid,
					'pmid'=>$pmid
				);
			}
		}

		$bool = $this->model->updateGroupRight($insert, $ugid);
		if (!$bool) {
			json_code(-5, array(), '更新失败');
		}
		json_code(1, array(), 'success');
	}
	
}