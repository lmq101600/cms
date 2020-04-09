<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Upload extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}

	public function index()
	{
		$this->load->load_template('admin/upload_form', array('error' => ' ' ));
	}

	public function do_upload()
	{



		$this->load->library('upload');

		if ( ! $this->upload->do_upload('userfile'))
		{
			$error = array('error' => $this->upload->display_errors());

			$this->load->load_template('admin/upload_form', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());

			$this->load->view('upload_success', $data);
		}
	}
}