<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-Purchase-Order-Mgmt
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerPoMgmtPoSupplierProduct extends Controller {
 	private $error_report = array();
  	public function __construct($registory) {
  		  parent::__construct($registory);
  		  $this->load->model('po_mgmt/po_supplier');
  		  $this->_poSupplier = $this->model_po_mgmt_po_supplier;
        if (!$this->config->get('module_oc_pom_status')) {
    			$this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
    		}
    }

    public function index() {
		$data = array();
		$data = array_merge($data, $this->load->language('po_mgmt/po_supplier_product'));

		if(isset($this->request->get['supplier_id'])) {
			$data['supplier_id'] = $supplier_id = $this->request->get['supplier_id'];
		}else{
			$data['supplier_id'] = $supplier_id = 0;
		}

		if (isset($this->request->get['filter_product_id'])) {
			$filter_product_id = $this->request->get['filter_product_id'];
		} else {
			$filter_product_id = '';
		}

		if (isset($this->request->get['filter_product_name'])) {
			$filter_product_name = $this->request->get['filter_product_name'];
		} else {
			$filter_product_name = '';
		}

		if (isset($this->request->get['filter_oc_model'])) {
			$filter_oc_model = $this->request->get['filter_oc_model'];
		} else {
			$filter_oc_model = '';
		}

		if (isset($this->request->get['filter_supplier_sku'])) {
			$filter_supplier_sku = $this->request->get['filter_supplier_sku'];
		} else {
			$filter_supplier_sku = null;
		}

		if (isset($this->request->get['filter_oc_price'])) {
			$filter_oc_price = $this->request->get['filter_oc_price'];
		} else {
			$filter_oc_price = null;
		}

		if (isset($this->request->get['filter_supplier_cost'])) {
			$filter_supplier_cost = $this->request->get['filter_supplier_cost'];
		} else {
			$filter_supplier_cost = null;
		}

		if(isset($this->request->get['tab_option'])) {
			$data['tab_option'] = $tab_option = $this->request->get['tab_option'];
		}else{
			$data['tab_option'] = $tab_option = false;
			$this->document->setTitle($this->language->get('heading_title_product'));
		}

		if($data['tab_option'] == 'assign'){
			$this->document->setTitle($this->language->get('heading_title_assign'));
			$data['heading_title_product'] = $this->language->get('heading_title_assign');
		}elseif($data['tab_option'] == 'import'){
			$data['heading_title_product'] = $this->language->get('heading_title_import');
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['tab_active']) && $this->request->get['tab_active'] == 'supplier_product')) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'id';
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['tab_active']) && $this->request->get['tab_active'] == 'supplier_product')) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['tab_active']) && $this->request->get['tab_active'] == 'supplier_product')) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		$url = '&tab_active=supplier_product';

		if (isset($this->request->get['tab_option'])) {
			$url .= '&tab_option=' . $this->request->get['tab_option'];
		}

		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
		}

		if (isset($this->request->get['filter_product_name'])) {
			$url .= '&filter_product_name=' . urlencode(html_entity_decode($this->request->get['filter_product_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_oc_model'])) {
			$url .= '&filter_oc_model=' . urlencode(html_entity_decode($this->request->get['filter_oc_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_supplier_sku'])) {
			$url .= '&filter_supplier_sku=' . $this->request->get['filter_supplier_sku'];
		}

		if (isset($this->request->get['filter_oc_price'])) {
			$url .= '&filter_oc_price=' . $this->request->get['filter_oc_price'];
		}

		if (isset($this->request->get['filter_supplier_cost'])) {
			$url .= '&filter_supplier_cost=' . $this->request->get['filter_supplier_cost'];
		}

		if (isset($this->request->get['supplier_id'])) {
			$url .= '&supplier_id=' . $this->request->get['supplier_id'];
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['tab_active']) && $this->request->get['tab_active'] == 'supplier_product')) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['tab_active']) && $this->request->get['tab_active'] == 'supplier_product')) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['tab_active']) && $this->request->get['tab_active'] == 'supplier_product')) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['import_from_ebay'] 	= $this->url->link('po_mgmt/po_suppler_product', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] 			= $this->url->link('po_mgmt/po_suppler_product/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['assign_products'] = array();

		$filter_data = array(
			'supplier_id' 								=> $supplier_id,
			'tab_option'									=> $tab_option,
			'filter_product_id'	  				=> $filter_product_id,
			'filter_product_name'	  			=> $filter_product_name,
			'filter_oc_model'							=> $filter_oc_model,
			'filter_oc_price'							=> $filter_oc_price,
			'filter_supplier_sku' 				=> $filter_supplier_sku,
			'filter_supplier_cost' 				=> $filter_supplier_cost,
			'sort'  											=> $sort,
			'order' 											=> $order,
			'start' 											=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 											=> $this->config->get('config_limit_admin')
		);

		$amazonProductTotal = $this->_poSupplier->getTotalSupplierProducts($filter_data);

		$results = $this->_poSupplier->getSupplierProducts($filter_data);

		if($results){
			foreach ($results as $result) {
				if(!isset($this->request->get['tab_option'])) {
					$type = true;
				}else{
					$type = false;
				}

				$data['assign_products'][] = array(
					'product_id' 					=> $result['product_id'],
					'product_type'				=> $type,
					'product_name'	 			=> $result['name'],
					'model'	 							=> $result['model'],
					'price'								=> $result['price'],
					'supplier_sku'				=> isset($result['supplier_sku']) ? $result['supplier_sku'] : '',
					'supplier_cost'				=> isset($result['supplier_cost']) ? $result['supplier_cost'] : 0.00,
				);
			}
		}

		$data['user_token'] 	= $this->session->data['user_token'];

		$data['currency_symbol'] = $this->config->get('config_currency');

    if (isset($this->session->data['supplier_sku'])) {
			$data['error_supplier_sku'] = $this->session->data['supplier_sku'];
			unset($this->session->data['supplier_sku']);
		} else{
			$data['error_supplier_sku'] = '';
		}

		if (isset($this->session->data['supplier_cost'])) {
			$data['error_supplier_cost'] = $this->session->data['supplier_cost'];
			unset($this->session->data['supplier_cost']);
		} else{
			$data['error_supplier_cost'] = '';
		}

		if (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];

			unset($this->session->data['warning']);
		} else if (isset($this->error_report['warning'])) {
			$data['error_warning'] = $this->error_report['warning'];
		} else {
			$data['error_warning'] = '';
		}

    if($data['error_supplier_sku'] || $data['error_warning'] || $data['error_supplier_cost']){
        if(isset($this->session->data['post_data'])){
            $data['post_data'] = $this->session->data['post_data'];

            unset($this->session->data['post_data']);
        }else{
            $data['post_data'] = array();
        }
    }else{
      if(isset($this->session->data['post_data']))
        unset($this->session->data['post_data']);

    }

		if (isset($this->session->data['success_assign'])) {
			$data['success'] = $this->session->data['success_assign'];

			unset($this->session->data['success_assign']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		$url = '&tab_active=supplier_product';

		if (isset($this->request->get['tab_option'])) {
			$url .= '&tab_option=' . $this->request->get['tab_option'];
		}

		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
		}

		if (isset($this->request->get['filter_product_name'])) {
			$url .= '&filter_product_name=' . urlencode(html_entity_decode($this->request->get['filter_product_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_oc_model'])) {
			$url .= '&filter_oc_model=' . urlencode(html_entity_decode($this->request->get['filter_oc_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_supplier_sku'])) {
			$url .= '&filter_supplier_sku=' . $this->request->get['filter_supplier_sku'];
		}

		if (isset($this->request->get['filter_oc_price'])) {
			$url .= '&filter_oc_price=' . $this->request->get['filter_oc_price'];
		}

		if (isset($this->request->get['filter_supplier_cost'])) {
			$url .= '&filter_supplier_cost=' . $this->request->get['filter_supplier_cost'];
		}

		if (isset($this->request->get['supplier_id'])) {
			$url .= '&supplier_id=' . $this->request->get['supplier_id'];
			if(!empty($data['tab_option'])){
					$data['clear_supplier_product_filter'] 	= $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&supplier_id=' . $this->request->get['supplier_id'].'&tab_active=supplier_product&tab_option='.$data['tab_option'], true);
			}else{
					$data['clear_supplier_product_filter'] 	= $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&supplier_id=' . $this->request->get['supplier_id'].'&tab_active=supplier_product', true);
			}
			$data['back_to_product_list'] 	= $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&supplier_id=' . $this->request->get['supplier_id'].'&tab_active=supplier_product', true);

			$data['assign_store_product'] 	= $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&supplier_id=' . $this->request->get['supplier_id'].'&tab_active=supplier_product&tab_option=assign', true);
		}

		$data['button_back_link'] = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] .$url, true);

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['tab_active']) && $this->request->get['tab_active'] == 'supplier_product')) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_map_id'] = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&sort=id' . $url, true);
		$data['sort_oc_product_id'] = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&sort=apm.oc_product_id' . $url, true);
		$data['sort_oc_name'] = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&sort=product_name' . $url, true);
		$data['sort_oc_price'] = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_oc_quantity'] = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);

		$url = '';

		$url = '&tab_active=supplier_product';

		if (isset($this->request->get['tab_option'])) {
			$url .= '&tab_option=' . $this->request->get['tab_option'];
		}

		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
		}

		if (isset($this->request->get['filter_product_name'])) {
			$url .= '&filter_product_name=' . urlencode(html_entity_decode($this->request->get['filter_product_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_oc_model'])) {
			$url .= '&filter_oc_model=' . urlencode(html_entity_decode($this->request->get['filter_oc_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_supplier_sku'])) {
			$url .= '&filter_supplier_sku=' . $this->request->get['filter_supplier_sku'];
		}

		if (isset($this->request->get['filter_oc_price'])) {
			$url .= '&filter_oc_price=' . $this->request->get['filter_oc_price'];
		}

		if (isset($this->request->get['filter_supplier_cost'])) {
			$url .= '&filter_supplier_cost=' . $this->request->get['filter_supplier_cost'];
		}

		if (isset($this->request->get['supplier_id'])) {
			$url .= '&supplier_id=' . $this->request->get['supplier_id'];
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['tab_active']) && $this->request->get['tab_active'] == 'supplier_product')) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['tab_active']) && $this->request->get['tab_active'] == 'supplier_product')) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $amazonProductTotal;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($amazonProductTotal) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($amazonProductTotal - $this->config->get('config_limit_admin'))) ? $amazonProductTotal : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $amazonProductTotal, ceil($amazonProductTotal / $this->config->get('config_limit_admin')));

		if (isset($this->request->get['page']) && (isset($this->request->get['tab_active']) && $this->request->get['tab_active'] == 'supplier_product')) {
			$url .= '&page=' . $this->request->get['page'];
		}

    $data['action_product'] = $this->url->link('po_mgmt/po_supplier_product/deleteMapProduct', 'user_token=' . $this->session->data['user_token'] .$url, true);
    $data['assign_product_link'] = $this->url->link('po_mgmt/po_supplier_product/addProducts', 'user_token=' . $this->session->data['user_token'] .$url, true);

		$data['filter_product_id'] 					= $filter_product_id;
		$data['filter_product_name'] 				= $filter_product_name;
		$data['filter_oc_model'] 						= $filter_oc_model;
		$data['filter_supplier_sku'] 				= $filter_supplier_sku;
		$data['filter_oc_price'] 						= $filter_oc_price;
		$data['filter_supplier_cost'] 			= $filter_supplier_cost;
		$data['sort'] 											= $sort;
		$data['order'] 											= $order;

		return $this->load->view('po_mgmt/po_supplier_product', $data);
	}

	public function addProducts() {
		$this->load->language('po_mgmt/po_supplier_product');

		$this->document->setTitle($this->language->get('heading_title_assign'));
    $url = '';

    if (isset($this->request->get['tab_active'])) {
      $url .= '&tab_active=' . $this->request->get['tab_active'];
    }

    if (isset($this->request->get['tab_option'])) {
      $url .= '&tab_option=' . $this->request->get['tab_option'];
    }

		if (isset($this->request->get['supplier_id'])) {
			$url .= '&supplier_id=' . $this->request->get['supplier_id'];
		}
    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateAssignForm()) {
			$this->_poSupplier->addSupplierProduct($this->request->post);

			if(!empty($this->request->post['post_data'])){
				$this->session->data['success_assign'] = sprintf($this->language->get('text_success_assign'), implode(",", array_keys($this->request->post['post_data'])));
			}
        $this->response->redirect($this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&tab_active=supplier_product&supplier_id='.$this->request->get['supplier_id'], true));
		}

    if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
		}

		if (isset($this->request->get['filter_product_name'])) {
			$url .= '&filter_product_name=' . urlencode(html_entity_decode($this->request->get['filter_product_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_oc_model'])) {
			$url .= '&filter_oc_model=' . urlencode(html_entity_decode($this->request->get['filter_oc_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_supplier_sku'])) {
			$url .= '&filter_supplier_sku=' . $this->request->get['filter_supplier_sku'];
		}

		if (isset($this->request->get['filter_oc_price'])) {
			$url .= '&filter_oc_price=' . $this->request->get['filter_oc_price'];
		}

		if (isset($this->request->get['filter_supplier_cost'])) {
			$url .= '&filter_supplier_cost=' . $this->request->get['filter_supplier_cost'];
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

		$this->response->redirect($this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	public function deleteSupplierProduct(){
		$json = array();
		$this->load->language('po_mgmt/po_supplier_product');

		if (isset($this->request->post['selected']) && $this->validateDelete() && isset($this->request->post['supplier_id'])) {
			$supplier_id = $this->request->post['supplier_id'];
			foreach ($this->request->post['selected'] as $product_id) {
				$this->_poSupplier->deleteSupplierProduct($product_id, $supplier_id);
			}
			if(!empty($this->request->post['selected'])){
				$this->session->data['success_assign'] = sprintf($this->language->get('text_success_delete'), implode(",", $this->request->post['selected']));
				$json['redirect'] = html_entity_decode($this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&tab_active=supplier_product&supplier_id=' . $supplier_id, true));
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function validateAssignForm(){
    unset($this->session->data['post_data']);
		if (!$this->user->hasPermission('modify', 'po_mgmt/po_supplier_product')) {
			$this->error_report['warning'] = $this->session->data['warning'] = $this->language->get('error_permission');
		}

		if(empty($this->request->post['selected'])){
				if(isset($this->request->get['tab_option']) && $this->request->get['tab_option'] == 'assign'){
						$this->error_report['warning'] = $this->session->data['warning'] = $this->language->get('error_select_assign_product');
				}else{
						$this->error_report['warning'] = $this->session->data['warning'] = $this->language->get('error_select_update_product');
				}
		}
		$post_data = array();
		foreach ($this->request->post['selected'] as $key => $product_id) {
				if(isset($this->request->post['product_details'][$product_id])){
            $this->session->data['post_data'][$product_id]   = $this->request->post['product_details'][$product_id];
						if(is_numeric($this->request->post['product_details'][$product_id]['supplier_cost']) && ($pos = strpos($this->request->post['product_details'][$product_id]['supplier_cost'], '-') === false)){
								$post_data[$product_id] = $this->request->post['product_details'][$product_id];
						}else{
								$this->error_report['supplier_cost'][$product_id] = $this->session->data['supplier_cost'][$product_id] = $this->language->get('error_supplier_cost');
						}

            if(preg_match('/[^A-Za-z_ .\-0-9]/i', $this->request->post['product_details'][$product_id]['supplier_sku'])){
								$this->error_report['supplier_sku'][$product_id] = $this->session->data['supplier_sku'][$product_id] = $this->language->get('error_supplier_sku');
						}
				}
		}
		unset($this->request->post['product_details']);
		$this->request->post['post_data']   = $post_data;

		return !$this->error_report;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'po_mgmt/po_supplier_product')) {
			$this->error_report['warning'] = $this->session->data['warning'] = $this->language->get('error_permission');
		}

		return !$this->error_report;
	}

	public function autocomplete(){
		$json = array();
			if(isset($this->request->get['supplier_id']) ){
					$getFilter = '';
					if(isset($this->request->get['filter_product_name'])){
						$getFilter = 'oc_product';
						$oc_product = $this->request->get['filter_product_name'];
					}else{
						$oc_product = '';
					}

					if(isset($this->request->get['filter_oc_model'])){
						$getFilter = 'oc_model';
						$oc_model = $this->request->get['filter_oc_model'];
					}else{
						$oc_model = '';
					}

					if(isset($this->request->get['filter_supplier_sku'])){
						$getFilter = 'sp_sku';
						$sp_sku = $this->request->get['filter_supplier_sku'];
					}else{
						$sp_sku = '';
					}

					$filter_data = array(
						'supplier_id' 						=> $this->request->get['supplier_id'],
						'tab_option'							=> isset($this->request->get['tab_option']) ? $this->request->get['tab_option'] : '',
						'filter_product_name' 		=> $oc_product,
						'filter_oc_model' 				=> $oc_model,
						'filter_supplier_sku' 		=> $sp_sku,
						'order'       => 'ASC',
						'start'       => 0,
						'limit'       => 5
					);

					$results = $this->_poSupplier->getSupplierProducts($filter_data);

					foreach ($results as $result) {
							if($getFilter == 'oc_product'){
									$json[$result['product_id']] = array(
										'item_id' 		=> $result['product_id'],
										'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
									);
							}else if($getFilter == 'oc_model'){
									$json[$result['product_id']] = array(
										'item_id' 		=> $result['product_id'],
										'name'        => strip_tags(html_entity_decode($result['model'], ENT_QUOTES, 'UTF-8'))
									);
							}else if($getFilter == 'sp_sku'){
									$json[$result['product_id']] = array(
										'item_id' 		=> $result['product_id'],
										'name'        => strip_tags(html_entity_decode($result['supplier_sku'], ENT_QUOTES, 'UTF-8'))
									);
							}
					}

				$sort_order = array();

				foreach ($json as $key => $value) {
					$sort_order[$key] = $value['name'];
				}

				array_multisort($sort_order, SORT_ASC, $json);

				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			}
	}
}
