<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-Purchase-Order-Mgmt
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerPoMgmtPoSupplierQuotation extends Controller {
 	private $error_report = array();

	public function __construct($registory) {
		parent::__construct($registory);
		$this->load->model('po_mgmt/po_quotation');
		$this->_poQuotation = $this->model_po_mgmt_po_quotation;
    if (!$this->config->get('module_oc_pom_status')) {
			$this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
		}
  }

    public function index() {
		$data = array();
		$data = array_merge($data, $this->load->language('po_mgmt/po_supplier_quotation'));

		if(isset($this->request->get['supplier_id'])) {
        if (isset($this->request->get['supplier_id'])) {
          $data['supplier_id'] = $supplier_id = $this->request->get['supplier_id'];
        }

    		if (isset($this->request->get['filter_quotation_id'])) {
    			$filter_quotation_id = $this->request->get['filter_quotation_id'];
    		} else {
    			$filter_quotation_id = '';
    		}

    		if (isset($this->request->get['filter_grand_total'])) {
    			$filter_grand_total = $this->request->get['filter_grand_total'];
    		} else {
    			$filter_grand_total = null;
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

    		$url = '&tab_active=supplier_quotation';

    		if (isset($this->request->get['filter_quotation_id'])) {
    			$url .= '&filter_quotation_id=' . $this->request->get['filter_quotation_id'];
    		}

    		if (isset($this->request->get['filter_grand_total'])) {
    			$url .= '&filter_grand_total=' . $this->request->get['filter_grand_total'];
    		}

        if (isset($this->request->get['filter_created_date'])) {
    			$url .= '&filter_created_date=' . $this->request->get['filter_created_date'];
    		}

        if (isset($this->request->get['filter_status'])) {
    			$url .= '&filter_status=' . $this->request->get['filter_status'];
    		}

    		if (isset($this->request->get['supplier_id'])) {
    			$url .= '&supplier_id=' . $this->request->get['supplier_id'];
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

    		$data['supplier_quotations'] = array();

    		$filter_data = array(
    			'filter_supplier_id' 					=> $supplier_id,
    			'filter_quotation_id'	  			=> $filter_quotation_id,
    			'filter_grand_total' 				  => $filter_grand_total,
          'filter_created_date'					=> $filter_created_date,
    			'filter_status' 				      => $filter_status,
    			'sort'  											=> $sort,
    			'order' 											=> $order,
    			'start' 											=> ($page - 1) * $this->config->get('config_limit_admin'),
    			'limit' 											=> $this->config->get('config_limit_admin')
    		);

        $data['quotation_status'] = $quotation_status = array('1' => 'Pending',
    																													'6' => 'Cancel',
    																												);

    		$supplierQuotationTotal = $this->_poQuotation->getTotalQuotations($filter_data);

    		$results = $this->_poQuotation->getQuotations($filter_data);

    		if($results){
    			foreach ($results as $result) {

    				$ordered_qty = 0;
    				if(isset($result['id'])){
    						$getQuoteProducts = $this->_poQuotation->getQuotationProducts($result['id']);

    						foreach ($getQuoteProducts as $key => $product) {
    								$ordered_qty = $ordered_qty + $product['ordered_qty'];
    						}
    				}

    				$data['supplier_quotations'][] = array(
              'quotation_id' 				=> $result['id'],
              'format_quote_id' 	  => '#'.$result['id'],
    					'total_ordered_qty'	  => $ordered_qty,
              'grand_total'	        => $result['total'],
    					'created_date'				=> $result['created_date'],
    					'status'				      => $quotation_status[$result['quotation_status']],
              'view'				        => $this->url->link('po_mgmt/po_quotation/getViewForm', 'user_token=' . $this->session->data['user_token'].'&quotation_id='.$result['id'], true),
    				);
    			}
    		}

    		$data['user_token'] 	= $this->session->data['user_token'];

    		$data['currency_symbol'] = $this->config->get('config_currency');

    		$url = '';

    		$url = '&tab_active=supplier_quotation';

        if (isset($this->request->get['filter_quotation_id'])) {
    			$url .= '&filter_quotation_id=' . $this->request->get['filter_quotation_id'];
    		}

    		if (isset($this->request->get['filter_grand_total'])) {
    			$url .= '&filter_grand_total=' . $this->request->get['filter_grand_total'];
    		}

        if (isset($this->request->get['filter_created_date'])) {
    			$url .= '&filter_created_date=' . $this->request->get['filter_created_date'];
    		}

        if (isset($this->request->get['filter_status'])) {
    			$url .= '&filter_status=' . $this->request->get['filter_status'];
    		}

    		if (isset($this->request->get['supplier_id'])) {
    			$url .= '&supplier_id=' . $this->request->get['supplier_id'];

    			$data['clear_quotation_filter'] 	= $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'].'&supplier_id=' . $this->request->get['supplier_id'].'&tab_active=supplier_quotation', true);
    		}

    		if ($order == 'ASC') {
    			$url .= '&order=DESC';
    		} else {
    			$url .= '&order=ASC';
    		}

    		if (isset($this->request->get['page'])) {
    			$url .= '&page=' . $this->request->get['page'];
    		}

    		$data['sort_quote_id'] = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&sort=id' . $url, true);
    		$data['sort_order_qty'] = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&sort=total_quantity' . $url, true);
    		$data['sort_total'] = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&sort=q.total' . $url, true);
    		$data['sort_created_date'] = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&sort=q.created_date' . $url, true);
    		$data['sort_status'] = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . '&sort=q.status' . $url, true);

    		$url = '';

    		$url = '&tab_active=supplier_quotation';

        if (isset($this->request->get['filter_quotation_id'])) {
    			$url .= '&filter_quotation_id=' . $this->request->get['filter_quotation_id'];
    		}

    		if (isset($this->request->get['filter_grand_total'])) {
    			$url .= '&filter_grand_total=' . $this->request->get['filter_grand_total'];
    		}

        if (isset($this->request->get['filter_created_date'])) {
    			$url .= '&filter_created_date=' . $this->request->get['filter_created_date'];
    		}

        if (isset($this->request->get['filter_status'])) {
    			$url .= '&filter_status=' . $this->request->get['filter_status'];
    		}

    		if (isset($this->request->get['supplier_id'])) {
    			$url .= '&supplier_id=' . $this->request->get['supplier_id'];
    		}

    		if (isset($this->request->get['sort'])) {
    			$url .= '&sort=' . $this->request->get['sort'];
    		}

    		if (isset($this->request->get['order'])) {
    			$url .= '&order=' . $this->request->get['order'];
    		}

    		$pagination = new Pagination();
    		$pagination->total = $supplierQuotationTotal;
    		$pagination->page = $page;
    		$pagination->limit = $this->config->get('config_limit_admin');
    		$pagination->url = $this->url->link('po_mgmt/po_supplier/edit', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

    		$data['pagination'] = $pagination->render();

    		$data['results'] = sprintf($this->language->get('text_pagination'), ($supplierQuotationTotal) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($supplierQuotationTotal - $this->config->get('config_limit_admin'))) ? $supplierQuotationTotal : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $supplierQuotationTotal, ceil($supplierQuotationTotal / $this->config->get('config_limit_admin')));

    		$data['filter_quotation_id'] 					= $filter_quotation_id;
    		$data['filter_grand_total'] 			    = $filter_grand_total;
        $data['filter_created_date'] 					= $filter_created_date;
    		$data['filter_status'] 			          = $filter_status;
    		$data['sort'] 											  = $sort;
    		$data['order'] 											  = $order;

    		return $this->load->view('po_mgmt/po_supplier_quotation', $data);
      }else{
        return false;
      }
	}
}
