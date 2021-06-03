<?php
class ControllerExtensionModuleDropImageSainent extends Controller {
	private $error; 
    
    public function index() {
        
        $this->load->language('extension/module/dropimage_sainent');
		
		$this->document->setTitle($this->language->get('text_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_dropimage_sainent', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
        
        $data['breadcrumbs'] = array();
        
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_title'),
			'href' => $this->url->link('extension/module/dropimage_sainent', 'user_token=' . $this->session->data['user_token'], true)
		);
		
		$data['heading_title'] = $this->language->get('text_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['entry_status'] = $this->language->get('entry_status');
		
		$data['action'] = $this->url->link('extension/module/dropimage_sainent', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_dropimage_sainent_default_path'])) {
			$data['module_dropimage_sainent_default_path'] = $this->request->post['module_dropimage_sainent_default_path'];
		} else {
			$data['module_dropimage_sainent_default_path'] = $this->config->get('module_dropimage_sainent_default_path');
		}
		
		if (isset($this->request->post['module_dropimage_sainent_status'])) {
			$data['module_dropimage_sainent_status'] = $this->request->post['module_dropimage_sainent_status'];
		} else {
			$data['module_dropimage_sainent_status'] = $this->config->get('module_dropimage_sainent_status');
		}
		
		$data['user_token'] = $this->session->data['user_token'];
		
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/dropimage_sainent', $data));
    }
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/dropimage_sainent')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
    
    public function install() {
        $this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/dropimage_sainent');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/dropimage_sainent');
    }

    public function uninstall() {
        $this->load->model('user/user_group');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/module/dropimage_sainent');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/module/dropimage_sainent');
    }
	
	public function filemanager() {
		if (isset($this->request->get['drop_target'])) {
			$data['drop_target'] = urlencode($this->request->get['drop_target']);
		} else {
			$data['drop_target'] = '';
		}
		
		$this->load->language('extension/module/dropimage_sainent');

		// Find which protocol to use to pass the full image link back
		if ($this->request->server['HTTPS']) {
			$server = HTTPS_CATALOG;
		} else {
			$server = HTTP_CATALOG;
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = rtrim(str_replace(array('*', '/', '\\'), '', $this->request->get['filter_name']), '/');
		} else {
			$filter_name = '';
		}

		// Make sure we have the correct directory
		if (isset($this->request->get['directory'])) {
			$directory = rtrim(DIR_IMAGE . '/' . str_replace('*', '', $this->request->get['directory']), '/');
		} else {
			$directory = DIR_IMAGE . '';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$directories = array();
		$files = array();

		$data['images'] = array();

		$this->load->model('tool/image');
		
		if (substr(str_replace('\\', '/', realpath($directory) . '/' . $filter_name), 0, strlen(DIR_IMAGE . '')) == str_replace('\\', '/', DIR_IMAGE . '')) {
			// Get directories
			$directories = glob($directory . '/' . $filter_name . '*', GLOB_ONLYDIR);

			if (!$directories) {
				$directories = array();
			}

			// Get files
			$files = glob($directory . '/' . $filter_name . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);

			if (!$files) {
				$files = array();
			}
		}

		// Merge directories and files
		$images = array_merge($directories, $files);

		// Get total number of files and directories
		$image_total = count($images);

		// Split the array based on current page number and max number of items per page of 10
		$images = array_splice($images, ($page - 1) * 16, 16);
		
		foreach ($images as $image) {
			
			// Ignore cache, flags and templates directories
			if (strpos($image, DIR_IMAGE . '/cache') === 0 || strpos($image, DIR_IMAGE . '/flags') === 0 || strpos($image, DIR_IMAGE . '/templates') === 0 || strpos($image, DIR_IMAGE . '/payment') === 0) {
				continue;
			}
			
			$name = str_split(basename($image), 14);

			if (is_dir($image)) {
				$url = '';

				if (isset($this->request->get['target'])) {
					$url .= '&target=' . $this->request->get['target'];
				}
				
				if (isset($this->request->get['drop_target'])) {
					$url .= '&drop_target=' . $this->request->get['drop_target'];
				}
				
				if (isset($this->request->get['thumb'])) {
					$url .= '&thumb=' . $this->request->get['thumb'];
				}

				$data['images'][] = array(
					'thumb' => '',
					'name'  => implode(' ', $name),
					'type'  => 'directory',
					'path'  => utf8_substr($image, utf8_strlen(DIR_IMAGE . '/')),
					'href'  => $this->url->link('extension/module/dropimage_sainent/filemanager', 'user_token=' . $this->session->data['user_token'] . '&directory=' . urlencode(utf8_substr($image, utf8_strlen(DIR_IMAGE . '/'))) . $url, true)
				);
			} elseif (is_file($image)) {
				$data['images'][] = array(
					'thumb' => $this->model_tool_image->resize(utf8_substr($image, utf8_strlen(DIR_IMAGE)), 100, 100),
					'name'  => implode(' ', $name),
					'type'  => 'image',
					'path'  => utf8_substr($image, utf8_strlen(DIR_IMAGE)),
					'href'  => $server . 'image/' . utf8_substr($image, utf8_strlen(DIR_IMAGE))
				);
			}
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['directory'])) {
			$data['directory'] = urlencode($this->request->get['directory']);
		} else {
			$data['directory'] = '';
		}

		if (isset($this->request->get['filter_name'])) {
			$data['filter_name'] = $this->request->get['filter_name'];
		} else {
			$data['filter_name'] = '';
		}

		// Return the target ID for the file manager to set the value
		if (isset($this->request->get['target'])) {
			$data['target'] = $this->request->get['target'];
		} else {
			$data['target'] = '';
		}

		// Return the thumbnail for the file manager to show a thumbnail
		if (isset($this->request->get['thumb'])) {
			$data['thumb'] = $this->request->get['thumb'];
		} else {
			$data['thumb'] = '';
		}

		// Parent
		$url = '';

		if (isset($this->request->get['directory'])) {
			$pos = strrpos($this->request->get['directory'], '/');

			if ($pos) {
				$url .= '&directory=' . urlencode(substr($this->request->get['directory'], 0, $pos));
			}
		}

		if (isset($this->request->get['target'])) {
			$url .= '&target=' . $this->request->get['target'];
		}
		
		if (isset($this->request->get['drop_target'])) {
			$url .= '&drop_target=' . $this->request->get['drop_target'];
		}

		if (isset($this->request->get['thumb'])) {
			$url .= '&thumb=' . $this->request->get['thumb'];
		}

		$data['parent'] = $this->url->link('extension/module/dropimage_sainent/filemanager', 'user_token=' . $this->session->data['user_token'] . $url, true);

		// Refresh
		$url = '';

		if (isset($this->request->get['directory'])) {
			$url .= '&directory=' . urlencode($this->request->get['directory']);
		}

		if (isset($this->request->get['target'])) {
			$url .= '&target=' . $this->request->get['target'];
		}
		
		if (isset($this->request->get['drop_target'])) {
			$url .= '&drop_target=' . $this->request->get['drop_target'];
		}

		if (isset($this->request->get['thumb'])) {
			$url .= '&thumb=' . $this->request->get['thumb'];
		}

		$data['refresh'] = $this->url->link('extension/module/dropimage_sainent/filemanager', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$url = '';

		if (isset($this->request->get['directory'])) {
			$url .= '&directory=' . urlencode(html_entity_decode($this->request->get['directory'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['target'])) {
			$url .= '&target=' . $this->request->get['target'];
		}
		
		if (isset($this->request->get['drop_target'])) {
			$url .= '&drop_target=' . $this->request->get['drop_target'];
		}

		if (isset($this->request->get['thumb'])) {
			$url .= '&thumb=' . $this->request->get['thumb'];
		}

		$pagination = new Pagination();
		$pagination->total = $image_total;
		$pagination->page = $page;
		$pagination->limit = 16;
		$pagination->url = $this->url->link('extension/module/dropimage_sainent/filemanager', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$this->response->setOutput($this->load->view('extension/module/dropfilemanager_sainent', $data));
	}

	public function upload() {
		$this->load->model('tool/image');
		
		$dropfile = array();
		
		$this->load->language('extension/module/dropimage_sainent');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'extension/module/dropimage_sainent')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Make sure we have the correct directory
		if (isset($this->request->get['directory'])) {
			$directory = rtrim(DIR_IMAGE . '/' . $this->request->get['directory'], '/');
		} else {
			$directory = DIR_IMAGE . '';
		}
		
		// Drag and Drop Make sure we have the correct directory
		if ($this->config->get('module_dropimage_sainent_status')) {
			if (isset($this->request->post['directory'])) {
				$directory = rtrim(DIR_IMAGE . '' . $this->request->post['directory'], '/');
			} else {
				$directory = DIR_IMAGE . '';
			}
		}

		// Check its a directory
		if (!is_dir($directory) || substr(str_replace('\\', '/', realpath($directory)), 0, strlen(DIR_IMAGE . '')) != str_replace('\\', '/', DIR_IMAGE . '')) {
			$json['error'] = $this->language->get('error_directory');
		}

		if (!$json) {
			// Check if multiple files are uploaded or just one
			$files = array();

			if (!empty($this->request->files['file']['name']) && is_array($this->request->files['file']['name'])) {
				foreach (array_keys($this->request->files['file']['name']) as $key) {
					$files[] = array(
						'name'     => $this->request->files['file']['name'][$key],
						'type'     => $this->request->files['file']['type'][$key],
						'tmp_name' => $this->request->files['file']['tmp_name'][$key],
						'error'    => $this->request->files['file']['error'][$key],
						'size'     => $this->request->files['file']['size'][$key]
					);
				}
			}

			foreach ($files as $file) {
				if (is_file($file['tmp_name'])) {
					// Sanitize the filename
					$filename = basename(html_entity_decode($file['name'], ENT_QUOTES, 'UTF-8'));

					// Validate the filename length
					if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
						$json['error'] = $this->language->get('error_filename');
					}

					// Allowed file extension types
					$allowed = array(
						'jpg',
						'jpeg',
						'gif',
						'png'
					);

					if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
						$json['error'] = $this->language->get('error_filetype');
					}

					// Allowed file mime types
					$allowed = array(
						'image/jpeg',
						'image/pjpeg',
						'image/png',
						'image/x-png',
						'image/gif'
					);

					if (!in_array($file['type'], $allowed)) {
						$json['error'] = $this->language->get('error_filetype');
					}

					// Return any upload error
					if ($file['error'] != UPLOAD_ERR_OK) {
						$json['error'] = $this->language->get('error_upload_' . $file['error']);
					}
				} else {
					$json['error'] = $this->language->get('error_upload');
				}

				if (!$json) {
					move_uploaded_file($file['tmp_name'], $directory . '/' . $filename);
					
					if (isset($this->request->get['drop_target'])) {
						$upload_image = str_replace(DIR_IMAGE, '', $directory . '/' . $filename); 
						if (substr($upload_image,0,1)=="/") {
							$upload_image = substr($upload_image,1);
						}	
						$dropfile[] = array(
							'thumb' => $this->model_tool_image->resize($upload_image, 100, 100),
							'path' => $upload_image,
						);
					}
				}
			}
		}
		
		if (count($dropfile) > 0) {
			$json['dropfile'] = $dropfile;
		}

		if (!$json) {
			$json['success'] = $this->language->get('text_uploaded');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function folder() {
		$this->load->language('extension/module/dropimage_sainent');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'extension/module/dropimage_sainent')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Make sure we have the correct directory
		if (isset($this->request->get['directory'])) {
			$directory = rtrim(DIR_IMAGE . '/' . $this->request->get['directory'], '/');
		} else {
			$directory = DIR_IMAGE . '';
		}

		// Check its a directory
		if (!is_dir($directory) || substr(str_replace('\\', '/', realpath($directory)), 0, strlen(DIR_IMAGE . '')) != str_replace('\\', '/', DIR_IMAGE . '')) {
			$json['error'] = $this->language->get('error_directory');
		}

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			// Sanitize the folder name
			$folder = basename(html_entity_decode($this->request->post['folder'], ENT_QUOTES, 'UTF-8'));

			// Validate the filename length
			if ((utf8_strlen($folder) < 3) || (utf8_strlen($folder) > 128)) {
				$json['error'] = $this->language->get('error_folder');
			}

			// Check if directory already exists or not
			if (is_dir($directory . '/' . $folder)) {
				$json['error'] = $this->language->get('error_exists');
			}
		}

		if (!isset($json['error'])) {
			mkdir($directory . '/' . $folder, 0777);
			chmod($directory . '/' . $folder, 0777);

			@touch($directory . '/' . $folder . '/' . 'index.html');

			$json['success'] = $this->language->get('text_directory');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete() {
		$this->load->language('extension/module/dropimage_sainent');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'extension/module/dropimage_sainent')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['path'])) {
			$paths = $this->request->post['path'];
		} else {
			$paths = array();
		}

		// Loop through each path to run validations
		foreach ($paths as $path) {
			// Check path exsists
			if ($path == DIR_IMAGE . 'catalog' || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $path)), 0, strlen(DIR_IMAGE . 'catalog')) != str_replace('\\', '/', DIR_IMAGE . 'catalog')) {
				$json['error'] = $this->language->get('error_delete');

				break;
			}
		}

		if (!$json) {
			// Loop through each path
			foreach ($paths as $path) {
				$path = rtrim(DIR_IMAGE . $path, '/');

				// If path is just a file delete it
				if (is_file($path)) {
					unlink($path);

				// If path is a directory beging deleting each file and sub folder
				} elseif (is_dir($path)) {
					$files = array();

					// Make path into an array
					$path = array($path);

					// While the path array is still populated keep looping through
					while (count($path) != 0) {
						$next = array_shift($path);

						foreach (glob($next) as $file) {
							// If directory add to path array
							if (is_dir($file)) {
								$path[] = $file . '/*';
							}

							// Add the file to the files to be deleted array
							$files[] = $file;
						}
					}

					// Reverse sort the file array
					rsort($files);

					foreach ($files as $file) {
						// If file just delete
						if (is_file($file)) {
							unlink($file);

						// If directory use the remove directory function
						} elseif (is_dir($file)) {
							rmdir($file);
						}
					}
				}
			}

			$json['success'] = $this->language->get('text_delete');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}