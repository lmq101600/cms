<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2020/2/19
 * Time: 12:26
 */
//文件上传配置
$config['upload_path']      = './uploads/';
$config['allowed_types']    = 'gif|jpg|png|zip|rar|7z';
$config['overwrite']    	= TRUE;
$config['max_size']     	= 102400;
$config['max_filename']     = 0;
$config['remove_spaces']    = TRUE;
$config['detect_mime']    	= TRUE;