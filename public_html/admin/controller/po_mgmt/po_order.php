<?php
class ControllerPOMgmtPoOrder extends Controller {
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
    $this->load->model('po_mgmt/po_order');
    $this->_poOrder = $this->model_po_mgmt_po_order;
		if (!$this->config->get('module_oc_pom_status')) {
			$this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
		}
  }

  public function index() {
    $this->load->language('po_mgmt/po_order');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->getList();
  }

	protected function getList() {
    $data = array();
		$data = array_merge($data, $this->load->language('po_mgmt/po_order'));

    if (isset($this->request->get['filter_purchase_id'])) {
			$filter_purchase_id = $this->request->get['filter_purchase_id'];
		} else {
			$filter_purchase_id = null;
		}

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

    if (isset($this->request->get['filter_owner_name'])) {
			$filter_owner_name = $this->request->get['filter_owner_name'];
		} else {
			$filter_owner_name = null;
		}

		if (isset($this->request->get['filter_purchase_date'])) {
			$filter_purchase_date = $this->request->get['filter_purchase_date'];
		} else {
			$filter_purchase_date = '';
		}

		if (isset($this->request->get['filter_grand_total'])) {
			$filter_grand_total = $this->request->get['filter_grand_total'];
		} else {
			$filter_grand_total = null;
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

		if (isset($this->request->get['filter_purchase_id'])) {
			$url .= '&filter_purchase_id=' . urlencode(html_entity_decode($this->request->get['filter_purchase_id'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_owner_name'])) {
			$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_purchase_date'])) {
			$url .= '&filter_purchase_date=' . $this->request->get['filter_purchase_date'];
		}

		if (isset($this->request->get['filter_grand_total'])) {
			$url .= '&filter_grand_total=' . $this->request->get['filter_grand_total'];
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
			'href' => $this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['delete'] = $this->url->link('po_mgmt/po_order/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['quotation_status'] = $quotation_status = array('2' => 'Converted To PO',
																													'3' => 'Confirm Order',
																													'4' => 'Completed',
																													'5' => 'Cancel',
																												);
		$quotation_status_color = array('2' => '#0196FC',
																		'3' => '#7b3d1a',
																		'4' => '#359800',
																		'5' => '#FF0000',
																	);

		$data['po_orders'] = array();

		$filter_data = array(
      'filter_purchase_id'	     => $filter_purchase_id,
			'filter_order_id'	     		 => $filter_order_id,
      'filter_owner_name'	       => $filter_owner_name,
			'filter_purchase_date'     => $filter_purchase_date,
			'filter_grand_total'			 => $filter_grand_total,
			'filter_status'            => $filter_status,
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

		$supplier_total = $this->_poOrder->getTotalPurchaseOrders($filter_data);

		$results = $this->_poOrder->getPurchaseOrders($filter_data);

    if(!empty($results))
  		foreach ($results as $result) {

				$ordered_qty = $received_qty = 0;
				if(isset($result['id'])){
						$getQuoteProducts = $this->_poOrder->getOrderProducts($result['id']);
						foreach ($getQuoteProducts as $key => $product) {
								$ordered_qty = $ordered_qty + $product['ordered_qty'];
								$received_qty= $received_qty + $product['received_qty'];
						}
				}

  			$data['po_orders'][] = array(
          'id'            => $result['id'],
  				'order_id'  		=> '#'.$result['quotation_id'],
					'oc_order_id'  	=> $result['order_id'] ? $result['order_id'] : '--N/A--',
					'purchase_date' => $result['purchase_date'],
          'supplier_name' => $result['owner_name'],
          'ordered_qty'   => $ordered_qty,
					'received_qty'  => $received_qty,
					'paid_total' 		=> $result['paid_total'],
          'ordered_total' => $result['total'],
  				'status'        => isset($quotation_status[$result['quotation_status']]) ? $quotation_status[$result['quotation_status']] : 'Cancel',
					'status_color'  => $quotation_status_color[$result['quotation_status']],
          'view'          => $this->url->link('po_mgmt/po_order/getForm', 'user_token=' . $this->session->data['user_token'] . $url . '&purchase_id=' . $result['id'], true),
					'oc_order_view' => $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&po_mgmt=true&order_id=' . $result['order_id'], true),
  			);
  		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['clear'] = $this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'], true);

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

		if (isset($this->request->get['filter_purchase_id'])) {
			$url .= '&filter_purchase_id=' . urlencode(html_entity_decode($this->request->get['filter_purchase_id'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_owner_name'])) {
			$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_purchase_date'])) {
			$url .= '&filter_purchase_date=' . $this->request->get['filter_purchase_date'];
		}

		if (isset($this->request->get['filter_grand_total'])) {
			$url .= '&filter_grand_total=' . $this->request->get['filter_grand_total'];
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

    $data['sort_quotation_id'] = $this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'] . '&sort=q.quotation_id' . $url, true);
		$data['sort_oc_order_id'] = $this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'] . '&sort=q.order_id' . $url, true);
		$data['sort_owner_name'] = $this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'] . '&sort=sd.owner_name' . $url, true);
		$data['sort_grand_total'] = $this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'] . '&sort=q.total' . $url, true);
		$data['sort_created_date'] = $this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'] . '&sort=q.purchase_date' . $url, true);
		$data['sort_status'] = $this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'] . '&sort=q.status' . $url, true);

		$url = '';

    if (isset($this->request->get['filter_purchase_id'])) {
			$url .= '&filter_purchase_id=' . urlencode(html_entity_decode($this->request->get['filter_purchase_id'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_owner_name'])) {
			$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_purchase_date'])) {
			$url .= '&filter_purchase_date=' . $this->request->get['filter_purchase_date'];
		}

		if (isset($this->request->get['filter_grand_total'])) {
			$url .= '&filter_grand_total=' . $this->request->get['filter_grand_total'];
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
		$pagination->url = $this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($supplier_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($supplier_total - $this->config->get('config_limit_admin'))) ? $supplier_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $supplier_total, ceil($supplier_total / $this->config->get('config_limit_admin')));

    $data['filter_purchase_id']  	= $filter_purchase_id ? '#'.$filter_purchase_id : $filter_purchase_id;
		$data['filter_order_id']    	= $filter_order_id;
    $data['filter_owner_name']    = $filter_owner_name;
		$data['filter_purchase_date'] = $filter_purchase_date;
		$data['filter_grand_total'] 	= $filter_grand_total;
    $data['filter_status']        = $filter_status;
		$data['sort']                 = $sort;
		$data['order']                = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('po_mgmt/po_order_list', $data));
	}

	public function delete() {
		$this->load->language('po_mgmt/po_order');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->_poOrder->deletePurchaseOrder($product_id);
			}

            foreach ($this->request->post['selected'] as $quoatation_id) {
                $this->load->model('sale/order');
                $this->model_sale_order->deletePO($quoatation_id);
                $this->_poOrder->deletePurchaseOrder($quoatation_id);
            }

			$this->session->data['success'] = $this->language->get('text_success_delete');

      $url = '';

			if (isset($this->request->get['filter_purchase_id'])) {
				$url .= '&filter_purchase_id=' . urlencode(html_entity_decode($this->request->get['filter_purchase_id'], ENT_QUOTES, 'UTF-8'));
			}

	    if (isset($this->request->get['filter_owner_name'])) {
				$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_purchase_date'])) {
				$url .= '&filter_purchase_date=' . $this->request->get['filter_purchase_date'];
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

			$this->response->redirect($this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'po_mgmt/po_order')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function getForm() {
		$data = array();
		$data = array_merge($data, $this->load->language('po_mgmt/po_order'));

		$this->document->setTitle($this->language->get('heading_title_view'));

		$data['purchase_id'] = $this->request->get['purchase_id'];

		if (isset($this->request->get['purchase_id'])) {
		  $purchase_id = $this->request->get['purchase_id'];
		} else {
		  $purchase_id = null;
		}

		if (isset($this->request->get['page'])) {
		  $page = $this->request->get['page'];
		} else {
		  $page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_purchase_id'])) {
		  $url .= '&filter_purchase_id=' . urlencode(html_entity_decode($this->request->get['filter_purchase_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_owner_name'])) {
			$url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['purchase_id'])) {
		  $url .= '&purchase_id=' . $this->request->get['purchase_id'];
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
			'href' => $this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token']. $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_view'),
			'href' => $this->url->link('po_mgmt/po_order/getForm', 'user_token=' . $this->session->data['user_token']. $url, true)
		);

		$filter_data = array(
		  'quotation_id'	     => $purchase_id,
		  'start'              => ($page - 1) * $this->config->get('config_limit_admin'),
		  'limit'              => $this->config->get('config_limit_admin')
		);

		$history_total = 0;

		if(isset($this->request->post['schedule_date_next'])){
			$data['schedule_date_next'][$purchase_id][$this->request->post['product_id']] = $this->request->post['schedule_date_next'];
		}else{
				$data['schedule_date_next'] = array();
		}

		if(isset($this->request->post['shipment_comment'])){
				$data['shipment_comment'][$purchase_id][$this->request->post['product_id']] = $this->request->post['shipment_comment'];
		}else{
				$data['shipment_comment'] = array();
		}

		if(isset($this->error['error_schedule_date'])){
			$data['error_schedule_date'] = $this->error['error_schedule_date'];
		}else{
			$data['error_schedule_date'] = array();
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

		if (isset($this->request->get['filter_purchase_id'])) {
		  $url .= '&filter_purchase_id=' . urlencode(html_entity_decode($this->request->get['filter_purchase_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_owner_name'])) {
		  $url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['purchase_id'])) {
		  $url .= '&purchase_id=' . $this->request->get['purchase_id'];
		}

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('po_mgmt/po_order/getViewForm', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($history_total - $this->config->get('config_limit_admin'))) ? $history_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $history_total, ceil($history_total / $this->config->get('config_limit_admin')));

		$quotation_info = array();
		$data['oc_view_link'] 	= false;
		if (isset($this->request->get['purchase_id'])) {
			$quotation_details = $this->_poOrder->getPurchaseOrders(array('filter_id' => $this->request->get['purchase_id']));
				if(isset($quotation_details[0]['id']) && $quotation_details[0]['id'] == $this->request->get['purchase_id']){
						$quotation_info = $quotation_details[0];
				}

				if(!empty($quotation_info)){
						if($quotation_info['order_id'] != 0){
							$data['oc_view_link'] = $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $quotation_info['order_id'], true);
						}
						$data['heading_order_id'] 				= $data['heading_order_id'].$quotation_info['quotation_id'];
						$data['format_purchase_id'] 			= $quotation_info['quotation_id'];
						$data['supplier_id'] 							= $quotation_info['supplier_id'];
						$quotation_info['supplier_link']	=	$this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token']. '&supplier_id='.$quotation_info['supplier_id'], true);

						$quotation_info['shipping_start_date'] 		= date('j F Y, H:i A', strtotime($quotation_info['shipping_start_date']));
						$data['purchase_date'] 										= date('j F Y, H:i A', strtotime($quotation_info['purchase_date']));
						$data['quotation_status'] 								= array('2' => 'Converted To PO',
																															'3' => 'Confirm Order',
																															'4' => 'Completed',
																															'5' => 'Cancel',);
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

						$quotation_products = $this->_poOrder->getOrderProducts($quotation_info['id']);

						foreach ($quotation_products as $key => $q_product) {
								if($q_product['schedule_date']){
										$expected_date1 = new DateTime($shipping_expected_date);
										$schedule_date2 = new DateTime($q_product['schedule_date']);

										if($schedule_date2 > $expected_date1){
											$data['quotation_info']['shipping_expected_date'] = date('j F Y, H:i A', strtotime($q_product['schedule_date']));
										}
								}
								$option_data = array();
								$options = $this->_poOrder->getQuotationOptions($q_product['quotation_id'], $q_product['id']);

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

								$shipment_histories = array();

								$getProductShipment = $this->_poOrder->getOrderProductShipment(array('quotation_id' => $quotation_info['id'], 'product_id' => $q_product['product_id'], 'po_product_id' => $q_product['id']));
								if(!empty($getProductShipment)){
										foreach ($getProductShipment as $key => $shipment) {
												$shipment_histories[] = array(
																									'id' 						=> $shipment['id'],
																									'po_product_id' => $shipment['quotation_product_id'],
																									'shipment_id' 	=> $quotation_info['iso_code_2'].'/ '.sprintf('%04d', $shipment['id']),
																									'received_qty' 	=> $shipment['received_qty'],
																									'date' 					=> date('j F Y, H:i A', strtotime($shipment['date'])),
																									'comment' 			=> $shipment['comment'],
																								);
										}
								}

								$sub_total = $sub_total + ($q_product['ordered_cost'] * $q_product['ordered_qty']);
								$tax_total = $tax_total + (($q_product['ordered_cost'] * $q_product['ordered_qty']) * $q_product['tax']) / 100;
								if(isset($q_product['schedule_date']) && $q_product['schedule_date']){
										$schedule_date = date('j F Y, H:i A', strtotime($q_product['schedule_date']));
								}else{
										$schedule_date = '';
								}
								$convertWeight = $this->weight->convert($q_product['weight'], $q_product['weight_class_id'], $this->config->get('config_weight_class_id'));
								$weight = $this->weight->format($convertWeight, $this->config->get('config_weight_class_id'), '.', ',');
								$oc_view_link = false;
								if($quotation_info['order_id'] != 0){
									$oc_view_link = $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $quotation_info['order_id'], true);
								}

								$data['quotation_products'][] = array(
									'row_id'					=> $q_product['id'],
									'product_id'			=> $q_product['product_id'],
									'oc_order_id'			=> $quotation_info['order_id'],
									'image'      			=> $image,
									'product_name'		=> $q_product['name'],
									'model'						=> $q_product['model'],
									'options'					=> $option_data,
									'weight'					=> $weight,
									'supplier_id'			=> $q_product['supplier_id'],
									'supplier_sku'		=> $q_product['supplier_sku'],
									'ordered_qty'			=> $q_product['ordered_qty'],
									'tax_rate'				=> $q_product['tax'],
									'received_qty'		=> $q_product['received_qty'],
									'remaining_qty'		=> $q_product['ordered_qty'] - $q_product['received_qty'],
									'ordered_cost'		=> isset($q_product['ordered_cost']) ? $q_product['ordered_cost'] : $q_product['supplier_cost'],
									'sub_total'				=> $this->currency->format($q_product['sub_total'], $this->config->get('config_currency')),
									'schedule_date'		=> $schedule_date ? $schedule_date : $quotation_info['shipping_expected_date'],
									'shipment_print'	=> $this->url->link('po_mgmt/po_order/print_shipment', 'user_token=' . $this->session->data['user_token'] . '&quote_id='.$quotation_info['id'].'&product_id='.$q_product['product_id'], true),
									'shipment_histories'	=> array_reverse($shipment_histories),
								);
						}
						$this->load->model('setting/setting');
						$moduleConfigSetting = $this->model_setting_setting->getSetting('oc_pom', $this->config->get('config_store_id'));
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

						if (isset($this->request->get['filter_purchase_id'])) {
						  $url .= '&filter_purchase_id=' . urlencode(html_entity_decode($this->request->get['filter_purchase_id'], ENT_QUOTES, 'UTF-8'));
						}

						if (isset($this->request->get['filter_owner_name'])) {
						  $url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
						}

						if (isset($this->request->get['purchase_id'])) {
						  $url .= '&purchase_id=' . $this->request->get['purchase_id'];
						}

						$data['user_token'] 						= $this->session->data['user_token'];
						$data['add_more_product'] =	$this->url->link('po_mgmt/po_order/addMoreProduct', 'user_token=' . $this->session->data['user_token'] . $url, true);
						$data['quantity_received']=	$this->url->link('po_mgmt/po_order/updateReceiveQuantity', 'user_token=' . $this->session->data['user_token'] . $url, true);
						$data['shipment_update']=	$this->url->link('po_mgmt/po_order/updateShipment', 'user_token=' . $this->session->data['user_token'] . $url, true);
						$data['print_po'] 				=	$this->url->link('po_mgmt/po_order/print_po', 'user_token=' . $this->session->data['user_token'] . $url, true);
						$data['cancel'] 					= $this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'], true);
						$data['header'] 					= $this->load->controller('common/header');
						$data['column_left'] 			= $this->load->controller('common/column_left');
						$data['footer'] 					= $this->load->controller('common/footer');

						$this->response->setOutput($this->load->view('po_mgmt/po_order_form', $data));
				}else{
						$this->response->redirect($this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'], true));
				}
			}else{
					$this->response->redirect($this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'], true));
			}
	}

	public function print_shipment(){
		$data = $data['quotation_detail'] = $quote_detail = $shipment_histories = $order_details 	= $quoteProduct = array();
		$data = array_merge($data, $this->load->language('po_mgmt/po_order'));
		$this->document->setTitle($this->language->get('heading_print_shipment'));
			if (isset($this->request->get['quote_id']) && isset($this->request->get['product_id'])) {
					$quotation_id = $this->request->get['quote_id'];
					$quotation_details = $this->_poOrder->getPurchaseOrders(array('filter_id' => $quotation_id));
					if(isset($quotation_details[0]['id']) && $quotation_details[0]['id'] == $quotation_id){
							$quote_detail 	= $quotation_details[0];
							$this->load->model('tool/image');
							$this->load->model('tool/upload');
							if($quote_detail['order_id']){
									$this->load->model('sale/order');
									if($getOrderDetail = $this->model_sale_order->getOrder($quote_detail['order_id'])){
											$order_details = array(
																				'order_id' 					=> '#'.$getOrderDetail['order_id'],
																				'customer_name' 		=> Ucfirst($getOrderDetail['firstname']).' '.$getOrderDetail['lastname'],
																				'customer_email'		=> $getOrderDetail['email'],
																				'customer_address' 	=> $getOrderDetail['shipping_address_1'].', '.$getOrderDetail['shipping_city'].', '.$getOrderDetail['shipping_zone'].', '.$getOrderDetail['shipping_country'],
																			);
									}
							}

							$quotationProduct = $this->_poOrder->getOrderProductData(array('quotation_id' => $quotation_id, 'product_id' => $this->request->get['product_id']));

							$shipping_status 	= '';
							$shipping_last_id = 0;
							if(!empty($quotationProduct)){
									$option_data = array();
									$options = $this->_poOrder->getQuotationOptions($quotation_id, $quotationProduct['id']);

									foreach ($options as $option) {
											if ($option['type'] != 'file') {
													$option_data[] = array(
														'name'  => $option['name'],
														'value' => $option['value'],
													);
											} else {
													$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
													if ($upload_info) {
														$option_data[] = array(
															'name'  => $option['name'],
															'value' => $upload_info['name'],
														);
													}
											}
									}

									if (is_file(DIR_IMAGE . $quotationProduct['image'])) {
										$image = $this->model_tool_image->resize($quotationProduct['image'], 60, 60);
									} else {
										$image = $this->model_tool_image->resize('no_image.png', 60, 60);
									}
									$quoteProduct = array(
												'product_image'	=> $image,
												'product_name'	=> $quotationProduct['name'],
												'product_sku'		=> $quotationProduct['supplier_sku'] ? $quotationProduct['supplier_sku'] : $quotationProduct['model'],
												'options'				=> $option_data,
												'ordered_cost'	=> $this->currency->format($quotationProduct['ordered_cost'], $this->config->get('config_currency')),
												'ordered_qty'		=> $quotationProduct['ordered_qty'],
												'received_qty'	=> $quotationProduct['received_qty'],
												'expected_date'	=> date('j F Y, H:i A', strtotime($quotationProduct['schedule_date']))
									);

									$getProductShipment = $this->_poOrder->getOrderProductShipment(array('quotation_id' => $quotation_id, 'product_id' => $quotationProduct['product_id']));

									if(!empty($getProductShipment)){
											foreach ($getProductShipment as $key => $shipment) {
													$shipment_histories[] = array(
																'id' 							=> $shipment['id'],
																'po_product_id' 	=> $shipment['quotation_product_id'],
																'shipment_id' 		=> $quote_detail['iso_code_2'].'/ '.sprintf('%04d', $shipment['id']),
																'received_qty' 		=> $shipment['received_qty'],
																'added_date' 			=> date('j F Y, H:i A', strtotime($shipment['date'])),
																'expected_date' 	=> date('j F Y, H:i A', strtotime($quotationProduct['schedule_date'] ? $quotationProduct['schedule_date'] : $shipment['date'])),
																'comment' 				=> $shipment['comment'],
													);
											}
									}
							}

							$quotation_status	= array('2' => 'Converted To PO','3' => 'Confirm Order','4' => 'Completed','5' => 'Cancel',);

							$data['quotation_detail'] = array(
										'quotation_id' 			=> $quotation_id,
										'po_id' 			 			=> '#'.$quote_detail['quotation_id'],
										'po_status' 				=> $quotation_status[$quote_detail['quotation_status']],
										'po_date'						=> date('j F Y, H:i A', strtotime($quote_detail['purchase_date'])),
										'oc_order_id' 			=> $quote_detail['order_id'] ? $quote_detail['order_id'] : '-N/A-',
										'order_details' 		=> $order_details,
										'supplier_name' 		=> $quote_detail['owner_name'],
										'supplier_company' 	=> $quote_detail['company_name'],
										'supplier_email'		=> $quote_detail['email'],
										'supplier_telephone'=> $quote_detail['telephone'],
										'supplier_address'	=> $quote_detail['street'].', '.$quote_detail['country_name'].' '. $quote_detail['postcode'],
										'product_detail'		=> $quoteProduct,
										'shipment_status'		=> $shipping_status,
										'shipping_method'		=> $quote_detail['shipping_method'],
										'shipping_cost'			=> $this->currency->format($quote_detail['shipping_cost'], $this->config->get('config_currency')),
										'shipment_histories'=> array_reverse($shipment_histories),
							);

						if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
							$data['store_logo'] = $this->model_tool_image->resize($this->config->get('config_logo'), 300, 100);
						} else {
							$data['store_logo'] = $this->model_tool_image->resize('no_image.png', 300, 100);
						}
						$data['config_name']	= $this->config->get('config_name');
						$data['config_owner']	= $this->config->get('config_owner');
						$data['config_email']	= $this->config->get('config_email');

						$data['user_token'] 						= $this->session->data['user_token'];
						$data['header'] 					= $this->load->controller('common/header');

						$this->response->setOutput($this->load->view('po_mgmt/po_print_shipment', $data));
				}else{
						$this->response->redirect($this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'], true));
				}
			}else{
					$this->response->redirect($this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'], true));
			}
	}

	public function print_po(){
		$data = array();
		$data = array_merge($data, $this->load->language('po_mgmt/po_order'));

		$quotation_info = array();

		if (isset($this->request->get['purchase_id'])) {
			$quotation_details = $this->_poOrder->getPurchaseOrders(array('filter_id' => $this->request->get['purchase_id']));
				if(isset($quotation_details[0]['id']) && $quotation_details[0]['id'] == $this->request->get['purchase_id']){
						$quotation_info = $quotation_details[0];
				}

				if(!empty($quotation_info)){
						$data['heading_order_id'] 				= $data['heading_order_id'].$quotation_info['quotation_id'];
						$quotation_info['supplier_link']	=	$this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token']. '&supplier_id='.$quotation_info['supplier_id'], true);

						$quotation_info['shipping_start_date'] 		= date('j F Y, H:i A', strtotime($quotation_info['shipping_start_date']));
						$data['purchase_date'] 										= date('j F Y, H:i A', strtotime($quotation_info['purchase_date']));
						$data['quotation_status'] 								= array('2' => 'Converted To PO',
																															'3' => 'Confirm Order',
																															'4' => 'Completed',
																															'5' => 'Cancel',);
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

						$quotation_products = $this->_poOrder->getOrderProducts($quotation_info['id']);
						foreach ($quotation_products as $key => $q_product) {
								if($q_product['schedule_date']){
									$expected_date1 = new DateTime($shipping_expected_date);
									$schedule_date2 = new DateTime($q_product['schedule_date']);
									if($schedule_date2 > $expected_date1){
										$data['quotation_info']['shipping_expected_date'] = date('j F Y, H:i A', strtotime($q_product['schedule_date']));
									}
								}
								$option_data = array();
								$options = $this->_poOrder->getQuotationOptions($q_product['quotation_id'], $q_product['id']);

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

								$shipment_histories = array();

								$getProductShipment = $this->_poOrder->getOrderProductShipment(array('quotation_id' => $quotation_info['id'], 'product_id' => $q_product['product_id']));
								if(!empty($getProductShipment)){
										foreach ($getProductShipment as $key => $shipment) {
												$shipment_histories[] = array(
																									'id' 						=> $shipment['id'],
																									'po_product_id' => $shipment['quotation_product_id'],
																									'shipment_id' 	=> $quotation_info['iso_code_2'].'/ '.sprintf('%04d', $shipment['id']),
																									'received_qty' 	=> $shipment['received_qty'],
																									'date' 					=> date('j F Y, H:i A', strtotime($shipment['date'])),
																									// 'date' 					=> date('jS F Y', strtotime($shipment['date'])),
																									'comment' 			=> $shipment['comment'],
																								);
										}
								}
								$convertWeight = $this->weight->convert($q_product['weight'], $q_product['weight_class_id'], $this->config->get('config_weight_class_id'));
								$weight = $this->weight->format($convertWeight, $this->config->get('config_weight_class_id'), '.', ',');

								$sub_total = $sub_total + ($q_product['ordered_cost'] * $q_product['ordered_qty']);
								$tax_total = $tax_total + (($q_product['ordered_cost'] * $q_product['ordered_qty']) * $q_product['tax']) / 100;
								$data['quotation_products'][] = array(
									'product_id'			=> $q_product['product_id'],
									'image'      			=> $image,
									'product_name'		=> $q_product['name'],
									'model'						=> $q_product['model'],
									'options'					=> $option_data,
									'weight'					=> $weight,
									'supplier_id'			=> $q_product['supplier_id'],
									'supplier_sku'		=> $q_product['supplier_sku'],
									'ordered_qty'			=> $q_product['ordered_qty'],
									'tax_rate'				=> $q_product['tax'],
									'received_qty'		=> $q_product['received_qty'],
									'remaining_qty'		=> $q_product['ordered_qty'] - $q_product['received_qty'],
									'ordered_cost'		=> isset($q_product['ordered_cost']) ? $q_product['ordered_cost'] : $q_product['supplier_cost'],
									'sub_total'				=> $this->currency->format($q_product['sub_total'], $this->config->get('config_currency')),
									'shipment_histories'	=> array_reverse($shipment_histories),
								);
						}
						$this->load->model('setting/setting');
						$moduleConfigSetting = $this->model_setting_setting->getSetting('oc_pom', $this->config->get('config_store_id'));
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

						if (isset($this->request->get['filter_purchase_id'])) {
						  $url .= '&filter_purchase_id=' . urlencode(html_entity_decode($this->request->get['filter_purchase_id'], ENT_QUOTES, 'UTF-8'));
						}

						if (isset($this->request->get['filter_owner_name'])) {
						  $url .= '&filter_owner_name=' . urlencode(html_entity_decode($this->request->get['filter_owner_name'], ENT_QUOTES, 'UTF-8'));
						}

						if (isset($this->request->get['purchase_id'])) {
						  $url .= '&purchase_id=' . $this->request->get['purchase_id'];
						}

						if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
							$data['store_logo'] = $this->model_tool_image->resize($this->config->get('config_logo'), 300, 100);
						} else {
							$data['store_logo'] = $this->model_tool_image->resize('no_image.png', 300, 100);
						}
						$data['config_name']	= $this->config->get('config_name');
						$data['config_owner']	= $this->config->get('config_owner');
						$data['config_email']	= $this->config->get('config_email');

						$data['user_token'] 						= $this->session->data['user_token'];
						$data['header'] 					= $this->load->controller('common/header');

						$this->response->setOutput($this->load->view('po_mgmt/po_print_order', $data));
				}else{
						$this->response->redirect($this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'], true));
				}
			}else{
					$this->response->redirect($this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'], true));
			}
	}

	public function addMoreProduct() {
			$this->load->language('po_mgmt/po_order');

			if(!isset($this->request->get['purchase_id'])){
				$this->response->redirect($this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'], true));
			}

			$this->document->setTitle($this->language->get('heading_title_view'));
			$url = '';
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
					$this->request->post['quotation_id'] = $this->request->get['purchase_id'];
					$this->load->model('po_mgmt/po_quotation');
					$this->model_po_mgmt_po_quotation->updateQuoteProductData($this->request->post);

					$this->session->data['success'] = $this->language->get('text_success_more_product');

		  		if (isset($this->request->get['purchase_id'])) {
		  			$url .= '&purchase_id=' . $this->request->get['purchase_id'];
		  		}
			}
			$this->response->redirect($this->url->link('po_mgmt/po_order/getForm', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	public function updateReceiveQuantity(){
		$this->load->language('po_mgmt/po_order');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateReceiveData('receive')) {
				$this->request->post['quotation_id'] = $this->request->post['purchase_id'];
				$this->_poOrder->updateOrderQuantity($this->request->post);

				$this->session->data['success'] = sprintf($this->language->get('text_success_receive_quantity'), $this->request->post['product_id']);

				$url = '';

	  		if (isset($this->request->get['purchase_id'])) {
	  			$url .= '&purchase_id=' . $this->request->get['purchase_id'];
	  		}
				$this->response->redirect($this->url->link('po_mgmt/po_order/getForm', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function updateShipment(){
		$this->load->language('po_mgmt/po_order');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateReceiveData('shipment')) {
				$this->request->post['quotation_id'] = $this->request->post['purchase_id'];
				$this->_poOrder->updateShipment($this->request->post);

				$this->session->data['success'] = sprintf($this->language->get('text_success_shipment_updated'), $this->request->post['product_id']);

				$url = '';

	  		if (isset($this->request->get['purchase_id'])) {
	  			$url .= '&purchase_id=' . $this->request->get['purchase_id'];
	  		}
				$this->response->redirect($this->url->link('po_mgmt/po_order/getForm', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function validateReceiveData($type){
		date_default_timezone_set("Asia/Kolkata");
		if(!isset($this->request->post['purchase_id'])){
			$this->error['warning'] = $this->language->get('error_wrong_purchase');
		}

		if(!isset($this->error['warning'])){
			$getQuoteProducts = $this->_poOrder->getOrderProductData(array('quotation_id' => $this->request->post['purchase_id'], 'product_id' => $this->request->post['product_id'], 'po_product_id' => $this->request->post['po_product_id']));

			if($type == 'receive'){
					if ((utf8_strlen(trim($this->request->post['comment'])) < 5) || (utf8_strlen(trim($this->request->post['comment'])) > 500)) {
							$this->error['warning'] = $this->language->get('error_comment_length');
					}
					if(!empty($getQuoteProducts) && isset($getQuoteProducts['ordered_qty'])){
							$this->request->post['already_received'] = $getQuoteProducts['received_qty'];
							if($getQuoteProducts['ordered_qty'] < ($getQuoteProducts['received_qty'] + $this->request->post['received_qty'])){
								$this->error['warning'] = $this->language->get('error_wrong_receive_qty');
							}
					}
			}

			if($type == 'shipment'){
					if ((utf8_strlen(trim($this->request->post['shipment_comment'])) < 5) || (utf8_strlen(trim($this->request->post['shipment_comment'])) > 500)) {
							$this->error['error_schedule_date'][$this->request->post['purchase_id']][$this->request->post['product_id']]['error_shipment_comment'] = $this->language->get('error_comment_length');
					}

					if(!empty($getQuoteProducts) && isset($getQuoteProducts['ordered_qty'])){
							if(isset($this->request->post['schedule_date_next']) && $this->request->post['schedule_date_next']){
									if(!preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/", $this->request->post['schedule_date_next'])){
											$this->error['error_schedule_date'][$this->request->post['purchase_id']][$this->request->post['product_id']]['error_schedule_date_next'] = $this->language->get('error_schedule_date_next');
									}
									$currentDateTime 	= date('Y-m-d H:m:s');
									if($currentDateTime > $this->request->post['schedule_date_next']){
											$this->error['error_schedule_date'][$this->request->post['purchase_id']][$this->request->post['product_id']]['error_schedule_date_next'] = $this->language->get('error_previous_schedule_date');
									}
							}else{
									$this->error['error_schedule_date'][$this->request->post['purchase_id']][$this->request->post['product_id']]['error_schedule_date_next'] = $this->language->get('error_schedule_date_next');
							}
					}
			}
		}
		if(isset($this->error['error_schedule_date'])){
				$this->error['warning'] = $this->language->get('error_warning');
		}
		return !$this->error;
	}

	public function generatePO(){
		$json = array();

		$this->load->language('po_mgmt/po_order');
			if(isset($this->request->get['order_id']) && $this->request->get['order_id']){
					$this->load->model('sale/order');
					$this->load->model('po_mgmt/po_order');
					$order_id = $this->request->get['order_id'];

					$getOrderDetails = $this->model_sale_order->getOrder($order_id);
					if($getOrderDetails){

							$getOrderProducts = $this->model_sale_order->getOrderProducts($order_id);
							if(!empty($getOrderProducts)){

									$getSupplierArray = $this->model_po_mgmt_po_order->getSupplierProductArray($getOrderProducts, $order_id);

									if(!empty($getSupplierArray)){
	                    foreach ($getSupplierArray as $supplier_id => $s_products) {
                          $result = $this->model_po_mgmt_po_order->addPurchaseOrder($s_products, 'order_status');
													if($result){
															$json['success'] = $result;
													}
	                    }
											if(isset($json['success'])){
												$this->session->data['success'] = $this->language->get('text_success_po_create');
											}
	                }else{
											$json['warning_po_order'] = sprintf($this->language->get('error_order_product_not_assign'), $order_id);
									}
							}
					}
			}else{
				$json['warning_po_order'] = $this->language->get('error_wrong_order_selection');
			}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function generateBulkPO(){
		$json = $order_id_result = $order_id_error = array();
		$this->load->language('po_mgmt/po_order');
		if(isset($this->request->post['order_ids']) && !empty($this->request->post['order_ids'])){

					$this->load->model('sale/order');
					$this->load->model('po_mgmt/po_order');
					$order_ids = $this->request->post['order_ids'];
					foreach ($order_ids as $order_id) {
							$getOrderDetails = $this->model_sale_order->getOrder($order_id);
							$getOrderPOEntry = $this->model_po_mgmt_po_order->getPOEntry($order_id);

							if($getOrderDetails && empty($getOrderPOEntry)){
									$getOrderProducts = $this->model_sale_order->getOrderProducts($order_id);

									if(!empty($getOrderProducts)){
											$getSupplierArray = $this->model_po_mgmt_po_order->getSupplierProductArray($getOrderProducts, $order_id);

											if(!empty($getSupplierArray)){
													foreach ($getSupplierArray as $supplier_id => $s_products) {
															$result = $this->model_po_mgmt_po_order->addPurchaseOrder($s_products, 'order_status');
															if($result){
																	if(!in_array('#'.$order_id, $order_id_result)){
																			array_push($order_id_result, '#'.$order_id);
																	}
																	$json['success'] 	= true;
															}
													}
											}else{
													if(!in_array('#'.$order_id, $order_id_error)){
															array_push($order_id_error, '#'.$order_id);
													}
											}
									}
							}
					}

					if(isset($json['success'])){
						$this->session->data['success'] = sprintf($this->language->get('text_success_bulk_po_create'), implode(", ", $order_id_result));
					}
					if(!empty($order_id_error)){
							$json['warning_po_order'] = sprintf($this->language->get('error_supplier_products'), implode(", ", $order_id_error));
					}
			}else{
				$json['warning_po_order'] = $this->language->get('error_wrong_order_selection');
			}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
