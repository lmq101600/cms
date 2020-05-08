<?php
class News extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('news_model');
		$this->load->helper('url_helper');
		
	}

	public function index()
	{
		$data['news'] = $this->news_model->get_news();
		$data['title'] = 'News archive';

		$this->load->view('templates/header', $data);
		$this->load->view('news/index', $data);
		$this->load->view('templates/footer');
	}

	public function view($slug = NULL)
	{
		$data['news_item'] = $this->news_model->get_news($slug);
		if (empty($data['news_item']))
		{
			show_404();
		}
		
		$data['title'] = $data['news_item']['title'];

		$this->load->view('templates/header', $data);
		$this->load->view('news/view', $data);
		$this->load->view('templates/footer');
	}
	public function test()
	{
//		$news = $this->news_model->get_news2();
//		$this->load->library('pagination');
//		$config['base_url'] = 'http://www.testci.com/index.php/news/test';
//		$config['total_rows'] = 20;
//		$config['per_page'] = 1;
//
//		$this->pagination->initialize($config);
//
//		echo $this->pagination->create_links();
		$page_num = '1';//每页的数据
		$this->load->model('news_model');
		$data= $this->news_model->page('news',$page_num,$this->uri->segment(3));

		//当加载model想上传多个数据的时候，这个时候：$data= $this->Page_model->page('ci_admin',$page_num,$this->uri->segment(4),$id);
		$total_nums=$data['total_nums']; //这里得到从数据库中的总页数
		$data['query']=$data[0]; //把查询结果放到$data['query']中
		$this->load->library('pagination');
		$config['base_url'] = $this->config->base_url('index.php/news/test');

		//路径变为：$config['base_url'] = $this->config->base_url("admin.php/Admin/index/{$id}”);
		//这也是做分类分页需要加的，$id是为获取的分类id;

		$config['total_rows'] = $total_nums;//总共多少条数据
		$config['per_page'] = $page_num;//每页显示几条数据
		$config['full_tag_open'] = '<p>';
		$config['full_tag_close'] = '</p>';
		$config['first_link'] = '首页';
		$config['first_tag_open'] = '<li>';//“第一页”链接的打开标签。
		$config['first_tag_close'] = '</li>';//“第一页”链接的关闭标签。
		$config['last_link'] = '末页';//你希望在分页的右边显示“最后一页”链接的名字。
		$config['last_tag_open'] = '<li>';//“最后一页”链接的打开标签。
		$config['last_tag_close'] = '</li>';//“最后一页”链接的关闭标签。
		$config['next_link'] = '下一页';//你希望在分页中显示“下一页”链接的名字。
		$config['next_tag_open'] = '<li>';//“下一页”链接的打开标签。
		$config['next_tag_close'] = '</li>';//“下一页”链接的关闭标签。
		$config['prev_link'] = '上一页';//你希望在分页中显示“上一页”链接的名字。
		$config['prev_tag_open'] = '<li>';//“上一页”链接的打开标签。
		$config['prev_tag_close'] = '</li>';//“上一页”链接的关闭标签。
		$config['cur_tag_open'] = '<li class="current">';//“当前页”链接的打开标签。
		$config['cur_tag_close'] = '</li>';//“当前页”链接的关闭标签。
		$config['num_tag_open'] = '<li>';//“数字”链接的打开标签。
		$config['num_tag_close'] = '</li>';

		//需要加$config['uri_segment']=4;
		
		$this->pagination->initialize($config);
		$page = $this->pagination->create_links();
//		var_dump($data['query']);
		$this->load->view('news/view',array('data'=>$data['query'],'page'=>$page));
		
	}
	public function view2()
	{
		$data['news_item'] = $this->news_model->get_news();
		if (empty($data['news_item']))
		{
			show_404();
		}
		$data['title'] = $data['news_item']['title'];

		$this->load->view('templates/header', $data);
		$this->load->view('news/view', $data);
		$this->load->view('templates/footer');
	}
	public function create()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');

		$data['title'] = 'Create a news item';

		$this->form_validation->set_rules('title', 'Title', 'required');
		$this->form_validation->set_rules('text', 'Text', 'required');

		if ($this->form_validation->run() === FALSE)
		{
			$this->load->view('templates/header', $data);
			$this->load->view('news/create');
			$this->load->view('templates/footer');

		}
		else
		{
			$this->news_model->set_news();
			$this->load->view('news/success');
		}
	}
}