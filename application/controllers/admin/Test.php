<?php
class Test extends CI_Controller {

	public function view($page = 'home')
	{
		echo 'admin';die;
		//http://www.test.com/CI/CodeIgniter-3.1.5/index.php/admin/test/view/
		//http://www.test.com/CI/CodeIgniter-3.1.5/index.php/pages/view
		if ( ! file_exists(APPPATH.'views/pages/'.$page.'.php'))
		{
			// Whoops, we don't have a page for that!
			show_404();
		}

		$data['title'] = ucfirst($page); // Capitalize the first letter

		$this->load->view('templates/header', $data);
		$this->load->view('pages/'.$page, $data);
		$this->load->view('templates/footer', $data);
	}
}