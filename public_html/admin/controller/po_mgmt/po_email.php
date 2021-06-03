<?php
class ControllerPOMgmtPOEmail extends Controller {
	 private $error = array();

    public function __construct($registory) {
      parent::__construct($registory);
      $this->load->model('po_mgmt/po_email');
      $this->_poEmail = $this->model_po_mgmt_po_email;
			if (!$this->config->get('module_oc_pom_status')) {
				$this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
			}
    }
  	public function index() {
      $this->load->language('po_mgmt/po_email');

  		$this->document->setTitle($this->language->get('heading_title_list'));
  		$this->getList();
  	}

  	public function getlist() {
      $data = array();
  		$data = array_merge($data, $this->load->language('po_mgmt/po_email'));

      if (isset($this->request->get['filter_id'])) {
  			$filter_id = $this->request->get['filter_id'];
  		} else {
  			$filter_id = null;
  		}

      if (isset($this->request->get['filter_name'])) {
  			$filter_name = $this->request->get['filter_name'];
  		} else {
  			$filter_name = null;
  		}

  		if (isset($this->request->get['filter_message'])) {
  			$filter_message = $this->request->get['filter_message'];
  		} else {
  			$filter_message = '';
  		}

  		if (isset($this->request->get['filter_subject'])) {
  			$filter_subject = $this->request->get['filter_subject'];
  		} else {
  			$filter_subject = null;
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

      if (isset($this->request->get['filter_id'])) {
  			$url .= '&filter_id=' . $this->request->get['filter_id'];
  		}

  		if (isset($this->request->get['filter_name'])) {
  			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
  		}

      if (isset($this->request->get['filter_message'])) {
  			$url .= '&filter_message=' . urlencode(html_entity_decode($this->request->get['filter_message'], ENT_QUOTES, 'UTF-8'));
  		}

      if (isset($this->request->get['filter_subject'])) {
  			$url .= '&filter_subject=' . urlencode(html_entity_decode($this->request->get['filter_subject'], ENT_QUOTES, 'UTF-8'));
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
       		 'text'      => $this->language->get('text_home'),
			     'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']. $url, true),
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title_list'),
			    'href'      => $this->url->link('po_mgmt/po_email', 'user_token=' . $this->session->data['user_token']. $url, true),
   		);

    $filter_data = array(
      'filter_id'	               => $filter_id,
      'filter_name'	             => $filter_name,
			'filter_message'           => $filter_message,
			'filter_subject'           => $filter_subject,
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

    $product_total = $this->_poEmail->getPOEmailTotal($filter_data);

    $results = $this->_poEmail->getPOEmailList($filter_data);

		$data['po_emails'] = array();

    foreach ($results as $result) {
         $data['po_emails'][] = array(
        			'id'               => $result['id'],
        			'name'             => $result['name'],
        			'subject'          => $result['subject'],
              'message'          => html_entity_decode($result['message']),
        			'action'           => $this->url->link('po_mgmt/po_email/getForm', 'user_token=' . $this->session->data['user_token'] . '&mail_id=' . $result['id'], true),
    		);
		}

 		$data['user_token'] = $this->session->data['user_token'];

		$data['delete'] = $this->url->link('po_mgmt/po_email/delete', 'user_token=' . $this->session->data['user_token'] , true);

		$data['insert'] = $this->url->link('po_mgmt/po_email/getForm', 'user_token=' . $this->session->data['user_token'] , true);

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

    if (isset($this->request->get['filter_id'])) {
      $url .= '&filter_id=' . $this->request->get['filter_id'];
    }

    if (isset($this->request->get['filter_name'])) {
      $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_message'])) {
      $url .= '&filter_message=' . urlencode(html_entity_decode($this->request->get['filter_message'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_subject'])) {
      $url .= '&filter_subject=' . urlencode(html_entity_decode($this->request->get['filter_subject'], ENT_QUOTES, 'UTF-8'));
    }

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name']    = $this->url->link('po_mgmt/po_email', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_id']      = $this->url->link('po_mgmt/po_email', 'user_token=' . $this->session->data['user_token'] . '&sort=id' . $url, true);
		$data['sort_message'] = $this->url->link('po_mgmt/po_email', 'user_token=' . $this->session->data['user_token'] . '&sort=message' . $url, true);
		$data['sort_subject'] = $this->url->link('po_mgmt/po_email', 'user_token=' . $this->session->data['user_token'] . '&sort=subject' . $url, true);

    $url = '';

    if (isset($this->request->get['filter_id'])) {
      $url .= '&filter_id=' . $this->request->get['filter_id'];
    }

    if (isset($this->request->get['filter_name'])) {
      $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_message'])) {
      $url .= '&filter_message=' . urlencode(html_entity_decode($this->request->get['filter_message'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_subject'])) {
      $url .= '&filter_subject=' . urlencode(html_entity_decode($this->request->get['filter_subject'], ENT_QUOTES, 'UTF-8'));
    }

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

    $pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('po_mgmt/po_email', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

    $data['filter_id']            = $filter_id;
    $data['filter_name']          = $filter_name;
    $data['filter_message']       = $filter_message;
    $data['filter_subject']       = $filter_subject;
    $data['sort']                 = $sort;
    $data['order']                = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		$data['column_left'] = $this->load->controller('common/column_left');

		$this->response->setOutput($this->load->view('po_mgmt/po_email_list',$data));
  }

  public function getForm() {
    $data = array();
    $data = array_merge($data, $this->load->language('po_mgmt/po_email'));

    if(isset($this->request->get['mail_id']) || isset($this->request->post['mail_id'])){
      $data['heading_title'] = $this->language->get('heading_title_edit');
      $this->document->setTitle($this->language->get('heading_title_edit'));
    }else{
      $data['heading_title'] = $this->language->get('heading_title_add');
      $this->document->setTitle($this->language->get('heading_title_add'));
    }

    $data['email_indexes'] = '{quotation_id}
															{quotation_details}
                              {comment}
															{new_product}
															{update_quotation}
                              {supplier_name}
                              {supplier_company_name}
                              {supplier_email}
                              {supplier_website}
                              {supplier_telephone}
                              {supplier_fax}
                              {supplier_address}
                              {shipping_address}
                              {order_source}
                              {config_logo}
                              {config_icon}
                              {config_currency}
                              {config_image}
                              {config_name}
                              {config_owner}
                              {config_address}
                              {config_geocode}
                              {config_email}
                              {config_telephone}';

		$post_data = array(
							'mail_id',
							'name',
							'message',
							'subject',
							);

		foreach($post_data as $post){
			if(isset($this->request->post[$post])){
				$data[$post] = $this->request->post[$post];
			}else{
				$data[$post] = '';
			}
		}

		if(isset($this->request->get['mail_id'])){
    		$result = $this->_poEmail->getMailTemplate($this->request->get['mail_id']);
    		if($result)
    			foreach($result as $key => $value){
    				$data[$key] = $value;
    			}

    		$data['mail_id'] = $this->request->get['mail_id'];
		}

		$data['user_token'] = $this->session->data['user_token'];


    $error_array = array(
      'warning',
      'name',
      'subject',
      'message',
    );
    foreach ($error_array as $key => $error) {
        if (isset($this->error[$error])) {
          $data['error_'.$error] = $this->error[$error];
        } else {
          $data['error_'.$error] = '';
        }
    }

		$url = '';

		$data['breadcrumbs'] = array();

 		$data['breadcrumbs'][] = array(
     		'text'      => $this->language->get('text_home'),
		     'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
 		);

 		$data['breadcrumbs'][] = array(
     		'text'      => $this->language->get('heading_title_list'),
		    'href'      => $this->url->link('po_mgmt/po_email', 'user_token=' . $this->session->data['user_token'] . $url, true),
 		);

    $data['cancel'] = $this->url->link('po_mgmt/po_email', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['save'] = $this->url->link('po_mgmt/po_email/mailSave', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$mailValues = str_replace('<br />', ',', nl2br($data['email_indexes']));
		$mailValues = explode(',', $mailValues);
		$find = array();
		foreach ($mailValues as $key => $value) {
			$find[] = trim($value);
		}

		$data['mail_help'] = $find;

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		$data['column_left'] = $this->load->controller('common/column_left');
		$this->response->setOutput($this->load->view('po_mgmt/po_email_form',$data));

	}

  public function mailSave() {
  	$this->load->language('po_mgmt/po_email');
    	 if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			//remove script tag from the description code starts here
			$this->registry->set('Htmlfilter', new Htmlfilter($this->registry));

			if($this->request->post['message']) {
				$this->request->post['message'] = 	htmlentities($this->Htmlfilter->HTMLFilter(html_entity_decode($this->request->post['message']),'',true));
			}
			//remove script tag from the description code ends here
			

  		    $this->_poEmail->addEmailTemplate($this->request->post);

      		if($this->request->post['mail_id'])
  					$this->session->data['success'] = $this->language->get('text_success_update');
  				else
  					$this->session->data['success'] = $this->language->get('text_success_add');

  			$this->response->redirect($this->url->link('po_mgmt/po_email', 'user_token=' . $this->session->data['user_token'], true));
  	   }

  	$this->getForm();
  }

  public function delete() {
  	$this->language->load('po_mgmt/po_email');
  	$this->document->setTitle($this->language->get('heading_title_delete'));

    		if (isset($this->request->post['selected']) && $this->validateDelete()) {
    			foreach ($this->request->post['selected'] as $id) {
    				$this->_poEmail->deletEmailTemplate($id);
    	  	}
    			$this->session->data['success'] = $this->language->get('text_success_delete');

    			$url='';

    			if (isset($this->request->get['page'])) {
    				$url .= '&page=' . $this->request->get['page'];
    			}

    			$this->response->redirect($this->url->link('po_mgmt/po_email', 'user_token=' . $this->session->data['user_token'] . $url, true));
    		}
  	$this->index();
	}

  private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'po_mgmt/po_email')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
    return !$this->error;
  }

	private function validate() {
		if (!$this->user->hasPermission('modify', 'po_mgmt/po_email')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen(trim($this->request->post['message'])) < 25) || (utf8_strlen(trim($this->request->post['message'])) > 5000)) {
        $this->error['message'] = $this->language->get('error_message');
    }

    if ((utf8_strlen(trim($this->request->post['subject'])) < 5) || (utf8_strlen(trim($this->request->post['subject'])) > 1000)) {
        $this->error['subject'] = $this->language->get('error_subject');
    }

    if ((utf8_strlen(trim($this->request->post['name'])) < 5) || (utf8_strlen(trim($this->request->post['name'])) > 150)) {
        $this->error['name'] = $this->language->get('error_name');
    }

    return !$this->error;
	}
}
?>
