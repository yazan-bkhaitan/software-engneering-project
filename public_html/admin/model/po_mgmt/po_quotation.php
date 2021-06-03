<?php
/**
 * model for Quotation's data management
 */
class ModelPOMgmtPoQuotation extends Model
{
  public function getQuotations($data = array()){
      $sql = "SELECT DISTINCT *, q.quotation_id as format_quote_id, q.status as quotation_status FROM ".DB_PREFIX."po_quotation q LEFT JOIN ".DB_PREFIX."po_supplier s ON (q.supplier_id = s.supplier_id) LEFT JOIN ".DB_PREFIX."po_supplier_detail sd ON (q.supplier_id = sd.supplier_id) WHERE sd.language_id = '".(int)$this->config->get('config_language_id')."' AND (q.status = 1 OR q.status = 6) ";

      if(!empty($data['filter_id'])){
        $sql .= " AND q.id = '".(int)$data['filter_id']."' ";
      }

      if(!empty($data['filter_quotation_id'])){
        $sql .= " AND q.id = '".(int)$data['filter_quotation_id']."' ";
      }

      if(!empty($data['filter_supplier_id'])){
        $sql .= " AND q.supplier_id = '".(int)$data['filter_supplier_id']."' ";
      }

      if(!empty($data['filter_owner_name'])){
        $sql .= " AND sd.owner_name LIKE '".$this->db->escape($data['filter_owner_name'])."%' ";
      }

      if(!empty($data['filter_grand_total'])){
        $sql .= " AND q.total LIKE '".$this->db->escape($data['filter_grand_total'])."%' ";
      }

      if(!empty($data['filter_status'])){
        $sql .= " AND q.status = '".(int)$data['filter_status']."' ";
      }

      if(!empty($data['filter_created_date'])){
        $sql .= " AND q.created_date LIKE '".$this->db->escape($data['filter_created_date'])."%' ";
      }

      $sort_data = array(
        'q.id',
        'q.quotation_id',
  			'sd.company_name',
        'sd.owner_name',
        'q.status',
  			'q.created_date',
  		);

  		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
  			$sql .= " ORDER BY " . $data['sort'];
  		} else {
  			$sql .= " ORDER BY q.id";
  		}

  		if (isset($data['order']) && ($data['order'] == 'DESC')) {
  			$sql .= " DESC";
  		} else {
  			$sql .= " ASC";
  		}

  		if (isset($data['start']) || isset($data['limit'])) {
  			if ($data['start'] < 0) {
  				$data['start'] = 0;
  			}

  			if ($data['limit'] < 1) {
  				$data['limit'] = 20;
  			}

  			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
  		}

  		$query = $this->db->query($sql);

  		return $query->rows;
  }

  public function getTotalQuotations($data = array()){
    $sql = "SELECT COUNT(DISTINCT q.id) AS total FROM ".DB_PREFIX."po_quotation q LEFT JOIN ".DB_PREFIX."po_supplier s ON (q.supplier_id = s.supplier_id) LEFT JOIN ".DB_PREFIX."po_supplier_detail sd ON (q.supplier_id = sd.supplier_id) WHERE sd.language_id = '".(int)$this->config->get('config_language_id')."'  AND (q.status = 1 OR q.status = 6) ";

    if(!empty($data['filter_id'])){
      $sql .= " AND q.id = '".(int)$data['filter_id']."' ";
    }

    if(!empty($data['filter_quotation_id'])){
      $sql .= " AND q.id = '".(int)$data['filter_quotation_id']."' ";
    }

    if(!empty($data['filter_supplier_id'])){
      $sql .= " AND q.supplier_id = '".(int)$data['filter_supplier_id']."' ";
    }

    if(!empty($data['filter_owner_name'])){
      $sql .= " AND sd.owner_name LIKE '".$this->db->escape($data['filter_owner_name'])."%' ";
    }

    if(!empty($data['filter_grand_total'])){
      $sql .= " AND q.total LIKE '".$this->db->escape($data['filter_grand_total'])."%' ";
    }

    if(!empty($data['filter_status'])){
      $sql .= " AND q.status = '".(int)$data['filter_status']."' ";
    }

    if(!empty($data['filter_created_date'])){
      $sql .= " AND q.created_date LIKE '".$this->db->escape($data['filter_created_date'])."%' ";
    }

		$query = $this->db->query($sql);

		return $query->row['total'];
  }

  public function getQuotation($quotation_id = false){
    $sql = "SELECT q.*, q.status as quotation_status FROM ".DB_PREFIX."po_quotation q LEFT JOIN ".DB_PREFIX."po_supplier s ON (q.supplier_id = s.supplier_id) LEFT JOIN ".DB_PREFIX."po_supplier_detail sd ON (q.supplier_id = sd.supplier_id) WHERE sd.language_id = '".(int)$this->config->get('config_language_id')."' AND `id` = '".(int)$quotation_id."' ";

    return $this->db->query($sql)->row;
  }

  public function getQuotationProducts($quotation_id = false){
    $result = $this->db->query("SELECT qp.*, p.price, p.quantity, p.image, p.model, pd.name, sp.supplier_sku FROM ".DB_PREFIX."po_quotation_product qp LEFT JOIN ".DB_PREFIX."po_quotation q ON(qp.quotation_id = q.id) LEFT JOIN ".DB_PREFIX."po_supplier_product sp ON ((qp.supplier_id = sp.supplier_id) AND (qp.product_id = sp.product_id)) LEFT JOIN ".DB_PREFIX."product p ON(qp.product_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$this->config->get('config_language_id')."' AND q.id = '".(int)$quotation_id."' AND qp.quotation_id = '".(int)$quotation_id."' ")->rows;

    return $result;
  }

  public function getQuotationOptions($quotation_id, $quotation_product_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "po_quotation_product_option WHERE quotation_id = '" . (int)$quotation_id . "' AND quotation_product_id = '" . (int)$quotation_product_id . "'");

		return $query->rows;
  }

  public function addQuotation($data = array()){
      date_default_timezone_set("Asia/Kolkata");
      $current_datetime = date('Y-m-d H:i:s');

      $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation SET `supplier_id` = '".(int)$data['general_info']['supplier_id']."', `shipping_address` = '".$this->db->escape($data['general_info']['shipping_address'])."', `shipping_method_id` = '".(int)$data['general_info']['shipping_method']."', `shipping_method` = '".$this->db->escape($data['general_info']['shipping_method_name'])."', `shipping_cost` = '".(float)$data['general_info']['shipping_cost']."', `shipping_start_date` = '".$this->db->escape($data['general_info']['shipping_start_date'])."', `shipping_expected_date` = '".$this->db->escape($data['general_info']['expected_delivery_date'])."', `payment_term_id` = '".(int)$data['general_info']['payment_method']."', `payment_term` = '".$this->db->escape($data['general_info']['payment_method_name'])."', `language_id` = '".(int)$this->config->get('config_language_id')."', `currency_id` = '".(int)$this->currency->getId($this->config->get('config_currency'))."', `currency_code` = '".$this->db->escape($this->config->get('config_currency'))."', `order_source` = '".$this->db->escape($data['general_info']['order_source'] ?? '')."', `total` = '".(float)$data['totals']['grand']."', `created_date` = '".$this->db->escape($current_datetime)."', `status` = '1',`paid_status`='0'");

      $quotation_id = $this->db->getLastId();

      if($quotation_id){
          $quotation_prefix = isset($data['oc_pom_order_prefix']) ? $data['oc_pom_order_prefix'] : $this->config->get('module_oc_pom_order_prefix');

          $this->db->query("UPDATE ".DB_PREFIX."po_quotation SET `quotation_id` = '".$this->db->escape($quotation_prefix.sprintf('%05d', $quotation_id))."' WHERE id = '".(int)$quotation_id."' AND supplier_id = '".(int)$data['general_info']['supplier_id']."' ");

          if(isset($data['add_product']) && !empty($data['add_product'])){
              foreach ($data['add_product'] as $key => $product) {
                  $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation_product SET `quotation_id` = '".(int)$quotation_id."', `supplier_id` = '".(int)$data['general_info']['supplier_id']."', `product_id` = '".(int)$product['product_id']."', `ordered_cost` = '".(float)($product['supplier_cost'] + $product['total_option_cost'])."', `ordered_qty` = '".(int)$product['supplier_qty']."', `sub_total` = '".(float)$product['sub_total']."', `tax` = '".$this->db->escape($product['supplier_tax'])."' ");

                  $quotation_product_id = $this->db->getLastId();

                  if(isset($product['option_data']) && !empty($product['option_data'])){
                      foreach ($product['option_data'] as $option) {
                          $this->db->query("INSERT INTO " . DB_PREFIX . "po_quotation_product_option SET quotation_id = '" . (int)$quotation_id . "', quotation_product_id = '" . (int)$quotation_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "',`price_prefix`='" . $this->db->escape($data['price_prefix']) . "',`price`='" . (float)$data['price'] . "'");
                      }
                  }
              }
          }

          if(isset($data['add_comment']['comment']) && !empty($data['add_comment']['comment'])){
              $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation_history SET `quotation_id` = '".(int)$quotation_id."', `description` = '".$this->db->escape($data['add_comment']['comment'])."', `date_added` = '".$this->db->escape($current_datetime)."' ");
          }

          if($data['general_info']['supplier_id']){
              $this->load->model('po_mgmt/po_email');
              $data['email_from']   = $this->config->get('config_email');
              $data['supplier_id']  = $data['general_info']['supplier_id'];
              $data['quotation_id'] = $quotation_id;
              $value_index          = array(
                'comment'           => $data['add_comment']['comment'],
                'shipping_address'  => $data['general_info']['shipping_address'],
                  'order_source'      => ucfirst($data['general_info']['order_source'] ?? '')
              );

              // send email to admin for quotation creation if option is set from module config
              if($this->config->get('module_oc_pom_quotation_email_admin')){
                  $data['email_to']     = $this->config->get('module_oc_pom_email_id') ? $this->config->get('module_oc_pom_email_id') : $this->config->get('config_email');
                  $data['templateId']   = $this->config->get('module_oc_pom_quotation_email_admin');
                  $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
              }
              // send email to Supplier for quotation creation if option is set from module config
              if($this->config->get('module_oc_pom_quotation_email_supplier')){
                  if(isset($data['email_to'])){
                    unset($data['email_to']);
                  }
                  $value_index['comment']                   = '';
                  $value_index['seller_comment_combine']    = false;
                  if(isset($data['add_comment']['mail_supplier']) && $data['add_comment']['mail_supplier']){
                    $value_index['comment']                 = $data['add_comment']['comment'];
                    $value_index['seller_comment_combine']  = true;
                  }
                  $data['templateId']   = $this->config->get('module_oc_pom_quotation_email_supplier');
                  $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
              }
          }
      }
      return $quotation_id;
  }

  public function updateQuoteProductData($data = array()){
    $email_status = false;
    $this->load->model('tool/upload');
      if(isset($data['quotation_id']) && $data['quotation_id']){
          if(isset($data['more_product']) && !empty($data['more_product'])){
              $productArray = array_values($data['more_product']);
              $html_temp = '<div style=" background-color: #ccc;padding: 10px;width: 40%;overflow:hidden;"><table style="width:100%;">';

              foreach ($productArray as $key => $product) {
                  $sub_total = 0;
                  $getQuoteProductEntry = $this->db->query("SELECT qp.* FROM ".DB_PREFIX."po_quotation_product qp LEFT JOIN ".DB_PREFIX."po_quotation q ON (qp.quotation_id = q.id) LEFT JOIN ".DB_PREFIX."product p ON (qp.product_id = p.product_id) WHERE qp.id = '".(int)(isset($product['po_product_id']) ? $product['po_product_id'] : 0)."' AND qp.quotation_id = '".(int)$data['quotation_id']."' AND qp.product_id = '".(int)$product['product_id']."' ")->row;

                  $sub_total = $product['supplier_cost'] * $product['supplier_qty'];
                  if(!empty($getQuoteProductEntry)){

                      $email_status = false;
                      $this->db->query("UPDATE ".DB_PREFIX."po_quotation_product SET `ordered_cost` = '".(float)$product['supplier_cost']."', `ordered_qty` = '".(int)$product['supplier_qty']."', `sub_total` = '".(float)$sub_total."', `tax` = '".$this->db->escape($product['supplier_tax'])."' WHERE quotation_id = '".(int)$data['quotation_id']."' AND product_id = '".(int)$product['product_id']."' AND id = '".(int)$getQuoteProductEntry['id']."' ");

                      $data['supplier_id'] = $getQuoteProductEntry['supplier_id'];
                      if($key == 0){
                          $html_temp .= '<tr><th colspan="4">-: Updated Product List :-</th></tr><tr><th style="border-bottom: 1px solid #333;text-align: left;">Product Name</th><th style="border-bottom: 1px solid #333;text-align: left;">Price</th><th style="border-bottom: 1px solid #333;text-align: left;">Quantity</th><th style="border-bottom: 1px solid #333;text-align: left;">Tax(%)</th></tr>';
                      }
                      $htmp_temp_option = '';
                      $getOptions = $this->getQuotationOptions($getQuoteProductEntry['quotation_id'], $getQuoteProductEntry['id']);
                      foreach ($getOptions as $option) {
                          if ($option['type'] != 'file') {
                              $htmp_temp_option .= '<br />&nbsp;<small><i> - '.$option['name'].':' .$option['value'].'</i></small>';
                          } else {
                              $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
                              if ($upload_info) {
                                  $htmp_temp_option .= '<br />&nbsp;<small><i> - '.$option['name'].':' .$upload_info['name'].'</i></small>';
                              }
                          }
                      }
                      $html_temp .= '<tr><td style="padding:10px;">'.$product['name'].$htmp_temp_option.'</td><td style="padding:10px;">'.$this->currency->format($product['supplier_cost'], $this->config->get('config_currency')).'</td><td style="padding:10px;">'.$product['supplier_qty'].'</td><td style="padding:10px;">'.$product['supplier_tax'].'</td></tr>';
                  }else{
                      $email_status = true;
                      $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation_product SET `quotation_id` = '".(int)$data['quotation_id']."', `supplier_id` = '".(int)$data['supplier_id']."', `product_id` = '".(int)$product['product_id']."', `ordered_cost` = '".(float)$product['supplier_cost']."', `ordered_qty` = '".(int)$product['supplier_qty']."', `sub_total` = '".(float)$sub_total."', `tax` = '".$this->db->escape($product['supplier_tax'])."' ");

                      $quotation_product_id = $this->db->getLastId();
                      $htmp_temp_option = '';
                      $option_price     = 0;
                      if(isset($product['option_data']) && !empty($product['option_data'])){
                          foreach ($product['option_data'] as $option) {
                              $this->db->query("INSERT INTO " . DB_PREFIX . "po_quotation_product_option SET quotation_id = '" . (int)$data['quotation_id'] . "', quotation_product_id = '" . (int)$quotation_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
                              if ($option['price_prefix'] == '+') {
                                $option_price += $option['price'];
                              } elseif ($option['price_prefix'] == '-') {
                                $option_price -= $option['price'];
                              }
                              if ($option['type'] != 'file') {
                                  $htmp_temp_option .= '<br />&nbsp;<small><i> - '.$option['name'].':' .$option['value'].'</i></small>';
                              } else {
                                  $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
                                  if ($upload_info) {
                                      $htmp_temp_option .= '<br />&nbsp;<small><i> - '.$option['name'].':' .$upload_info['name'].'</i></small>';
                                  }
                              }
                          }
                          $supplier_cost = $product['supplier_cost'] + $option_price;
                          $sub_total = $supplier_cost * $product['supplier_qty'];
                          $this->db->query("UPDATE ".DB_PREFIX."po_quotation_product SET `ordered_cost` = '".(float)$supplier_cost."', `sub_total` = '".(float)$sub_total."' WHERE id = '".(int)$quotation_product_id."' AND `quotation_id` = '".(int)$data['quotation_id']."' AND `supplier_id` = '".(int)$data['supplier_id']."' AND `product_id` = '".(int)$product['product_id']."' ");
                      }
                      if($key == 0){
                          $html_temp .= '<tr><th colspan="4">-: Added Product List :-</th></tr><tr><th style="border-bottom: 1px solid #333;text-align: left;">Product Name</th><th style="border-bottom: 1px solid #333;text-align: left;">Price</th><th style="border-bottom: 1px solid #333;text-align: left;">Quantity</th><th style="border-bottom: 1px solid #333;text-align: left;">Tax(%)</th></tr>';
                      }
                      $html_temp .= '<tr><td style="padding:10px;text-align: left;">'.$product['name'].$htmp_temp_option.'</td><td style="padding:10px;text-align: left;">'.$this->currency->format(($product['supplier_cost'] + $option_price), $this->config->get('config_currency')).'</td><td style="padding:10px;text-align: left;">'.$product['supplier_qty'].'</td><td style="padding:10px;text-align: left;">'.$product['supplier_tax'].'</td></tr>';
                  }
              }
              $html_temp .= '</table></div>';
              $this->load->model('po_mgmt/po_email');
              if($email_status){
                  if($this->config->get('module_oc_pom_add_product_email_admin')){
                    $data['templateId'] = $this->config->get('module_oc_pom_add_product_email_admin');
                    $data['email_to']   = $this->config->get('module_oc_pom_email_id') ? $this->config->get('module_oc_pom_email_id') : $this->config->get('config_email');
                    $data['email_from'] = $this->config->get('config_email');
                    $value_index        = array(
                                              'quotation_id'  => '#'.$data['quotation_id'],
                                              'new_product'   => $html_temp,
                    );
                    $this->model_po_mgmt_po_email->sendEmail($data, $value_index);
                  }
                  if($this->config->get('module_oc_pom_add_product_email_supplier')){
                    if(isset($data['email_to'])){
                      unset($data['email_to']);
                    }
                    $data['templateId'] = $this->config->get('module_oc_pom_add_product_email_supplier');
                    $data['email_from'] = $this->config->get('config_email');
                    $value_index        = array(
                                              'quotation_id'  => '#'.$data['quotation_id'],
                                              'new_product'   => $html_temp,
                    );
                    $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
                  }
              }else{
                  // update product/status to quotation
                  if($this->config->get('module_oc_pom_update_product_email_admin')){
                    $data['templateId'] = $this->config->get('module_oc_pom_update_product_email_admin');
                    $data['email_to']   = $this->config->get('module_oc_pom_email_id') ? $this->config->get('module_oc_pom_email_id') : $this->config->get('config_email');
                    $data['email_from'] = $this->config->get('config_email');
                    $value_index        = array(
                                              'quotation_id'      => '#'.$data['quotation_id'],
                                              'update_quotation'  => $html_temp,
                    );
                    $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
                  }
                  if($this->config->get('module_oc_pom_update_product_email_supplier')){
                    if(isset($data['email_to'])){
                      unset($data['email_to']);
                    }
                    $data['templateId'] = $this->config->get('module_oc_pom_update_product_email_supplier');
                    $data['email_from'] = $this->config->get('config_email');
                    $value_index        = array(
                                              'quotation_id'      => '#'.$data['quotation_id'],
                                              'update_quotation'  => $html_temp,
                    );
                    $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
                  }
              }
          }
          SELF::updateQuotationTotal($data['quotation_id']);
      }
  }

  public function updateQuotationTotal($quotation_id) {

    $quotation_info = array();

    $total = 0;
    $shipping_cost = 0;
    $sub_total = 0;
    $tax_total = 0;

    $quotation_shipping = SELF::getQuotationInfoPrice($quotation_id);

    if($quotation_shipping) {
      $shipping_cost += $quotation_shipping['shipping_cost'];
    }

    $quotation_products = SELF::getQuotationProductsWithPrice($quotation_id);

    foreach ($quotation_products as $key => $product) {
      
      $product_options = SELF::getQuotationProductOptionPrice($quotation_id,$product['id']);
      $option_price = 0;

      if($product_options) {
        foreach ($product_options as $option) {
          
          if($option['price_prefix'] == '+') {
            $option_price += $option['price'];
          } else {
            $option_price -= $option['price'];
          }
        }        
      }
      $sub_total = $sub_total + (($product['ordered_cost'] + $option_price) * $product['ordered_qty']);
      $tax_total = $tax_total + ((($product['ordered_cost'] + $option_price) * $product['ordered_qty']) * $product['tax']) / 100;
    }

    $total = ($sub_total + $shipping_cost + $tax_total);

    $this->db->query("UPDATE " . DB_PREFIX . "po_quotation SET total='" . (float)$total . "' WHERE id='" . (int)$quotation_id . "'");
  }

  public function getQuotationInfoPrice($quotation_id) {

    $query = $this->db->query("SELECT shipping_cost FROM " . DB_PREFIX . "po_quotation WHERE id='" . (int)$quotation_id . "'")->row;

    return $query;
  }

  public function getQuotationProductsWithPrice($quotation_id) {

    $query = $this->db->query("SELECT tax,ordered_cost,id,ordered_qty FROM " . DB_PREFIX . "po_quotation_product WHERE quotation_id='" . (int)$quotation_id . "'")->rows;

    return $query;
  }

  function getQuotationProductOptionPrice($quotation_id,$product_id) {

    $query = $this->db->query("SELECT price_prefix,price FROM " . DB_PREFIX . "po_quotation_product_option WHERE quotation_id='" . (int)$quotation_id . "' AND quotation_product_id='" . (int)$product_id ."'")->rows;

    return $query;
  }


  public function deleteQuotation($quote_id = false){
    if($quote_id){
        $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation_history WHERE quotation_id = '".(int)$quote_id."' ");
        $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation_product WHERE quotation_id = '".(int)$quote_id."' ");
        $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation_product_option WHERE quotation_id = '".(int)$quote_id."' ");
        $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation WHERE id = '".(int)$quote_id."' ");
    }
  }

  public function deleteQuoteProduct($data = array()){
    $getQuoteProductEntry = $this->db->query("SELECT qp.*, pd.name FROM ".DB_PREFIX."po_quotation_product qp LEFT JOIN ".DB_PREFIX."po_quotation q ON (qp.quotation_id = q.id) LEFT JOIN ".DB_PREFIX."product p ON (qp.product_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_description pd ON (qp.product_id = pd.product_id) WHERE qp.quotation_id = '".(int)$data['quotation_id']."' AND qp.id = '".(int)$data['po_product_id']."' AND qp.supplier_id = '".(int)$data['supplier_id']."' AND pd.language_id = '".(int)$this->config->get('config_language_id')."' ")->row;

      if(!empty($getQuoteProductEntry)){
          $this->load->model('po_mgmt/po_email');
          $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation_product_option WHERE quotation_id = '".(int)$data['quotation_id']."' AND quotation_product_id = '".(int)$getQuoteProductEntry['id']."' ");
          $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation_product WHERE id = '".(int)$data['po_product_id']."' AND quotation_id = '".(int)$data['quotation_id']."' AND product_id = '".(int)$getQuoteProductEntry['product_id']."' AND supplier_id = '".(int)$data['supplier_id']."' ");

          $html_temp = '<div style=" background-color: #ccc;padding: 10px;width: 80%;overflow:hidden;"><table style="width:100%;"><tr><th>-: Deleted Product List :-</th><tr><tr><th style="border-bottom: 1px solid #333;text-align: left;">Product Name</th><th style="border-bottom: 1px solid #333;text-align: left;">Price</th><th style="border-bottom: 1px solid #333;text-align: left;">Quantity</th><th style="border-bottom: 1px solid #333;text-align: left;">Tax(%)</th></tr>';
          $html_temp .= '<tr><td style="padding:10px;">'.$getQuoteProductEntry['name'].'</td><td style="padding:10px;">'.$this->currency->format($getQuoteProductEntry['ordered_cost'], $this->config->get('config_currency')).'</td><td style="padding:10px;">'.$getQuoteProductEntry['ordered_qty'].'</td><td style="padding:10px;">'.$getQuoteProductEntry['tax'].'</td></tr></table></div>';
          // update product/status to quotation
          if($this->config->get('module_oc_pom_update_product_email_admin')){
            $data['templateId'] = $this->config->get('module_oc_pom_update_product_email_admin');
            $data['email_to']   = $this->config->get('module_oc_pom_email_id') ? $this->config->get('module_oc_pom_email_id') : $this->config->get('config_email');
            $data['email_from'] = $this->config->get('config_email');
            $value_index        = array(
                                      'quotation_id'      => '#'.$data['quotation_id'],
                                      'update_quotation'  => $html_temp,
            );
            $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
          }
          if($this->config->get('module_oc_pom_update_product_email_supplier')){
            if(isset($data['email_to'])){
              unset($data['email_to']);
            }
            $data['templateId'] = $this->config->get('module_oc_pom_update_product_email_supplier');
            $data['email_from'] = $this->config->get('config_email');
            $value_index        = array(
                                      'quotation_id'      => '#'.$data['quotation_id'],
                                      'update_quotation'  => $html_temp,
            );
            $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
          }
      }
  }

  public function saveQuoteCommentHistory($data = array()){
      date_default_timezone_set("Asia/Kolkata");
      $current_datetime = date('Y-m-d H:i:s');
      $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation_history SET `quotation_id` = '".(int)$data['quotation_id']."', description = '".$this->db->escape($data['comment'])."', `date_added` = '".$this->db->escape($current_datetime)."' ");

      if($this->db->getLastId()){
        $this->load->model('po_mgmt/po_email');
        $html_temp = '<div style=" background-color: #ccc;padding: 10px;width: 80%;overflow:hidden;"><table style="width:100%;"><tr><th colspan="2">-: New Comment Added To Quotation :-</th><tr><tr><th style="border-bottom: 1px solid #333;text-align: left;">Quotation Id </th><th style="border-bottom: 1px solid #333;text-align: left;">Comment</th></tr>';
        $html_temp .= '<tr><td style="padding:10px;">'.'#'.$data['quotation_id'].'</td><td style="padding:10px;">'.$data['comment'].'</td></tr></table></div>';
        // add comment to quotation status to quotation
        if($this->config->get('module_oc_pom_update_product_email_admin')){
          $data['templateId']         = $this->config->get('module_oc_pom_update_product_email_admin');
          $data['email_to']           = $this->config->get('module_oc_pom_email_id') ? $this->config->get('module_oc_pom_email_id') : $this->config->get('config_email');
          $data['email_from']         = $this->config->get('config_email');
          $value_index        = array(
                                    'quotation_id'      => '#'.$data['quotation_id'],
                                    'comment'           => $html_temp
          );
          $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
        }
        if($this->config->get('module_oc_pom_update_product_email_supplier')){
          if(isset($data['email_to'])){
            unset($data['email_to']);
          }
          $data['templateId'] = $this->config->get('module_oc_pom_update_product_email_supplier');
          $data['email_from'] = $this->config->get('config_email');
          $value_index        = array(
                                    'quotation_id'      => '#'.$data['quotation_id'],
          );
          if(!isset($data['send_mail'])){
            $value_index['comment']                 = '';
          }else{
            $value_index['comment']                 = $html_temp;
            $value_index['seller_comment_combine']  = true;
          }
          $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
        }
      }
  }
  public function getQuoteCommentHistory($data = array()){
    $sql = "SELECT qh.* FROM ".DB_PREFIX."po_quotation_history qh LEFT JOIN ".DB_PREFIX."po_quotation q ON (qh.quotation_id = q.id) WHERE qh.quotation_id = '".(int)$data['quotation_id']."' ";

    if (isset($data['start']) || isset($data['limit'])) {
      if ($data['start'] < 0) {
        $data['start'] = 0;
      }

      if ($data['limit'] < 1) {
        $data['limit'] = 20;
      }

      $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
    }

    $query = $this->db->query($sql);

    return $query->rows;
  }
  public function getTotalQuoteCommentHistory($data = array()){
    $sql = "SELECT COUNT(DISTINCT qh.id) AS total FROM ".DB_PREFIX."po_quotation_history qh LEFT JOIN ".DB_PREFIX."po_quotation q ON (qh.quotation_id = q.id) WHERE qh.quotation_id = '".(int)$data['quotation_id']."' ";

    $query = $this->db->query($sql);

    return $query->row['total'];
  }


  public function getRemainingSupplierProducts($data = array()){
      $sql = "SELECT DISTINCT p.product_id, p.image, p.model, p.price, sp.*, pd.name as product_name, pd.name FROM ".DB_PREFIX."po_supplier_product sp  LEFT JOIN ".DB_PREFIX."po_supplier s ON (sp.supplier_id = s.supplier_id) LEFT JOIN ".DB_PREFIX."product p ON (sp.product_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND sp.supplier_id = '".(int)$data['filter_supplier_id']."' AND sp.product_id NOT IN (SELECT product_id FROM ".DB_PREFIX."po_quotation_product qp WHERE qp.quotation_id = '".(int)$data['filter_quotation_id']."' AND qp.supplier_id = '".(int)$data['filter_supplier_id']."') ";

      if(!empty($data['filter_product_name'])){
          $sql .= " AND pd.name LIKE '".$this->db->escape($data['filter_product_name'])."%' ";
      }

      if (isset($data['start']) || isset($data['limit'])) {
  			if ($data['start'] < 0) {
  				$data['start'] = 0;
  			}

  			if ($data['limit'] < 1) {
  				$data['limit'] = 20;
  			}

  			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
  		}

      $query = $this->db->query($sql)->rows;

      return $query;
    }

    public function updateQuoteStatus($data = array()){
      $this->load->model('po_mgmt/po_email');
      date_default_timezone_set("Asia/Kolkata");
      $current_datetime = date('Y-m-d H:i:s');

      if($data['status'] == 4){
          $this->receivedAllRemainingQty($data);
      }

      $this->db->query("UPDATE ".DB_PREFIX."po_quotation SET `purchase_date` = '".$this->db->escape($current_datetime)."', `status` = '".(int)$data['status']."' WHERE `id` = '".(int)$data['quotation_id']."' AND `supplier_id` = '".(int)$data['supplier_id']."'");

      $html_temp = '<div style=" background-color: #ccc;padding: 10px;width: 80%;overflow:hidden;"><table style="width:100%;"><tr><th colspan="2">-: Quotation Status Updated :-</th></tr><tr><th style="border-bottom: 1px solid #333;text-align: left;">Quotation Id </th><th style="border-bottom: 1px solid #333;text-align: left;">Quotation Status</th></tr>';
      $html_temp .= '<tr><td style="padding:10px;text-align: left;">'.'#'.$data['quotation_id'].'</td><td style="padding:10px;text-align: left;">'.$data['status_text'].'</td></tr></table></div>';

      // check for convert to PO to send a create PO mail
      if($data['status'] == 2) {
        if($data['supplier_id']) {
          $sql = "SELECT shipping_address, order_source FROM " . DB_PREFIX . "po_quotation WHERE `id` = '".(int)$data['quotation_id']."' AND `supplier_id` = '".(int)$data['supplier_id']."'";
          $temp_result = $this->db->query($sql)->row;

          $data['email_from']   = $this->config->get('config_email');
          $data['supplier_id']  = $data['supplier_id'];
          $value_index          = array(
            'comment'           => isset($data['comment']) ? $data['comment'] : '',
            'shipping_address'  => $temp_result['shipping_address'],
            'order_source'      => ucfirst($temp_result['order_source'])
          );

          // send email to admin for quotation creation if option is set from module config
          if($this->config->get('oc_pom_purchase_email_admin')){
              $data['email_to']     = $this->config->get('oc_pom_email_id') ? $this->config->get('oc_pom_email_id') : $this->config->get('config_email');
              $data['templateId']   = $this->config->get('oc_pom_purchase_email_admin');
              $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
          }

          // send email to Supplier for quotation creation if option is set from module config
          if($this->config->get('oc_pom_purchase_email_supplier')){
              if(isset($data['email_to'])){
                unset($data['email_to']);
              }
              $value_index['comment']                   = '';
              $value_index['seller_comment_combine']  = true;

              $data['templateId']   = $this->config->get('oc_pom_purchase_email_supplier');
              $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
          }
        }
      } else {
        // update quotation status to quotation
        if($this->config->get('module_oc_pom_update_product_email_admin')){
          $data['templateId'] = $this->config->get('module_oc_pom_update_product_email_admin');
          $data['email_to']   = $this->config->get('module_oc_pom_email_id') ? $this->config->get('module_oc_pom_email_id') : $this->config->get('config_email');
          $data['email_from'] = $this->config->get('config_email');
          $value_index        = array(
                                    'quotation_id'      => '#'.$data['quotation_id'],
                                    'update_quotation'  => $html_temp,
          );
          $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
        }
        if($this->config->get('module_oc_pom_update_product_email_supplier')){
          if(isset($data['email_to'])){
            unset($data['email_to']);
          }
          $data['templateId'] = $this->config->get('module_oc_pom_update_product_email_supplier');
          $data['email_from'] = $this->config->get('config_email');
          $value_index        = array(
                                    'quotation_id'      => '#'.$data['quotation_id'],
                                    'update_quotation'  => $html_temp,
          );
          $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
        }
      }
    }

    public function receivedAllRemainingQty($data = array()){
        if($data['quotation_id']){
            $getQuoteProducts = $this->getQuotationProducts($data['quotation_id']);
            if(!empty($getQuoteProducts)){
                foreach ($getQuoteProducts as $key => $q_product) {
                    if(($q_product['ordered_qty'] > $q_product['received_qty']) && ($q_product['ordered_qty'] != $q_product['received_qty'])){
                      $getRemainingQty = $q_product['ordered_qty'] - $q_product['received_qty'];
                      $totalProductQty = $getRemainingQty + $q_product['quantity'];
                      $this->db->query("UPDATE ".DB_PREFIX."po_quotation_product SET `received_qty` = '".(int)$q_product['ordered_qty']."' WHERE id = '".(int)$q_product['id']."' AND quotation_id = '".(int)$q_product['quotation_id']."' ");
                      $this->db->query("UPDATE ".DB_PREFIX."product SET `quantity` = '".(int)$totalProductQty."' WHERE product_id = '".(int)$q_product['product_id']."' ");
                    }
                }
            }
        }
    }

    public function saveQuotationCost($quotation_id) {

      $quotation_products = SELF::getQuotationProducts($quotation_id);

      $this->db->query("DELETE FROM " . DB_PREFIX . "po_quotation_product_details WHERE quotation_id='" . (int)$quotation_id . "'");

      if($quotation_products) {
        foreach ($quotation_products as $key => $product) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "po_quotation_product_details SET quotation_id='" . (int)$quotation_id . "',supplier_id='" . (int)$product['supplier_id'] . "',product_id='" . (int)$product['product_id'] . "',quotation_cost='" . (float)$product['ordered_cost'] . "'");          
        }
      }

    }
}
