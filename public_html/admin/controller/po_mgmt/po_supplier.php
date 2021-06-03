<?php
class ControllerPOMgmtPoSupplier extends Controller {
	private $error = array();

	public function __construct($registory) {
		parent::__construct($registory);
		if (!$this->config->get('module_oc_pom_status')) {
			$this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
		}
	}

	public function index() {
    $this->load->language('po_mgmt/po_supplier');

		$this->document->setTitle($this->language->get('heading_title_list'));

		$this->load->model('po_mgmt/po_supplier');

		$this->getList();
	}

	public function add() {
		$this->load->language('po_mgmt/po_supplier');

		$this->document->setTitle($this->language->get('heading_title_add'));

		$this->load->model('po_mgmt/po_supplier');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_po_mgmt_po_supplier->addSupplier($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success_add');

			$url = '';

			if (isset($this->request->get['filter_company_name'])) {
				$url .= '&filter_company_name=' . urlencode(html_entity_decode($this->request->get['filter_company_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_address'])) {
				$url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_owner_name'])) {
				$url .= '&filter_owner_name=' . $this->request->get['filter_owner_name'];
			}

			if (isset($this->request->get['filter_postcode'])) {
				$url .= '&filter_postcode=' . $this->request->get['filter_postcode'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('po_mgmt/po_supplier');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('po_mgmt/po_supplier');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_po_mgmt_po_supplier->editSupplier($this->request->get['supplier_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success_edit');

			$url = '';

			if (isset($this->request->get['filter_company_name'])) {
				$url .= '&filter_company_name=' . urlencode(html_entity_decode($this->request->get['filter_company_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_address'])) {
				$url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_owner_name'])) {
				$url .= '&filter_owner_name=' . $this->request->get['filter_owner_name'];
			}

			if (isset($this->request->get['filter_postcode'])) {
				$url .= '&filter_postcode=' . $this->request->get['filter_postcode'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('po_mgmt/po_supplier');

		$this->document->setTitle($this->language->get('heading_title_edit'));

		$this->load->model('po_mgmt/po_supplier');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $supplier_id) {
				$this->model_po_mgmt_po_supplier->deleteSupplier($supplier_id);
			}

			$this->session->data['success'] = $this->language->get('text_success_delete');

			$url = '';

			if (isset($this->request->get['filter_company_name'])) {
				$url .= '&filter_company_name=' . urlencode(html_entity_decode($this->request->get['filter_company_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_address'])) {
				$url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_owner_name'])) {
				$url .= '&filter_owner_name=' . $this->request->get['filter_owner_name'];
			}

			if (isset($this->request->get['filter_postcode'])) {
				$url .= '&filter_postcode=' . $this->request->get['filter_postcode'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
    $data = array();
		$data = array_merge($data, $this->load->language('po_mgmt/po_supplier'));

		if (isset($this->request->get['filter_company_name'])) {
			$filter_company_name = $this->request->get['filter_company_name'];
		} else {
			$filter_company_name = null;
		}

		if (isset($this->request->get['filter_address'])) {
			$filter_address = $this->request->get['filter_address'];
		} else {
			$filter_address = null;
		}

		if (isset($this->request->get['filter_owner_name'])) {
			$filter_owner_name = $this->request->get['filter_owner_name'];
		} else {
			$filter_owner_name = null;
		}

		if (isset($this->request->get['filter_postcode'])) {
			$filter_postcode = $this->request->get['filter_postcode'];
		} else {
			$filter_postcode = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 's.owner_name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_company_name'])) {
			$url .= '&filter_company_name=' . urlencode(html_entity_decode($this->request->get['filter_company_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_address'])) {
			$url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_owner_name'])) {
			$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_postcode'])) {
			$url .= '&filter_postcode=' . $this->request->get['filter_postcode'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

    if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_list'),
			'href' => $this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('po_mgmt/po_supplier/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('po_mgmt/po_supplier/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['suppliers'] = array();

		$filter_data = array(
			'filter_company_name'	     => $filter_company_name,
			'filter_address'	         => $filter_address,
			'filter_owner_name'	       => $filter_owner_name,
			'filter_postcode'          => $filter_postcode,
			'filter_status'            => $filter_status,
			'filter_email'             => $filter_email,
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

		$supplier_total = $this->model_po_mgmt_po_supplier->getTotalSuppliers($filter_data);

		$results = $this->model_po_mgmt_po_supplier->getSuppliers($filter_data);

    if(!empty($results))
  		foreach ($results as $result) {
  			$data['suppliers'][] = array(
  				'supplier_id'   => $result['supplier_id'],
  				'company_name'  => $result['company_name'],
          'owner_name'    => $result['owner_name'],
  				'email'         => $result['email'],
  				'address'       => $result['address'],
  				'postcode'      => $result['postcode'],
  				'status'        => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
  				'edit'          => $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&supplier_id=' . $result['supplier_id'] . $url, true)
  			);
  		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['clear'] = $this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

    if (isset($this->request->get['filter_company_name'])) {
			$url .= '&filter_company_name=' . urlencode(html_entity_decode($this->request->get['filter_company_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_address'])) {
			$url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_owner_name'])) {
			$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_postcode'])) {
			$url .= '&filter_postcode=' . $this->request->get['filter_postcode'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

    if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_company_name'] = $this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'] . '&sort=sd.company_name' . $url, true);
		$data['sort_address'] = $this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'] . '&sort=s.address' . $url, true);
		$data['sort_owner_name'] = $this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'] . '&sort=s.owner_name' . $url, true);
		$data['sort_postcode'] = $this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'] . '&sort=s.postcode' . $url, true);
		$data['sort_status'] = $this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'] . '&sort=s.status' . $url, true);
		$data['sort_email'] = $this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'] . '&sort=s.email' . $url, true);

		$url = '';

    if (isset($this->request->get['filter_company_name'])) {
			$url .= '&filter_company_name=' . urlencode(html_entity_decode($this->request->get['filter_company_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_address'])) {
			$url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_owner_name'])) {
			$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_postcode'])) {
			$url .= '&filter_postcode=' . $this->request->get['filter_postcode'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

    if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $supplier_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($supplier_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($supplier_total - $this->config->get('config_limit_admin'))) ? $supplier_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $supplier_total, ceil($supplier_total / $this->config->get('config_limit_admin')));

		$data['filter_company_name'] = $filter_company_name;
		$data['filter_address'] = $filter_address;
		$data['filter_owner_name'] = $filter_owner_name;
		$data['filter_postcode'] = $filter_postcode;
		$data['filter_status'] = $filter_status;
		$data['filter_email'] = $filter_email;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('po_mgmt/po_supplier_list', $data));
	}

	protected function getForm() {
		$data = array();
		$data = array_merge($data, $this->load->language('po_mgmt/po_supplier'));

		$data['heading_title'] = !isset($this->request->get['supplier_id']) ? $this->language->get('heading_title_add') : $this->language->get('heading_title_edit');

		$data['text_form'] = !isset($this->request->get['supplier_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$error_array = array(
			'company_name',
			'owner_name',
			'email',
			'telephone',
			'street',
			'city',
			'postcode',
			'country',
			'zone',
		);
		foreach ($error_array as $key => $errors) {
				if (isset($this->error[$errors])) {
					$data['error_'.$errors] = $this->error[$errors];
				} else {
					$data['error_'.$errors] = '';
				}
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_company_name'])) {
			$url .= '&filter_company_name=' . urlencode(html_entity_decode($this->request->get['filter_company_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_address'])) {
			$url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_owner_name'])) {
			$url .= '&filter_owner_name=' . $this->request->get['filter_owner_name'];
		}

		if (isset($this->request->get['filter_postcode'])) {
			$url .= '&filter_postcode=' . $this->request->get['filter_postcode'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_list'),
			'href' => $this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => !isset($this->request->get['supplier_id']) ? $this->language->get('heading_title_add') : $this->language->get('heading_title_edit'),
			'href' => $this->url->link(isset($this->request->get['supplier_id']) ? 'po_mgmt/po_supplier/edit&supplier_id='.$this->request->get['supplier_id'] : 'po_mgmt/po_supplier/add', 'user_token=' . $this->session->data['user_token']. $url, true)
		);

		if (!isset($this->request->get['supplier_id'])) {
			$data['action'] = $this->url->link('po_mgmt/po_supplier/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
			$data['supplier_id'] = 0;
		} else {
			$data['action'] = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&supplier_id=' . $this->request->get['supplier_id'] . $url, true);
			$data['supplier_id'] = $this->request->get['supplier_id'];
		}

		$data['cancel'] = $this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('po_mgmt/po_supplier');
		if (isset($this->request->get['supplier_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$supplier_info = $this->model_po_mgmt_po_supplier->getSupplier($this->request->get['supplier_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$post_array = array(
			'supplier_email',
			'supplier_website',
			'supplier_tax_number',
			'supplier_gender',
			'supplier_telephone',
			'supplier_fax',
			'supplier_street',
			'supplier_city',
			'supplier_postcode',
			'supplier_country_id',
			'supplier_state_id',
			'supplier_status',
		);

		foreach ($post_array as $key => $post_index) {
				if (isset($this->request->post[$post_index])) {
					$data[$post_index] = $this->request->post[$post_index];
				} elseif (!empty($supplier_info)) {
					$data[$post_index] = $supplier_info[str_replace("supplier_","",$post_index)];
				} else {
					$data[$post_index] = '';
				}
		}
		if (isset($this->request->post['supplier_details'])) {
			$data['supplier_details'] = $this->request->post['supplier_details'];
		} elseif (isset($this->request->get['supplier_id'])) {
			$data['supplier_details'] = $this->model_po_mgmt_po_supplier->getSupplierDetail($this->request->get['supplier_id']);
		} else {
			$data['supplier_details'] = '';
		}

		$this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['supplier_product'] 	= $this->load->controller('po_mgmt/po_supplier_product');
		$data['supplier_quotation'] = $this->load->controller('po_mgmt/po_supplier_quotation');
		$data['supplier_po'] 	    = $this->load->controller('po_mgmt/po_supplier_po');
		$data['supplier_payment']   = $this->load->controller('po_mgmt/po_supplier_payment');;

		$data['header'] 			= $this->load->controller('common/header');
		$data['column_left'] 	= $this->load->controller('common/column_left');
		$data['footer'] 			= $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('po_mgmt/po_supplier_form', $data));
	}

	protected function validateForm() {
		$this->load->model('po_mgmt/po_supplier');

		if (!$this->user->hasPermission('modify', 'po_mgmt/po_supplier')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['supplier_details'] as $language_id => $value) {
			if ((utf8_strlen(trim($value['company_name'])) < 3) || (utf8_strlen(trim($value['company_name'])) > 150)) {
				$this->error['company_name'][$language_id] = $this->language->get('error_company_length');
			}

			if ((utf8_strlen(trim($value['owner_name'])) < 3) || (utf8_strlen(trim($value['owner_name'])) > 150)) {
				$this->error['owner_name'][$language_id] = $this->language->get('error_owner_length');
			}
		}

		if ((utf8_strlen($this->request->post['supplier_email']) > 150) || !filter_var($this->request->post['supplier_email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_supplier_email');
		}

		$supplier_info = $this->model_po_mgmt_po_supplier->getSupplierByEmail($this->request->post['supplier_email']);

		if (!isset($this->request->get['supplier_id'])) {
				if ($supplier_info) {
					$this->error['email'] = $this->language->get('error_supplier_exists');
				}
		} else {
				if ($supplier_info && ($this->request->get['supplier_id'] != $supplier_info['supplier_id'])) {
					$this->error['email'] = $this->language->get('error_supplier_exists');
				}
		}

		if ((utf8_strlen(trim($this->request->post['supplier_telephone'])) < 3) || (utf8_strlen(trim($this->request->post['supplier_telephone'])) > 32) || !is_numeric($this->request->post['supplier_telephone'])) {
			$this->error['telephone'] = $this->language->get('error_supplier_telephone');
		}

		if ((utf8_strlen(trim($this->request->post['supplier_street'])) < 3) || (utf8_strlen(trim($this->request->post['supplier_street'])) > 150)) {
			$this->error['street'] = $this->language->get('error_supplier_street');
		}

		if ((utf8_strlen(trim($this->request->post['supplier_city'])) < 2) || (utf8_strlen(trim($this->request->post['supplier_city'])) > 150)) {
			$this->error['city'] = $this->language->get('error_supplier_city');
		}

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->post['supplier_country_id']);

		if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['supplier_postcode'])) < 2 || utf8_strlen(trim($this->request->post['supplier_postcode'])) > 10)) {
			$this->error['postcode'] = $this->language->get('error_supplier_postcode');
		}

		if ($this->request->post['supplier_country_id'] == '') {
			$this->error['country'] = $this->language->get('error_supplier_country');
		}

		if (!isset($this->request->post['supplier_state_id']) || $this->request->post['supplier_state_id'] == '') {
			$this->error['zone'] = $this->language->get('error_supplier_zone');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_company_name']) || isset($this->request->get['filter_owner_name']) || isset($this->request->get['filter_email']) || isset($this->request->get['filter_address']) || isset($this->request->get['supplier_id'])) {
			$this->load->model('po_mgmt/po_supplier');
			$get_filter = '';
			if (isset($this->request->get['filter_company_name'])) {
				$filter_company_name = $this->request->get['filter_company_name'];
				$get_filter = 'company';
			} else {
				$filter_company_name = '';
			}

			if (isset($this->request->get['filter_owner_name'])) {
				$filter_owner_name = $this->request->get['filter_owner_name'];
				$get_filter = 'owner';
			} else {
				$filter_owner_name = '';
			}

			if (isset($this->request->get['supplier_id'])) {
				$filter_supplier_id = $this->request->get['supplier_id'];
				$get_filter = 'supplier_id';
			} else {
				$filter_supplier_id = '';
			}

			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
				$get_filter = 'email';
			} else {
				$filter_email = '';
			}

			if (isset($this->request->get['filter_address'])) {
				$filter_address = $this->request->get['filter_address'];
				$get_filter = 'address';
			} else {
				$filter_address = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_supplier_id'  	=> $filter_supplier_id,
				'filter_company_name'  	=> $filter_company_name,
				'filter_owner_name' 		=> $filter_owner_name,
				'filter_email' 					=> $filter_email,
				'filter_address' 				=> $filter_address,
				'start'        					=> 0,
				'limit'        					=> $limit
			);

			$results = $this->model_po_mgmt_po_supplier->getSuppliers($filter_data);

			foreach ($results as $result) {
					if($get_filter == 'company'){
							$json[] = array(
									'supplier_id' => $result['supplier_id'],
									'name'       	=> strip_tags(html_entity_decode($result['company_name'], ENT_QUOTES, 'UTF-8')),
							);
					}elseif($get_filter == 'owner'){
						$json[] = array(
								'supplier_id' => $result['supplier_id'],
								'name'       	=> strip_tags(html_entity_decode($result['owner_name'], ENT_QUOTES, 'UTF-8')),
						);
					}elseif($get_filter == 'email'){
						$json[] = array(
								'supplier_id' => $result['supplier_id'],
								'name'       	=> strip_tags(html_entity_decode($result['email'], ENT_QUOTES, 'UTF-8')),
						);
					}elseif($get_filter == 'address'){
						$json[] = array(
								'supplier_id' => $result['supplier_id'],
								'name'       	=> strip_tags(html_entity_decode($result['address'], ENT_QUOTES, 'UTF-8')),
						);
					}elseif($get_filter == 'supplier_id'){
						$json['success'] = array(
								'supplier_id' => $result['supplier_id'],
								'owner_name'  => strip_tags(html_entity_decode($result['owner_name'], ENT_QUOTES, 'UTF-8')),
								'email'  			=> $result['email'],
								'company_name'=> strip_tags(html_entity_decode($result['company_name'], ENT_QUOTES, 'UTF-8')),
								'telephone'		=> $result['telephone'],
								'address'  		=> strip_tags(html_entity_decode($result['address'], ENT_QUOTES, 'UTF-8')),
						);
					}
			 }
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
