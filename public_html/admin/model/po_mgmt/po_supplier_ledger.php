<?php

Class ModelPOMgmtPoSupplierLedger extends Model {
    
    /**
     * function to get the supplier ledger information
     *
     * @param array $data
     * @param boolean $type
     * @return void
     */
    public function getSupplierInformation($data = array(),$type = false) {

        $sql = '';

        if($type) {

            $sql .= "SELECT COUNT(ps.supplier_id) as total FROM " . DB_PREFIX ."po_supplier ps LEFT JOIN " . DB_PREFIX . "po_supplier_detail psd ON(ps.supplier_id = psd.supplier_id) WHERE ps.status=1 AND psd.language_id='" . (int)$this->config->get('config_language_id') . "'";

            if (!empty($data['filter_supplier'])) {
                $sql .= " AND psd.owner_name LIKE '" . $this->db->escape($data['filter_supplier']) . "%'";
            }

            $query = $this->db->query($sql);
            return $query->row['total'];            
        } else {

            $sql .= "SELECT ps.supplier_id,psd.owner_name as name,(SELECT SUM(pq.total) FROM " . DB_PREFIX . "po_quotation pq WHERE pq.supplier_id=ps.supplier_id AND status=4) as credit,(SELECT SUM(pq.paid_total) FROM " . DB_PREFIX . "po_quotation pq WHERE pq.supplier_id=ps.supplier_id AND status=4) as debit,(SELECT SUM(shipping_cost) FROM " . DB_PREFIX . "po_quotation pq WHERE pq.supplier_id=ps.supplier_id AND status=4) as expense FROM " . DB_PREFIX ."po_supplier ps LEFT JOIN " . DB_PREFIX . "po_supplier_detail psd ON(ps.supplier_id = psd.supplier_id) WHERE ps.status=1 AND psd.language_id='" . (int)$this->config->get('config_language_id') . "'";

            if (!empty($data['filter_supplier'])) {
                $sql .= " AND psd.owner_name LIKE '" . $this->db->escape($data['filter_supplier']) . "%'";
            }

            $sort_data = array(
                'psd.owner_name',
            );
    
            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY psd.owner_name";
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
            
            return $query;
        }
    }

    /**
     * function for the autocomplete list of supplier name
     *
     * @param array $data
     * @return void
     */
    public function supplierAutoComplete($data = array()) {
        
        $sql = "SELECT ps.supplier_id,psd.owner_name as name FROM " . DB_PREFIX ."po_supplier ps LEFT JOIN " . DB_PREFIX . "po_supplier_detail psd ON(ps.supplier_id = psd.supplier_id) WHERE ps.status=1 AND psd.language_id='" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_supplier'])) {
            $sql .= " AND psd.owner_name LIKE '" . $this->db->escape($data['filter_supplier']) . "%'";
        }

        $sort_data = array(
            'psd.owner_name',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY psd.owner_name";
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
        
        return $query;
    }

    /**
     * function to get the supplier details
     *
     * @param integer $supplier_id
     * @return void
     */
    public function getSupplierDetails($supplier_id) {
        
        $query = $this->db->query("SELECT ps.supplier_id,ps.email,psd.owner_name as name FROM " . DB_PREFIX . "po_supplier ps LEFT JOIN " . DB_PREFIX ."po_supplier_detail psd ON(ps.supplier_id = psd.supplier_id) WHERE ps.supplier_id='" . (int)$supplier_id . "' AND ps.status='1' AND psd.language_id='" . (int)$this->config->get('config_language_id') . "'")->row;

        return $query;
    }

    /**
     * function to get all the supplier complete purchase order
     *
     * @param array $data
     * @return void
     */
    public function getSupplierCompletePurchaseOrder($data = array(),$type = false) {
        
        $sql = '';

        if($type) {

            $sql = "SELECT COUNT(pq.id) as total FROM " . DB_PREFIX . "po_quotation pq WHERE pq.status='4' AND pq.supplier_id='" . (int)$data['supplier_id'] . "' AND pq.paid_status='0'";

            $query = $this->db->query($sql);
            return $query->row['total']; 

        } else {

            $sql = "SELECT pq.id,pq.quotation_id,pq.order_id,pq.purchase_date,pq.total FROM " . DB_PREFIX . "po_quotation pq WHERE pq.status='4' AND pq.supplier_id='" . (int)$data['supplier_id'] . "' AND pq.paid_status='0'";

            $sort_data = array(
                'pq.quotation_id',
            );
    
            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY pq.quotation_id";
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
            
            return $query;
        }
    }

    public function getSupplierDueAmount($supplier_id) {
        
        $query = $this->db->query("SELECT SUM(total) as total_amount,SUM(paid_total) as paid_amount FROM " . DB_PREFIX . "po_quotation WHERE supplier_id='" . (int)$supplier_id . "' AND status='4' AND paid_status='0'")->row;

        return $query;
    }

    public function checkQuotationId($supplier_id,$quotation_id) {
        
        $query = $this->db->query("SELECT quotation_id FROM " . DB_PREFIX . "po_quotation WHERE supplier_id='" . (int)$supplier_id . "' AND id='" . (int)$quotation_id . "' AND status='4' AND paid_status='0'")->row;

        if($query && !empty($query) && isset($query['quotation_id'])) {
            return true;
        } else {
            return false;
        }
    }

    public function addQuotationPaymentDetails($quotation_id,$comment) {
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "po_quotation_payment_details SET quotation_id='" . (int)$quotation_id . "', comment='" . $this->db->escape($comment) . "'")->row;        
    }

    public function makePurcahseOrderPaid($supplier_id,$quotation_id,$comment) {
        
        $quotation_total = $this->db->query("SELECT total FROM " . DB_PREFIX . "po_quotation WHERE supplier_id='" . (int)$supplier_id . "' AND id='" . (int)$quotation_id . "' AND status='4' AND paid_status='0'")->row;

        if($quotation_total) {

            $this->db->query("UPDATE " . DB_PREFIX . "po_quotation SET paid_total='" . (float)$quotation_total['total'] . "',paid_status='1' WHERE id='" . (int)$quotation_id . "' AND supplier_id='" . (int)$supplier_id . "'");

            if($comment) {
                SELF::addQuotationPaymentDetails($quotation_id,$comment);
            }            
        }
    }

    function getPurcahseOrderProducts($data = array(),$type= false) {

        if($type) {
            $sql = "SELECT COUNT(id) as total FROM " . DB_PREFIX . "po_quotation_product WHERE supplier_id='" . (int)$data['supplier_id'] . "'";

            $query = $this->db->query($sql);
            return $query->row['total'];
        } else {
            $sql = "SELECT id,quotation_id,product_id,ordered_cost,tax,ordered_qty FROM " . DB_PREFIX . "po_quotation_product WHERE supplier_id='" . (int)$data['supplier_id'] . "'";

            $sort_data = array(
                'quotation_id',
            );
    
            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY quotation_id";
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
            
            return $query;
        }        
    }

    function getQuotationInfo($quotation_id) {

        $query = $this->db->query("SELECT quotation_id,paid_status,purchase_date,currency_code FROM " . DB_PREFIX . "po_quotation WHERE id='" . (int)$quotation_id . "'")->row;

        return $query;
    }

    function getQuoteProductPrice($supplier_id,$quotation_id,$product_id) {

        $query = $this->db->query("SELECT quotation_cost FROM " . DB_PREFIX . "po_quotation_product_details WHERE product_id='" . (int)$product_id . "' AND supplier_id='" . (int)$supplier_id . "' AND quotation_id='" . (int)$quotation_id . "'")->row;

        return $query;
    }

}