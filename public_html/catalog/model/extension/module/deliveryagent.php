<?php
class ModelExtensionModuleDeliveryAgent extends Model {

	public function addLoginAttempt($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "deliveryagent_login WHERE email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");

		if (!$query->num_rows) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "deliveryagent_login SET email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', total = 1, date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "', date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "deliveryagent_login SET total = (total + 1), date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE deliveryagent_login_id = '" . (int)$query->row['deliveryagent_login_id'] . "'");
		}
	}

	public function getLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "deliveryagent_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "deliveryagent_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function getAgent($deliveryagent_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "deliveryagent WHERE deliveryagent_id = '" . (int)$deliveryagent_id . "'");

		return $query->row;
	}

	public function getAgentByEmail($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "deliveryagent WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function login($email, $password) {
		$deliveryagent_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "deliveryagent WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");

		if ($deliveryagent_query->num_rows) {
			$this->session->data['deliveryagent_id'] = $deliveryagent_query->row['deliveryagent_id'];
			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->session->data['deliveryagent_id']);
	}

	public function isLogged() {
		if(isset($this->session->data['deliveryagent_id'])) {
			return $this->session->data['deliveryagent_id'];	
		} else {
			return 0;
		}
		
	}

	public function getOrder($order_id) {
		$sql = "SELECT *  FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "deliveryagent_transaction` dt ON (o.order_id = dt.order_id) WHERE o.order_id = '" . (int)$order_id . "' AND dt.deliveryagent_id = '" . (int)$this->session->data['deliveryagent_id'] . "' AND o.order_status_id > '0'";

		if($this->config->get("deliveryagent_orderstatus")) {
			$orderstatusarray = $this->config->get("deliveryagent_orderstatus");
			if(!empty($orderstatusarray)) {
				$orderstatusstring = implode(",", $orderstatusarray);
				$sql .= " AND o.order_status_id IN (".$orderstatusstring.")";
			}
		}
		
		$order_query = $this->db->query($sql);

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			return array(
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'telephone'               => $order_query->row['telephone'],
				'email'                   => $order_query->row['email'],
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_method'          => $order_query->row['payment_method'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_method'         => $order_query->row['shipping_method'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'language_id'             => $order_query->row['language_id'],
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added'],
				'ip'                      => $order_query->row['ip']
			);
		} else {
			return false;
		}
	}

	public function getOrders($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}

		$sql = "SELECT dt.sort_order,dt.deliverytime,o.order_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) LEFT JOIN `" . DB_PREFIX . "deliveryagent_transaction` dt ON (o.order_id = dt.order_id) WHERE dt.deliveryagent_id = '" . (int)$this->session->data['deliveryagent_id'] . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if($this->config->get("deliveryagent_orderstatus")) {
			$orderstatusarray = $this->config->get("deliveryagent_orderstatus");
			if(!empty($orderstatusarray)) {
				$orderstatusstring = implode(",", $orderstatusarray);
				$sql .= " AND o.order_status_id IN (".$orderstatusstring.")";
			}
		}

		$sql .= " AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY dt.sort_order ASC LIMIT " . (int)$start . "," . (int)$limit;
	
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalOrders() {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "deliveryagent_transaction` dt ON (o.order_id = dt.order_id) WHERE dt.deliveryagent_id = '" . (int)$this->session->data['deliveryagent_id'] . "' AND o.order_status_id > '0'";

		if($this->config->get("deliveryagent_orderstatus")) {
			$orderstatusarray = $this->config->get("deliveryagent_orderstatus");
			if(!empty($orderstatusarray)) {
				$orderstatusstring = implode(",", $orderstatusarray);
				$sql .= " AND o.order_status_id IN (".$orderstatusstring.")";
			}
		}

		$sql .= "AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getOrderStatuses($data = array()) {

		$sql = "SELECT order_status_id, name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ";

		if($this->config->get("deliveryagent_orderstatuschange")) {
			$orderstatusarray = $this->config->get("deliveryagent_orderstatuschange");
			if(!empty($orderstatusarray)) {
				$orderstatusstring = implode(",", $orderstatusarray);
				$sql .= " AND order_status_id IN (".$orderstatusstring.")";
			}
		}

		$sql .= " ORDER BY name";

		$query = $this->db->query($sql);

		$order_status_data = $query->rows;

		return $order_status_data;
		
	}
}