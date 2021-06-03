<?php
class ModelPoMgmtPomgmt extends Model {
    
    public function generatePO($order_info, $order_status_id = false, $stock_check = true) {
        if(isset($order_info['order_id'])){
            $this->load->model('account/order');
            $product_total = $this->model_account_order->getOrderProducts($order_info['order_id']);

            if(!empty($product_total)){
                $getSupplierArray = $this->getSupplierProductArray($product_total, $order_info['order_id']);

                if(!empty($getSupplierArray)){
                    foreach ($getSupplierArray as $supplier_id => $s_products) {
                        // Case:1
                        // check for the order status and status for generate PO
                        
                        if($this->config->get('module_oc_pom_order_to_po') && ($this->config->get('module_oc_pom_order_status_po') == $order_status_id || $stock_check)){
                            $this->addPurchaseOrder($s_products, 'order_status');
                        }else if($this->config->get('module_oc_pom_precure_method') && $this->config->get('module_oc_pom_precure_method') == 'order'){ // Case:2
                            foreach ($s_products['quote_products'] as $key => $q_product) {
                                if($q_product['precure_needed'] == 1){
                                    $s_products['precure_needed'] = 1;
                                }
                            }
                            if($s_products['precure_needed'] == 1){
                                $this->addPurchaseOrder($s_products, 'precure');
                            }
                        }
                    }
                }
            }
        }
    }

    public function getSupplierProductArray($product_array = array(), $order_id){
        $suppluerProduct  = array();
        $this->load->model('account/order');
        $this->load->model('tool/upload');

        foreach ($product_array as $key => $product) {
            $getSupplier = $this->db->query("SELECT s.*, sp.*, p.quantity, cn.name as country_name, z.name as state_name FROM ".DB_PREFIX."po_supplier_product sp LEFT JOIN ".DB_PREFIX."po_supplier s ON (sp.supplier_id = s.supplier_id) LEFT JOIN ".DB_PREFIX."product p ON (sp.product_id = p.product_id) LEFT JOIN ".DB_PREFIX."country cn ON (s.country_id = cn.country_id) LEFT JOIN ".DB_PREFIX."zone z ON ((s.state_id = z.zone_id) AND (s.country_id = z.country_id)) WHERE p.status = '1' AND sp.product_id = '".(int)$product['product_id']."' AND p.product_id = '".(int)$product['product_id']."' ")->row;

            if(!empty($getSupplier)){
                $precure_needed = $precure_product = 0;
                if($getSupplier['quantity'] == 0){
                    $precure_product = 1;
                }

                $option_data = array();
        				$options = $this->model_account_order->getOrderOptions($order_id, $product['order_product_id']);
        				foreach ($options as $option) {
        					if ($option['type'] != 'file') {
        						  $value = $option['value'];
        					} else {
          						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
          						if ($upload_info) {
          							$value = $upload_info['name'];
          						} else {
          							$value = '';
          						}
        					}

        					$option_data[] = array(
                    'product_option_id'        => $option['product_option_id'],
                    'product_option_value_id'  => $option['product_option_value_id'],
        						'name'  => $option['name'],
        						'value' => $value,
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
                          'order_source'          => $this->getSource(),
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
        $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation SET `supplier_id` = '".(int)$data['supplier_id']."', `order_id` = '".(int)$data['order_id']."', `shipping_address` = '".$this->db->escape($data['shipping_address'])."', `shipping_method_id` = '".(int)$data['shipping_method_id']."', `shipping_method` = '".$this->db->escape($data['shipping_method_name'])."', `shipping_cost` = '".(float)$data['shipping_cost']."', `shipping_start_date` = NOW(), `shipping_expected_date` = NOW(), `payment_term_id` = '".(int)$data['payment_method_id']."', `payment_term` = '".$this->db->escape($data['payment_method_name'])."', `language_id` = '".(int)$this->config->get('config_language_id')."', `currency_id` = '".(int)$this->currency->getId($this->config->get('config_currency'))."', `currency_code` = '".$this->db->escape($this->config->get('config_currency'))."', `order_source` = '".$this->db->escape($data['order_source'])."', `total` = '0.00', `created_date` = NOW(), `purchase_date` = NOW(), `status` = '2' ");

        $getLastId = $this->db->getLastId();

        if($getLastId){
            if(isset($data['quote_products']) && !empty($data['quote_products'])){
                foreach ($data['quote_products'] as $key => $product) {
                    if($po_type == 'precure' && $product['precure_needed'] == 1){
                        $getTotalSub += $product['sub_total'];
                        $getTotalTax += ($product['sub_total'] * $product['supplier_tax'])/100;
                        $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation_product SET `quotation_id` = '".(int)$getLastId."', `supplier_id` = '".(int)$data['supplier_id']."', `product_id` = '".(int)$product['product_id']."', `ordered_cost` = '".(float)$product['supplier_cost']."', `ordered_qty` = '".(int)$product['supplier_qty']."', `sub_total` = '".(float)$product['sub_total']."', `tax` = '".$this->db->escape($product['supplier_tax'])."' ");

                        $quotation_product_id = $this->db->getLastId();
                        if(!empty($product['option_data'])){
                            foreach ($product['option_data'] as $key => $p_option) {
                                $this->db->query("INSERT INTO ".DB_PREFIX."po_quotation_product_option SET `quotation_id` = '".(int)$getLastId."', `quotation_product_id` = '".(int)$quotation_product_id."', `product_option_id` = '".(int)$p_option['product_option_id']."', `product_option_value_id` = '".(int)$p_option['product_option_value_id']."', `name` = '".$this->db->escape($p_option['name'])."', `value` = '".$this->db->escape($p_option['value'])."', `type` = '".$this->db->escape($p_option['type'])."' ");
                            }
                        }
                    }else if($po_type == 'order_status'){
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
            }
            $getTotal = $getTotalSub + $data['shipping_cost'] + $getTotalTax;
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

    public function getSource(){
      if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$_SERVER['HTTP_USER_AGENT'])||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($_SERVER['HTTP_USER_AGENT'],0,4))){
          return $order_source = 'phone';
      }else{
          return $order_source = 'website';
      }
    }

}
