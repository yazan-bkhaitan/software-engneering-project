<?php
class ControllerPOMgmtPoQuotation extends Controller {
	private $error = array();

	private $totals = array('sub' => array(
															'title' => 'Sub Total',
															'index' => 'sub',
															'value' => 0),
									'ship' => array(
															'title' => 'Shipping Rate',
															'index' => 'ship',
															'value' => 0),
									'tax' => array(
															'title' => 'Tax(%)',
															'index' => 'tax',
															'value' => 0),
									'grand' => array(
															'title' => 'Grand Total',
															'index' => 'grand',
															'value' => 0));


  public function __construct($registory) {
    parent::__construct($registory);
    $this->load->model('po_mgmt/po_quotation');
    $this->_poQuotation = $this->model_po_mgmt_po_quotation;
		if (!$this->config->get('module_oc_pom_status')) {
			$this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
		}
  }

	public function index() {
    $this->load->language('po_mgmt/po_quotation');

		if(isset($this->session->data['quotation_steps'])){
			unset($this->session->data['quotation_steps']);
		}

		$this->document->setTitle($this->language->get('heading_title_list'));
		$this->getList();
	}

	public function add() {
		$this->load->language('po_mgmt/po_quotation');

		$this->document->setTitle($this->language->get('heading_title_add'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->_poQuotation->addQuotation($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success_add');

			$url = '';

  		if (isset($this->request->get['filter_quotation_id'])) {
  			$url .= '&filter_quotation_id=' . urlencode(html_entity_decode($this->request->get['filter_quotation_id'], ENT_QUOTES, 'UTF-8'));
  		}

      if (isset($this->request->get['filter_owner_name'])) {
  			$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
  		}

  		if (isset($this->request->get['filter_created_date'])) {
  			$url .= '&filter_created_date=' . $this->request->get['filter_created_date'];
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

			$this->response->redirect($this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getForm();
	}

	public function view() {
		$this->load->language('po_mgmt/po_quotation');
		if(!isset($this->request->get['quotation_id'])){
			$this->response->redirect($this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->document->setTitle($this->language->get('heading_title_view'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateQuotationForm()) {
			$this->_poQuotation->editQuotation($this->request->get['product_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success_edit');

      $url = '';

  		if (isset($this->request->get['filter_quotation_id'])) {
  			$url .= '&filter_quotation_id=' . urlencode(html_entity_decode($this->request->get['filter_quotation_id'], ENT_QUOTES, 'UTF-8'));
  		}

      if (isset($this->request->get['filter_owner_name'])) {
  			$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
  		}

  		if (isset($this->request->get['filter_created_date'])) {
  			$url .= '&filter_created_date=' . $this->request->get['filter_created_date'];
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

			$this->response->redirect($this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getViewForm();
	}

	public function delete() {
		$this->load->language('po_mgmt/po_quotation');

		$this->document->setTitle($this->language->get('heading_title_list'));

		$this->load->model('po_mgmt/po_quotation');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->_poQuotation->deleteQuotation($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success_delete');

      $url = '';

			if (isset($this->request->get['filter_quotation_id'])) {
				$url .= '&filter_quotation_id=' . urlencode(html_entity_decode($this->request->get['filter_quotation_id'], ENT_QUOTES, 'UTF-8'));
			}

	    if (isset($this->request->get['filter_owner_name'])) {
				$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_created_date'])) {
				$url .= '&filter_created_date=' . $this->request->get['filter_created_date'];
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

			$this->response->redirect($this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
    $data = array();
		$data = array_merge($data, $this->load->language('po_mgmt/po_quotation'));

    if (isset($this->request->get['filter_quotation_id'])) {
			$filter_quotation_id = $this->request->get['filter_quotation_id'];
		} else {
			$filter_quotation_id = null;
		}

    if (isset($this->request->get['filter_owner_name'])) {
			$filter_owner_name = $this->request->get['filter_owner_name'];
		} else {
			$filter_owner_name = null;
		}

		if (isset($this->request->get['filter_created_date'])) {
			$filter_created_date = $this->request->get['filter_created_date'];
		} else {
			$filter_created_date = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'q.id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_quotation_id'])) {
			$url .= '&filter_quotation_id=' . urlencode(html_entity_decode($this->request->get['filter_quotation_id'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_owner_name'])) {
			$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_created_date'])) {
			$url .= '&filter_created_date=' . $this->request->get['filter_created_date'];
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
			'href' => $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('po_mgmt/po_quotation/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('po_mgmt/po_quotation/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['quotation_status'] = $quotation_status = array('1' => 'Pending',
																													'2' => 'Convert To PO',
																													'6' => 'Cancel',
																												);
		$quotation_status_color = array('1' => '#0196FC',
																		'2' => '#359800',
																		'6' => '#FC0E01',
																	);


		$data['po_quotations'] = array();

		$filter_data = array(
      'filter_quotation_id'	     => $filter_quotation_id,
      'filter_owner_name'	       => $filter_owner_name,
			'filter_created_date'      => $filter_created_date,
			'filter_status'            => $filter_status,
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

		$supplier_total = $this->_poQuotation->getTotalQuotations($filter_data);

		$results = $this->_poQuotation->getQuotations($filter_data);

    if(!empty($results))
		foreach ($results as $result) {

			$ordered_qty = 0;
			$orderTotal =0;
			if(isset($result['id'])){
					$getQuoteProducts = $this->_poQuotation->getQuotationProducts($result['id']);

					foreach ($getQuoteProducts as $key => $product) {
							$ordered_qty = $ordered_qty + $product['ordered_qty'];
							$orderTotal += $product['sub_total'] +($product['sub_total']*$product['tax']/100);
					}
			}

			$data['po_quotations'][] = array(
				'id'            => $result['id'],
				'format_quote_id'  => '#'.$result['id'],
				'supplier_name' => $result['owner_name'],
				'ordered_qty'   => $ordered_qty,
				'ordered_total' => $this->currency->format($orderTotal+$result['shipping_cost'], $this->config->get('config_currency')),
				'created_date'  => $result['created_date'],
				'status_id'     => $result['quotation_status'],
				'status'        => $quotation_status[$result['quotation_status']],
				'status_color'  => $quotation_status_color[$result['quotation_status']],
				'view'          => $this->url->link('po_mgmt/po_quotation/getViewForm', 'user_token=' . $this->session->data['user_token'] . $url . '&quotation_id=' . $result['id'], true),
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['clear'] = $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'], true);

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

		if (isset($this->request->get['filter_quotation_id'])) {
			$url .= '&filter_quotation_id=' . urlencode(html_entity_decode($this->request->get['filter_quotation_id'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_owner_name'])) {
			$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_created_date'])) {
			$url .= '&filter_created_date=' . $this->request->get['filter_created_date'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

    $data['sort_quotation_id'] = $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'] . '&sort=q.quotation_id' . $url, true);
    $data['sort_company_name'] = $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'] . '&sort=sd.company_name' . $url, true);
		$data['sort_owner_name'] = $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'] . '&sort=sd.owner_name' . $url, true);
		$data['sort_created_date'] = $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'] . '&sort=q.created_date' . $url, true);
		$data['sort_status'] = $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'] . '&sort=q.status' . $url, true);

		$url = '';

    if (isset($this->request->get['filter_quotation_id'])) {
			$url .= '&filter_quotation_id=' . urlencode(html_entity_decode($this->request->get['filter_quotation_id'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_owner_name'])) {
			$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_created_date'])) {
			$url .= '&filter_created_date=' . $this->request->get['filter_created_date'];
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

		$pagination = new Pagination();
		$pagination->total = $supplier_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($supplier_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($supplier_total - $this->config->get('config_limit_admin'))) ? $supplier_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $supplier_total, ceil($supplier_total / $this->config->get('config_limit_admin')));

    $data['filter_quotation_id']  = $filter_quotation_id ? '#'.$filter_quotation_id : $filter_quotation_id;
    $data['filter_owner_name']    = $filter_owner_name;
		$data['filter_created_date']  = $filter_created_date;
    $data['filter_status']        = $filter_status;
		$data['sort']                 = $sort;
		$data['order']                = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('po_mgmt/po_quotation_list', $data));
	}

	public function getForm() {
		$data = array();
		$data = array_merge($data, $this->load->language('po_mgmt/po_quotation'));

		$this->load->model('setting/setting');
		$this->load->model('po_mgmt/po_supplier');
		$this->load->model('po_mgmt/pom_install');

		$moduleConfigSetting = $this->model_setting_setting->getSetting('module_oc_pom', $this->config->get('config_store_id'));

		$data['shipping_methods'] = $this->model_po_mgmt_pom_install->getMethods('shipping', $this->config->get('config_store_id'));
		$data['payment_methods'] = $this->model_po_mgmt_pom_install->getMethods('payment', $this->config->get('config_store_id'));

		$data['heading_title'] = !isset($this->request->get['supplier_id']) ? $this->language->get('heading_title_add') : $this->language->get('heading_title_edit');
		$data['text_form'] = !isset($this->request->get['supplier_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['currency_symbol'] = $this->config->get('config_currency');


		$data['supplier_products'] = $supplier_products = $data['sessionStepData'] 	=  array();
		$data['default_quantity']  = 1;
		$data['last_key'] 				 = '';

		if(isset($this->session->data['quotation_steps']) && !empty($this->session->data['quotation_steps'])){
			if(isset($this->session->data['next_index'])){
					$data['last_key'] 		= $this->session->data['next_index'];
			}

			$data['sessionStepData'] 	= $this->session->data['quotation_steps'];

			if(isset($data['sessionStepData']['general_info']['supplier_id']) && $data['sessionStepData']['general_info']['supplier_id']){

					$supplier_id = $data['sessionStepData']['general_info']['supplier_id'];
					$data['sessionStepData']['general_info']['supplier_link'] = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token']. '&supplier_id='.$supplier_id, true);

					//get shipping method name
					if(isset($data['sessionStepData']['general_info']['shipping_method']) && in_array($data['sessionStepData']['general_info']['shipping_method'], $this->array_column_alternate($data['shipping_methods'], 'method_id'))){
						$getShippingIndex = array_search($data['sessionStepData']['general_info']['shipping_method'], $this->array_column_alternate($data['shipping_methods'], 'method_id'));
								if(isset($data['shipping_methods'][$getShippingIndex]['shipping_description'][$this->config->get('config_language_id')]['name'])){
										$data['sessionStepData']['general_info']['shipping_method_name'] = $this->session->data['quotation_steps']['general_info']['shipping_method_name'] = $data['shipping_methods'][$getShippingIndex]['shipping_description'][$this->config->get('config_language_id')]['name'];
								}else{
										$data['sessionStepData']['general_info']['shipping_method_name'] = $this->session->data['quotation_steps']['general_info']['shipping_method_name'] = 'N/A';
								}
					}
					//get payment method name
					if(isset($data['sessionStepData']['general_info']['payment_method']) && in_array($data['sessionStepData']['general_info']['payment_method'], $this->array_column_alternate($data['payment_methods'], 'method_id'))){
						$getPaymentIndex = array_search($data['sessionStepData']['general_info']['payment_method'], $this->array_column_alternate($data['payment_methods'], 'method_id'));
								if(isset($data['payment_methods'][$getPaymentIndex]['payment_description'][$this->config->get('config_language_id')]['name'])){
										$data['sessionStepData']['general_info']['payment_method_name'] = $this->session->data['quotation_steps']['general_info']['payment_method_name'] =  $data['payment_methods'][$getPaymentIndex]['payment_description'][$this->config->get('config_language_id')]['name'];
								}else{
										$data['sessionStepData']['general_info']['payment_method_name'] =
										$this->session->data['quotation_steps']['general_info']['payment_method_name'] = 'N/A';
								}
					}

					//Quotation dates section
					$data['start_date'] = $data['expected_date'] = ' N/A ';
					if($data['sessionStepData']['general_info']['shipping_start_date']){
							$data['start_date'] 		= date('j F Y, H:i A', strtotime($data['sessionStepData']['general_info']['shipping_start_date']));
					}
					if($data['sessionStepData']['general_info']['expected_delivery_date']){
							$data['expected_date'] 	= date('j F Y, H:i A', strtotime($data['sessionStepData']['general_info']['expected_delivery_date']));
					}
					$data['created_date'] 	= date('j F Y, H:i A');

					$this->session->data['quotation_steps']['general_info']['shipping_cost_formatted'] = $this->currency->format($data['sessionStepData']['general_info']['shipping_cost'], $this->config->get('config_currency'));

					$this->load->model('tool/image');
					$this->load->model('tool/upload');
					$this->load->model('catalog/product');
					$this->load->model('catalog/option');

					//Quotation total section
					$sub_total_amount = $shipping_rate = $tax_amount = 0;
					$data['default_quantity'] = $moduleConfigSetting['module_oc_pom_quotation_quantity'];
					$data['sessionStepData_update_product'] = array();
					if(isset($data['sessionStepData']['update_product']) && !empty($data['sessionStepData']['update_product'])){
							foreach ($data['sessionStepData']['update_product'] as $key => $product) {
								//Quotation product section
								$supplier_product = $this->model_po_mgmt_po_supplier->getSupplierProduct(array('supplier_id' => $supplier_id, 'filter_product_id' => $product['product_id']));

								$option_data_value 	= $product_options = array();
								$option_price 			= 0;
								if(!empty($supplier_product)){
										if (is_file(DIR_IMAGE . $supplier_product['image'])) {
											$image = $this->model_tool_image->resize($supplier_product['image'], 60, 60);
										} else {
											$image = $this->model_tool_image->resize('no_image.png', 60, 60);
										}

										// Options
										$productOptions = array();
										if (isset($product['product_id'])) {
											$product_options = $this->model_catalog_product->getProductOptions($product['product_id']);
										}

										if(!empty($product_options)){
												foreach ($product_options as $product_option) {
														$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

														if($option_info){
																// start make product option for added product on refresh
																$product_option_value_data = array();
																foreach ($product_option['product_option_value'] as $product_option_value) {
																		$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

																		if ($option_value_info) {
																			$product_option_value_data[$product_option_value['product_option_value_id']] = array(
																				'product_option_value_id' => $product_option_value['product_option_value_id'],
																				'option_value_id'         => $product_option_value['option_value_id'],
																				'name'                    => $option_value_info['name'],
																				'price'                   => (float)$product_option_value['price'] ? $product_option_value['price'] : false,
																				'price_prefix'            => $product_option_value['price_prefix']
																			);
																		}
																}

																if ($option_info['type'] == 'file' && isset($product['options'][$product_option['product_option_id']])){
																		$upload_info = $this->model_tool_upload->getUploadByCode($product['options'][$product_option['product_option_id']]);
																		if ($upload_info) {
																			$product_option['value'] = $upload_info['name'];
																		}
																}

																$productOptions[$product_option['product_option_id']] = array(
																		'product_option_id'    => $product_option['product_option_id'],
																		'product_option_value' => $product_option_value_data,
																		'option_id'            => $product_option['option_id'],
																		'name'                 => $option_info['name'],
																		'type'                 => $option_info['type'],
																		'value'                => $product_option['value'],
																		'required'             => $product_option['required'],
																);
																// end make product option for added product on refresh

																// start to get the sub-total and tax
																$product_option_value_data = isset($product['options'][$product_option['product_option_id']]) ? $product['options'][$product_option['product_option_id']] : '';

																if ($option_info['type'] == 'select' || $option_info['type'] == 'radio' || $option_info['type'] == 'image') {
																		$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_data . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

																		if ($option_value_query->num_rows) {
																			if ($option_value_query->row['price_prefix'] == '+') {
																				$option_price += $option_value_query->row['price'];
																			} elseif ($option_value_query->row['price_prefix'] == '-') {
																				$option_price -= $option_value_query->row['price'];
																			}

																			$option_data_value[] = array(
																				'name'  => $option_info['name'],
																				'value' => (utf8_strlen($option_value_query->row['name']) > 20 ? utf8_substr($option_value_query->row['name'], 0, 20) . '..' : $option_value_query->row['name']),
																				'type'  => $option_info['type']
																			);
																		}
																} elseif ($option_info['type'] == 'checkbox' && is_array($product_option_value_data)) {
																		foreach ($product_option_value_data as $product_option_value_id) {
																			$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

																			if ($option_value_query->num_rows) {
																				if ($option_value_query->row['price_prefix'] == '+') {
																					$option_price += $option_value_query->row['price'];
																				} elseif ($option_value_query->row['price_prefix'] == '-') {
																					$option_price -= $option_value_query->row['price'];
																				}

																				$option_data_value[] = array(
																					'name'  => $option_info['name'],
																					'value' => (utf8_strlen($option_value_query->row['name']) > 20 ? utf8_substr($option_value_query->row['name'], 0, 20) . '..' : $option_value_query->row['name']),
																					'type'  => $option_info['type']
																				);
																			}
																		}
																} elseif ($option_info['type'] == 'text' || $option_info['type'] == 'textarea' || $option_info['type'] == 'file' || $option_info['type'] == 'date' || $option_info['type'] == 'datetime' || $option_info['type'] == 'time') {

																			$option_data_value[] = array(
																				'name'  => $option_info['name'],
																				'value' => (utf8_strlen($product_option_value_data) > 20 ? utf8_substr($product_option_value_data, 0, 20) . '..' : $product_option_value_data),
																				'type'  => $option_info['type']
																			);
																}
																// end to get the sub-total and tax
														}
												}
												$sub_total_amount += (($product['supplier_cost'] + $option_price) * $product['supplier_qty']);
												$tax_amount 			+= ((($product['supplier_cost'] + $option_price) * $product['supplier_qty']) * $product['supplier_tax']) / 100;
										}else{
											$sub_total_amount += ($product['supplier_cost'] * $product['supplier_qty']);
											$tax_amount 			+= (($product['supplier_cost'] * $product['supplier_qty']) * $product['supplier_tax']) / 100;
										}

										$updated_fields = array(
												'product_id'			=> $product['product_id'],
												'image'      			=> $image,
												'product_name'		=> $supplier_product['name'],
												'model'						=> $supplier_product['model'],
												'price'      			=> $supplier_product['price'],
												'options_data'		=> $productOptions,
												'supplier_id'			=> $supplier_product['supplier_id'],
												'supplier_sku'		=> $supplier_product['supplier_sku'],
										);
										$this->session->data['quotation_steps']['update_product'][$key] = array_merge($this->session->data['quotation_steps']['update_product'][$key], $updated_fields);
								}
								$this->session->data['quotation_steps']['update_product'][$key]['total_option_cost'] = $option_price;
							}
							$data['sessionStepData_update_product'] = $this->array_column_alternate($data['sessionStepData']['update_product'], 'product_id');
							$this->totals['sub']['value'] = $sub_total_amount;
							$this->totals['tax']['value'] = $tax_amount;
					}

					if(isset($data['sessionStepData']['general_info']['shipping_cost'])){
							$this->totals['ship']['value'] = $shipping_rate = $data['sessionStepData']['general_info']['shipping_cost'];
					}else if($moduleConfigSetting['module_oc_pom_shipping_cost']){
							$this->totals['ship']['value'] = $shipping_rate = $moduleConfigSetting['module_oc_pom_shipping_cost'];
					}

					$this->totals['grand']['value'] = $sub_total_amount + $shipping_rate + $tax_amount;
			}
			$data['sessionStepData'] = $this->session->data['quotation_steps'];
		}

		$data['totals'] = array();

		foreach ($this->totals as $total) {
			$data['totals'][$total['index']] = array(
				'title' => $total['title'],
				'value' => $total['value'],
				'text'  => $this->currency->format($total['value'], $this->config->get('config_currency'))
			);
		}

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
		if(isset($this->error['add_product'])){
			$data['error_add_product'] = $this->error['add_product'];
		}else{
			$data['error_add_product'] = array();
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
			'text' => $this->language->get('heading_title_list'),
			'href' => $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => !isset($this->request->get['supplier_id']) ? $this->language->get('heading_title_add') : $this->language->get('heading_title_edit'),
			'href' => $this->url->link(!isset($this->request->get['supplier_id']) ? 'po_mgmt/po_quotation/add' : 'po_mgmt/po_quotation/edit', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['supplier_id'])) {
			$data['action'] = $this->url->link('po_mgmt/po_quotation/getForm', 'user_token=' . $this->session->data['user_token'], true);
			$data['supplier_id'] = 0;
		} else {
			$data['action'] = $this->url->link('po_mgmt/po_quotation/edit', 'user_token=' . $this->session->data['user_token'] . '&supplier_id=' . $this->request->get['supplier_id'], true);
			$data['supplier_id'] = $this->request->get['supplier_id'];
		}

		$data['create_quotation'] = $this->url->link('po_mgmt/po_quotation/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'], true);

		$data['order_source_options'] = array('Phone', 'Email', 'Fax', 'Website');

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

		$data['suppliers'] = $this->model_po_mgmt_po_supplier->getSuppliers(array('filter_status' => 1));

		$data['config_langauge_id'] = $this->config->get('config_language_id');

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['header'] 			= $this->load->controller('common/header');
		$data['column_left'] 	= $this->load->controller('common/column_left');
		$data['footer'] 			= $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('po_mgmt/po_quotation_form', $data));
	}

	public function getViewForm($quotation_id = false) {
		$data = array();
		$data = array_merge($data, $this->load->language('po_mgmt/po_quotation'));

		$this->document->setTitle($this->language->get('heading_title_view'));

		$data['quotation_id'] = isset($this->request->get['quotation_id']) ? $this->request->get['quotation_id'] : $quotation_id;

		if (isset($this->request->get['quotation_id'])) {
		  $quotation_id = $this->request->get['quotation_id'];
		} else {
		  $quotation_id = null;
		}

		if (isset($this->request->get['page'])) {
		  $page = $this->request->get['page'];
		} else {
		  $page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_quotation_id'])) {
		  $url .= '&filter_quotation_id=' . urlencode(html_entity_decode($this->request->get['filter_quotation_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_owner_name'])) {
			$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['quotation_id'])) {
		  $url .= '&quotation_id=' . $this->request->get['quotation_id'];
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
			'href' => $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token']. $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_view'),
			'href' => $this->url->link('po_mgmt/po_quotation/getViewForm', 'user_token=' . $this->session->data['user_token']. $url, true)
		);

		$filter_data = array(
		  'quotation_id'	     => $quotation_id,
		  'start'              => ($page - 1) * $this->config->get('config_limit_admin'),
		  'limit'              => $this->config->get('config_limit_admin')
		);

		$history_total = $this->_poQuotation->getTotalQuoteCommentHistory($filter_data);
		$data['comment_histories'] = array();
		$comment_histories = $this->_poQuotation->getQuoteCommentHistory($filter_data);
		if(!empty($comment_histories)){
			foreach ($comment_histories as $key => $comment_history) {
				$data['comment_histories'][] = array(
					'date_added'	=> date('h:i A, jS F Y ', strtotime($comment_history['date_added'])),
					'description'	=> $comment_history['description'],
				);
			}
		}


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

		$url = '';

		if (isset($this->request->get['filter_quotation_id'])) {
		  $url .= '&filter_quotation_id=' . urlencode(html_entity_decode($this->request->get['filter_quotation_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_owner_name'])) {
		  $url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['quotation_id'])) {
		  $url .= '&quotation_id=' . $this->request->get['quotation_id'];
		}

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('po_mgmt/po_quotation/getViewForm', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($history_total - $this->config->get('config_limit_admin'))) ? $history_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $history_total, ceil($history_total / $this->config->get('config_limit_admin')));

		$quotation_info = array();
		if (isset($data['quotation_id'])) {
			$quotation_details = $this->_poQuotation->getQuotations(array('filter_id' => $data['quotation_id']));
				if(isset($quotation_details[0]['id']) && $quotation_details[0]['id'] == $data['quotation_id']){
						$quotation_info = $quotation_details[0];
				}
		}
			if(!empty($quotation_info)){
					$data['heading_quotation_id'] 						= $data['heading_quotation_id'].$quotation_info['id'];
					$quotation_info['supplier_link']					=	$this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token']. '&supplier_id='.$quotation_info['supplier_id'], true);

					$quotation_info['shipping_start_date'] 		= date('j F Y, H:i A', strtotime($quotation_info['shipping_start_date']));
					$data['created_date'] 										= date('j F Y, H:i A', strtotime($quotation_info['created_date']));
					$data['quotation_status'] 								= array('1' => 'Pending',
																														'2' => 'Convert To PO',
																														'6' => 'Cancel',);
					$data['currency_symbol'] 									= $this->config->get('config_currency');
					$quotation_info['shipping_cost_formatted']= $this->currency->format($quotation_info['shipping_cost'], $this->config->get('config_currency'));

					$data['quotation_info'] 									= $quotation_info;

					$this->load->model('po_mgmt/po_supplier');
					$getSupplierDetails = $this->model_po_mgmt_po_supplier->getSuppliers(array('filter_supplier_id' => $quotation_info['supplier_id']));

					if(isset($getSupplierDetails[0]['supplier_id']) && !empty($getSupplierDetails[0])){
						$data['supplier_details'] 							= $getSupplierDetails[0];
					}
					$sub_total = $tax_total = 0;
					$data['quotation_products'] = array();
					$this->load->model('tool/image');
					$this->load->model('tool/upload');
					$shipping_expected_date = $quotation_info['shipping_expected_date'];
					$data['quotation_info']['shipping_expected_date'] = date('j F Y, H:i A', strtotime($shipping_expected_date));

					$quotation_products = $this->_poQuotation->getQuotationProducts($quotation_info['id']);

					foreach ($quotation_products as $key => $q_product) {
							if($q_product['schedule_date']){
								$expected_date1 = new DateTime($shipping_expected_date);
								$schedule_date2 = new DateTime($q_product['schedule_date']);
								if($schedule_date2 > $expected_date1){
									$data['quotation_info']['shipping_expected_date'] = date('j F Y, H:i A', strtotime($q_product['schedule_date']));
								}
							}
							$option_data = array();
							$options = $this->_poQuotation->getQuotationOptions($q_product['quotation_id'], $q_product['id']);

							foreach ($options as $option) {
								if ($option['type'] != 'file') {
									$option_data[] = array(
										'name'  => $option['name'],
										'value' => $option['value'],
										'type'  => $option['type']
									);
								} else {
									$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

									if ($upload_info) {
										$option_data[] = array(
											'name'  => $option['name'],
											'value' => $upload_info['name'],
											'type'  => $option['type'],
											'href'  => $this->url->link('tool/upload/download', 'user_token=' . $this->session->data['user_token'] . '&code=' . $upload_info['code'], true)
										);
									}
								}
							}

							if (is_file(DIR_IMAGE . $q_product['image'])) {
								$image = $this->model_tool_image->resize($q_product['image'], 60, 60);
							} else {
								$image = $this->model_tool_image->resize('no_image.png', 60, 60);
							}
							$sub_total = $sub_total + ($q_product['ordered_cost'] * $q_product['ordered_qty']);
							$tax_total = $tax_total + (($q_product['ordered_cost'] * $q_product['ordered_qty']) * $q_product['tax']) / 100;
							$data['quotation_products'][] = array(
								'po_product_id'		=> $q_product['id'],
								'product_id'			=> $q_product['product_id'],
								'image'      			=> $image,
								'product_name'		=> $q_product['name'],
								'model'						=> $q_product['model'],
								'options'					=> $option_data,
								'supplier_id'			=> $q_product['supplier_id'],
								'supplier_sku'		=> $q_product['supplier_sku'],
								'ordered_qty'			=> $q_product['ordered_qty'],
								'ordered_cost'		=> isset($q_product['ordered_cost']) ? $q_product['ordered_cost'] : $q_product['supplier_cost'],
								'supplier_tax'		=> $q_product['tax'],
								'sub_total'				=> $q_product['sub_total'],
							);
					}
					$this->load->model('setting/setting');
					$moduleConfigSetting = $this->model_setting_setting->getSetting('module_oc_pom', $this->config->get('config_store_id'));
					$this->totals['sub']['value'] 	= $sub_total;
					$this->totals['ship']['value'] 	= $quotation_info['shipping_cost'];
					$this->totals['tax']['value'] 	= $tax_total;
					$this->totals['grand']['value'] = ($sub_total + $quotation_info['shipping_cost'] + $tax_total);
					$data['totals'] = array();

					foreach ($this->totals as $total) {
						$data['totals'][$total['index']] = array(
							'title' => $total['title'],
							'value' => $total['value'],
							'text'  => $this->currency->format($total['value'], $this->config->get('config_currency'))
						);
					}

					$url = '';

					if (isset($this->request->get['filter_quotation_id'])) {
					  $url .= '&filter_quotation_id=' . urlencode(html_entity_decode($this->request->get['filter_quotation_id'], ENT_QUOTES, 'UTF-8'));
					}

					if (isset($this->request->get['filter_owner_name'])) {
					  $url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
					}

					if (isset($this->request->get['quotation_id'])) {
					  $url .= '&quotation_id=' . $this->request->get['quotation_id'];
					}

					$data['user_token'] 						= $this->session->data['user_token'];
					$data['quotation_id'] 		= $this->request->get['quotation_id'];
					$data['add_more_product'] =	$this->url->link('po_mgmt/po_quotation/addMoreProduct', 'user_token=' . $this->session->data['user_token'] . $url, true);
					$data['print_quotation'] 	=	$this->url->link('po_mgmt/po_quotation/print_quotation', 'user_token=' . $this->session->data['user_token'] . $url, true);
					$data['cancel'] 					= $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'], true);

					if(isset($this->error['more_product'])){
							$data['error_more_product'] = $this->error['more_product'];
					}else{
							$data['error_more_product'] = array();
					}

					if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['more_product'])){
							$data['more_products'] = array();
							$more_post = $this->request->post['more_product'];

							$this->load->model('po_mgmt/po_supplier');
							$this->load->model('catalog/option');
							foreach ($more_post as $product_id => $product_data) {
									$results 		= $this->model_po_mgmt_po_supplier->getSupplierProducts(array('filter_product_id' => $product_id));
									foreach ($results as $key => $s_product) {
											if (is_file(DIR_IMAGE . $s_product['image'])) {
												$image = $this->model_tool_image->resize($s_product['image'], 60, 60);
											} else {
												$image = $this->model_tool_image->resize('no_image.png', 60, 60);
											}
											$supplier_cost 	= $product_data['supplier_cost'];
											$supplier_qty  	= $product_data['supplier_qty'];

											// Options
											$product_options = $this->model_catalog_product->getProductOptions($product_id);

											$productOptions = array();
											foreach ($product_options as $product_option) {
													$option_info = $this->model_catalog_option->getOption($product_option['option_id']);
													if($option_info){
															$product_option_value_data = array();

															foreach ($product_option['product_option_value'] as $product_option_value) {
																$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

																if ($option_value_info) {
																	$product_option_value_data[$product_option_value['product_option_value_id']] = array(
																		'product_option_value_id' => $product_option_value['product_option_value_id'],
																		'option_value_id'         => $product_option_value['option_value_id'],
																		'name'                    => $option_value_info['name'],
																		'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
																		'price_prefix'            => $product_option_value['price_prefix']
																	);
																}
															}

															if(isset($product_data['options'][$product_option['product_option_id']])){
																$option_selected = $product_data['options'][$product_option['product_option_id']];
															}else{
																$option_selected = $product_option['value'];
															}

															$productOptions[$product_option['product_option_id']] = array(
																'product_option_id'    => $product_option['product_option_id'],
																'product_option_value' => $product_option_value_data,
																'option_id'            => $product_option['option_id'],
																'name'                 => $option_info['name'],
																'type'                 => $option_info['type'],
																'value'                => $option_selected,
																'required'             => $product_option['required']
															);
													}
											}

											$data['more_products'][$s_product['product_id']] = array(
												'product_id'			=> $s_product['product_id'],
												'image'      			=> $image,
												'product_name'		=> strip_tags(html_entity_decode($s_product['name'], ENT_QUOTES, 'UTF-8')),
												'model'						=> $s_product['model'],
												'price'      			=> $s_product['price'],
												'options'					=> $productOptions,
												'supplier_id'			=> $s_product['supplier_id'],
												'supplier_sku'		=> strip_tags(html_entity_decode($s_product['supplier_sku'], ENT_QUOTES, 'UTF-8')),
												'supplier_cost'		=> $supplier_cost,
												'supplier_qty'		=> $supplier_qty,
												'supplier_tax'		=> $product_data['supplier_tax'],
											);
								 }
							}
					}

					$data['header'] 					= $this->load->controller('common/header');
					$data['column_left'] 			= $this->load->controller('common/column_left');
					$data['footer'] 					= $this->load->controller('common/footer');

					$this->response->setOutput($this->load->view('po_mgmt/po_quotation_view', $data));
			}else{
					$this->response->redirect($this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'], true));
			}
	}

	public function print_quotation(){
		$data = array();
		$data = array_merge($data, $this->load->language('po_mgmt/po_quotation'));

		if($this->request->get['quotation_id']){
			$quotation_details = $this->_poQuotation->getQuotations(array('filter_id' => $this->request->get['quotation_id']));
				if(isset($quotation_details[0]['id']) && $quotation_details[0]['id'] == $this->request->get['quotation_id']){
						$quotation_info = $quotation_details[0];
				}
				if(!empty($quotation_info)){
						$data['heading_quotation_id'] 						= $data['heading_quotation_id'].$quotation_info['id'];
						$quotation_info['supplier_link']					=	$this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token']. '&supplier_id='.$quotation_info['supplier_id'], true);

						$quotation_info['shipping_start_date'] 		= date('j F Y, H:i A', strtotime($quotation_info['shipping_start_date']));
						$data['created_date'] 										= date('j F Y, H:i A', strtotime($quotation_info['created_date']));
						$data['quotation_status'] 								= array('1' => 'Pending',
																															'2' => 'Convert To PO',
																															'6' => 'Cancel',);
						$data['currency_symbol'] 									= $this->config->get('config_currency');
						$quotation_info['shipping_cost_formatted']= $this->currency->format($quotation_info['shipping_cost'], $this->config->get('config_currency'));

						$data['quotation_info'] 									= $quotation_info;

						$this->load->model('po_mgmt/po_supplier');
						$getSupplierDetails = $this->model_po_mgmt_po_supplier->getSuppliers(array('filter_supplier_id' => $quotation_info['supplier_id']));

						if(isset($getSupplierDetails[0]['supplier_id']) && !empty($getSupplierDetails[0])){
							$data['supplier_details'] 							= $getSupplierDetails[0];
						}
						$sub_total = $tax_total = 0;
						$data['quotation_products'] = array();
						$this->load->model('tool/image');
						$this->load->model('tool/upload');

						$shipping_expected_date = $quotation_info['shipping_expected_date'];
						$data['quotation_info']['shipping_expected_date'] = date('j F Y, H:i A', strtotime($shipping_expected_date));

						$quotation_products = $this->_poQuotation->getQuotationProducts($quotation_info['id']);
						foreach ($quotation_products as $key => $q_product) {
								if($q_product['schedule_date']){
									$expected_date1 = new DateTime($shipping_expected_date);
									$schedule_date2 = new DateTime($q_product['schedule_date']);

									if($schedule_date2 > $expected_date1){
										$data['quotation_info']['shipping_expected_date'] = date('j F Y, H:i A', strtotime($q_product['schedule_date']));
									}
								}
								$option_data = array();
								$options = $this->_poQuotation->getQuotationOptions($q_product['quotation_id'], $q_product['id']);

								foreach ($options as $option) {
									if ($option['type'] != 'file') {
										$option_data[] = array(
											'name'  => $option['name'],
											'value' => $option['value'],
											'type'  => $option['type']
										);
									} else {
										$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

										if ($upload_info) {
											$option_data[] = array(
												'name'  => $option['name'],
												'value' => $upload_info['name'],
												'type'  => $option['type'],
												'href'  => $this->url->link('tool/upload/download', 'user_token=' . $this->session->data['user_token'] . '&code=' . $upload_info['code'], true)
											);
										}
									}
								}
								if (is_file(DIR_IMAGE . $q_product['image'])) {
									$image = $this->model_tool_image->resize($q_product['image'], 60, 60);
								} else {
									$image = $this->model_tool_image->resize('no_image.png', 60, 60);
								}
								$sub_total = $sub_total + ($q_product['ordered_cost'] * $q_product['ordered_qty']);
								$tax_total = $tax_total + (($q_product['ordered_cost'] * $q_product['ordered_qty']) * $q_product['tax']) / 100;
								$data['quotation_products'][] = array(
									'product_id'			=> $q_product['product_id'],
									'image'      			=> $image,
									'product_name'		=> $q_product['name'],
									'model'						=> $q_product['model'],
									'options'					=> $option_data,
									'supplier_id'			=> $q_product['supplier_id'],
									'supplier_sku'		=> $q_product['supplier_sku'],
									'ordered_qty'			=> $q_product['ordered_qty'],
									'ordered_cost'		=> isset($q_product['ordered_cost']) ? $q_product['ordered_cost'] : $q_product['supplier_cost'],
									'supplier_tax'		=> $q_product['tax'],
									'sub_total'				=> $q_product['sub_total'],
								);
						}
						$this->load->model('setting/setting');
						$moduleConfigSetting = $this->model_setting_setting->getSetting('module_oc_pom', $this->config->get('config_store_id'));
						$this->totals['sub']['value'] 	= $sub_total;
						$this->totals['ship']['value'] 	= $quotation_info['shipping_cost'];
						$this->totals['tax']['value'] 	= $tax_total;
						$this->totals['grand']['value'] = ($sub_total + $quotation_info['shipping_cost'] + $tax_total);
						$data['totals'] = array();

						foreach ($this->totals as $total) {
							$data['totals'][$total['index']] = array(
								'title' => $total['title'],
								'value' => $total['value'],
								'text'  => $this->currency->format($total['value'], $this->config->get('config_currency'))
							);
						}

						if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
							$data['store_logo'] = $this->model_tool_image->resize($this->config->get('config_logo'), 300, 100);
						} else {
							$data['store_logo'] = $this->model_tool_image->resize('no_image.png', 300, 100);
						}
						$data['config_name']	= $this->config->get('config_name');
						$data['config_owner']	= $this->config->get('config_owner');
						$data['config_email']	= $this->config->get('config_email');

						$data['user_token'] 				= $this->session->data['user_token'];
						$data['quotation_id'] = $this->request->get['quotation_id'];
						$data['header'] 			= $this->load->controller('common/header');

						$this->response->setOutput($this->load->view('po_mgmt/po_print_quotation', $data));
				}else{
					$this->response->redirect($this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'], true));
				}
		}else{
			$this->response->redirect($this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'], true));
		}
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'po_mgmt/po_quotation')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateForm() {
		$this->load->language('po_mgmt/po_quotation');
		if (!$this->user->hasPermission('modify', 'po_mgmt/po_quotation')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if(!isset($this->error['warning'])){
				if(isset($this->request->post['general_info'])){
						$post_array = array('supplier_name','shipping_method','payment_method');
						foreach ($post_array as $post_index) {
							if(empty($this->request->post['general_info'][$post_index]) || !isset($this->request->post['general_info'][$post_index])){
									$this->error[$post_index] = sprintf($this->language->get('error_required_field'), ucfirst($post_index));
							}
						}

						if(isset($this->request->post['general_info']['shipping_start_date']) && $this->request->post['general_info']['shipping_start_date']){
								if(!preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/", $this->request->post['general_info']['shipping_start_date'])){
										$this->error['shipping_start_date'] = $this->language->get('error_shipping_start_date_format');
								}
						}

						if(isset($this->request->post['general_info']['expected_delivery_date']) && $this->request->post['general_info']['expected_delivery_date']){
								if(!preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/", $this->request->post['general_info']['expected_delivery_date'])){
										$this->error['expected_delivery_date'] = $this->language->get('error_expected_delivery_date_format');
								}
						}

						if(empty($this->error) && $this->request->post['general_info']['shipping_start_date'] && $this->request->post['general_info']['expected_delivery_date'] && $this->request->post['general_info']['shipping_start_date'] > $this->request->post['general_info']['expected_delivery_date']){
								$this->error['expected_delivery_date'] = $this->language->get('error_expected_delivery_date');
						}
				}

				if(isset($this->session->data['quotation_steps']['update_product']) && !empty($this->session->data['quotation_steps']['update_product']) && isset($this->request->post['add_product'])){
						foreach ($this->session->data['quotation_steps']['update_product'] as $key => $product) {
								if(!is_numeric($product['supplier_cost'])){
									$this->error['add_product'][$product['product_id']] = array('supplier_cost' => $this->language->get('error_supplier_cost'));
								}
								if(!preg_match('/^[1-9][0-9]*$/', $product['supplier_qty'])){
									$this->error['add_product'][$product['product_id']] = array('supplier_qty' => $this->language->get('error_supplier_qty'));
								}

								if(empty($this->error) && isset($this->request->post['add_product'][$key])){
									$option_data = array();
									if(isset($product['options']) && !empty($product['options'])){
											$this->request->post['add_product'][$key]['option_data'] = $this->getOptionData($product['options'], $product['product_id']);
									}
									$this->request->post['add_product'][$key]['total_option_cost'] 	= $product['total_option_cost'];
									$this->request->post['add_product'][$key]['sub_total'] 					= (($product['supplier_cost'] + $product['total_option_cost']) * $product['supplier_qty']);
								} else{
										$this->error['warning'] = $this->language->get('error_warning');
								}
						}
				}
				else{
						$this->error['warning'] = $this->language->get('error_no_product_found');
				}
		}
		return !$this->error;
	}

	public function quotationFormStepData(){
		$json = array();
		$this->load->language('po_mgmt/po_quotation');

		if(!isset($this->session->data['quotation_steps'])){
			$this->session->data['quotation_steps'] = array();
		}

		if(isset($this->request->post['step']) && !isset($json['warning'])){
				$step 				= $this->request->post['step'];
				$resultError 	= $this->validateQuotationForm($this->request->post, $step);

				if(!empty($resultError)){
						$json['error'] = $resultError;
				}else{
						$json['success'] = true;
				}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function validateQuotationForm($data = array(), $step){
		$error_check = $addedProduct = array();

		if($step == 'general_info' && isset($data[$step])){
				// validate for the require fields under the general info step
				$post_array = array('supplier_name','shipping_method','payment_method');
				foreach ($post_array as $post_index) {
						if(empty($data[$step][$post_index]) || !isset($data[$step][$post_index])){
								$error_check[$post_index] = sprintf($this->language->get('error_required_field'), ucfirst($post_index));
						}
				}
				if(isset($data[$step]['shipping_start_date']) && isset($data[$step]['expected_delivery_date']) && $data[$step]['shipping_start_date'] > $data[$step]['expected_delivery_date']){
						$error_check['expected_delivery_date'] = $this->language->get('error_expected_delivery_date');
				}
				$this->session->data['quotation_steps'][$step] = $data[$step];
		}

		if($step == 'add_product' && isset($data[$step])){
				$this->load->model('catalog/product');
				foreach ($data[$step] as $key => $product_data) {

						$addedProduct[$key] = $data[$step][$key];
						if(isset($addedProduct[$key]['options'])){
								$options = $addedProduct[$key]['options'];
						}else{
								$options = array();
						}
						$product_options = $this->model_catalog_product->getProductOptions($product_data['product_id']);
						if(!empty($product_options)){
								foreach ($product_options as $product_option) {
										if ($product_option['required'] && empty($options[$product_option['product_option_id']])){
												$error_check[$step][$key]['options'][$product_option['product_option_id']] = sprintf($this->language->get('error_required_field'), $product_option['name']);
										}
										if (!$product_option['required'] && empty($options[$product_option['product_option_id']]) && isset($this->request->post[$step][$key]['options'][$product_option['product_option_id']])){
												unset($this->request->post[$step][$key]['options'][$product_option['product_option_id']]);
												unset($addedProduct[$key]['options'][$product_option['product_option_id']]);
										}
								}
						}
						if(!is_numeric($product_data['supplier_cost'])){
							$error_check[$step][$key]['supplier_cost'] = $this->language->get('error_supplier_cost');
						}
						if(!preg_match('/^[1-9][0-9]*$/', $product_data['supplier_qty'])){
							$error_check[$step][$key]['supplier_qty'] = $this->language->get('error_supplier_qty');
						}
				}
				$this->session->data['quotation_steps']['update_product'] = $addedProduct;
		}else if($step == 'add_product' && !isset($data[$step])){
				$error_check[$step]['warning'] = $this->language->get('error_choose_quotation_product');
		}

		//add comment validate
		if($step == 'add_comment' && isset($data[$step])){
				if ((utf8_strlen($data[$step]['comment']) > 500)) {
					$error_check[$step] = $this->language->get('error_comment_length');
				}
				$this->session->data['quotation_steps'][$step] = $data[$step];
		}

		if($step == 'update_product' && isset($data['step']) && $data['step'] == $step){
				$addedProduct	=	array();
				if(isset($data['update_product']) && !empty($data['update_product'])){
					foreach ($data['update_product'] as $product_id => $product_data) {
							if(!is_numeric($product_data['supplier_cost'])){
								$error_check['add_product'][$product_id]['supplier_cost'] = $this->language->get('error_supplier_cost');
							}
							if(!preg_match('/^[1-9][0-9]*$/', $product_data['supplier_qty'])){
								$error_check['add_product'][$product_id]['supplier_qty'] = $this->language->get('error_supplier_qty');
							}
							if(!isset($error_check['add_product']) && isset($this->session->data['quotation_steps']['update_product'][$product_id])){
									$this->session->data['quotation_steps']['update_product'][$product_id]['supplier_cost'] 	= $product_data['supplier_cost'];
									$this->session->data['quotation_steps']['update_product'][$product_id]['supplier_qty'] 	= $product_data['supplier_qty'];
									$this->session->data['quotation_steps']['update_product'][$product_id]['supplier_tax'] 	= $product_data['supplier_tax'];
							}
					}
				}else{
						$error_check['selected_product'] = $this->language->get('error_choose_quotation_product');
				}
				if(empty($error_check)){
						$this->session->data['success'] = $this->language->get('text_success_edit');
				}
		}
		if($step != 'update_product' && empty($error_check)){
				$this->session->data['next_index'] = $step;
		}
		return $error_check;
	}

	public function updateQuotationProducts(){
		$json = $changeArray = $updatedArray = array();
		$post_arrayIndex = array('po_product_id', 'product_id', 'name', 'supplier_cost', 'supplier_qty', 'supplier_tax');
		$this->load->language('po_mgmt/po_quotation');

		if(isset($this->request->post['product_data']) && !empty($this->request->post['product_data'])){
			$changeArray = array_combine($post_arrayIndex, $this->request->post['product_data']);
				if(!preg_match('/^[1-9][0-9]*$/', $changeArray['supplier_qty'])){
					$json['warning']['quantity']= $this->language->get('error_supplier_qty');
				}
				if(!is_numeric($changeArray['supplier_cost'])){
					$json['warning']['price']		= $this->language->get('error_supplier_cost');
				}
				if(!is_numeric($changeArray['supplier_tax'])){
					$json['warning']['tax']		= $this->language->get('error_supplier_cost');
				}
				if(!isset($json['warning'])){
						if(!isset($changeArray[0])){
							$updatedArray['more_product'][0] = $changeArray;
						}
						$updatedArray['quotation_id'] = $this->request->get['quotation_id'];
						$this->_poQuotation->updateQuoteProductData($updatedArray);
						$this->session->data['success'] = $this->language->get('text_success_edit');
						$json['success'] = true;
				}
		}else{
			$json['warning']['product_error'] = $this->language->get('error_wrong_product_data');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function removeQuotationProducts(){
		$json = array();
		$this->load->language('po_mgmt/po_quotation');

		if(isset($this->request->post['quotation_id']) && $this->request->post['quotation_id'] && isset($this->request->post['po_product_id']) && $this->request->post['po_product_id']){
			$quotation_products = $this->_poQuotation->getQuotationProducts($this->request->post['quotation_id']);

			if(!empty($quotation_products) && count($quotation_products) > 1){
                $this->load->model('sale/order');
                $this->model_sale_order->deleteProductFromPO($this->request->post['quotation_id'], $this->request->post['product_id']);

					$this->_poQuotation->deleteQuoteProduct($this->request->post);
					$json['success'] = true;
					$this->session->data['success'] = $this->language->get('text_success_remove_product');
			}else{
					$json['warning']['product_error'] = $this->language->get('error_quotation_product');
			}
		}else{
			if(isset($this->request->post['product_id']) && $this->request->post['product_id'] && isset($this->session->data['quotation_steps']['update_product'])){
					if((count($this->session->data['quotation_steps']['update_product']) > 1) && isset($this->session->data['quotation_steps']['update_product'][$this->request->post['product_id']])){
							unset($this->session->data['quotation_steps']['update_product'][$this->request->post['product_id']]);
							$this->session->data['success'] = $this->language->get('text_success_remove_product');
							$json['success'] = true;
					}else{
							$json['error_warning'] = $this->language->get('error_choose_quotation_product');
					}
			}else{
					$json['warning']['product_error'] = $this->language->get('error_wrong_product_data');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_product_name'])) {

			$get_filter = '';
			if (isset($this->request->get['filter_product_name'])) {
				$filter_product_name = $this->request->get['filter_product_name'];
				$get_filter = 'product';
			} else {
				$filter_product_name = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_product_name'  	=> $filter_product_name,
				'filter_quotation_id'  	=> $this->request->get['quotation_id'],
				'filter_supplier_id'  	=> $this->request->get['supplier_id'],
				'start'        					=> 0,
				'limit'        					=> $limit
			);

			$this->load->model('setting/setting');
			$moduleConfigSetting = $this->model_setting_setting->getSetting('module_oc_pom', $this->config->get('config_store_id'));

			$results = $this->_poQuotation->getRemainingSupplierProducts($filter_data);

			foreach ($results as $result) {
					$json[] = array(
							'supplier_id' 	=> $result['supplier_id'],
							'product_id' 		=> $result['product_id'],
							'name'       		=> strip_tags(html_entity_decode($result['product_name'], ENT_QUOTES, 'UTF-8')),
							'supplier_sku' 	=> strip_tags(html_entity_decode($result['supplier_sku'], ENT_QUOTES, 'UTF-8')),
							'supplier_cost' => $result['supplier_cost'],
							'supplier_qty' 	=> isset($result['supplier_qty']) ? $result['supplier_qty'] : $moduleConfigSetting['module_oc_pom_quotation_quantity'],
							'supplier_tax' 	=> isset($moduleConfigSetting['module_oc_pom_tax_cost']) ? $moduleConfigSetting['module_oc_pom_tax_cost'] : 0,
					);
			 }
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocompleteProduct() {
		$json = array();

		if (isset($this->request->get['filter_name']) && $this->request->get['supplier_id']) {
				$this->load->model('setting/setting');
				$this->load->model('po_mgmt/po_supplier');
				$this->load->model('tool/image');
				$this->load->model('catalog/product');
				$this->load->model('catalog/option');

				if (isset($this->request->get['filter_name'])) {
					$filter_name = $this->request->get['filter_name'];
				} else {
					$filter_name = '';
				}

				if (isset($this->request->get['limit'])) {
					$limit = $this->request->get['limit'];
				} else {
					$limit = 5;
				}

				$filter_data = array(
					'filter_product_name'  	=> $filter_name,
					'filter_supplier_id'  	=> $this->request->get['supplier_id'],
					'start'        					=> 0,
					'limit'        					=> $limit
				);
				if(isset($this->request->get['quotation_id'])){
						$filter_data['filter_quotation_id'] = $this->request->get['quotation_id'];
				}

				$results 		= $this->model_po_mgmt_po_supplier->getSupplierProducts($filter_data);

				foreach ($results as $key => $s_product) {
						if (is_file(DIR_IMAGE . $s_product['image'])) {
							$image = $this->model_tool_image->resize($s_product['image'], 60, 60);
						} else {
							$image = $this->model_tool_image->resize('no_image.png', 60, 60);
						}
						$supplier_cost 	= isset($s_product['supplier_cost']) ? $s_product['supplier_cost'] : '0.00';
						$supplier_qty  	= $this->config->get('module_oc_pom_quotation_quantity') ? $this->config->get('module_oc_pom_quotation_quantity') : 1;

						// Options
						$product_options = $this->model_catalog_product->getProductOptions($s_product['product_id']);

						$productOptions = array();
						foreach ($product_options as $product_option) {
								$option_info = $this->model_catalog_option->getOption($product_option['option_id']);
								if($option_info){
										$product_option_value_data = array();

										foreach ($product_option['product_option_value'] as $product_option_value) {
											$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

											if ($option_value_info) {
												$product_option_value_data[] = array(
													'product_option_value_id' => $product_option_value['product_option_value_id'],
													'option_value_id'         => $product_option_value['option_value_id'],
													'name'                    => $option_value_info['name'],
													'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
													'price_prefix'            => $product_option_value['price_prefix']
												);
											}
										}

										$productOptions[] = array(
											'product_option_id'    => $product_option['product_option_id'],
											'product_option_value' => $product_option_value_data,
											'option_id'            => $product_option['option_id'],
											'name'                 => $option_info['name'],
											'type'                 => $option_info['type'],
											'value'                => $product_option['value'],
											'required'             => $product_option['required']
										);
								}
						}

						$json[$s_product['product_id']] = array(
							'key' 						=> str_replace("=","",base64_encode(serialize(array('product_id'=>$s_product['product_id'], rand(pow(10, 3), pow(10, 4)-1))))),
							'product_id'			=> $s_product['product_id'],
							'image'      			=> $image,
							'product_name'		=> strip_tags(html_entity_decode($s_product['name'], ENT_QUOTES, 'UTF-8')),
							'model'						=> $s_product['model'],
							'price'      			=> $s_product['price'],
							'options'					=> $productOptions,
							'supplier_id'			=> $s_product['supplier_id'],
							'supplier_sku'		=> strip_tags(html_entity_decode($s_product['supplier_sku'], ENT_QUOTES, 'UTF-8')),
							'supplier_cost'		=> $supplier_cost,
							'supplier_qty'		=> $supplier_qty,
							'supplier_tax'		=> $this->config->get('oc_pom_tax_cost') ? $this->config->get('oc_pom_tax_cost') : 0,
						);
			 }
		 }

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function addMoreProduct() {

		$json = array();

		$this->load->language('po_mgmt/po_quotation');
		if(!isset($this->request->get['quotation_id'])){
			$json['redirect'] = $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'], true);
		}

		$this->document->setTitle($this->language->get('heading_title_view'));
		$url = '';
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateMoreProduct()) {
				$json['success'] = true;
				$this->request->post['quotation_id'] = $this->request->get['quotation_id'];
				$this->_poQuotation->updateQuoteProductData($this->request->post);

				$this->session->data['success'] = $this->language->get('text_success_more_product');

			  if (isset($this->request->get['quotation_id'])) {
				  $url .= '&quotation_id=' . $this->request->get['quotation_id'];
			  }
				$json['redirect'] = $this->url->link('po_mgmt/po_quotation/getViewForm', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$json['error'] = $this->error;
		}
		$this->response->addHeader('Content-Type:application/json');
		$this->response->setOutput(json_encode($json));
		// $this->getViewForm($this->request->get['quotation_id']);
	}

	public function validateMoreProduct(){
		$this->load->language('po_mgmt/po_quotation');
		$this->load->model('catalog/product');
		$post_data = $this->request->post;
		if(isset($post_data['more_product']) && !empty($post_data['more_product'])){
				foreach ($post_data['more_product'] as $key_index => $product_data) {
						if(isset($more_product[$key_index]['options'])){
								$options = $more_product[$key_index]['options'];
						}else{
								$options = array();
						}
						$product_options = $this->model_catalog_product->getProductOptions($product_data['product_id']);
						if(!empty($product_options)){
								foreach ($product_options as $product_option) {
										if ($product_option['required'] && empty($post_data['more_product'][$key_index]['options'][$product_option['product_option_id']])){
												$this->error['more_product'][$key_index]['options'][$product_option['product_option_id']] = sprintf($this->language->get('error_required_field'), $product_option['name']);
										}
										if (!$product_option['required'] && empty($post_data['more_product'][$key_index]['options'][$product_option['product_option_id']])){
												unset($this->request->post['more_product'][$key_index]['options'][$product_option['product_option_id']]);
										}
								}
						}
						if(!is_numeric($product_data['supplier_cost'])){
							$this->error['more_product'][$key_index]['supplier_cost'] = $this->language->get('error_supplier_cost');
						}
						if(!preg_match('/^[1-9][0-9]*$/', $product_data['supplier_qty'])){
							$this->error['more_product'][$key_index]['supplier_qty'] = $this->language->get('error_supplier_qty');
						}

						if(empty($this->error)){
							$option_data = array();
							if(isset($product_data['options']) && !empty($product_data['options'])){
									$this->request->post['more_product'][$key_index]['option_data'] = $this->getOptionData($product_data['options'], $product_data['product_id']);
							}
						}
				}
		}else{
			$this->error['more_product']['warning'] = $this->language->get('error_choose_quotation_product');
		}

		return !$this->error;
	}

	public function saveCommentHistory(){
			$json = array();
			$this->load->language('po_mgmt/po_quotation');

			if(isset($this->request->post['quotation_id']) && $this->request->post['quotation_id']){
				if ((utf8_strlen(trim($this->request->post['comment'])) < 5) || (utf8_strlen(trim($this->request->post['comment'])) > 500)) {
						$json['warning'] = $this->language->get('error_comment_length');
				}
				if(!isset($json['warning'])){
						$this->_poQuotation->saveQuoteCommentHistory($this->request->post);
						$json['success'] = true;
						$this->session->data['success'] = $this->language->get('text_success_save_comment');
				}
			}else{
				$json['warning'] = $this->language->get('error_wrong_product_data');
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
	}

	public function updateQuotationStatus(){
			$json = array();
			$this->load->language('po_mgmt/po_quotation');
			$quotation_status = array('1' => 'Pending','2' => 'Convert To PO','3' => 'Confirm Order', '4' => 'Completed','5' => 'Cancel', '6' => 'Cancel');
			if(isset($this->request->post['quotation_id']) && $this->request->post['quotation_id'] && isset($this->request->post['status'])){
				$getQuoteDetails = $this->_poQuotation->getQuotation($this->request->post['quotation_id']);
				if(!empty($getQuoteDetails) && $this->request->post['status']){
						if($getQuoteDetails['quotation_status'] >= $this->request->post['status']){
							$json['warning'] = $this->language->get('error_wrong_status_selection');
						}else{
							$this->request->post['status_text']	= $quotation_status[$this->request->post['status']];
							$this->_poQuotation->updateQuoteStatus($this->request->post);
							$json['success'] = true;
							$this->session->data['success'] = $this->language->get('text_success_status_updated');
							if($this->request->post['status'] == 2){
									
									$this->_poQuotation->saveQuotationCost($this->request->post['quotation_id']);
									$json['redirect'] = html_entity_decode($this->url->link('po_mgmt/po_order/getForm', 'user_token=' . $this->session->data['user_token'].'&purchase_id='.$this->request->post['quotation_id'], true));
							}else{
									$json['redirect'] = html_entity_decode($this->url->link('po_mgmt/po_quotation/getViewForm', 'user_token=' . $this->session->data['user_token'].'&quotation_id='.$this->request->post['quotation_id'], true));
							}
						}
				}else{
					$json['warning'] = $this->language->get('error_quotation_details_missing');
				}
			}else{
				$json['warning'] = $this->language->get('error_wrong_quotation');
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
	}

	public function getOptionData($options, $product_id){
		$option_data 	= array();
		foreach ($options as $product_option_id => $value) {
			$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

			if ($option_query->num_rows) {
					if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
						$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

							if ($option_value_query->num_rows) {
									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $value,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'price_prefix'						=> $option_value_query->row['price_prefix'],
										'price'										=> $option_value_query->row['price'],
									);
							}
					} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
									$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

									if ($option_value_query->num_rows) {
											$option_data[] = array(
												'product_option_id'       => $product_option_id,
												'product_option_value_id' => $product_option_value_id,
												'option_id'               => $option_query->row['option_id'],
												'option_value_id'         => $option_value_query->row['option_value_id'],
												'name'                    => $option_query->row['name'],
												'value'                   => $option_value_query->row['name'],
												'type'                    => $option_query->row['type'],
												'price_prefix'						=> $option_value_query->row['price_prefix'],
												'price'										=> $option_value_query->row['price'],
											);
									}
							}
					} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => '',
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => '',
									'name'                    => $option_query->row['name'],
									'value'                   => $value,
									'type'                    => $option_query->row['type'],
									'price_prefix'						=> '+',
									'price'										=> 0,
								);
					}
			}
		}
		return $option_data;
	}

	public function array_column_alternate($array_data = array(), $column_name){
			$returnArr = array();
			foreach ($array_data as $key => $ArrVal) {
					if(isset($ArrVal[$column_name])){
							array_push($returnArr, $ArrVal[$column_name]);
					}
			}
			return $returnArr;
	}
}
