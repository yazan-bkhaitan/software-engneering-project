<?php

Class ControllerPOMgmtPoSupplierLedger extends Controller {

    //initialize private variable here 
    private $error = array();
    private $data = array();

    /**
     * create the construt method here
     *
     * @param array $registry
     */
    public function __construct($registry) {
      parent::__construct($registry);

      if (!$this->config->get('module_oc_pom_status')) {
			  $this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
		  }

        $this->data = $this->load->language('po_mgmt/po_supplier_ledger');
        $this->load->model('po_mgmt/po_supplier_ledger');

        $this->data['user_token'] = $this->session->data['user_token'];
    }

    /**
     * function to get the common controller here
     *
     * @return void
     */
    private function _getCommonController() {

        $this->data['header']       = $this->load->controller('common/header');
        $this->data['column_left']  = $this->load->controller('common/column_left');
        $this->data['footer']       = $this->load->controller('common/footer');
    }

    /**
     * Index function of controller
     *
     * @return void
     */
    public function index() {

      $this->document->setTitle($this->data['heading_title']);

      $url = $this->setRequestgetVar();

      //create breadcrumb code starts here
      $this->data['breadcrumbs'] = array();

      $this->data['breadcrumbs'][] = array(
       'text' => $this->data['text_home'],
       'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
      );
      $this->data['breadcrumbs'][] = array(
       'text' => $this->data['heading_title'],
       'href' => $this->url->link('po_mgmt/po_supplier_ledger', 'user_token=' . $this->session->data['user_token'] . $url, true),
      );
      //create breadcrumb code ends here

      //get variable values form the query string code starts here
      $get_request_vars = array(
       'sort'            => 'psd.owner_name',
       'order'           => 'DESC',
       'page'            => 1,
       'filter_date'     => '',
       'filter_supplier' => null,
      );
              
      foreach ($get_request_vars as $key => $value) {
              
        $this->data[$key] = $this->_setGetRequestVar($key,$value);
          $$key = $this->data[$key];
        }
      //get variable values form the query string code ends here
              
      //make filter array  code starts here
      $filter_array = array(
       'filter_date'     => $filter_date,
       'filter_supplier' => $filter_supplier,
       'order'           => $order,
       'sort'            => $sort,
       'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
       'limit'           => $this->config->get('config_limit_admin'),
      );
      //make filter array code ends here
           

      $suppliers = $this->model_po_mgmt_po_supplier_ledger->getSupplierInformation($filter_array);
      $suplliers_total = $this->model_po_mgmt_po_supplier_ledger->getSupplierInformation($filter_array,true);

      $this->data['suppliers'] = array();

      foreach ($suppliers as $key => $supplier) {
        $this->data['suppliers'][] = array(
         'supplier_id' => $supplier['supplier_id'],
         'date'        => DATE('Y-m-d'),
         'name'        => $supplier['name'],
         'credit'      => $this->currency->format($supplier['credit'],$this->config->get('config_currency')),
         'debit'       => $this->currency->format($supplier['debit'],$this->config->get('config_currency')), 
         'expense'     => $this->currency->format($supplier['expense'],$this->config->get('config_currency')),
         'net_balance' =>  $this->currency->format(($supplier['credit']- $supplier['debit']),$this->config->get('config_currency')),
         'pay_now'     => $this->url->link('po_mgmt/po_supplier_ledger/payNow', 'user_token=' . $this->session->data['user_token'] . $url . '&supplier_id=' . $supplier['supplier_id'], true),
         'view_profit' => $this->url->link('po_mgmt/po_supplier_ledger/showProfitDetails', 'user_token=' . $this->session->data['user_token'] . $url . '&supplier_id=' . $supplier['supplier_id'], true),
        );
      }

      // get URL quert string for sorting URL code starts here
      $url = $this->setRequestgetVar('sort');
      // get URL quert string code ends here

      $this->data['sort_name'] = $this->url->link('po_mgmt/po_supplier_ledger', 'user_token=' . $this->session->data['user_token'] . '&sort=psd.owner_name' . $url, true);
      $this->data['sort_date'] = $this->url->link('po_mgmt/po_supplier_ledger', 'user_token=' . $this->session->data['user_token'] . '&sort=psd.owner_name' . $url, true);

      // get URL quert string for sorting URL code starts here
      $url = $this->setRequestgetVar('page');
      // get URL quert string code ends here

      $pagination = new Pagination();
		  $pagination->total = $suplliers_total;
		  $pagination->page = $page;
		  $pagination->limit = $this->config->get('config_limit_admin');
		  $pagination->url = $this->url->link('po_mgmt/po_supplier_ledger', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		  $this->data['pagination'] = $pagination->render();

		  $this->data['results'] = sprintf($this->data['text_pagination'], ($suplliers_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($suplliers_total - $this->config->get('config_limit_admin'))) ? $suplliers_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $suplliers_total, ceil($suplliers_total / $this->config->get('config_limit_admin')));

      //reset URL here
      $this->data['reset'] = $this->url->link('po_mgmt/po_supplier_ledger', 'user_token=' . $this->session->data['user_token'], true);

      if(isset($this->session->data['success'])) {
        $this->data['success'] = $this->session->data['success'];
        unset($this->session->data['success']);
      } else {
        $this->data['success'] = '';
      }

      //call common controller function here
      SELF::_getCommonController();

      $this->response->setOutput($this->load->view('po_mgmt/po_supplier_ledger',$this->data));
    }

    public function payNow() {
        
      if(isset($this->request->get['supplier_id']) && $this->request->get['supplier_id']) {

        $supplier_details = SELF::validateSupplier($this->request->get['supplier_id']);

        if(!$supplier_details) {
          $this->response->redirect($this->url->link('po_mgmt/po_supplier_ledger', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->data['heading_title_supplier'] = sprintf($this->data['heading_title_supplier'],$supplier_details['name']);

        $this->document->setTitle($this->data['heading_title_supplier']);

        // get URL quert stringcode starts here
        $url = $this->setRequestgetVar();
        // get URL quert string code starts here

        if($this->request->server['REQUEST_METHOD'] == 'POST' && SELF::validateForm()) {

          foreach ($this->request->post['selected'] as $key => $quotation_id) {
              if(isset($this->request->post['comment']) && $this->request->post['comment']) {
                $this->model_po_mgmt_po_supplier_ledger->makePurcahseOrderPaid($this->request->get['supplier_id'],$quotation_id,$this->request->post['comment']);
              } else {
                $this->model_po_mgmt_po_supplier_ledger->makePurcahseOrderPaid($this->request->get['supplier_id'],$quotation_id,'');
              }
          }

          $this->session->data['success'] = $this->data['text_success'];

          $this->response->redirect($this->url->link('po_mgmt/po_supplier_ledger', 'user_token=' . $this->session->data['user_token'] . $url, true));
          
        }

        $error_array = array(
         'warning',
         'selected',
         'comment',
        );

        foreach ($error_array as $key => $err) {
          if(isset($this->error[$err])) {
            $this->data['error_' . $err] = $this->error[$err];
          } else {
            $this->data['error_' . $err] = '';
          }
        }

        //create breadcrumb code starts here
        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
         'text' => $this->data['text_home'],
         'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
        );
        $this->data['breadcrumbs'][] = array(
         'text' => $this->data['heading_title'],
         'href' => $this->url->link('po_mgmt/po_supplier_ledger', 'user_token=' . $this->session->data['user_token'] . $url, true),
        );
        $this->data['breadcrumbs'][] = array(
         'text' => $this->data['heading_title_supplier'],
         'href' => $this->url->link('po_mgmt/po_supplier_ledger/payNow', 'user_token=' . $this->session->data['user_token'] . $url . '&supplier_id=' . $this->request->get['supplier_id'], true),
        );
        //create breadcrumb code ends here

        //get variable values form the query string code starts here
        $get_request_vars = array(
         'sort'            => 'psd.owner_name',
         'order'           => 'DESC',
         'page'            => 1,
         'pay_page'        => 1,
         'filter_date'     => '',
         'filter_supplier' => null,
        );
               
       foreach ($get_request_vars as $key => $value) {
               
         $this->data[$key] = $this->_setGetRequestVar($key,$value);
           $$key = $this->data[$key];
         }
       //get variable values form the query string code ends here

        $filter_array = array(
         'supplier_id' => $this->request->get['supplier_id'],
         'order'           => 'ASC',
         'sort'            => 'pq.quotation_id',
         'start'           => ($pay_page - 1) * $this->config->get('config_limit_admin'),
         'limit'           => $this->config->get('config_limit_admin'),
        );

        $this->data['cancel'] = $this->url->link('po_mgmt/po_supplier_ledger', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $this->data['action'] = $this->url->link('po_mgmt/po_supplier_ledger/payNow', 'user_token=' . $this->session->data['user_token'] . $url . '&supplier_id=' . $this->request->get['supplier_id'], true);

        $supplier_purcahse_orders = $this->model_po_mgmt_po_supplier_ledger->getSupplierCompletePurchaseOrder($filter_array);
        $supplier_purcahse_order_total  = $this->model_po_mgmt_po_supplier_ledger->getSupplierCompletePurchaseOrder($filter_array,true);

        $this->data['supplier_name']  = $supplier_details['name'];
        $this->data['supplier_id']    = $supplier_details['supplier_id'];
        $this->data['supplier_email'] = $supplier_details['email'];        

        $this->data['purchase_orders'] = array();

        foreach ($supplier_purcahse_orders as $key => $purcahse_orders) {
          $this->data['purchase_orders'][] = array(
            'id'            => $purcahse_orders['id'],
            'quotation_id'  => $purcahse_orders['quotation_id'],
            'order_id'      => ($purcahse_orders['order_id']) ? $purcahse_orders['order_id'] : 'N/A',
            'purchase_date' => $purcahse_orders['purchase_date'],
            'total'         => $purcahse_orders['total'],
          );
        }

        $supplier_payment_info = $this->model_po_mgmt_po_supplier_ledger->getSupplierDueAmount($this->request->get['supplier_id']);

        $this->data['due_amount'] = $this->currency->format(($supplier_payment_info['total_amount']- $supplier_payment_info['paid_amount']),$this->config->get('config_currency'));

        // get URL quert string for sorting URL code starts here
        $url = $this->setRequestgetVar('pay_page');
        // get URL quert string code ends here
        
        $pagination = new Pagination();
		    $pagination->total = $supplier_purcahse_order_total;
		    $pagination->page = $pay_page;
		    $pagination->limit = $this->config->get('config_limit_admin');
		    $pagination->url = $this->url->link('po_mgmt/po_supplier_ledger/payNow', 'user_token=' . $this->session->data['user_token'] . $url . '&pay_page={page}' . '&supplier_id=' . $this->request->get['supplier_id'], true);

		    $this->data['pagination'] = $pagination->render();

		    $this->data['results'] = sprintf($this->data['text_pagination'], ($supplier_purcahse_order_total) ? (($pay_page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($pay_page - 1) * $this->config->get('config_limit_admin')) > ($supplier_purcahse_order_total - $this->config->get('config_limit_admin'))) ? $supplier_purcahse_order_total : ((($pay_page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $supplier_purcahse_order_total, ceil($supplier_purcahse_order_total / $this->config->get('config_limit_admin')));


        //call common controller function here
        SELF::_getCommonController();

        $this->response->setOutput($this->load->view('po_mgmt/po_supplier_ledger_pay',$this->data));

      } else {
        $this->response->redirect($this->url->link('po_mgmt/po_supplier_ledger', 'user_token=' . $this->session->data['user_token'], true));
      }
      
    }

    private function validateForm() {

      if (!$this->user->hasPermission('modify', 'po_mgmt/po_supplier_ledger')) {
        $this->error['warning'] = $this->data['error_permission'];
      }

      if(isset($this->request->post['selected']) && is_array($this->request->post['selected'])) {
        foreach ($this->request->post['selected'] as $key => $value) {
          if(!$this->model_po_mgmt_po_supplier_ledger->checkQuotationId($this->request->get['supplier_id'],$value)) {
            $this->error['selected'] = $this->data['error_valid_quotation'];
          }
        }
      } else {
        $this->error['selected'] = $this->data['error_empty'];
      }

      if(!isset($this->request->post['comment']) || ((utf8_strlen(trim($this->request->post['comment'])) < 1) || (utf8_strlen($this->request->post['comment']) > 500))) {
        $this->error['comment'] = $this->data['comment_error'];
      }
      
      return !$this->error;
    }

    public function showProfitDetails() {

      if(isset($this->request->get['supplier_id']) && $this->request->get['supplier_id']) {

        $supplier_details = SELF::validateSupplier($this->request->get['supplier_id']);

        if(!$supplier_details) {
          $this->response->redirect($this->url->link('po_mgmt/po_supplier_ledger', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->data['heading_title_details'] = sprintf($this->data['heading_title_details'],$supplier_details['name']);

        $this->document->setTitle($this->data['heading_title_details']);

        $this->data['purcahse_products'] = array();

        //get variable values form the query string code starts here
        $get_request_vars = array(
        'sort'            => 'quotation_id',
        'order'           => 'DESC',
        'profit_page'     => 1,
       );
               
       foreach ($get_request_vars as $key => $value) {
               
         $this->data[$key] = $this->_setGetRequestVar($key,$value);
           $$key = $this->data[$key];
         }
       //get variable values form the query string code ends here
               
       //make filter array  code starts here
       $filter_array = array(  
        'supplier_id'     => $this->request->get['supplier_id'],      
        'order'           => $order,
        'sort'            => $sort,
        'start'           => ($profit_page - 1) * $this->config->get('config_limit_admin'),
        'limit'           => $this->config->get('config_limit_admin'),
       );
       //make filter array code ends here

        $supplier_purcahse_order_products = $this->model_po_mgmt_po_supplier_ledger->getPurcahseOrderProducts($filter_array);
        $supplier_purcahse_order_product_total  = $this->model_po_mgmt_po_supplier_ledger->getPurcahseOrderProducts($filter_array,true);

        if($supplier_purcahse_order_products) {
          foreach($supplier_purcahse_order_products as $product) {

            $quotation_info = $this->model_po_mgmt_po_supplier_ledger->getQuotationInfo($product['quotation_id']);

            $tax_amount = 0;
            $price_total = 0;
            $price = 0;

            $price_total = $price_total + ($product['ordered_cost'] * $product['ordered_qty']);
            $tax_amount = $tax_amount + (($product['ordered_cost'] * $product['ordered_qty']) * $product['tax']) / 100;

            $price = ($price_total + $tax_amount);

            //get quote product price
            $quote_price = $this->model_po_mgmt_po_supplier_ledger->getQuoteProductPrice($this->request->get['supplier_id'],$product['quotation_id'],$product['product_id']);

            $profit_loss = '';

            if($quote_price) {
              if($quote_price['quotation_cost'] > $product['ordered_cost']) {
                $profit_loss = '+' . $this->currency->format((($quote_price['quotation_cost'] - $product['ordered_cost']) * $product['ordered_qty']),$quotation_info['currency_code']);
              } elseif($quote_price['quotation_cost'] < $product['ordered_cost']) {
                $profit_loss = '-' . $this->currency->format((($product['ordered_cost'] - $quote_price['quotation_cost']) * $product['ordered_qty']),$quotation_info['currency_code']);
              } else {
                $profit_loss = 0;
              }
            }

            $this->data['purcahse_products'][] = array(
              'purchase_date' => ($quotation_info['purchase_date'] != '0000-00-00 00:00:00') ? DATE("Y-m-d",strtotime($quotation_info['purchase_date'])) : '0000-00-00',
              'invoice'       => $quotation_info['quotation_id'],
              'status'        => ($quotation_info['paid_status']) ? $this->data['text_paid'] : $this->data['text_not_paid'],
              'credit'        =>  $this->currency->format($price,$quotation_info['currency_code']),
              'debit'         => ($quotation_info['paid_status']) ? $this->currency->format($price,$quotation_info['currency_code']) : $this->currency->format('0.00',$quotation_info['currency_code']),
              'profit_loss'   => $profit_loss,
            );
          }
        }

        // get URL quert string for sorting URL code starts here
        $url = $this->setRequestgetVar('profit_page');
        // get URL quert string code ends here

        $pagination = new Pagination();
		    $pagination->total = $supplier_purcahse_order_product_total;
		    $pagination->page = $profit_page;
		    $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('po_mgmt/po_supplier_ledger/showProfitDetails', 'user_token=' . $this->session->data['user_token'] . $url . '&profit_page={page}' . '&supplier_id=' . $this->request->get['supplier_id'], true);

		    $this->data['pagination'] = $pagination->render();

		    $this->data['results'] = sprintf($this->data['text_pagination'], ($supplier_purcahse_order_product_total) ? (($profit_page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($profit_page - 1) * $this->config->get('config_limit_admin')) > ($supplier_purcahse_order_product_total - $this->config->get('config_limit_admin'))) ? $supplier_purcahse_order_product_total : ((($profit_page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $supplier_purcahse_order_product_total, ceil($supplier_purcahse_order_product_total / $this->config->get('config_limit_admin')));
      
        //call common controller function here
        SELF::_getCommonController();

        $this->response->setOutput($this->load->view('po_mgmt/po_supplier_ledger_details',$this->data));
      } else {
        $this->response->redirect($this->url->link('po_mgmt/po_supplier_ledger', 'user_token=' . $this->session->data['user_token'], true));
      }
    }

    private function validateSupplier($supplier_id) {

      $supplier_details =  $this->model_po_mgmt_po_supplier_ledger->getSupplierDetails($supplier_id);

      if($supplier_details && isset($supplier_details['name'])) {
        return $supplier_details;
      } else {
        return false;
      }
    }

    public function supplierAutoComplete() {

        $json = array();

        if(isset($this->request->get['filter_supplier'])) {

            $filter_array = array(
             'filter_supplier' => $this->request->get['filter_supplier'],
             'start'       => 0,
             'limit'       => 5,
            );

           $result = $this->model_po_mgmt_po_supplier_ledger->supplierAutoComplete($filter_array);

           foreach ($result as $key => $value) {
            $json[] = array(
                'supplier_id' => $value['supplier_id'],
                'name'        => $value['name'],
            );
           }

        }

        $this->response->addHeader('Content-type:application/json');
        $this->response->setOutput(json_encode($json));
    }

    function setRequestgetVar($type = '') {  

        $order = 'ASC';
    
        if(isset($this->request->get['order']) && $this->request->get['order'])
          $order = $this->request->get['order'];
         
        //setting get variable in URL code starts here
        $url = '';
         
        $uri_component = array(
         'filter_supplier',
         'filter_date',   
         'sort',
         'page',
         'pay_page',
         'profit_page',
        );
    
        switch ($type) {
          case 'page':
            foreach ($uri_component as $key => $value) {
              if($value != 'page')
              $url .=  $this->_setStringURLs($value);
            }
            break;
          case 'pay_page':
            foreach ($uri_component as $key => $value) {
              if($value != 'pay_page')
              $url .=  $this->_setStringURLs($value);
            }
            break;
          case 'sort':
            foreach ($uri_component as $key => $value) {
              if($value != 'sort')
                $url .=  $this->_setStringURLs($value);
              }
              if ($order == 'ASC') {
                $order = 'DESC';
              } else {
                $order = 'ASC';
              }
            break;
          
          default:
            foreach ($uri_component as $key => $value) {
              $url .=  $this->_setStringURLs($value);
            }
            break;
        }
    
        $url .= '&order=' . $order;        
            
        //setting get variable in URL code ends here
    
        return $url;
    } 
    
    /**
    * [_setStringURLs append string type variable set in the url]
    * @param [type] $filter_var [usr key]
    */
    public function _setStringURLs($filter_var) {
        return isset($this->request->get[$filter_var]) ? '&' . $filter_var . '=' . urlencode(html_entity_decode($this->request->get[$filter_var], ENT_QUOTES, 'UTF-8')): '';
    }

    /**
	* [_setGetRequestVar assign a get request variable value if set otherwise default val]
	* @param [type] $key [get var key2]
	* @param [type] $val [default values if not set]
	*/
    public function _setGetRequestVar($key, $val) {
        return isset($this->request->get[$key]) ? $this->request->get[$key] : $val;
    }

}
?>