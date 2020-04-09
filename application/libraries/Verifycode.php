<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Verifycode
{

	protected $CI;

	public function __construct()
	{
		$this->CI = &get_instance();
	}

	/**
	 * 生成验证码
	 *
	 * @return string
	 */
	public function generateCode()
	{
		$this->CI->load->helper('captcha');
		$this->CI->load->library('string');
		$code = $this->CI->string->buildRandomString();
		$this->CI->load->library('session');
		$this->CI->session->set_userdata('Verifycode', $code);
		$vals = [
			'word' => $code,
			'img_path' => 'captcha/',
			'img_url' => base_url() . 'captcha/',
			'font_path' => '',
			'img_width' => '150',
			'img_height' => 35,
			'expiration' => 600,
			'word_length' => 100,
			'font_size' => 200,
			'img_id' => 'Verifycode',
			'pool' => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',

			// White background and border, black text and red grid
			'colors' => [
				'background' => [255, 255, 255],
				'border' => [255, 255, 255],
				'text' => [0, 0, 0],
				'grid' => [100, 40, 40],
			],
		];
		$cap = create_captcha($vals);
		$cap['code'] = $code;
		return $cap;
	}

	public function checkVerifyCode($code)
	{
		$rs = ['success' => false, 'msg' => ''];
		if (strtolower($code) == strtolower($this->CI->session->Verifycode)) {
			$rs['success'] = true;
			// session 置空
			$this->CI->session->set_userdata('Verifycode', null);
		} else {
			$rs['msg'] = false;
		}
		return $rs;
	}
}
