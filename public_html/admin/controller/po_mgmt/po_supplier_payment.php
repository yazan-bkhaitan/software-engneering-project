<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-Purchase-Order-Mgmt
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerPoMgmtPoSupplierPayment extends Controller {
 	private $error = array();

	public function __construct($registory) {
		parent::__construct($registory);
		$this->load->model('po_mgmt/po_order');
		$this->_poOrder = $this->model_po_mgmt_po_order;
    	if (!$this->config->get('module_oc_pom_status')) {
			$this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->load->model('po_mgmt/pom_install');
		$this->load->model('po_mgmt/po_supplier');
  }

    public function index() {
		
		$data = array();
		$data = array_merge($data, $this->load->language('po_mgmt/po_supplier_payment'));

		if(isset($this->request->get['supplier_id'])) {

		$extensions  = $this->model_po_mgmt_pom_install->getMethods('supplier_payment', $this->config->get('module_oc_pom_store'));

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['extensions'] = array();

		$data['user_token'] = $this->session->data['user_token'];

		if($extensions && !empty($extensions)) {
			foreach ($extensions as $key => $value) {
				if($value['status']) {
					$data['extensions'][] = array(
						'method_id' => $value['method_id'],
						'name'		=> (isset($value['payment_description'][$this->config->get('config_language_id')])) ? $value['payment_description'][$this->config->get('config_language_id')]['name'] : $value['payment_description'][1]['name'],
						'type'		=> $value['type'],
					);
				}
			}
		}

		$data['supplier_payment'] = array();
		$data['supplier_payment_details'] = array();
		$supplier_payment = $this->model_po_mgmt_po_supplier->getSupplierPaymentMethod($this->request->get['supplier_id']);

		if($supplier_payment && isset($supplier_payment) && $supplier_payment['method_id']) {
			$data['supplier_payment']['method_id'] = $supplier_payment['method_id'];
			$data['supplier_payment']['name']      = $supplier_payment['name'];

			$payment_method_details = $this->model_po_mgmt_po_supplier->getSupplierPaymentMethodDetails($this->request->get['supplier_id'],$supplier_payment['method_id']);

			if($payment_method_details && !empty($payment_method_details)) {
				foreach ($payment_method_details as $key => $value) {
					$data['supplier_payment_details'][] = array(
						'name'  => $value['name'],
						'value' => $value['value'],
					);
				}
			}
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
		
		$data['action'] = $this->url->link('po_mgmt/po_supplier_payment/addPaymentDetails', 'user_token=' . $this->session->data['user_token'] . '&supplier_id=' . $this->request->get['supplier_id'] . $url, true);
    		return $this->load->view('po_mgmt/po_supplier_payment', $data);
      }else{
        return false;
      }
	}
	public function addPaymentDetails(){

		$json = array();

		$this->load->language('po_mgmt/po_supplier_payment');

		$this->load->model('po_mgmt/po_supplier');

		if(isset($this->request->get['supplier_id']) && $this->request->get['supplier_id']) {

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

				if(isset($this->request->post['payment_method_details'])) {

					$this->model_po_mgmt_po_supplier->addSupplierPaymentMethod($this->request->get['supplier_id'],$this->request->post['selected'],$this->request->post['payment_method_details']);
				} else {
					$this->model_po_mgmt_po_supplier->addSupplierPaymentMethod($this->request->get['supplier_id'],$this->request->post['selected']);
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
				$json['success']  = true;

			} else {

				$json['error'] = true;

				$error_array = array(
					'warning',
					'empty_method',
					'method_detail'
				);

				foreach ($error_array as $key => $value) {
					if(isset($this->error[$value])) {
						$json['error_' . $value] = $this->error[$value];
					}
				}
			}
		} else {

			$json['error'] = true;
			$json['error_warning'] = $this->language->get('error_server'); 
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}

	public function validateForm() {
		
		if (!$this->user->hasPermission('modify', 'po_mgmt/po_supplier_payment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if(!isset($this->request->post['selected']) && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_server');
		}

		if(isset($this->request->post['payment_method_details'])) {
			if(!empty($this->request->post['payment_method_details'])) {
				foreach ($this->request->post['payment_method_details'] as $key => $value) {
					if(isset($value['name'])) {
						if((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 32)){
							$this->error['method_detail'][$key]['name'] = $this->language->get('error_name');
						}
					} else {
						$this->error['method_detail'][$key]['name'] = $this->language->get('error_name');
					}

					if(isset($value['value'])) {
						if((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 32)) {
							$this->error['method_detail'][$key]['value'] = $this->language->get('error_value');
						}
					} else {
						$this->error['method_detail'][$key]['value'] = $this->language->get('error_name');
					}
				}
			} else {
				$this->error['empty_method'] = $this->language->get('');
			}
		}

		return !$this->error;
	}
}
