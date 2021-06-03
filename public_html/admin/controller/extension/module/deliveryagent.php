<?php
class ControllerExtensionModuleDeliveryAgent extends Controller
{
    private $error = array();
    public function index()
    {
        $this->load->language('extension/module/deliveryagent');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/deliveryagent');
        $this->model_extension_module_deliveryagent->createTable();
        $this->getList();
    }
    public function add()
    {
        $this->load->language('extension/module/deliveryagent');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/deliveryagent');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_module_deliveryagent->adddeliveryagent($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $url                            = '';
            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
            $this->response->redirect($this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'] . $url, TRUE));
        }
        $this->getForm();
    }
    public function edit()
    {
        $this->load->language('extension/module/deliveryagent');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/deliveryagent');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_module_deliveryagent->editdeliveryagent($this->request->get['deliveryagent_id'], $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $url                            = '';
            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
            $this->response->redirect($this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'] . $url, TRUE));
        }
        $this->getForm();
    }
    public function delete()
    {
        $this->load->language('extension/module/deliveryagent');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/deliveryagent');
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $deliveryagent_id) {
                $this->model_extension_module_deliveryagent->deletedeliveryagent($deliveryagent_id);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $url                            = '';
            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
            $this->response->redirect($this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'] . $url, TRUE));
        }
        $this->getList();
    }
    protected function getList()
    {
        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }
        if (isset($this->request->get['filter_email'])) {
            $filter_email = $this->request->get['filter_email'];
        } else {
            $filter_email = null;
        }
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'firstname';
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
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
        $data['breadcrumbs']   = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'] . $url, TRUE)
        );
        $data['menu']          = $this->getMenu();
        $data['add']           = $this->url->link('extension/module/deliveryagent/add', 'user_token=' . $this->session->data['user_token'], TRUE);
        $data['delete']        = $this->url->link('extension/module/deliveryagent/delete', 'user_token=' . $this->session->data['user_token'], TRUE);
        $this->document->addScript('view/javascript/jquery/deliveryagent.js');
        $data['deliveryagents'] = array();
        $filter_data            = array(
            'filter_name' => $filter_name,
            'filter_email' => $filter_email,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );
        $deliveryagent_total    = $this->model_extension_module_deliveryagent->getTotaldeliveryagents($filter_data);
        $results                = $this->model_extension_module_deliveryagent->getdeliveryagents($filter_data);
        foreach ($results as $result) {
            $data['deliveryagents'][] = array(
                'deliveryagent_id' => $result['deliveryagent_id'],
                'name' => $result['firstname'] . " " . $result['lastname'],
                'email' => $result['email'],
                'status' => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'edit' => $this->url->link('extension/module/deliveryagent/edit', 'user_token=' . $this->session->data['user_token'] . '&deliveryagent_id=' . $result['deliveryagent_id'] . $url, TRUE)
            );
        }
        $data['user_token'] = $this->session->data['user_token'];
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
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }
        $url = '';
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
        $data['sort_name']  = $this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, TRUE);
        $data['sort_email'] = $this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'] . '&sort=c.email' . $url, TRUE);
        $url                = '';
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
        $pagination           = new Pagination();
        $pagination->total    = $deliveryagent_total;
        $pagination->page     = $page;
        $pagination->limit    = $this->config->get('config_limit_admin');
        $pagination->url      = $this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', TRUE);

        $data['pagination']   = $pagination->render();
        $data['results']      = sprintf($this->language->get('text_pagination'), ($deliveryagent_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($deliveryagent_total - $this->config->get('config_limit_admin'))) ? $deliveryagent_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $deliveryagent_total, ceil($deliveryagent_total / $this->config->get('config_limit_admin')));
        $data['filter_name']  = $filter_name;
        $data['filter_email'] = $filter_email;
        $this->load->model('localisation/order_status');
        $data['order_statuses']                  = $this->model_localisation_order_status->getOrderStatuses();
        $data['deliveryagent_orderstatus']       = $this->config->get('deliveryagent_orderstatus');
        $data['deliveryagent_orderstatuschange'] = $this->config->get('deliveryagent_orderstatuschange');
        if ($this->request->server['HTTPS']) {
            $data['frontendlogin'] = HTTPS_CATALOG . "index.php?route=extension/module/deliveryagent";
        } else {
            $data['frontendlogin'] = HTTP_CATALOG . "index.php?route=extension/module/deliveryagent";
        }
        $data['sort']                    = $sort;
        $data['order']                   = $order;
        $data['header']                  = $this->load->controller('common/header');
        $data['column_left']             = $this->load->controller('common/column_left');
        $data['footer']                  = $this->load->controller('common/footer');
        $data['oc_licensing_home']       = 'https://www.cartbinder.com/store/';
        $data['extension_id']            = 35269;
        $admin_support_email             = 'support@cartbinder.com';
        $data['license_purchase_thanks'] = sprintf($this->language->get('license_purchase_thanks'), $admin_support_email);
        if (isset($this->request->get['emailmal'])) {
            $data['emailmal'] = true;
        }
        if (isset($this->request->get['regerror'])) {
            if ($this->request->get['regerror'] == 'emailmal') {
                $this->error['warning'] = $this->language->get('regerror_email');
            } elseif ($this->request->get['regerror'] == 'orderidmal') {
                $this->error['warning'] = $this->language->get('regerror_orderid');
            } elseif ($this->request->get['regerror'] == 'noreferer') {
                $this->error['warning'] = $this->language->get('regerror_noreferer');
            } elseif ($this->request->get['regerror'] == 'localhost') {
                $this->error['warning'] = $this->language->get('regerror_localhost');
            } elseif ($this->request->get['regerror'] == 'licensedupe') {
                $this->error['warning'] = $this->language->get('regerror_licensedupe');
            }
        }
        $domainssl        = explode("//", 'HTTPS_SERVER');
        $domainnonssl     = explode("//", HTTP_SERVER);
        echo HTTP_SERVER;die;
        $domain           = ($domainssl[1] != '' ? $domainssl[1] : $domainnonssl[1]);
        $data['domain']   = $domain;
        $data['licensed'] = @file_get_contents($data['oc_licensing_home'] . 'licensed.php?domain=' . $domain . '&extension=' . $data['extension_id']);
        if ($data['licensed'] || $data['licensed'] != '') {
            if (extension_loaded('curl')) {
                $post_data = array(
                    'domain' => $domain,
                    'extension' => $data['extension_id']
                );
                $curl      = curl_init();
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
                $follow_allowed = (ini_get('open_basedir') || ini_get('safe_mode')) ? false : true;
                if ($follow_allowed) {
                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
                }
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 9);
                curl_setopt($curl, CURLOPT_TIMEOUT, 60);
                curl_setopt($curl, CURLOPT_AUTOREFERER, true);
                curl_setopt($curl, CURLOPT_VERBOSE, 1);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_URL, $data['oc_licensing_home'] . 'licensed.php');
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
                $data['licensed'] = curl_exec($curl);
                curl_close($curl);
            } else {
                $data['licensed'] = 'curl';
            }
        }
        $data['licensed_md5']       = md5($data['licensed']);
        $data['entry_free_support'] = $this->language->get('entry_free_support');
        $order_details              = @file_get_contents($data['oc_licensing_home'] . 'order_details.php?domain=' . $domain . '&extension=' . $data['extension_id']);
        $order_data                 = json_decode($order_details, true);
        if (is_array($order_data) || $order_data != '') {
            if (extension_loaded('curl')) {
                $post_data2 = array(
                    'domain' => $domain,
                    'extension' => $data['extension_id']
                );
                $curl2 = curl_init();
                curl_setopt($curl2, CURLOPT_HEADER, false);
                curl_setopt($curl2, CURLINFO_HEADER_OUT, true);
                curl_setopt($curl2, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
                $follow_allowed2 = (ini_get('open_basedir') || ini_get('safe_mode')) ? false : true;
                if ($follow_allowed2) {
                    curl_setopt($curl2, CURLOPT_FOLLOWLOCATION, 1);
                }
                curl_setopt($curl2, CURLOPT_CONNECTTIMEOUT, 9);
                curl_setopt($curl2, CURLOPT_TIMEOUT, 60);
                curl_setopt($curl2, CURLOPT_AUTOREFERER, true);
                curl_setopt($curl2, CURLOPT_VERBOSE, 1);
                curl_setopt($curl2, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl2, CURLOPT_FORBID_REUSE, false);
                curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl2, CURLOPT_URL, $data['oc_licensing_home'] . 'order_details.php');
                curl_setopt($curl2, CURLOPT_POST, true);
                curl_setopt($curl2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
                $order_data = json_decode(curl_exec($curl2), true);
                curl_close($curl2);
            } else {
                $order_data['status'] = 'disabled';
            }
        }
        if (isset($order_data['status']) && $order_data['status'] == 'enabled') {
            $isSecure = false;
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
                $isSecure = true;
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
                $isSecure = true;
            }
            $data['support_status']          = 'enabled';
            $data['support_order_id']        = $order_data['order_id'];
            $data['support_extension_name']  = $order_data['extension_name'];
            $data['support_domain']          = $order_data['domain'];
            $data['support_username']        = $order_data['username'];
            $data['support_email']           = $order_data['email'];
            $data['support_registered_date'] = strftime('%Y-%m-%d', $order_data['registered_date']);
            $data['support_order_date']      = strftime('%Y-%m-%d', ($order_data['order_date'] + 31536000));
            if ((time() - $order_data['order_date']) > 31536000) {
                $data['text_free_support_remaining'] = sprintf($this->language->get('text_free_support_expired'), 1, ($isSecure ? 1 : 0), urlencode($domain), $data['extension_id'], $this->session->data['token']);
            } else {
                $data['text_free_support_remaining'] = sprintf($this->language->get('text_free_support_remaining'), 366 - ceil((time() - $order_data['order_date']) / 86400));
            }
        } else {
            $data['support_status']              = 'disabled';
            $data['text_free_support_remaining'] = sprintf($this->language->get('text_free_support_remaining'), 'unknown');
        }
        return $this->response->setOutput($this->load->view('extension/module/deliveryagent_list', $data));
    }
    protected function getForm()
    {
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_form']     = !isset($this->request->get['deliveryagent_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['user_token']    = $this->session->data['user_token'];
        if (isset($this->request->get['deliveryagent_id'])) {
            $data['deliveryagent_id'] = $this->request->get['deliveryagent_id'];
        } else {
            $data['deliveryagent_id'] = 0;
        }
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        if (isset($this->error['firstname'])) {
            $data['error_firstname'] = $this->error['firstname'];
        } else {
            $data['error_firstname'] = '';
        }
        if (isset($this->error['lastname'])) {
            $data['error_lastname'] = $this->error['lastname'];
        } else {
            $data['error_lastname'] = '';
        }
        if (isset($this->error['password'])) {
            $data['error_password'] = $this->error['password'];
        } else {
            $data['error_password'] = '';
        }
        if (isset($this->error['confirm'])) {
            $data['error_confirm'] = $this->error['confirm'];
        } else {
            $data['error_confirm'] = '';
        }
        if (isset($this->error['email'])) {
            $data['error_email'] = $this->error['email'];
        } else {
            $data['error_email'] = '';
        }
        if (isset($this->error['telephone'])) {
            $data['error_telephone'] = $this->error['telephone'];
        } else {
            $data['error_telephone'] = '';
        }
        $url = '';
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
        $data['breadcrumbs']   = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'] . $url, TRUE)
        );
        if (!isset($this->request->get['deliveryagent_id'])) {
            $data['action'] = $this->url->link('extension/module/deliveryagent/add', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
        } else {
            $data['action'] = $this->url->link('extension/module/deliveryagent/edit', 'user_token=' . $this->session->data['user_token'] . '&deliveryagent_id=' . $this->request->get['deliveryagent_id'] . $url, TRUE);
        }
        $data['cancel'] = $this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'] . $url, TRUE);
        if (isset($this->request->get['deliveryagent_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $deliveryagent_info = $this->model_extension_module_deliveryagent->getdeliveryagent($this->request->get['deliveryagent_id']);
        }
        if (isset($this->request->post['firstname'])) {
            $data['firstname'] = $this->request->post['firstname'];
        } elseif (!empty($deliveryagent_info)) {
            $data['firstname'] = $deliveryagent_info['firstname'];
        } else {
            $data['firstname'] = '';
        }
        if (isset($this->request->post['lastname'])) {
            $data['lastname'] = $this->request->post['lastname'];
        } elseif (!empty($deliveryagent_info)) {
            $data['lastname'] = $deliveryagent_info['lastname'];
        } else {
            $data['lastname'] = '';
        }
        if (isset($this->request->post['extras'])) {
            $data['extras'] = $this->request->post['extras'];
        } elseif (!empty($deliveryagent_info)) {
            $data['extras'] = $deliveryagent_info['extras'];
        } else {
            $data['extras'] = '';
        }
        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } elseif (!empty($deliveryagent_info)) {
            $data['email'] = $deliveryagent_info['email'];
        } else {
            $data['email'] = '';
        }
        if (isset($this->request->post['telephone'])) {
            $data['telephone'] = $this->request->post['telephone'];
        } elseif (!empty($deliveryagent_info)) {
            $data['telephone'] = $deliveryagent_info['telephone'];
        } else {
            $data['telephone'] = '';
        }
        if (isset($this->request->post['alertemail'])) {
            $data['alertemail'] = $this->request->post['alertemail'];
        } elseif (!empty($deliveryagent_info)) {
            $data['alertemail'] = $deliveryagent_info['alertemail'];
        } else {
            $data['alertemail'] = '1';
        }
        if (isset($this->request->post['password'])) {
            $data['password'] = $this->request->post['password'];
        } else {
            $data['password'] = '';
        }
        if (isset($this->request->post['confirm'])) {
            $data['confirm'] = $this->request->post['confirm'];
        } else {
            $data['confirm'] = '';
        }
        if (isset($this->request->post['commission'])) {
            $data['commission'] = $this->request->post['commission'];
        } elseif (!empty($deliveryagent_info)) {
            $data['commission'] = $deliveryagent_info['commission'];
        } else {
            $data['commission'] = 5;
        }
        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($deliveryagent_info)) {
            $data['status'] = $deliveryagent_info['status'];
        } else {
            $data['status'] = true;
        }
        if (isset($this->request->post['city'])) {
            $data['city'] = $this->request->post['city'];
        } elseif (!empty($deliveryagent_info)) {
            $data['city'] = $deliveryagent_info['city'];
        } else {
            $data['city'] = "";
        }
        if (isset($this->request->post['address'])) {
            $data['address'] = $this->request->post['address'];
        } elseif (!empty($deliveryagent_info)) {
            $data['address'] = $deliveryagent_info['address'];
        } else {
            $data['address'] = "";
        }
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/deliveryagent_form', $data));
    }
    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/deliveryagent')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }
        if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }
        if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
            $this->error['email'] = $this->language->get('error_email');
        }
        $deliveryagent_info = $this->model_extension_module_deliveryagent->getdeliveryagentByEmail($this->request->post['email']);
        if (!isset($this->request->get['deliveryagent_id'])) {
            if ($deliveryagent_info) {
                $this->error['warning'] = $this->language->get('error_exists');
            }
        } else {
            if ($deliveryagent_info && ($this->request->get['deliveryagent_id'] != $deliveryagent_info['deliveryagent_id'])) {
                $this->error['warning'] = $this->language->get('error_exists');
            }
        }
        if ($this->request->post['password'] || (!isset($this->request->get['deliveryagent_id']))) {
            if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
                $this->error['password'] = $this->language->get('error_password');
            }
            if ($this->request->post['password'] != $this->request->post['confirm']) {
                $this->error['confirm'] = $this->language->get('error_confirm');
            }
        }
        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }
        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }
        return !$this->error;
    }
    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/deliveryagent')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }
    public function autocomplete()
    {
        $json = array();
        if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_email'])) {
            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }
            if (isset($this->request->get['filter_email'])) {
                $filter_email = $this->request->get['filter_email'];
            } else {
                $filter_email = '';
            }
            $this->load->model('extension/module/deliveryagent');
            $filter_data = array(
                'filter_name' => $filter_name,
                'filter_email' => $filter_email,
                'start' => 0,
                'limit' => 5
            );
            $results     = $this->model_extension_module_deliveryagent->getdeliveryagents($filter_data);
            foreach ($results as $result) {
                $json[] = array(
                    'deliveryagent_id' => $result['deliveryagent_id'],
                    'parentname' => $this->model_extension_module_deliveryagent->getSecondParentName($result['deliveryagent_id']),
                    'firstname' => $result['firstname'],
                    'lastname' => $result['lastname'],
                    'name' => $result['firstname'] . " " . $result['lastname'],
                    'email' => $result['email'],
                    'telephone' => $result['telephone'],
                    'fax' => $result['fax']
                );
            }
        }
        $sort_order = array();
        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['firstname'];
        }
        array_multisort($sort_order, SORT_ASC, $json);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function getMenu()
    {
        $data['list']              = $this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'], TRUE);
        $data['timeslots']         = $this->url->link('extension/module/deliveryagent/getTimeList', 'user_token=' . $this->session->data['user_token'], TRUE);
        $data['assignment']        = $this->url->link('extension/module/deliveryagent/getReport', 'user_token=' . $this->session->data['user_token'], TRUE);
        $data['transactionreport'] = $this->url->link('extension/module/deliveryagent/transactionreport', 'user_token=' . $this->session->data['user_token'], TRUE);
        return $this->load->view('extension/module/deliveryagent_menu', $data);
    }
    public function insertTime()
    {
        $this->load->language('extension/module/deliveryagent');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/deliveryagent');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateTimeForm()) {
            $this->model_extension_module_deliveryagent->addtime($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/deliveryagent/getTimeList', 'user_token=' . $this->session->data['user_token'], TRUE));
        }
        $this->getTimeForm();
    }
    public function updateTime()
    {
        $this->load->language('extension/module/deliveryagent');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/deliveryagent');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateTimeForm()) {
            $this->model_extension_module_deliveryagent->edittime($this->request->post, $this->request->get['timeid']);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/deliveryagent/getTimeList', 'user_token=' . $this->session->data['user_token'], TRUE));
        }
        $this->getTimeForm();
    }
    public function deleteTime()
    {
        $this->load->language('extension/module/deliveryagent');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/deliveryagent');
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $timeid) {
                $this->model_extension_module_deliveryagent->deleteTime($timeid);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/deliveryagent/getTimeList', 'user_token=' . $this->session->data['user_token'], TRUE));
        }
        $this->getTimeList();
    }
    public function getTimeList()
    {
        $this->load->model("extension/module/deliveryagent");
        $this->load->language("extension/module/deliveryagent");
        $this->document->setTitle($this->language->get('heading_title_timelist'));
        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/dashbaord', 'user_token=' . $this->session->data['user_token'], TRUE),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );
        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('extension/module/deliveryagent/getTimeList', 'user_token=' . $this->session->data['user_token'], TRUE),
            'text' => $this->language->get('heading_title_timelist'),
            'separator' => ' :: '
        );
        $data['insert']        = $this->url->link('extension/module/deliveryagent/insertTime', 'user_token=' . $this->session->data['user_token'], TRUE);
        $data['delete']        = $this->url->link('extension/module/deliveryagent/deleteTime', 'user_token=' . $this->session->data['user_token'], TRUE);
        $data['cancel']        = $this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'], TRUE);
        $results               = $this->model_extension_module_deliveryagent->getTimeSlots();
        $data['timeslots']     = array();
        foreach ($results as $result) {
            $action              = array();
            $action[]            = array(
                'text' => $this->language->get('button_edit'),
                'href' => $this->url->link('extension/module/deliveryagent/updateTime', 'user_token=' . $this->session->data['user_token'] . '&timeid=' . $result['timeid'], TRUE)
            );
            $data['timeslots'][] = array(
                'timeid' => $result['timeid'],
                'time' => $result['time'],
                'notes' => $result['notes'],
                'action' => $action
            );
        }
        $data['user_token'] = $this->session->data['user_token'];
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
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/deliveryagenttime_list', $data));
    }
    private function getTimeForm()
    {
        $this->load->model("extension/module/deliveryagent");
        $this->load->language("extension/module/deliveryagent");
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        $data['breadcrumbs']   = array();
        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/dashbaord', 'user_token=' . $this->session->data['user_token'], TRUE),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );
        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('extension/module/deliveryagent/getTimeList', 'user_token=' . $this->session->data['user_token'], TRUE),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );
        if (!isset($this->request->get['timeid'])) {
            $data['action'] = $this->url->link('extension/module/deliveryagent/insertTime', 'user_token=' . $this->session->data['user_token'], TRUE);
        } else {
            $data['action'] = $this->url->link('extension/module/deliveryagent/updateTime', 'user_token=' . $this->session->data['user_token'] . '&timeid=' . $this->request->get['timeid'], TRUE);
        }
        $data['user_token'] = $this->session->data['user_token'];
        $data['cancel']     = $this->url->link('extension/module/deliveryagent/getTimeList', 'user_token=' . $this->session->data['user_token'], TRUE);
        if (isset($this->request->get['timeid']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $time_info = $this->model_extension_module_deliveryagent->getTimeSlot($this->request->get['timeid']);
        }
        if (isset($this->request->post['time'])) {
            $data['time'] = $this->request->post['time'];
        } elseif (isset($time_info)) {
            $data['time'] = $time_info['time'];
        } else {
            $data['time'] = '';
        }
        if (isset($this->request->post['notes'])) {
            $data['notes'] = $this->request->post['notes'];
        } elseif (isset($time_info)) {
            $data['notes'] = $time_info['notes'];
        } else {
            $data['notes'] = '';
        }
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/deliveryagenttime_form', $data));
    }
    private function validateTimeForm()
    {
        if (trim($this->request->post['time']) == "") {
            $this->error['warning'] = $this->language->get('error_time');
        }
        if (!$this->user->hasPermission('modify', 'extension/module/deliveryagent')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function getReport()
    {
        $this->load->language('extension/module/deliveryagent');
        $this->load->model('extension/module/deliveryagent');
        $this->document->setTitle($this->language->get('heading_title_report'));
        if (isset($this->request->get['filter_order_id'])) {
            $filter_order_id = $this->request->get['filter_order_id'];
        } else {
            $filter_order_id = '';
        }
        if (isset($this->request->get['filter_customer'])) {
            $filter_customer = $this->request->get['filter_customer'];
        } else {
            $filter_customer = '';
        }
        if (isset($this->request->get['filter_order_status'])) {
            $filter_order_status = $this->request->get['filter_order_status'];
        } else {
            $filter_order_status = '';
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $filter_order_status_id = $this->request->get['filter_order_status_id'];
        } else {
            $filter_order_status_id = '';
        }
        if (isset($this->request->get['filter_total'])) {
            $filter_total = $this->request->get['filter_total'];
        } else {
            $filter_total = '';
        }
        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = '';
        }
        if (isset($this->request->get['filter_date_modified'])) {
            $filter_date_modified = $this->request->get['filter_date_modified'];
        } else {
            $filter_date_modified = '';
        }
        if (isset($this->request->get['filter_delivery_time'])) {
            $filter_delivery_time = $this->request->get['filter_delivery_time'];
        } else {
            $filter_delivery_time = '';
        }
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'o.order_id';
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
        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }
        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }
        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }
        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }
        if (isset($this->request->get['filter_delivery_time'])) {
            $url .= '&filter_delivery_time=' . $this->request->get['filter_delivery_time'];
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
        $data['breadcrumbs']   = array();
        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], TRUE),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );
        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'], TRUE),
            'text' => $this->language->get('heading_title_list'),
            'separator' => ' :: '
        );
        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('extension/module/deliveryagent/getReport', 'user_token=' . $this->session->data['user_token'], TRUE),
            'text' => $this->language->get('heading_title_report'),
            'separator' => ' :: '
        );
        $data['invoice']       = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'], true);
        $data['shipping']      = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'], true);
        $data['add']           = $this->url->link('sale/order/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['orderpage']     = $this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'], TRUE);
        $data['orders']        = array();
        $filter_data           = array(
            'filter_order_id' => $filter_order_id,
            'filter_customer' => $filter_customer,
            'filter_order_status' => $filter_order_status,
            'filter_order_status_id' => $filter_order_status_id,
            'filter_total' => $filter_total,
            'filter_date_added' => $filter_date_added,
            'filter_date_modified' => $filter_date_modified,
            'filter_delivery_time' => $filter_delivery_time,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );
        $order_total           = $this->model_extension_module_deliveryagent->getTotalOrders($filter_data);
        $results               = $this->model_extension_module_deliveryagent->getOrders($filter_data);
        if (!$this->config->get('module_deliveryagent_dateformat')) {
            $format         = "d-m-Y";
            $data['format'] = "DD-MM-YYYY";
        } else {
            $format         = 'm/d/Y';
            $data['format'] = "MM/DD/YYYY";
        }
        foreach ($results as $result) {
            if ($result['deliverydate']) {
                $result['deliverydate'] = date($format, strtotime($result['deliverydate']));
            }
            $data['orders'][] = array(
                'order_id' => $result['order_id'],
                'customer' => $result['customer'],
                'order_status' => $result['order_status'] ? $result['order_status'] : $this->language->get('text_missing'),
                'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
                'sort_order' => $result['sort_order'],
                'deliveryagent_id' => $result['deliveryagent_id'],
                'shipping_code' => $result['shipping_code'],
                'deliverytime' => $result['deliverytime'],
                'timeid' => $result['timeslot_id'],
                'view' => $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true),
                'edit' => $this->url->link('sale/order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true)
            );
        }
        $time_slots_results = $this->model_extension_module_deliveryagent->getTimeSlots();
        $data['timeslots']  = array();
        foreach ($time_slots_results as $result) {
            $data['timeslots'][] = array(
                'timeid' => $result['timeid'],
                'time' => $result['time'],
                'notes' => $result['notes']
            );
        }
        $deliveryagents_results = $this->model_extension_module_deliveryagent->getdeliveryagents();
        $data['deliveryagents'] = array();
        foreach ($deliveryagents_results as $result) {
            $data['deliveryagents'][] = array(
                'deliveryagent_id' => $result['deliveryagent_id'],
                'name' => $result['name']
            );
        }
        $data['user_token'] = $this->session->data['user_token'];
        $url                = '';
        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }
        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }
        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }
        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }
        if (isset($this->request->get['filter_delivery_time'])) {
            $url .= '&filter_delivery_time=' . $this->request->get['filter_delivery_time'];
        }
        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $data['sort_order']         = $this->url->link('extension/module/deliveryagent/getReport', 'user_token=' . $this->session->data['user_token'] . '&sort=o.order_id' . $url, true);
        $data['sort_customer']      = $this->url->link('extension/module/deliveryagent/getReport', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
        $data['sort_status']        = $this->url->link('extension/module/deliveryagent/getReport', 'user_token=' . $this->session->data['user_token'] . '&sort=order_status' . $url, true);
        $data['sort_total']         = $this->url->link('extension/module/deliveryagent/getReport', 'user_token=' . $this->session->data['user_token'] . '&sort=o.total' . $url, true);
        $data['sort_date_added']    = $this->url->link('extension/module/deliveryagent/getReport', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, true);
        $data['sort_date_modified'] = $this->url->link('extension/module/deliveryagent/getReport', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_modified' . $url, true);
        $data['sort_sort_order']    = $this->url->link('extension/module/deliveryagent/getReport', 'user_token=' . $this->session->data['user_token'] . '&sort=od.sort_order' . $url, true);
        $url                        = '';
        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }
        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }
        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }
        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }
        if (isset($this->request->get['filter_delivery_time'])) {
            $url .= '&filter_delivery_time=' . $this->request->get['filter_delivery_time'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        $pagination                     = new Pagination();
        $pagination->total              = $order_total;
        $pagination->page               = $page;
        $pagination->limit              = $this->config->get('config_limit_admin');
        $pagination->url                = $this->url->link('extension/module/deliveryagent/getReport', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
        $data['pagination']             = $pagination->render();
        $data['results']                = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
        $data['filter_order_id']        = $filter_order_id;
        $data['filter_customer']        = $filter_customer;
        $data['filter_order_status']    = $filter_order_status;
        $data['filter_order_status_id'] = $filter_order_status_id;
        $data['filter_total']           = $filter_total;
        $data['filter_date_added']      = $filter_date_added;
        $data['filter_date_modified']   = $filter_date_modified;
        $data['filter_delivery_time']   = $filter_delivery_time;
        $data['sort']                   = $sort;
        $data['order']                  = $order;
        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/deliveryagent_assignment', $data));
    }
    public function saveDateTime()
    {
        $json = array();
        if (isset($this->request->post['timeslotid'])) {
            $timeslotid = $this->request->post['timeslotid'];
        } else {
            $timeslotid = "";
        }
        if (isset($this->request->post['order_id'])) {
            $order_id = $this->request->post['order_id'];
        } else {
            $order_id = 0;
        }
        if (isset($this->request->post['deliveryagent_id'])) {
            $deliveryagent_id = $this->request->post['deliveryagent_id'];
        } else {
            $deliveryagent_id = 0;
        }
        if (isset($this->request->post['sort_order'])) {
            $sort_order = $this->request->post['sort_order'];
        } else {
            $sort_order = 0;
        }
        $this->load->language("extension/module/deliveryagent");
        if ($this->validateTimeSaveForm() && $order_id) {
            $this->load->model("extension/module/deliveryagent");
            $this->model_extension_module_deliveryagent->saveDateTime($timeslotid, $order_id, $deliveryagent_id, $sort_order);
            $json['success'] = 1;
        } else {
            $json['error'] = $this->language->get('error_permission');
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    protected function validateTimeSaveForm()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/deliveryagent')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function transactionreport()
    {
        $this->load->language('extension/module/deliveryagent');
        $this->document->setTitle($this->language->get('heading_title_transactionreport'));
        if (isset($this->request->get['filter_date_start'])) {
            $filter_date_start = $this->request->get['filter_date_start'];
        } else {
            $filter_date_start = "";
        }
        if (isset($this->request->get['filter_date_end'])) {
            $filter_date_end = $this->request->get['filter_date_end'];
        } else {
            $filter_date_end = "";
        }
        if (isset($this->request->get['filter_deliveryagent'])) {
            $filter_deliveryagent = $this->request->get['filter_deliveryagent'];
        } else {
            $filter_deliveryagent = '';
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $filter_order_status_id = $this->request->get['filter_order_status_id'];
        } else {
            $filter_order_status_id = 0;
        }
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }
        $url = '';
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }
        if (isset($this->request->get['filter_deliveryagent'])) {
            $url .= '&filter_deliveryagent=' . $this->request->get['filter_deliveryagent'];
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $data['breadcrumbs']   = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], TRUE)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_transactionreport'),
            'href' => $this->url->link('extension/module/deliveryagent/transactionreport', 'user_token=' . $this->session->data['user_token'] . $url, TRUE)
        );
        $data['cancel']        = $this->url->link('extension/module/deliveryagent', 'user_token=' . $this->session->data['user_token'], TRUE);
        $this->load->model('extension/module/deliveryagent');
        $data['orders']         = array();
        $filter_data            = array(
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_deliveryagent' => $filter_deliveryagent,
            'filter_order_status_id' => $filter_order_status_id,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );
        $order_total            = $this->model_extension_module_deliveryagent->getTotalTransactions($filter_data);
        $data['report_results'] = $this->model_extension_module_deliveryagent->getTransactions($filter_data);
        foreach ($data['report_results'] as $key => $value) {
            $data['report_results'][$key]['amount']           = $this->currency->format($value['amount'], $value['currency_code'], $value['currency_value']);
            $data['report_results'][$key]['total']            = $this->currency->format($value['total'], $value['currency_code'], $value['currency_value']);
            $data['report_results'][$key]['sub_total']        = $this->currency->format($value['sub_total'], $value['currency_code'], $value['currency_value']);
            $data['report_results'][$key]['sub_total_amount'] = $value['sub_total'];
            $data['report_results'][$key]['product']          = $this->model_extension_module_deliveryagent->getProductCount($value['order_id']);
        }
        $data['report_exacttotal'] = $this->model_extension_module_deliveryagent->getExactTotal($filter_data);
        $data['user_token']        = $this->session->data['user_token'];
        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['deliveryagents'] = $this->model_extension_module_deliveryagent->getdeliveryagents();
        $url                    = '';
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }
        if (isset($this->request->get['filter_deliveryagent'])) {
            $url .= '&filter_deliveryagent=' . $this->request->get['filter_deliveryagent'];
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        $pagination                     = new Pagination();
        $pagination->total              = $order_total;
        $pagination->page               = $page;
        $pagination->limit              = $this->config->get('config_limit_admin');
        $pagination->url                = $this->url->link('extension/module/deliveryagent/transactionreport', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', TRUE);
        $data['pagination']             = $pagination->render();
        $data['results']                = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
        $data['filter_date_start']      = $filter_date_start;
        $data['filter_date_end']        = $filter_date_end;
        $data['filter_deliveryagent']   = $filter_deliveryagent;
        $data['filter_order_status_id'] = $filter_order_status_id;
        $data['header']                 = $this->load->controller('common/header');
        $data['column_left']            = $this->load->controller('common/column_left');
        $data['footer']                 = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/deliveryagent_transactionreport', $data));
    }
    public function savesettings()
    {
        $this->load->model('setting/setting');
        $temp['deliveryagent_installed'] = 1;
        if (isset($this->request->get['deliveryagent_orderstatus']) && isset($this->request->get['deliveryagent_orderstatuschange'])) {
            $temp['deliveryagent_orderstatus']       = $this->request->get['deliveryagent_orderstatus'];
            $temp['deliveryagent_orderstatuschange'] = $this->request->get['deliveryagent_orderstatuschange'];
            if (!$this->user->hasPermission('modify', 'extension/module/deliveryagent')) {
                $json['error'] = "No Permission to change settings. Only main admin can change this.";
            } else {
                $this->model_setting_setting->editSetting('deliveryagent', $temp);
                $json['success'] = "Settings has been saved";
            }
        } else {
            $json['error'] = "You must select atleast one status";
        }
        $this->response->setOutput(json_encode($json));
    }
}