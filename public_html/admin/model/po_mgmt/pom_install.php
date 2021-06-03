<?php
/**
 * model for installing the purchase order management tables
 */
class ModelPOMgmtPOMInstall extends Model
{
  public function createTables(){
    $this->removeTables();
    // Table structure for table `po_methods`
    $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."po_methods` (
      `method_id` int(11) NOT NULL AUTO_INCREMENT,
      `status` tinyint(1) NOT NULL,
      `store_id` int(10) NOT NULL,
      `method_type` varchar(100) NOT NULL,
      PRIMARY KEY (`method_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

    // Table structure for table `po_methods_description`
    $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."po_methods_description` (
      `method_id` int(11) NOT NULL,
      `language_id` int(11) NOT NULL,
      `method_name` varchar(100) NOT NULL
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

    // Table structure for table `po_supplier`
    $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."po_supplier` (
      `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
      `email` varchar(150) NOT NULL,
      `status` tinyint(1) NOT NULL,
      `website` varchar(150) NOT NULL,
      `tax_number` varchar(150) NOT NULL,
      `gender` varchar(1) NOT NULL,
      `street` varchar(150) NOT NULL,
      `city` varchar(150) NOT NULL,
      `state_id` int(11) NOT NULL,
      `country_id` int(11) NOT NULL,
      `postcode` varchar(10) NOT NULL,
      `telephone` varchar(32) NOT NULL,
      `fax` varchar(32) NOT NULL,
      PRIMARY KEY (`supplier_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

    // Table structure for table `po_supplier_details`
    $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."po_supplier_detail` (
      `supplier_id` int(11) NOT NULL,
      `company_name` varchar(150) NOT NULL,
      `owner_name` varchar(150) NOT NULL,
      `language_id` int(5) NOT NULL
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

    // Table structure for table `po_supplier_product`
    $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."po_supplier_product` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `supplier_id` int(11) NOT NULL,
      `product_id` int(11) NOT NULL,
      `supplier_sku` varchar(150) NOT NULL,
      `supplier_cost` decimal(15,4) NOT NULL,
      `lead_day` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

    $this->db->query("CREATE TABLE `" . DB_PREFIX . "po_supplier_payment` (
      `supplier_id` int(11) NOT NULL,
      `method_id` int(11) NOT NULL
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

    $this->db->query("CREATE TABLE `" . DB_PREFIX . "po_supplier_payment_details` (
      `supplier_id` int(11) NOT NULL,
      `method_id` int(11) NOT NULL,
      `name` varchar(255) NOT NULL,
      `value` varchar(255) NOT NULL
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

    // Table structure for table `po_quotation`
    $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."po_quotation` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `quotation_id` varchar(15) NOT NULL,
      `supplier_id` int(11) NOT NULL,
      `order_id` int(11) NOT NULL,
      `shipping_address` varchar(250) NOT NULL,
      `shipping_method_id` int(11) NOT NULL,
      `shipping_method` varchar(150) NOT NULL,
      `shipping_cost` decimal(15,4) NOT NULL,
      `shipping_start_date` datetime NOT NULL,
      `shipping_expected_date` datetime NOT NULL,
      `payment_term` varchar(150) NOT NULL,
      `payment_term_id` int(11) NOT NULL,
      `language_id` int(11) NOT NULL,
      `currency_id` int(11) NOT NULL,
      `currency_code` varchar(100) NOT NULL,
      `order_source` varchar(50) NOT NULL,
      `total` decimal(15,4) NOT NULL,
      `paid_total` decimal(15,4) NOT NULL,
      `created_date` datetime NOT NULL,
      `purchase_date` datetime NOT NULL,
      `status` tinyint(1) NOT NULL,
      `paid_status` tinyint(1) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

    $this->db->query("CREATE TABLE `" . DB_PREFIX . "po_quotation_payment_details` (
      `quotation_id` int(11) NOT NULL,
      `comment` text NOT NULL
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

    // Table structure for table `po_quotation_product`
    $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."po_quotation_product` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `quotation_id` int(11) NOT NULL,
      `supplier_id` int(11) NOT NULL,
      `product_id` int(11) NOT NULL,
      `ordered_cost` decimal(15,4) NOT NULL,
      `ordered_qty` int(11) NOT NULL,
      `received_qty` int(11) NOT NULL,
      `sub_total` decimal(15,4) NOT NULL,
      `tax` varchar(50) NOT NULL,
      `discount` decimal(10,4) NOT NULL,
      `schedule_date` varchar(100) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

    $this->db->query("CREATE TABLE `" . DB_PREFIX . "po_quotation_product_details` (
      `quotation_id` int(11) NOT NULL,
      `supplier_id` int(11) NOT NULL,
      `product_id` int(11) NOT NULL,
      `quotation_cost` decimal(15,4) NOT NULL
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

    // Table structure for table `po_quotation_product_option`
    $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."po_quotation_product_option` (
      `quotation_option_id` int(11) NOT NULL AUTO_INCREMENT,
      `quotation_id` int(11) NOT NULL,
      `quotation_product_id` int(11) NOT NULL,
      `product_option_id` int(11) NOT NULL,
      `product_option_value_id` int(11) NOT NULL,
      `name`  varchar(255) NOT NULL,
      `value` text NOT NULL,
      `type`  varchar(32) NOT NULL,
      `price_prefix` varchar(1) NOT NULL,
      `price` decimal(15,4) NOT NULL,
      PRIMARY KEY (`quotation_option_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

    // Table structure for table `po_quotation_product_shipment`
    $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."po_quotation_product_shipment` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `quotation_product_id` int(11) NOT NULL,
      `quotation_id` int(11) NOT NULL,
      `product_id` int(11) NOT NULL,
      `received_qty` int(11) NOT NULL,
      `comment` text NOT NULL,
      `date` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

    $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."po_quotation_history` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `quotation_id` int(11) NOT NULL,
      `description` varchar(300) NOT NULL,
      `date_added` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

    // Table structure for table `po_mails`
    $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."po_mails` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(150) NOT NULL,
      `subject` varchar(1000) NOT NULL,
		  `message` varchar(5000) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

  }

  public function removeTables(){
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_methods`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_methods_description`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_supplier`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_supplier_detail`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_supplier_product`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_quotation`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_quotation_product`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_quotation_history`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_quotation_product_option`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_quotation_product_shipment`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_mails`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_quotation_product_details`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_quotation_payment_details`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_supplier_payment`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."po_supplier_payment_details`");
  }

  public function saveMethods($data = array()){
      if(isset($data['module_oc_pom_store'])){
        $store_id = $data['module_oc_pom_store'];
      }else{
        $store_id = 0;
      }

      $getShipping = $this->getMethods('shipping', $store_id);
      if(!empty($getShipping)){
        foreach ($getShipping as $key => $value) {
          $this->db->query("DELETE FROM ".DB_PREFIX."po_methods WHERE store_id = '".(int)$store_id."' AND method_type = 'shipping' AND method_id = '".(int)$value['method_id']."' ");
          $this->db->query("DELETE FROM ".DB_PREFIX."po_methods_description WHERE method_id = '".(int)$value['method_id']."' ");
        }
      }
      if(isset($data['module_oc_pom_shipping_method']) && $data['module_oc_pom_shipping_method']){
        foreach ($data['module_oc_pom_shipping_method'] as $key => $shipping_methods) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "po_methods SET store_id = '".(int)$store_id."', status = '" . (int)$shipping_methods['status'] . "', method_type = 'shipping'");

            $method_id = $this->db->getLastId();

            foreach ($shipping_methods['shipping_description'] as $language_id => $value) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "po_methods_description SET method_id = '" . (int)$method_id . "', language_id = '" . (int)$language_id . "', method_name = '" . $this->db->escape($value['name']) . "'");
            }
        }
      }

      $getPayment = $this->getMethods('payment', $store_id);
      if(!empty($getPayment)){
        foreach ($getPayment as $key => $value) {
          $this->db->query("DELETE FROM ".DB_PREFIX."po_methods WHERE store_id = '".(int)$store_id."' AND method_type = 'payment' AND method_id = '".(int)$value['method_id']."' ");
          $this->db->query("DELETE FROM ".DB_PREFIX."po_methods_description WHERE method_id = '".(int)$value['method_id']."' ");
        }
      }
      if(isset($data['module_oc_pom_payment_method']) && $data['module_oc_pom_payment_method']){
          foreach ($data['module_oc_pom_payment_method'] as $key => $payment_methods) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "po_methods SET store_id = '".(int)$store_id."', status = '" . (int)$payment_methods['status'] . "', method_type = 'payment'");
              $method_id = $this->db->getLastId();

              foreach ($payment_methods['payment_description'] as $language_id => $value) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "po_methods_description SET method_id = '" . (int)$method_id . "', language_id = '" . (int)$language_id . "', method_name = '" . $this->db->escape($value['name']) . "'");
              }
          }
      }

      $getSupplierPayment = $this->getMethods('supplier_payment',$store_id);
      if(!empty($getSupplierPayment)){
        foreach ($getSupplierPayment as $key => $value) {
          $this->db->query("DELETE FROM ".DB_PREFIX."po_methods WHERE store_id = '".(int)$store_id."' AND method_type = 'supplier_payment' AND method_id = '".(int)$value['method_id']."' ");
          $this->db->query("DELETE FROM ".DB_PREFIX."po_methods_description WHERE method_id = '".(int)$value['method_id']."' ");
        }
      }
      if(isset($data['module_oc_pom_supplier_payment_method']) && $data['module_oc_pom_supplier_payment_method']){
          foreach ($data['module_oc_pom_supplier_payment_method'] as $key => $supplier_payment_methods) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "po_methods SET store_id = '".(int)$store_id."', status = '" . (int)$supplier_payment_methods['status'] . "', method_type = 'supplier_payment'");
              $method_id = $this->db->getLastId();

              foreach ($supplier_payment_methods['payment_description'] as $language_id => $value) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "po_methods_description SET method_id = '" . (int)$method_id . "', language_id = '" . (int)$language_id . "', method_name = '" . $this->db->escape($value['name']) . "'");
              }
          }
      }
  }

  public function getMethods($type = false, $store_id){
    $method_data = array();
    $sql = 1;
    if($type){
      $sql = "AND method_type = '".$this->db->escape($type)."' ";
    }
    $getMethods = $this->db->query("SELECT * FROM " . DB_PREFIX . "po_methods WHERE store_id = '".(int)$store_id."' ".$sql." ORDER BY method_id ASC ")->rows;

    foreach ($getMethods as $key => $method) {
      $method_data[$key]['method_id']   = $method['method_id'];
      $method_data[$key]['status']      = $method['status'];
      $method_data[$key]['store_id']    = $method['store_id'];
      $method_data[$key]['type']        = $method['method_type'];

      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "po_methods_description WHERE method_id = '" . (int)$method['method_id'] . "' ORDER BY method_id ASC ");

  		foreach ($query->rows as $result) {
        if($method['method_type'] == 'shipping'){
            $method_data[$key]['shipping_description'][$result['language_id']] = array('name' => $result['method_name']);
        }else{
            $method_data[$key]['payment_description'][$result['language_id']]  = array('name' => $result['method_name']);
        }
  		}
    }
    return $method_data;
  }
}
