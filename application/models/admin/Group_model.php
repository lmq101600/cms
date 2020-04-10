<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/4/10
 * Time: 11:26
 */

class Group_model extends CI_Model
{
	private $_db = '';
	private $user_group = 'user_group';
	private $user_group_right = 'user_group_right';
	private $plat_menu = 'plat_menu';
	
	
	public function __construct()
	{
		parent::__construct();
		if (empty($this->_db)) {
			$this->_db = $this->load->database('default', true);
		}
	}
	public function getGroup($data=null){
		$query = $this->_db->get_where($this->user_group,$data);
		return  $query->result_array();
	}
	
	public function addGroup($data){
		return $this->_db->insert($this->user_group,$data);
	}
	public function deleteGroup($id){
		$this->_db->delete($this->user_group_right, array('ugid'=>$id));
		return $this->_db->delete($this->user_group, array('id'=>$id));
	}

	function getGroupRight($id)
	{
		$sql = "select p.action,u.pmid from {$this->user_group_right} u left join {$this->plat_menu} as p on u.pmid = p.id where u.ugid = ? ";
		
		return $this->_db->query($sql , array($id))->result_array();
	}

}