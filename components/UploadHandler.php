<?php

 namespace app\components;
/*
 * jQuery File Upload Plugin PHP Class
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

class UploadHandler
{
	public $complete = false;
	protected $options;

	// PHP File Upload error message codes:
	// http://php.net/manual/en/features.file-upload.errors.php
	protected $error_messages = array(
		1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
		2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
		3 => 'The uploaded file was only partially uploaded',
		4 => 'No file was uploaded',
		6 => 'Missing a temporary folder',
		7 => 'Failed to write file to disk',
		8 => 'A PHP extension stopped the file upload',
		'post_max_size' => 'The uploaded file exceeds the post_max_size directive in php.ini',
		'max_file_size' => 'File is too big',
		'min_file_size' => 'File is too small',
		'accept_file_types' => 'Filetype not allowed',
		'max_number_of_files' => 'Maximum number of files exceeded',
		'max_width' => 'Image exceeds maximum width',
		'min_width' => 'Image requires a minimum width',
		'max_height' => 'Image exceeds maximum height',
		'min_height' => 'Image requires a minimum height',
		'abort' => 'File upload aborted',
		'image_resize' => 'Failed to resize image'
	);

	protected $image_objects = array();

	public function __construct($options = null) {
		$this->response = array();
		$this->options = array(
			'upload_dir' => sys_get_temp_dir().DIRECTORY_SEPARATOR,
			'upload_url' => false,
			'param_name' => 'FsFile',
			'script_url' => '',
			'input_stream' => 'php://input',
			'user_dirs' => false,
			'mkdir_mode' => 0775,
			'access_control_allow_origin' => '*',
			'access_control_allow_credentials' => false,
			'access_control_allow_methods' => ['POST'],
			'access_control_allow_headers' => array('Content-Type','Content-Range','Content-Disposition'),
		);
		if ($options) {
			$this->options = $options + $this->options;
		}
        $this->initialize();
	}

	protected function initialize() {
		switch ($this->get_server_var('REQUEST_METHOD')) {
			case 'POST':
				$this->post();
				break;
			default:
				$this->header('HTTP/1.1 405 Method Not Allowed');
		}
	}
	protected function fix_integer_overflow($size) {
		if ($size < 0) {
			$size += 2.0 * (PHP_INT_MAX + 1);
		}
		return $size;
	}
	public function get_file_size($file_path, $clear_stat_cache = false) {
		if ($clear_stat_cache) {
			if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
				clearstatcache(true, $file_path);
			} else {
				clearstatcache();
			}
		}
		return $this->fix_integer_overflow(filesize($file_path));
	}
	protected function get_error_message($error) {
		return isset($this->error_messages[$error]) ?
			$this->error_messages[$error] : $error;
	}
	protected function trim_file_name($name) {
		// Remove path information and dots around the filename, to prevent uploading
		// into different directories or replacing hidden system files.
		// Also remove control characters and spaces (\x00..\x20) around the filename:
		$name = trim($this->basename(stripslashes($name)), ".\x00..\x20");
		// Use a timestamp for empty filenames:
		if (!$name) {
			$name = str_replace('.', '-', microtime(true));
		}
		return $name;
	}
	protected function get_file_name($name) {
		return $this->trim_file_name($name);
	}
	protected function get_upload_path($file_name = '') {
		return $this->options['upload_dir'].$file_name;
	}
	protected function handle_file_upload($uploaded_file, $name, $size, $type, $content_range = null) {
		$file = new \stdClass();
		$file->name = $this->get_file_name($name);
		$file->size = $this->fix_integer_overflow((int)$size);
		$file->type = $type;

		$upload_dir = $this->get_upload_path();
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir, $this->options['mkdir_mode'], true);
		}
		$file_path = $this->get_upload_path($file->name);

		if (!file_exists($file_path)){
			touch($file_path);
		}

		$append_file = $content_range && is_file($file_path) &&
		               $file->size > $this->get_file_size($file_path);

		if ($uploaded_file && is_uploaded_file($uploaded_file)) {
			// multipart/formdata uploads (POST method uploads)
			if ($append_file) {
				file_put_contents(
					$file_path,
					fopen($uploaded_file, 'r'),
					FILE_APPEND
				);
			} else {
				move_uploaded_file($uploaded_file, $file_path);
			}
		} else {
			// Non-multipart uploads (PUT method support)
			file_put_contents(
				$file_path,
				fopen($this->options['input_stream'], 'r'),
				$append_file ? FILE_APPEND : 0
			);
		}
		$file_size = $this->get_file_size($file_path, $append_file);
		if ($file_size === $file->size) {
			// complete
			$this->complete = $file_path;
		} else {
			$file->size = $file_size;
			if (!$content_range) {
				unlink($file_path);
				$file->error = $this->get_error_message('abort');
			}
		}
		return $file;
	}

	protected function body($str) {
		echo $str;
	}

	protected function header($str) {
		header($str);
	}

	protected function get_upload_data($id) {
		return isset($_FILES[$id])?$_FILES[$id]:null;
	}

	protected function get_server_var($id) {
		return isset($_SERVER[$id])?$_SERVER[$id]:null;
	}

	protected function send_content_type_header() {
		$this->header('Vary: Accept');
		if (strpos($this->get_server_var('HTTP_ACCEPT'), 'application/json') !== false) {
			$this->header('Content-type: application/json');
		} else {
			$this->header('Content-type: text/plain');
		}
	}

	protected function send_access_control_headers() {
		$this->header('Access-Control-Allow-Origin: '.$this->options['access_control_allow_origin']);
		$this->header('Access-Control-Allow-Credentials: '
		              .($this->options['access_control_allow_credentials'] ? 'true' : 'false'));
		$this->header('Access-Control-Allow-Methods: '
		              .implode(', ', $this->options['access_control_allow_methods']));
		$this->header('Access-Control-Allow-Headers: '
		              .implode(', ', $this->options['access_control_allow_headers']));
	}

	public function generate_response($content) {
		$this->response = $content;
		$json = json_encode($content);
		$this->head();
		if ($this->get_server_var('HTTP_CONTENT_RANGE')) {
			$files = isset($content[$this->options['param_name']]) ?
				$content[$this->options['param_name']] : null;
			if ($files && is_array($files) && is_object($files[0]) && $files[0]->size) {
				$this->header('Range: 0-'.(
						$this->fix_integer_overflow((int)$files[0]->size) - 1
					));
			}
		}
		// $this->body($json);
		return $content;
	}

	public function head() {
		$this->header('Pragma: no-cache');
		$this->header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->header('Content-Disposition: inline; filename="files.json"');
		// Prevent Internet Explorer from MIME-sniffing the content-type:
		$this->header('X-Content-Type-Options: nosniff');
		if ($this->options['access_control_allow_origin']) {
			$this->send_access_control_headers();
		}
		$this->send_content_type_header();
	}

	public function post() {
		$upload = $this->get_upload_data($this->options['param_name']);
		// Parse the Content-Disposition header, if available:
		$content_disposition_header = $this->get_server_var('HTTP_CONTENT_DISPOSITION');
		$file_name = $content_disposition_header ?
			rawurldecode(preg_replace(
				'/(^[^"]+")|("$)/',
				'',
				$content_disposition_header
			)) : null;
		// Parse the Content-Range header, which has the following form:
		// Content-Range: bytes 0-524287/2000000
		$content_range_header = $this->get_server_var('HTTP_CONTENT_RANGE');
		$content_range = $content_range_header ?
			preg_split('/[^0-9]+/', $content_range_header) : null;
		$size =  $content_range ? $content_range[3] : null;

		$files = array();

		if ($upload) {
			if (is_array($upload['tmp_name'])) {
				// param_name is an array identifier like "files[]",
				// $upload is a multi-dimensional array:
				foreach ($upload['tmp_name'] as $index => $value) {
					$files[] = $this->handle_file_upload(
						$upload['tmp_name'][$index],
						$file_name ? $file_name : $upload['name'][$index],
						$size ? $size : $upload['size'][$index],
						$upload['type'][$index],
						$content_range
					);
				}
			} else {
				// param_name is a single object identifier like "file",
				// $upload is a one-dimensional array:
				$files[] = $this->handle_file_upload(
					isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
					$file_name ? $file_name : (isset($upload['name']) ?
						$upload['name'] : null),
					$size ? $size : (isset($upload['size']) ?
						$upload['size'] : $this->get_server_var('CONTENT_LENGTH')),
					isset($upload['type']) ?
						$upload['type'] : $this->get_server_var('CONTENT_TYPE'),
					$content_range
				);
			}
		} else {
			// No uploads
			$this->complete = false;
		}
		$response = array($this->options['param_name'] => $files);
		return $this->generate_response($response);
	}

	protected function basename($filepath, $suffix = null) {
		$splited = preg_split('/\//', rtrim ($filepath, '/ '));
		return substr(basename('X'.$splited[count($splited)-1], $suffix), 1);
	}
}
