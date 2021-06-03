<?php
class ControllerExtensionModuleOcPom extends Controller {
	private $error = array();

	public function __construct($registory) {
		parent::__construct($registory);
		$this->load->model('po_mgmt/pom_install');
		$this->pom_install = $this->model_po_mgmt_pom_install;

		$this->load->model('setting/extension');
	}

  public function install(){
		$this->pom_install->createTables();
	}

  public function uninstall() {
      $this->pom_install->removeTables();
  }

  public function pom_columnLeft(){
    // Price List
    $purchase_order = array();

    if($this->config->get('module_oc_pom_status')){
        $this->language->load('extension/module/oc_pom');

        if ($this->user->hasPermission('access', 'po_mgmt/po_supplier')) {
          $purchase_order['link'][] = array(
            'name'	   => $this->language->get('text_po_supplier'),
            'href'     => $this->url->link('po_mgmt/po_supplier', 'user_token=' . $this->session->data['user_token'], true),
            'children' => array()
          );
        }

        if ($this->user->hasPermission('access', 'po_mgmt/po_quotation')) {
          $purchase_order['link'][] = array(
            'name'	   => $this->language->get('text_po_quotation'),
            'href'     => $this->url->link('po_mgmt/po_quotation', 'user_token=' . $this->session->data['user_token'], true),
            'children' => array()
          );
        }

		if ($this->user->hasPermission('access', 'po_mgmt/po_order')) {
			$purchase_order['link'][] = array(
			 'name'	   => $this->language->get('text_purchase_order'),
			 'href'     => $this->url->link('po_mgmt/po_order', 'user_token=' . $this->session->data['user_token'], true),
			 'children' => array()
			);
		}

		if ($this->user->hasPermission('access', 'po_mgmt/po_email')) {
        	$purchase_order['link'][] = array(
        	 'name'	   => $this->language->get('text_po_email'),
             'href'     => $this->url->link('po_mgmt/po_email', 'user_token=' . $this->session->data['user_token'], true),
             'children' => array()
          	);
		}

		if ($this->user->hasPermission('access', 'po_mgmt/po_supplier_ledger')) {
			$purchase_order['link'][] = array(
			 'name'	   => $this->language->get('text_po_supplier_ledger'),
			 'href'     => $this->url->link('po_mgmt/po_supplier_ledger', 'user_token=' . $this->session->data['user_token'], true),
			 'children' => array()
			);
		}

		if ($this->user->hasPermission('access', 'extension/module/oc_pom')) {
			$purchase_order['link'][] = array(
			 'name'	   => $this->language->get('text_purchase_config'),
			 'href'     => $this->url->link('extension/module/oc_pom', 'user_token=' . $this->session->data['user_token'], true),
		   	 'children' => array()
			);
		}

        $purchase_order['title'] = $this->language->get('text_po_management');
      }

      return $purchase_order;
  }

	public function index() {

    $data = array();
    $data = array_merge($data, $this->load->language('extension/module/oc_pom'));

		$extensions = $this->model_setting_extension->getInstalled('module');

      // check for the POS insatlled
  		if (!in_array('oc_pom', $extensions)) {
  			die('<h2>' . $this->language->get('error_installation') . '</h2>');
  		}

		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/store');
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				$this->model_setting_setting->editSetting('module_oc_pom', $this->request->post);
					$this->pom_install->saveMethods($this->request->post);
					$this->session->data['success'] = $this->language->get('text_success');

					$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'].'&type=module', true));

		}

		$data['stores'] = array();

		$data['stores'][] = array(
				'store_id' => 0,
				'name'     => $this->config->get('config_name'),
		);

		$results = $this->model_setting_store->getStores();
		if ($results) {
			foreach ($results as $result) {
					$data['stores'][] = array(
							'store_id' => $result['store_id'],
							'name'     => $result['name'],
					);
			}
		}

    $post_data = array(
     'module_oc_pom_status',
	 'module_oc_pom_store',
	 'module_oc_pom_order_prefix',
	 'module_oc_pom_precure_method',
	 'module_oc_pom_order_to_po',
	 'module_oc_pom_order_status_po',
	 'module_oc_pom_email_id',
	 'module_oc_pom_low_stock',
     'module_oc_pom_quotation_quantity',
     'module_oc_pom_quotation_email_admin',
     'module_oc_pom_quotation_email_supplier',
	 'module_oc_pom_add_product_email_admin',
     'module_oc_pom_add_product_email_supplier',
	 'module_oc_pom_update_product_email_admin',
     'module_oc_pom_update_product_email_supplier',
     'module_oc_pom_purchase_email_admin',
     'module_oc_pom_purchase_email_supplier',
     'module_oc_pom_shipping_cost',
     'module_oc_pom_tax_cost',
	 'module_oc_pom_payment_terms',
    );

		foreach ($post_data as $key => $post_index) {
        if(isset($this->request->post[$post_index])){
          	$data[$post_index] = $this->request->post[$post_index];
        }else{
						$data[$post_index] = $this->config->get($post_index);
				}
    }

		if(isset($this->request->post['module_oc_pom_shipping_method']) && $this->request->post['module_oc_pom_shipping_method']){
				foreach ($this->request->post['module_oc_pom_shipping_method'] as $key => $value) {
						$this->request->post['module_oc_pom_shipping_method'][$key]['type'] 			= 'shipping';
						$this->request->post['module_oc_pom_shipping_method'][$key]['store_id'] 	= $data['module_oc_pom_store'];
				}
				$data['getShippingMethods'] = $this->request->post['module_oc_pom_shipping_method'];
		}else{
				$data['getShippingMethods'] = $this->pom_install->getMethods('shipping', $data['module_oc_pom_store']);
		}

		if(isset($this->request->post['module_oc_pom_payment_method']) && $this->request->post['module_oc_pom_payment_method']){
				foreach ($this->request->post['module_oc_pom_payment_method'] as $key => $value) {
						$this->request->post['module_oc_pom_payment_method'][$key]['type'] 		= 'payment';
						$this->request->post['module_oc_pom_payment_method'][$key]['store_id'] = $data['module_oc_pom_store'];
				}
				$data['getPaymentMethods'] = $this->request->post['module_oc_pom_payment_method'];
		}else{
				$data['getPaymentMethods'] = $this->pom_install->getMethods('payment', $data['module_oc_pom_store']);
		}

		if(isset($this->request->post['module_oc_pom_payment_method_supplier']) && $this->request->post['module_oc_pom_payment_method_supplier']){
			foreach ($this->request->post['module_oc_pom_payment_method_supplier'] as $key => $value) {
					$this->request->post['module_oc_pom_payment_method_supplier'][$key]['type'] 		= 'payment';
					$this->request->post['module_oc_pom_payment_method_supplier'][$key]['store_id'] = $data['module_oc_pom_store'];
			}
			$data['getPaymentMethods'] = $this->request->post['module_oc_pom_payment_method_supplier'];
	}else{
			$data['getPaymentMethods'] = $this->pom_install->getMethods('payment', $data['module_oc_pom_store']);
	}

	if(isset($this->request->post['module_oc_pom_supplier_payment_method']) && $this->request->post['module_oc_pom_supplier_payment_method']){
		foreach ($this->request->post['module_oc_pom_supplier_payment_method'] as $key => $value) {
				$this->request->post['module_oc_pom_supplier_payment_method'][$key]['type'] 		= 'supplier_payment';
				$this->request->post['module_oc_pom_supplier_payment_method'][$key]['store_id'] = $data['module_oc_pom_store'];
		}
		$data['getSupplierPaymentMethods'] = $this->request->post['module_oc_pom_supplier_payment_method'];
	}else{
		$data['getSupplierPaymentMethods'] = $this->pom_install->getMethods('supplier_payment', $data['module_oc_pom_store']);
	}



		$error_data = array(
			'warning',
			'module_oc_pom_order_prefix',
			'module_oc_pom_quotation_quantity',
			'shipping_name',
			'payment_name',
			'shipping_cost',
			'tax_cost',
			'module_oc_pom_email_id',
			'module_oc_pom_low_stock',
			'module_oc_pom_supplier_payment_method',
			'module_oc_pom_shipping_cost',
			'module_oc_pom_tax_cost'
		);
		foreach ($error_data as $error) {
				if (isset($this->error[$error])) {
					$data['error_'.$error] = $this->error[$error];
				} else {
					$data['error_'.$error] = '';
				}
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'].'&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/oc_pom', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/oc_pom', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'].'&type=module', true);

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		if($this->currency->getSymbolLeft($this->config->get('config_currency'))){
			$data['currency_code'] = $this->currency->getSymbolLeft($this->config->get('config_currency'));
		}else{
			$data['currency_code'] = $this->currency->getSymbolRight($this->config->get('config_currency'));
		}

		$this->load->model('po_mgmt/po_email');
		$data['emails_list'] = $this->model_po_mgmt_po_email->getPOEmailList();

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['header']       = $this->load->controller('common/header');
		$data['column_left']  = $this->load->controller('common/column_left');
		$data['footer']       = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/module/oc_pom', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/oc_pom')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['module_oc_pom_order_prefix'])) {

			if((preg_match('/[^A-Za-z_.\-0-9]/i', $this->request->post['module_oc_pom_order_prefix']))) {
				$this->error['module_oc_pom_order_prefix'] = $this->language->get('error_oc_pom_order_prefix_invalid');
			} elseif ((utf8_strlen(trim($this->request->post['module_oc_pom_order_prefix'])) < 2) || (utf8_strlen(trim($this->request->post['module_oc_pom_order_prefix'])) > 5)) {
				$this->error['module_oc_pom_order_prefix'] = $this->language->get('error_oc_pom_order_prefix');
			}
		} else {
			$this->error['module_oc_pom_order_prefix'] = $this->language->get('error_oc_pom_order_prefix');
		}

		if(isset($this->request->post['module_oc_pom_shipping_method'])){
				foreach ($this->request->post['module_oc_pom_shipping_method'] as $key => $shipping_row) {
						foreach ($shipping_row['shipping_description'] as $language_id => $value) {
							if ((utf8_strlen(trim($value['name'])) < 3) || (utf8_strlen(trim($value['name'])) > 70)) {
								$this->error['shipping_name'][$key][$language_id] = $this->language->get('error_shipping_name');
							}
							if(preg_match('/[^A-Za-z_ .\-0-9]/i', $value['name'])){
									$this->error['shipping_name'][$key][$language_id] = $this->language->get('error_invalid_value');
							}
						}
				}
		}

		if(isset($this->request->post['module_oc_pom_payment_method'])){
				foreach ($this->request->post['module_oc_pom_payment_method'] as $key => $shipping_row) {
						foreach ($shipping_row['payment_description'] as $language_id => $value) {
							if ((utf8_strlen(trim($value['name'])) < 3) || (utf8_strlen(trim($value['name'])) > 70)) {
								$this->error['payment_name'][$key][$language_id] = $this->language->get('error_payment_name');
							}
							if(preg_match('/[^A-Za-z_ .\-0-9]/i', $value['name'])){
									$this->error['payment_name'][$key][$language_id] = $this->language->get('error_invalid_value');
							}
						}
				}
		}

		if(isset($this->request->post['module_oc_pom_supplier_payment_method'])){
			foreach ($this->request->post['module_oc_pom_supplier_payment_method'] as $key => $shipping_row) {
					foreach ($shipping_row['payment_description'] as $language_id => $value) {
						if ((utf8_strlen(trim($value['name'])) < 3) || (utf8_strlen(trim($value['name'])) > 70)) {
							$this->error['module_oc_pom_supplier_payment_method'][$key][$language_id] = $this->language->get('error_payment_name');
						}
						if(preg_match('/[^A-Za-z_ .\-0-9]/i', $value['name'])){
								$this->error['module_oc_pom_supplier_payment_method'][$key][$language_id] = $this->language->get('error_payment_name');
						}
					}
			}
		}

		if(!isset($this->request->post['module_oc_pom_quotation_quantity']) || $this->request->post['module_oc_pom_quotation_quantity'] == '') {
			$this->error['module_oc_pom_quotation_quantity'] = $this->language->get('error_supplier_qty');
		} elseif($this->request->post['module_oc_pom_quotation_quantity'] < 0 || !filter_var($this->request->post['module_oc_pom_quotation_quantity'], FILTER_VALIDATE_INT)) {
			$this->error['module_oc_pom_quotation_quantity'] = $this->language->get('error_supplier_qty');
		}

		if(!isset($this->request->post['module_oc_pom_shipping_cost']) || $this->request->post['module_oc_pom_shipping_cost'] == '') {
			$this->error['module_oc_pom_shipping_cost'] = $this->language->get('error_shipping_cost');
		} elseif($this->request->post['module_oc_pom_shipping_cost'] < 0 || !is_numeric($this->request->post['module_oc_pom_shipping_cost']) || ($this->request->post['module_oc_pom_shipping_cost'] != 0 && !filter_var($this->request->post['module_oc_pom_shipping_cost'], FILTER_VALIDATE_INT))) {
			$this->error['module_oc_pom_shipping_cost'] = $this->language->get('error_shipping_cost');
		}

		if(!isset($this->request->post['module_oc_pom_tax_cost']) || $this->request->post['module_oc_pom_tax_cost'] == '') {
			$this->error['module_oc_pom_tax_cost'] = $this->language->get('error_tax_cost');
		} elseif($this->request->post['module_oc_pom_tax_cost'] < 0 || !is_numeric($this->request->post['module_oc_pom_tax_cost']) || ($this->request->post['module_oc_pom_tax_cost'] != 0 && !filter_var($this->request->post['module_oc_pom_tax_cost'], FILTER_VALIDATE_INT))) {
			$this->error['module_oc_pom_tax_cost'] = $this->language->get('error_tax_cost');
		}

		if(!isset($this->request->post['module_oc_pom_low_stock']) || $this->request->post['module_oc_pom_low_stock'] == '') {
			$this->error['module_oc_pom_low_stock'] = $this->language->get('error_po_low_stock');
		} elseif($this->request->post['module_oc_pom_low_stock'] < 0 || !filter_var($this->request->post['module_oc_pom_low_stock'], FILTER_VALIDATE_INT)) {
			$this->error['oc_pom_low_stock'] = $this->language->get('error_po_low_stock');
		}

		if(!isset($this->request->post['module_oc_pom_email_id']) || !filter_var($this->request->post['module_oc_pom_email_id'], FILTER_VALIDATE_EMAIL)){
				$this->error['module_oc_pom_email_id'] = $this->language->get('error_oc_pom_email_invalid');
		}

		if(!empty($this->error)){
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}


}
