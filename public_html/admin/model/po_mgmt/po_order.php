<?php
/**
 * model for Order's data management
 */
class ModelPOMgmtPoOrder extends Model
{
  public function getPurchaseOrders($data = array()){
      $sql = "SELECT DISTINCT q.*, s.*, sd.*, q.status as quotation_status, c.iso_code_2, c.name as country_name FROM ".DB_PREFIX."po_quotation q LEFT JOIN ".DB_PREFIX."po_supplier s ON (q.supplier_id = s.supplier_id) LEFT JOIN ".DB_PREFIX."po_supplier_detail sd ON (q.supplier_id = sd.supplier_id) LEFT JOIN ".DB_PREFIX."country c ON (s.country_id = c.country_id) WHERE sd.language_id = '".(int)$this->config->get('config_language_id')."' AND q.status NOT IN (1,6) ";

      if(!empty($data['filter_id'])){
        $sql .= " AND q.id = '".(int)$data['filter_id']."' ";
      }

      if(!empty($data['filter_supplier_id'])){
        $sql .= " AND q.supplier_id = '".(int)$data['filter_supplier_id']."' ";
      }

      if(!empty($data['filter_order_id'])){
        $sql .= " AND q.order_id = '".(int)$data['filter_order_id']."' ";
      }

      if(!empty($data['filter_purchase_id'])){
        $sql .= " AND q.quotation_id LIKE '".$this->db->escape($data['filter_purchase_id'])."%' ";
      }

      if(!empty($data['filter_owner_name'])){
        $sql .= " AND sd.owner_name LIKE '".$this->db->escape($data['filter_owner_name'])."%' ";
      }

      if (isset($data['filter_grand_total']) && !is_null($data['filter_grand_total'])) {
        $sql .= " AND q.total LIKE '" . $this->db->escape($data['filter_grand_total']) . "%'";
      }

      if (isset($data['filter_grand_total_po']) && !is_null($data['filter_grand_total_po'])) {
        $sql .= " AND q.total LIKE '" . $this->db->escape($data['filter_grand_total_po']) . "%'";
      }

      if(!empty($data['filter_status'])){
        $sql .= " AND q.status = '".(int)$data['filter_status']."' ";
      }

      if(!empty($data['filter_purchase_date'])){
        $sql .= " AND q.purchase_date LIKE '".$this->db->escape($data['filter_purchase_date'])."%' ";
      }

      $sort_data = array(
        'q.id',
        'q.quotation_id',
  			'sd.company_name',
        'sd.owner_name',
        'q.status',
        'q.purchase_date',
        'q.total',
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

  public function getTotalPurchaseOrders($data = array()){
    $sql = "SELECT COUNT(DISTINCT q.id) AS total FROM ".DB_PREFIX."po_quotation q LEFT JOIN ".DB_PREFIX."po_supplier s ON (q.supplier_id = s.supplier_id) LEFT JOIN ".DB_PREFIX."po_supplier_detail sd ON (q.supplier_id = sd.supplier_id) WHERE sd.language_id = '".(int)$this->config->get('config_language_id')."'  AND q.status NOT IN (1,6) ";

    if(!empty($data['filter_supplier_id'])){
      $sql .= " AND q.supplier_id = '".(int)$data['filter_supplier_id']."' ";
    }

    if(!empty($data['filter_purchase_id'])){
      $sql .= " AND q.quotation_id LIKE '".$this->db->escape($data['filter_purchase_id'])."%' ";
    }

    if(!empty($data['filter_owner_name'])){
      $sql .= " AND sd.owner_name LIKE '".$this->db->escape($data['filter_owner_name'])."%' ";
    }

    if (isset($data['filter_grand_total']) && !is_null($data['filter_grand_total'])) {
      $sql .= " AND q.total LIKE '" . $this->db->escape($data['filter_grand_total']) . "%'";
    }

    if (isset($data['filter_grand_total_po']) && !is_null($data['filter_grand_total_po'])) {
      $sql .= " AND q.total LIKE '" . $this->db->escape($data['filter_grand_total_po']) . "%'";
    }

    if(!empty($data['filter_status'])){
      $sql .= " AND q.status = '".(int)$data['filter_status']."' ";
    }

    if(!empty($data['filter_purchase_date'])){
      $sql .= " AND q.purchase_date LIKE '".$this->db->escape($data['filter_purchase_date'])."%' ";
    }

		$query = $this->db->query($sql);

		return $query->row['total'];
  }

  public function getOrderProducts($quotation_id = false){
    $result = $this->db->query("SELECT DISTINCT qp.*, sp.supplier_sku, p.image, p.model, pd.name, p.weight, p.weight_class_id FROM ".DB_PREFIX."po_quotation_product qp LEFT JOIN ".DB_PREFIX."po_quotation q ON(qp.quotation_id = q.id) LEFT JOIN ".DB_PREFIX."po_supplier_product sp ON ((qp.supplier_id = sp.supplier_id) AND (qp.product_id = sp.product_id)) LEFT JOIN ".DB_PREFIX."product p ON(qp.product_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."weight_class_description wcd ON(p.weight_class_id = wcd.weight_class_id) WHERE pd.language_id = '".(int)$this->config->get('config_language_id')."' AND q.id = '".(int)$quotation_id."' AND qp.quotation_id = '".(int)$quotation_id."' ")->rows;

    return $result;
  }

  public function getQuotationOptions($quotation_id, $quotation_product_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "po_quotation_product_option WHERE quotation_id = '" . (int)$quotation_id . "' AND quotation_product_id = '" . (int)$quotation_product_id . "'");

		return $query->rows;
  }

  public function getOrderProductData($data = array()){
    $sql = "SELECT qp.*, sp.supplier_sku, p.image, p.model, pd.name FROM ".DB_PREFIX."po_quotation_product qp LEFT JOIN ".DB_PREFIX."po_quotation q ON(qp.quotation_id = q.id) LEFT JOIN ".DB_PREFIX."po_supplier_product sp ON ((qp.supplier_id = sp.supplier_id) AND (qp.product_id = sp.product_id)) LEFT JOIN ".DB_PREFIX."product p ON(qp.product_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$this->config->get('config_language_id')."' AND q.id = '".(int)$data['quotation_id']."' AND qp.quotation_id = '".(int)$data['quotation_id']."' AND qp.product_id = '".(int)$data['product_id']."' ";

    if(isset($data['po_product_id']) && !empty($data['po_product_id'])){
      $sql .= " AND qp.id = '".(int)$data['po_product_id']."' ";
    }
    $result = $this->db->query($sql)->row;
    return $result;
  }

  public function updateOrderQuantity($data = array()){
    $totalReceivedQty = 0;
    $this->load->model('tool/upload');
    if(isset($data['already_received'])){
      $totalReceivedQty = $data['already_received'] + $data['received_qty'];
    }
    $getProductDetails = $this->db->query("SELECT p.quantity, p.product_id, pd.name FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) WHERE p.product_id = '".(int)$data['product_id']."' AND pd.language_id = '".(int)$this->config->get('config_language_id')."' ")->row;

    $getQuoteProduct = $this->getOrderProductData($data);
    $htmp_temp_option = '';
    $getOptions = $this->getQuotationOptions($data['quotation_id'], $getQuoteProduct['id']);
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

    if(!empty($getProductDetails) && isset($getQuoteProduct['id'])){
        date_default_timezone_set("Asia/Kolkata");
        $current_datetime = date('Y-m-d H:i:s');

        $productTotalQty = $getProductDetails['quantity'] + $data['received_qty'];
        $this->db->query("UPDATE ".DB_PREFIX."product SET `quantity` = '".(int)$productTotalQty."' WHERE `product_id` = '".(int)$data['product_id']."' ");

        $this->db->query("UPDATE ".DB_PREFIX."po_quotation_product SET `received_qty` = '".(int)$totalReceivedQty."' WHERE `id` = '".(int)$data['po_product_id']."' AND `quotation_id` = '".(int)$data['quotation_id']."' AND `product_id` = '".(int)$data['product_id']."' ");

        $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation_product_shipment SET `quotation_product_id` = '".(int)$data['po_product_id']."', `quotation_id` = '".(int)$data['purchase_id']."', `product_id` = '".(int)$data['product_id']."', `received_qty` = '".(int)$data['received_qty']."', `comment` = '".$this->db->escape($data['comment'])."', `date` = '".$this->db->escape($current_datetime)."' ");

        $this->load->model('po_mgmt/po_email');
        $html_temp = '<div style=" background-color: #ccc;padding: 10px;width: 80%;overflow:hidden;"><table style="width:100%;"><tr><th colspan="3">-: Product Quantity Received :-</th></tr><tr><th style="border-bottom: 1px solid #333;text-align: left;">Purchase Order Id </th><th style="border-bottom: 1px solid #333;text-align: left;">Product Name</th><th style="border-bottom: 1px solid #333;text-align: left;">Ordered Quantity</th><th style="border-bottom: 1px solid #333;text-align: left;">Already Received Qty</th><th style="border-bottom: 1px solid #333;text-align: left;">Currently Received Qty</th></tr>';

        $html_temp .= '<tr><td style="padding:10px;text-align: left;">'.'#'.$data['format_purchase_id'].'</td><td style="padding:10px;text-align: left;">'.$getProductDetails['name'].$htmp_temp_option.' (#'.$getProductDetails['product_id'].')</td><td style="padding:10px;text-align: left;">'.$data['ordered_qty'].'</td><td style="padding:10px;text-align: left;">'.$data['already_received'].'</td><td style="padding:10px;text-align: left;">'.$data['received_qty'].'</td></tr></table></div>';
        // update quotation status to quotation
        if($this->config->get('module_oc_pom_update_product_email_admin')){
          $data['templateId'] = $this->config->get('module_oc_pom_update_product_email_admin');
          $data['email_to']   = $this->config->get('module_oc_pom_email_id') ? $this->config->get('module_oc_pom_email_id') : $this->config->get('config_email');
          $data['email_from'] = $this->config->get('config_email');
          $value_index        = array(
                                    'quotation_id'      => '#'.$data['format_purchase_id'],
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
                                    'quotation_id'      => '#'.$data['format_purchase_id'],
                                    'update_quotation'  => $html_temp,
          );
          $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
        }
    }
  }

  public function updateShipment($data = array()){
    $this->load->model('tool/upload');
    $getProductDetails  = $this->getOrderProductData($data);
    $getQuoteProduct    = $this->getOrderProductData($data);
    $htmp_temp_option   = '';
    $getOptions         = $this->getQuotationOptions($data['quotation_id'], $getQuoteProduct['id']);
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
    if(!empty($getProductDetails)){
        date_default_timezone_set("Asia/Kolkata");
        $current_datetime = date('Y-m-d H:i:s');

        $this->db->query("UPDATE ".DB_PREFIX."po_quotation_product SET `schedule_date` = '".$this->db->escape($data['schedule_date_next'])."' WHERE `id` = '".(int)$data['po_product_id']."' AND `quotation_id` = '".(int)$data['purchase_id']."' AND `product_id` = '".(int)$data['product_id']."' ");

        $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation_product_shipment SET `quotation_product_id` = '".(int)$data['po_product_id']."', `quotation_id` = '".(int)$data['purchase_id']."', `product_id` = '".(int)$data['product_id']."', `received_qty` = '0', `comment` = '".$this->db->escape($data['shipment_comment'].' <b>- (Shipment Notification)</b>')."', `date` = '".$this->db->escape($current_datetime)."' ");

        $this->load->model('po_mgmt/po_email');
        $html_temp = '<div style=" background-color: #ccc;padding: 10px;width: 80%;overflow:hidden;"><table style="width:100%;"><tr><th colspan="3">-: Shipment Updated :-</th></tr><tr><th style="border-bottom: 1px solid #333;text-align: left;">Purchase Order Id </th><th style="border-bottom: 1px solid #333;text-align: left;">Product Name</th><th style="border-bottom: 1px solid #333;text-align: left;">Next Shipment date</th></tr>';
        $html_temp .= '<tr><td style="padding:10px;text-align: left;">'.'#'.$data['format_purchase_id'].'</td><td style="padding:10px;text-align: left;">'.$getProductDetails['name'].' (#'.$getProductDetails['product_id'].')</td><td style="padding:10px;text-align: left;">'.$data['schedule_date_next'].'</td></tr></table></div>';
        // update quotation status to quotation
        if($this->config->get('module_oc_pom_update_product_email_admin')){
          $data['templateId'] = $this->config->get('module_oc_pom_update_product_email_admin');
          $data['email_to']   = $this->config->get('module_oc_pom_email_id') ? $this->config->get('module_oc_pom_email_id') : $this->config->get('config_email');
          $data['email_from'] = $this->config->get('config_email');
          $value_index        = array(
                                    'quotation_id'      => '#'.$data['format_purchase_id'],
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
                                    'quotation_id'      => '#'.$data['format_purchase_id'],
                                    'update_quotation'  => $html_temp,
          );
          $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
        }
    }
  }

  public function deletePurchaseOrder($quotation_id){
      $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation_history WHERE quotation_id = '".(int)$quotation_id."' ");
      $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation_product WHERE quotation_id = '".(int)$quotation_id."' ");
      $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation_product_option WHERE quotation_id = '".(int)$quotation_id."' ");
      $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation WHERE id = '".(int)$quotation_id."' ");
  }

  public function getOrderProductShipment($data = array()){
    $sql = "SELECT DISTINCT ps.id, ps.* FROM ".DB_PREFIX."po_quotation_product_shipment ps LEFT JOIN ".DB_PREFIX."po_quotation q ON (ps.quotation_id = q.id) LEFT JOIN ".DB_PREFIX."po_quotation_product qp ON (ps.product_id = qp.product_id) LEFT JOIN ".DB_PREFIX."product p ON(ps.product_id = p.product_id) WHERE ps.quotation_id = '".(int)$data['quotation_id']."' AND ps.product_id = '".(int)$data['product_id']."' AND qp.product_id = '".(int)$data['product_id']."' ";

    if(isset($data['po_product_id']) && !empty($data['po_product_id'])){
      $sql .= " AND ps.quotation_product_id = '".(int)$data['po_product_id']."' ";
    }

    $results = $this->db->query($sql)->rows;
    return $results;
  }

  public function getPOEntry($order_id){
    $result = $this->db->query("SELECT * FROM ".DB_PREFIX."po_quotation WHERE order_id = '".(int)$order_id."' ")->row;

    return $result;
  }

  public function getSupplierProductArray($product_array = array(), $order_id){
      $suppluerProduct  = array();
      $this->load->model('sale/order');
      $this->load->model('tool/upload');

      foreach ($product_array as $key => $product) {
          $getSupplier = $this->db->query("SELECT s.*, sp.*, p.quantity, cn.name as country_name, z.name as state_name FROM ".DB_PREFIX."po_supplier_product sp LEFT JOIN ".DB_PREFIX."po_supplier s ON (sp.supplier_id = s.supplier_id) LEFT JOIN ".DB_PREFIX."product p ON (sp.product_id = p.product_id) LEFT JOIN ".DB_PREFIX."country cn ON (s.country_id = cn.country_id) LEFT JOIN ".DB_PREFIX."zone z ON ((s.state_id = z.zone_id) AND (s.country_id = z.country_id)) WHERE p.status = '1' AND sp.product_id = '".(int)$product['product_id']."' AND p.product_id = '".(int)$product['product_id']."' ")->row;

          if(!empty($getSupplier)){
              $precure_needed = $precure_product = 0;
              if($getSupplier['quantity'] == 0){
                  $precure_product = 1;
              }

              $option_data = array();
              $options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);
              foreach ($options as $option) {
                $option_data[] = array(
                  'product_option_id'        => $option['product_option_id'],
                  'product_option_value_id'  => $option['product_option_value_id'],
                  'name'  => $option['name'],
                  'value' => $option['value'],
                  'type'  => $option['type'],
                );
              }
              if($getSupplier['supplier_cost'] == 0){
                  $product_price = $product['price'];
              }else{
                  $product_price = $getSupplier['supplier_cost'];
              }

              $product_data = array(
                                  'product_id'            => $product['product_id'],
                                  'order_product_id'      => $product['order_product_id'],
                                  'supplier_cost'         => $product_price,
                                  'supplier_qty'          => $product['quantity'],
                                  'sub_total'             => ($product_price * $product['quantity']),
                                  'supplier_tax'          => $this->config->get('module_oc_pom_tax_cost'),
                                  'precure_needed'        => $precure_product,
                                  'option_data'           => $option_data,
                              );
              if(isset($suppluerProduct[$getSupplier['supplier_id']]['quote_products'])){
                  $suppluerProduct[$getSupplier['supplier_id']]['precure_needed']   = $precure_needed;
                  $suppluerProduct[$getSupplier['supplier_id']]['quote_products'][] = $product_data;
              }else{
                  $suppluerProduct[$getSupplier['supplier_id']] = array(
                        'supplier_id'           => $getSupplier['supplier_id'],
                        'order_id'              => $order_id,
                        'shipping_address'      => $getSupplier['street'].', '.$getSupplier['city'].', '.$getSupplier['state_name'].', '.$getSupplier['country_name'].', '.$getSupplier['postcode'],
                        'shipping_method_id'    => 0,
                        'shipping_method_name'  => 'N/A',
                        'shipping_cost'         => $this->config->get('module_oc_pom_shipping_cost'),
                        'payment_method_id'     => 0,
                        'payment_method_name'   => 'N/A',
                        'order_source'          => 'website',
                        'precure_needed'        => $precure_needed,
                        'mail_supplier'         => true,
                        'quote_products'        => array($product_data),
                    );
              }
          }
      }
      return $suppluerProduct;
  }

  public function addPurchaseOrder($data = array(), $po_type = ''){
      $getTotal = $getTotalSub = $getTotalTax = 0;
      date_default_timezone_set("Asia/Kolkata");
      $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation SET `supplier_id` = '".(int)$data['supplier_id']."', `order_id` = '".(int)$data['order_id']."', `shipping_address` = '".$this->db->escape($data['shipping_address'])."', `shipping_method_id` = '".(int)$data['shipping_method_id']."', `shipping_method` = '".$this->db->escape($data['shipping_method_name'])."', `shipping_cost` = '".(float)$data['shipping_cost']."', `shipping_start_date` = NOW(), `shipping_expected_date` = NOW(), `payment_term_id` = '".(int)$data['payment_method_id']."', `payment_term` = '".$this->db->escape($data['payment_method_name'])."', `language_id` = '".(int)$this->config->get('config_language_id')."', `currency_id` = '".(int)$this->currency->getId($this->config->get('config_currency'))."', `currency_code` = '".$this->db->escape($this->config->get('config_currency'))."', `order_source` = '".$this->db->escape($data['order_source'])."', `total` = '0.00', `created_date` = NOW(), `purchase_date` = NOW(), `status` = '2',`paid_status`='0'");

      $getLastId = $this->db->getLastId();

      if($getLastId){
          if(isset($data['quote_products']) && !empty($data['quote_products'])){
              foreach ($data['quote_products'] as $key => $product) {
                  $getTotalSub += $product['sub_total'];
                  $getTotalTax += ($product['sub_total'] * $product['supplier_tax'])/100;
                  $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation_product SET `quotation_id` = '".(int)$getLastId."', `supplier_id` = '".(int)$data['supplier_id']."', `product_id` = '".(int)$product['product_id']."', `ordered_cost` = '".(float)$product['supplier_cost']."', `ordered_qty` = '".(int)$product['supplier_qty']."', `sub_total` = '".(float)$product['sub_total']."', `tax` = '".$this->db->escape($product['supplier_tax'])."' ");

                  $quotation_product_id = $this->db->getLastId();
                  if(!empty($product['option_data'])){
                      foreach ($product['option_data'] as $key => $p_option) {
                          $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation_product_option SET `quotation_id` = '".(int)$getLastId."', `quotation_product_id` = '".(int)$quotation_product_id."', `product_option_id` = '".(int)$p_option['product_option_id']."', `product_option_value_id` = '".(int)$p_option['product_option_value_id']."', `name` = '".$this->db->escape($p_option['name'])."', `value` = '".$this->db->escape($p_option['value'])."', `type` = '".$this->db->escape($p_option['type'])."' ");
                      }
                  }
              }
          }
          $getTotal         = $getTotalSub + $data['shipping_cost'] + $getTotalTax;
          $quotation_prefix = $this->config->get('module_oc_pom_order_prefix');

          $this->db->query("UPDATE ".DB_PREFIX."po_quotation SET `quotation_id` = '".$this->db->escape($quotation_prefix.sprintf('%05d', $getLastId))."', `total` = '".(float)$getTotal."' WHERE id = '".(int)$getLastId."' AND supplier_id = '".(int)$data['supplier_id']."' ");

          if(isset($data['comment']) && !empty($data['comment'])){
              $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation_history SET `quotation_id` = '".(int)$getLastId."', `description` = '".$this->db->escape($data['comment'])."', `date_added` = NOW() ");
          }

          if($data['supplier_id']){
              $this->load->model('po_mgmt/po_email');
              $data['email_from']   = $this->config->get('config_email');
              $data['supplier_id']  = $data['supplier_id'];
              $data['quotation_id'] = $getLastId;
              $value_index          = array(
                'comment'           => isset($data['comment']) ? $data['comment'] : '',
                'shipping_address'  => $data['shipping_address'],
                'order_source'      => ucfirst($data['order_source'])
              );

              // send email to admin for quotation creation if option is set from module config
              if($this->config->get('module_oc_pom_purchase_email_admin')){
                  $data['email_to']     = $this->config->get('module_oc_pom_email_id') ? $this->config->get('module_oc_pom_email_id') : $this->config->get('config_email');
                  $data['templateId']   = $this->config->get('module_oc_pom_purchase_email_admin');
                  $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
              }
              // send email to Supplier for quotation creation if option is set from module config
              if($this->config->get('module_oc_pom_purchase_email_supplier')){
                  if(isset($data['email_to'])){
                    unset($data['email_to']);
                  }
                  $value_index['comment']                   = '';
                  $value_index['seller_comment_combine']    = false;
                  if(isset($data['mail_supplier']) && $data['mail_supplier']){
                    $value_index['comment']                 = isset($data['comment']) ? $data['comment'] : '';
                    $value_index['seller_comment_combine']  = true;
                  }
                  $data['templateId']   = $this->config->get('module_oc_pom_purchase_email_supplier');
                  $this->model_po_mgmt_po_email->sendEmail($data,$value_index);
              }
          }
      }
      return $getLastId;
  }
}
