<?php
/**
 * model for supplier's data management
 */
class ModelPOMgmtPoSupplier extends Model
{
  public function getSuppliers($data = array()){
    $sql = "SELECT s.*, sd.*, CONCAT(s.street, ', ', s.city,', ', z.name, ', ', c.name) AS address FROM " . DB_PREFIX . "po_supplier s LEFT JOIN " . DB_PREFIX . "po_supplier_detail sd ON (s.supplier_id = sd.supplier_id) LEFT JOIN ".DB_PREFIX."country c ON (s.country_id = c.country_id) LEFT JOIN ".DB_PREFIX."zone z on ((s.state_id = z.zone_id) AND (s.country_id = z.country_id)) WHERE sd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    if (!empty($data['filter_supplier_id'])) {
			$sql .= " AND s.supplier_id = '" . (int)$data['filter_supplier_id'] . "'";
		}

		if (!empty($data['filter_company_name'])) {
			$sql .= " AND sd.company_name LIKE '" . $this->db->escape(trim($data['filter_company_name'])) . "%'";
		}

    if (!empty($data['filter_owner_name'])) {
			$sql .= " AND sd.owner_name LIKE '" . $this->db->escape(trim($data['filter_owner_name'])) . "%'";
		}

		if (!empty($data['filter_address'])) {
			$sql .= " AND CONCAT(s.street, ', ', s.city,', ', z.name, ', ', c.name) LIKE '%" . $this->db->escape(trim($data['filter_address'])) . "%'";
		}

    if (!empty($data['filter_email'])) {
			$sql .= " AND s.email LIKE '" . $this->db->escape(trim($data['filter_email'])) . "%'";
		}

    if (!empty($data['filter_postcode'])) {
			$sql .= " AND s.postcode LIKE '" . $this->db->escape(trim($data['filter_postcode'])) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND s.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY s.supplier_id";

		$sort_data = array(
			'sd.company_name',
			'sd.owner_name',
			's.email',
			'address',
      's.postcode',
			's.status'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sd.owner_name";
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

  public function getTotalSuppliers($data = array()){
    $sql = "SELECT COUNT(DISTINCT s.supplier_id) AS total FROM " . DB_PREFIX . "po_supplier s LEFT JOIN " . DB_PREFIX . "po_supplier_detail sd ON (s.supplier_id = sd.supplier_id) LEFT JOIN ".DB_PREFIX."country c ON (s.country_id = c.country_id) LEFT JOIN ".DB_PREFIX."zone z on ((s.state_id = z.zone_id) AND (s.country_id = z.country_id))";

		$sql .= " WHERE sd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    if (!empty($data['filter_supplier_id'])) {
      $sql .= " AND s.supplier_id = '" . (int)$data['filter_supplier_id'] . "'";
    }

		if (!empty($data['filter_company_name'])) {
			$sql .= " AND sd.company_name LIKE '" . $this->db->escape(trim($data['filter_company_name'])) . "%'";
		}

    if (!empty($data['filter_owner_name'])) {
			$sql .= " AND sd.owner_name LIKE '" . $this->db->escape(trim($data['filter_owner_name'])) . "%'";
		}

		if (!empty($data['filter_address'])) {
			$sql .= " AND CONCAT(s.street, ', ', s.city,', ', z.name, ', ', c.name) LIKE '%" . $this->db->escape(trim($data['filter_address'])) . "%'";
		}

    if (!empty($data['filter_email'])) {
			$sql .= " AND s.email LIKE '" . $this->db->escape(trim($data['filter_email'])) . "%'";
		}

    if (!empty($data['filter_postcode'])) {
			$sql .= " AND s.postcode LIKE '" . $this->db->escape(trim($data['filter_postcode'])) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND s.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
  }

  public function getSupplier($supplier_id) {
    // $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "po_supplier s LEFT JOIN ".DB_PREFIX."po_supplier_detail sd ON (s.supplier_id = sd.supplier_id) WHERE s.supplier_id = '" . (int)$supplier_id . "' AND sd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
    $query = $this->db->query("SELECT s.*, sd.*, CONCAT(s.street, ', ', s.city,', ', z.name, ', ', c.name) AS address FROM " . DB_PREFIX . "po_supplier s LEFT JOIN ".DB_PREFIX."po_supplier_detail sd ON (s.supplier_id = sd.supplier_id) LEFT JOIN ".DB_PREFIX."country c ON (s.country_id = c.country_id) LEFT JOIN ".DB_PREFIX."zone z on ((s.state_id = z.zone_id) AND (s.country_id = z.country_id)) WHERE s.supplier_id = '" . (int)$supplier_id . "' AND sd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

    return $query->row;
  }

	public function getSupplierDetail($supplier_id) {
		$supplier_detail_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "po_supplier_detail WHERE supplier_id = '" . (int)$supplier_id . "'");

		foreach ($query->rows as $result) {
			$supplier_detail_data[$result['language_id']] = array(
				'company_name'   => $result['company_name'],
				'owner_name'     => $result['owner_name'],
			);
		}

		return $supplier_detail_data;
	}

  public function getSupplierByEmail($email) {
    $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "po_supplier WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

    return $query->row;
  }

  public function addSupplier($data = array()){
      $this->db->query("INSERT INTO ".DB_PREFIX."po_supplier SET `email` = '".$this->db->escape($data['supplier_email'])."', `status` = '".(int)$data['supplier_status']."', `website` = '".$this->db->escape($data['supplier_website'])."', `tax_number` = '".$this->db->escape($data['supplier_tax_number'])."', `gender` = '".$this->db->escape($data['supplier_gender'])."', `street` = '".$this->db->escape($data['supplier_street'])."', `city` = '".$this->db->escape($data['supplier_city'])."', `state_id` = '".$this->db->escape($data['supplier_state_id'])."', `country_id` = '".$this->db->escape($data['supplier_country_id'])."', `postcode` = '".$this->db->escape($data['supplier_postcode'])."', `telephone` = '".$this->db->escape($data['supplier_telephone'])."', `fax` = '".$this->db->escape($data['supplier_fax'])."' ");

      $supplier_id = $this->db->getLastId();

      foreach ($data['supplier_details'] as $language_id => $value) {
  			$this->db->query("INSERT INTO " . DB_PREFIX . "po_supplier_detail SET supplier_id = '" . (int)$supplier_id . "', company_name = '" . $this->db->escape($value['company_name']) . "', owner_name = '" . $this->db->escape($value['owner_name']) . "', language_id = '" . (int)$language_id . "' ");
  		}
      return $supplier_id;
  }

  public function editSupplier($supplier_id, $data = array()){
    $getSupplierDetails = $this->getSupplierDetail($supplier_id);
    if(!empty($getSupplierDetails)){
      $this->db->query("UPDATE ".DB_PREFIX."po_supplier SET `email` = '".$this->db->escape($data['supplier_email'])."', `status` = '".(int)$data['supplier_status']."', `website` = '".$this->db->escape($data['supplier_website'])."', `tax_number` = '".$this->db->escape($data['supplier_tax_number'])."', `gender` = '".$this->db->escape($data['supplier_gender'])."', `street` = '".$this->db->escape($data['supplier_street'])."', `city` = '".$this->db->escape($data['supplier_city'])."', `state_id` = '".(int)$data['supplier_state_id']."', `country_id` = '".(int)$data['supplier_country_id']."', `postcode` = '".$this->db->escape($data['supplier_postcode'])."', `telephone` = '".$this->db->escape($data['supplier_telephone'])."', `fax` = '".$this->db->escape($data['supplier_fax'])."' WHERE `supplier_id` = '".(int)$supplier_id."' ");

      if(!empty($data['supplier_details']) && isset($data['supplier_details'])){
          foreach ($data['supplier_details'] as $language_id => $details) {
              $this->db->query("UPDATE ".DB_PREFIX."po_supplier_detail SET `company_name` = '".$this->db->escape($details['company_name'])."', `owner_name` = '".$this->db->escape($details['owner_name'])."' WHERE language_id = '".(int)$language_id."' AND supplier_id = '".(int)$supplier_id."' ");
          }
      }
    }
  }

  public function getSupplierProducts($data = array()){
      $sql = "SELECT DISTINCT * FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_description pd ON (p.product_id = pd.product_id)";

      if(empty($data['tab_option'])){
        $sql .= " RIGHT JOIN ".DB_PREFIX."po_supplier_product sp ON (p.product_id = sp.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
      }else if(isset($data['tab_option']) && $data['tab_option'] == 'assign') {
        $sql .= " WHERE p.product_id NOT IN (SELECT sp.product_id FROM ".DB_PREFIX."po_supplier_product sp) AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
      }

      if(!empty($data['supplier_id']) && empty($data['tab_option'])){
        $sql .= " AND sp.supplier_id = '".(int)$data['supplier_id']."' ";
      }

      if(!empty($data['filter_supplier_id'])){
        $sql .= " AND sp.supplier_id = '".(int)$data['filter_supplier_id']."' ";
      }

      if(!empty($data['filter_product_id'])){
        $sql .= " AND p.product_id = '".(int)$data['filter_product_id']."' ";
      }

      if(!empty($data['filter_product_name'])){
        $sql .= " AND pd.name LIKE '".$this->db->escape($data['filter_product_name'])."%' ";
      }

      if(!empty($data['filter_oc_model'])){
        $sql .= " AND p.model LIKE '".$this->db->escape($data['filter_oc_model'])."%' ";
      }

      if (isset($data['filter_oc_price']) && !is_null($data['filter_oc_price'])) {
        $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_oc_price']) . "%'";
      }

      if(!empty($data['filter_supplier_sku'])){
        $sql .= " AND LCASE(sp.supplier_sku) LIKE '".$this->db->escape(strtolower($data['filter_supplier_sku']))."%' ";
      }

      if (isset($data['filter_supplier_cost']) && !is_null($data['filter_supplier_cost']) && empty($data['tab_option'])) {
        $sql .= " AND sp.supplier_cost LIKE '" . $this->db->escape($data['filter_supplier_cost']) . "%'";
      }

      $sql .= " GROUP BY p.product_id ";

      $sort_data = array(
        'p.product_id',
  			'pd.name',
  			'p.model',
        'p.price',
  			'sp.supplier_sku',
  			'sp.supplier_cost',
  		);

  		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
  			$sql .= " ORDER BY " . $data['sort'];
  		} else {
  			$sql .= " ORDER BY pd.name";
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

  public function getSupplierProduct($data = array()){
    $sql = "SELECT DISTINCT * FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_description pd ON (p.product_id = pd.product_id)";

    if(empty($data['tab_option'])){
      $sql .= " RIGHT JOIN ".DB_PREFIX."po_supplier_product sp ON (p.product_id = sp.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
    }else if(isset($data['tab_option']) && $data['tab_option'] == 'assign') {
      $sql .= " WHERE p.product_id NOT IN (SELECT sp.product_id FROM ".DB_PREFIX."po_supplier_product sp) AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
    }

    if(!empty($data['supplier_id']) && empty($data['tab_option'])){
      $sql .= " AND sp.supplier_id = '".(int)$data['supplier_id']."' ";
    }

    if(!empty($data['filter_product_id'])){
      $sql .= " AND p.product_id = '".(int)$data['filter_product_id']."' ";
    }

    return $this->db->query($sql)->row;
  }

  public function getTotalSupplierProducts($data = array()){
    $sql = "SELECT  COUNT(DISTINCT p.product_id) AS total FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_description pd ON (p.product_id = pd.product_id)";

    if(empty($data['tab_option'])){
      $sql .= " RIGHT JOIN ".DB_PREFIX."po_supplier_product sp ON (p.product_id = sp.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
    }else if(isset($data['tab_option']) && $data['tab_option'] == 'assign') {
      $sql .= " WHERE p.product_id NOT IN (SELECT sp.product_id FROM ".DB_PREFIX."po_supplier_product sp) AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
    }

    if(!empty($data['supplier_id']) && empty($data['tab_option'])){
      $sql .= " AND sp.supplier_id = '".(int)$data['supplier_id']."' ";
    }

    if(!empty($data['filter_product_id'])){
      $sql .= " AND p.product_id = '".(int)$data['filter_product_id']."' ";
    }

    if(!empty($data['filter_product_name'])){
      $sql .= " AND pd.name LIKE '".$this->db->escape($data['filter_product_name'])."%' ";
    }

    if(!empty($data['filter_oc_model'])){
      $sql .= " AND p.model LIKE '".$this->db->escape($data['filter_oc_model'])."%' ";
    }

    if (isset($data['filter_oc_price']) && !is_null($data['filter_oc_price'])) {
      $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_oc_price']) . "%'";
    }

    if(!empty($data['filter_supplier_sku'])){
      $sql .= " AND LCASE(sp.supplier_sku) LIKE '".$this->db->escape(strtolower($data['filter_supplier_sku']))."%' ";
    }

    if (isset($data['filter_supplier_cost']) && !is_null($data['filter_supplier_cost']) && empty($data['tab_option'])) {
      $sql .= " AND sp.supplier_cost LIKE '" . $this->db->escape($data['filter_supplier_cost']) . "%'";
    }

		$query = $this->db->query($sql);

		return $query->row['total'];
  }



  public function addSupplierProduct($data = array()){
      if(isset($data['post_data']) && !empty($data['post_data'])){
          foreach ($data['post_data'] as $product_id => $post_data_value) {
              $getEntry = $this->db->query("SELECT sp.* FROM ".DB_PREFIX."po_supplier_product sp LEFT JOIN ".DB_PREFIX."product p ON(sp.product_id = p.product_id) WHERE sp.supplier_id = '".(int)$data['supplier_id']."' AND sp.product_id = '".(int)$product_id."' AND p.product_id = '".(int)$product_id."' ")->row;

              if(!empty($getEntry)){
                  $this->db->query("UPDATE ".DB_PREFIX."po_supplier_product SET `supplier_sku` = '".$this->db->escape($post_data_value['supplier_sku'])."', `supplier_cost` = '".(float) $post_data_value['supplier_cost']."' WHERE id = '".(int)$getEntry['id']."' AND supplier_id = '".(int)$data['supplier_id']."' AND product_id = '".(int)$product_id."' ");
              }else{
                  $this->db->query("INSERT INTO ".DB_PREFIX."po_supplier_product SET `supplier_id` = '".(int)$data['supplier_id']."', `product_id` = '".(int)$product_id."', `supplier_sku` = '".$this->db->escape($post_data_value['supplier_sku'])."', `supplier_cost` = '".(float) $post_data_value['supplier_cost']."' ");
              }
          }
      }
  }

  public function deleteSupplier($supplier_id){
      if($supplier_id){
        $getQuotation = $this->db->query("SELECT * FROM ".DB_PREFIX."po_quotation WHERE supplier_id = '".(int)$supplier_id."' ")->rows;
        if(!empty($getQuotation)){
            foreach ($getQuotation as $quotation) {
                $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation_product WHERE quotation_id = '".(int)$quotation['id']."' ");
                $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation_product_option WHERE quotation_id = '".(int)$quotation['id']."' ");
                $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation_product_shipment WHERE quotation_id = '".(int)$quotation['id']."' ");
                $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation_history WHERE quotation_id = '".(int)$quotation['id']."' ");
                $this->db->query("DELETE FROM ".DB_PREFIX."po_quotation WHERE quotation_id = '".(int)$quotation['id']."' ");
            }
        }
        $this->db->query("DELETE FROM ".DB_PREFIX."po_supplier_product WHERE supplier_id = '".(int)$supplier_id."' ");
        $this->db->query("DELETE FROM ".DB_PREFIX."po_supplier_detail WHERE supplier_id = '".(int)$supplier_id."' ");
        $this->db->query("DELETE FROM ".DB_PREFIX."po_supplier WHERE supplier_id = '".(int)$supplier_id."' ");
      }
  }

  public function deleteSupplierProduct($product_id, $supplier_id){
      if($product_id){
        $this->db->query("DELETE FROM ".DB_PREFIX."po_supplier_product WHERE product_id = '".(int)$product_id."' AND supplier_id = '".(int)$supplier_id."' ");
      }
  }

  public function getSupplierPaymentMethod($supplier_id) {
    $query = $this->db->query("SELECT psp.method_id,pmd.method_name as name FROM " . DB_PREFIX . "po_methods pm LEFT JOIN " . DB_PREFIX . "po_supplier_payment psp ON(pm.method_id = psp.method_id) LEFT JOIN " . DB_PREFIX . "po_methods_description pmd ON(pmd.method_id = psp.method_id) WHERE psp.supplier_id='" . (int)$supplier_id . "' AND pm.status='1' AND pmd.language_id='" . (int)$this->config->get('config_language_id') . "'")->row;

    return $query;
  }

  public function getSupplierPaymentMethodDetails($supplier_id,$method_id) {
    
    $query = $this->db->query("SELECT name,value FROM " . DB_PREFIX . "po_supplier_payment_details WHERE supplier_id='" . (int)$supplier_id . "' AND method_id='" . (int)$method_id . "'")->rows;

    return $query;
  }

  public function addSupplierPaymentMethod($supplier_id,$method_id,$payment_method_details = array()) {

    $this->db->query("DELETE FROM " . DB_PREFIX . "po_supplier_payment WHERE supplier_id='" . (int)$supplier_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "po_supplier_payment_details WHERE supplier_id='" . (int)$supplier_id . "'");

    $this->db->query("INSERT INTO " . DB_PREFIX . "po_supplier_payment SET supplier_id='" . (int)$supplier_id . "',method_id='" . (int)$method_id . "'");

    if($payment_method_details && !empty($payment_method_details) && is_array($payment_method_details)) {
      foreach ($payment_method_details as $key => $value) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "po_supplier_payment_details SET supplier_id='" . (int)$supplier_id . "',method_id='" . (int)$method_id . "',name='" . $this->db->escape($value['name']) . "',value='" . $this->db->escape($value['value']) . "'");
      }
    }
  }

    public function getSuppliersForFilters($data = array()){
        $sql = "SELECT s.supplier_id, sd.company_name FROM " . DB_PREFIX . "po_supplier s LEFT JOIN " . DB_PREFIX . "po_supplier_detail sd ON (s.supplier_id = sd.supplier_id) WHERE sd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        $query = $this->db->query($sql);

        return $query->rows;
    }
}
