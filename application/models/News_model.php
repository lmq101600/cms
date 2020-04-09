<?php
class News_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}
	public function get_news($slug = FALSE)
	{
		if ($slug === FALSE)
		{
			$query = $this->db->get('news');
			return $query->result_array();
		}
		$query = $this->db->get_where('news', array('slug' => $slug));
		return $query->row_array();
	}
	public function set_news()
	{
		$this->load->helper('url');

		$slug = url_title($this->input->post('title'), 'dash', TRUE);

		$data = array(
			'title' => $this->input->post('title'),
			'slug' => $slug,
			'text' => $this->input->post('text')
		);

		return $this->db->insert('news', $data);
	}
	public function page($tablename,$per_nums,$start_position)
	{	
		$this->db->order_by('id','asc');
		$this->db->limit($per_nums,$start_position);
		$query=$this->db->get($tablename);
		$data=$query->result();
		$data2['total_nums']=$this->db->count_all($tablename);
		$data2[]=$data; //这里大家可能看的优点不明白，可以分别将$data和$data2打印出来看看是什么结果。
		return $data2;
	}


}