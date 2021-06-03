<?php
class ModelExtensionModuleDeliveryAgent extends Model {
	public function adddeliveryagent($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "deliveryagent SET  firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', address = '" . $this->db->escape($data['address'])  . "', extras = '" . $this->db->escape($data['extras'])  . "', city = '" . $data['city'] . "', commission = '" . $this->db->escape($data['commission']) . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', alertemail = '" . (int)$data['alertemail'] . "', status = '" . (int)$data['status'] . "', date_added = NOW()");
		$deliveryagent_id = $this->db->getLastId();
	}

	public function editdeliveryagent($deliveryagent_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "deliveryagent  SET  firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', address = '" . $this->db->escape($data['address'])  . "', extras = '" . $this->db->escape($data['extras'])  . "', city = '" . $data['city'] . "', commission = '" . $this->db->escape($data['commission']) . "', alertemail = '" . (int)$data['alertemail'] . "', status = '" . (int)$data['status'] . "'  WHERE deliveryagent_id = '" . (int)$deliveryagent_id . "'");

		if ($data['password']) {
			$this->db->query("UPDATE " . DB_PREFIX . "deliveryagent SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE deliveryagent_id = '" . (int)$deliveryagent_id . "'");
		}
	}

	public function editToken($deliveryagent_id, $token) {
		$this->db->query("UPDATE " . DB_PREFIX . "deliveryagent SET token = '" . $this->db->escape($token) . "' WHERE deliveryagent_id = '" . (int)$deliveryagent_id . "'");
	}

	public function deletedeliveryagent($deliveryagent_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "deliveryagent WHERE deliveryagent_id = '" . (int)$deliveryagent_id . "'");
	}

	public function getdeliveryagent($deliveryagent_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "deliveryagent WHERE deliveryagent_id = '" . (int)$deliveryagent_id . "'");

		return $query->row;
	}

	public function getdeliveryagentname($deliveryagent_id) {
		$query = $this->db->query("SELECT CONCAT(firstname, ' ', lastname) AS name FROM " . DB_PREFIX . "deliveryagent WHERE deliveryagent_id = '" . (int)$deliveryagent_id . "'");
		if($query->num_rows) {
			return $query->row['name'];
		} else {
			return "";
		}
		
	}

	public function getAgentName($order_id) {
		$name = "";
		$query = $this->db->query("SELECT st.name FROM " . DB_PREFIX . "deliveryagent_transaction st LEFT JOIN " . DB_PREFIX . "deliveryagent_order o ON (st.deliveryagent_id = o.deliveryagent_id) WHERE o.order_id = '" . (int)$order_id . "' LIMIT 1");
		if($query->num_rows) {
			$name = ucwords($query->row['name']);
		}
		return $name;
	}

	public function getAgentId($order_id) {
		$query = $this->db->query("SELECT st.deliveryagent_id FROM " . DB_PREFIX . "deliveryagent_transaction st LEFT JOIN " . DB_PREFIX . "deliveryagent_order o ON (st.deliveryagent_id = o.deliveryagent_id) WHERE o.order_id = '" . (int)$order_id . "' LIMIT 1");
		if($query->num_rows) {
			$deliveryagent_id = $query->row['deliveryagent_id'];
		} else {
			$deliveryagent_id = 0;
		}
		return $deliveryagent_id;
	}

	public function getdeliveryagentByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "deliveryagent WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getOrderCheck($order_id) {
		 $deliveryagent_id =  $this->user->getdeliveryagentId();
		 if($deliveryagent_id) {
          	$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "deliveryagent_order WHERE order_id = '" . (int)$order_id . "' AND  deliveryagent_id IN (".$deliveryagent_id.") ");
         	if($query->num_rows) {
         		return 0;
         	} else {
         		return 1;
         	}
         } else {
         	return 0;
         }
	}

	public function getdeliveryagents($data = array()) {
		$sql = "SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM " . DB_PREFIX . "deliveryagent WHERE 1  ";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'firstname',
			'email',
			'status',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY firstname";
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

	
	public function getTotaldeliveryagents($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "deliveryagent WHERE 1 ";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function createTable() {
		//$this->db->query("DROP TABLE `". DB_PREFIX ."deliveryagent`");
	    if ($this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."deliveryagent'")->num_rows == 0) {
            $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "deliveryagent` (
    		  `deliveryagent_id` int(11) NOT NULL AUTO_INCREMENT,
			  `store_id` int(11) NOT NULL DEFAULT '0',
			  `firstname` varchar(32) NOT NULL,
			  `lastname` varchar(32) NOT NULL,
			  `email` varchar(256) NOT NULL,
			  `telephone` varchar(32) NOT NULL,
			  `commission` varchar(8) NOT NULL,
			  `address` text NOT NULL,
			  `city` varchar(40) NOT NULL,
			  `password` varchar(40) NOT NULL,
  			  `salt` varchar(9) NOT NULL,
			  `status` tinyint(1) NOT NULL,
			  `alertemail` tinyint(1) NOT NULL,
			  `extras`  varchar(512) NOT NULL,
			  `date_added` datetime NOT NULL,
			  PRIMARY KEY (`deliveryagent_id`))
			  ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
       		$this->db->query($sql);
        }

        //$this->db->query("DROP TABLE `". DB_PREFIX ."deliveryagent_transaction`");
	    if ($this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."deliveryagent_transaction'")->num_rows == 0) {
            $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "deliveryagent_transaction` (
    		  `deliveryagent_transaction_id` int(11) NOT NULL AUTO_INCREMENT,
    		  `deliveryagent_id` int(11) NOT NULL,
    		  `name` varchar(128) NOT NULL,
			  `order_id` int(11) NOT NULL,
			  `sort_order` int(11) NOT NULL,
			  `deliverydate` DATE NOT NULL,
			  `deliverytime` varchar(128) NOT NULL,
			  `completed` tinyint(1) NOT NULL,
			  `timeslot_id` int(11) NOT NULL,
			  `customer_id` int(11) NOT NULL,
			  `customer_email`   varchar(255) NOT NULL,
			  `commission` float(11) NOT NULL,
			  `sub_total`   float(11) NOT NULL,
			  `calculationtext` text NOT NULL,
			  `product` int(11) NOT NULL,
			  `amount` varchar(32) NOT NULL,
			  `date_added` datetime NOT NULL,
			   PRIMARY KEY (`deliveryagent_transaction_id`))
			  ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
       		$this->db->query($sql);
       		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'deliveryagent', `key` = 'deliveryagent_installed', `value` = '1'");
        }

        //$this->db->query("DROP TABLE `". DB_PREFIX ."deliveryagent_order`");
	    if ($this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."deliveryagent_order'")->num_rows == 0) {
            $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "deliveryagent_order` (
					  `order_id` int(11) NOT NULL,
        		      `deliveryagent_id` int(11) NOT NULL)
  					  ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
       			  $this->db->query($sql);
        }

        if ($this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."deliveryagent_timeslots'")->num_rows == 0) {
            $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "deliveryagent_timeslots` (
                      `timeid` int(11) NOT NULL AUTO_INCREMENT,
					  `time` varchar(32) NOT NULL,
					  `notes` varchar(32) NOT NULL,
					  PRIMARY KEY (`timeid`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1";$this->db->query($sql);
       }

        if ($this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."deliveryagent_login'")->num_rows == 0) {
        	$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "deliveryagent_login` (
             `deliveryagent_login_id` int(11) NOT NULL AUTO_INCREMENT,
			  `email` varchar(96) NOT NULL,
			  `ip` varchar(40) NOT NULL,
			  `total` int(4) NOT NULL,
			  `date_added` datetime NOT NULL,
			  `date_modified` datetime NOT NULL,
			  PRIMARY KEY (`deliveryagent_login_id`),
			  KEY `email` (`email`),
			  KEY `ip` (`ip`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1";$this->db->query($sql);
        }	
    }

    
	public function newOrderAssignment($email,$name) {
		$this->load->language('extension/module/deliveryagent');
		$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

		$message = sprintf($this->language->get('text_greetings'), $name) . "\n\n";

		$message .= $this->language->get('text_message'). "\n";
		$link = HTTPS_CATALOG."index.php?route=extension/module/deliveryagent";
		$message .= $link. "\n";

		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($email);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$mail->setSubject($subject);
		$mail->setText($message);
		$mail->send();
	}

	public function addtime($data=array()) {
		//$data['time'] = date("H:i", strtotime($data['time']));
		//$data['notes'] = date("H:i", strtotime($data['notes']));
		$this->db->query("INSERT INTO " . DB_PREFIX . "deliveryagent_timeslots SET time='".$this->db->escape($data['time'])."',notes='".$this->db->escape($data['notes'])."' ");
	}

	public function edittime($data=array(),$timeid) {
		//$data['time'] = date("H:i", strtotime($data['time']));
		//$data['notes'] = date("H:i", strtotime($data['notes']));
		$this->db->query("UPDATE " . DB_PREFIX . "deliveryagent_timeslots SET time='".$this->db->escape($data['time'])."',notes='".$this->db->escape($data['notes'])."' WHERE timeid = '".(int)$timeid."' ");
	}

	public function deleteTime($timeid) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "deliveryagent_timeslots WHERE timeid = '".(int)$timeid."'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "deliveryagent_timeslots_days WHERE timeid = '".(int)$timeid."'");
	}

	public function getTimeSlot($timeid) {
		$query = $this->db->query("SELECT * FROM  " . DB_PREFIX . "deliveryagent_timeslots WHERE timeid = '".(int)$timeid."'");
		return $query->row;
	}

	public function getTimeSlots() {
		$query = $this->db->query("SELECT * FROM `". DB_PREFIX ."deliveryagent_timeslots` WHERE 1");
		return $query->rows;
	}

	public function saveDateTime($timeslot_id = 0,$order_id = 0,$deliveryagent_id = 0,$sort_order = 1) {
		$deliverydate_query = $this->db->query("DELETE FROM " . DB_PREFIX . "deliveryagent_transaction WHERE order_id = '" . (int)$order_id . "'");

		$deliveryagent_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "deliveryagent WHERE deliveryagent_id = '" . (int)$deliveryagent_id . "'")->row;

		if(isset($deliveryagent_query['deliveryagent_id'])) {
			$deliveryagent_name = $deliveryagent_query['firstname']." ".$deliveryagent_query['lastname'];
		} else {
			$deliveryagent_name = "";
		}	

		$this->load->model("sale/order");
		$order_info = $this->model_sale_order->getOrder($order_id);
		if($order_info) {
			
			$commission = $amount = strtolower($deliveryagent_query['commission']);
			$total = $order_info['total'];

			if(strpos($commission, "p") !== false) {
				$commissionvalue = str_replace("p", "", $commission);
				$amount =  ($total * $commissionvalue)/100.00;
				$calculationtext = $commissionvalue." Percent";
			} else {
				$amount = $commission;
				$calculationtext = $commission." Fixed";
			}

			$this->db->query("INSERT INTO " . DB_PREFIX . "deliveryagent_transaction SET deliveryagent_id = '" . (int)$deliveryagent_id . "', name = '".$this->db->escape($deliveryagent_name)."', order_id = '" . (int)$order_id . "', customer_id = '".(int)$order_info['customer_id']."', customer_email = '".$this->db->escape($order_info['email'])."', commission = '".$this->db->escape($commission)."', calculationtext = '".$calculationtext."', sort_order = '".(int)$sort_order."', amount = '".(float)$amount."', date_added = NOW()");

			if(isset($timeslot_id) && $timeslot_id != "*") {
				$deliverytime_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "deliveryagent_timeslots WHERE timeid = '".(int)$timeslot_id."'");


				if($deliverytime_query->num_rows) {
					$time = $deliverytime_query->row['time'];
					$notes = $deliverytime_query->row['notes'];

					$this->db->query("UPDATE " . DB_PREFIX . "deliveryagent_transaction SET deliverytime = '".$time.' - '.$notes."', timeslot_id = '".(int)$timeslot_id."' WHERE order_id = '" . (int)$order_id . "'");
				}
			} else {
				$this->db->query("UPDATE " . DB_PREFIX . "deliveryagent_transaction SET deliverytime = '', timeslot_id = '' WHERE order_id = '" . (int)$order_id . "'");
			}

			if(isset($deliveryagent_query['email']) && $deliveryagent_query['alertemail']) {
				$this->newOrderAssignment($deliveryagent_query['email'],$deliveryagent_name);
			}
		}	
	}

	public function getOrders($data = array()) {
		$sql = "SELECT od.sort_order,od.deliverydate,od.deliveryagent_id,od.deliverytime,od.timeslot_id,o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status, o.shipping_code, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" . DB_PREFIX . "order` o";

		$sql .= " LEFT JOIN `" . DB_PREFIX . "deliveryagent_transaction` od ON (o.order_id = od.order_id) ";

		if (!empty($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} elseif (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) >= DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_added) <= DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
		}

		$sort_data = array(
			'o.order_id',
			'customer',
			'order_status',
			'od.sort_order',
			'o.date_modified',
			'o.total'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
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


	public function getTotalOrders($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o";

		$sql .= " LEFT JOIN `" . DB_PREFIX . "deliveryagent_transaction` od ON (o.order_id = od.order_id) ";

		if (!empty($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} elseif (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(od.deliverydate) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(od.deliverydate) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTransactions($data) {
		
		$sql  = "SELECT st.name,st.calculationtext,st.customer_email,st.commission,st.product,st.amount,st.order_id,o.firstname,o.lastname,o.total,st.sub_total,o.currency_code,o.currency_value, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status FROM `" . DB_PREFIX . "deliveryagent_transaction` st LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = st.order_id) LEFT JOIN `" . DB_PREFIX . "deliveryagent` s ON (s.deliveryagent_id = st.deliveryagent_id) WHERE 1 = 1 ";

		if (!empty($data['filter_deliveryagent'])) {
			$sql .= " AND st.deliveryagent_id = '" . (int)$data['filter_deliveryagent'] . "'";
		}

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$sql .= " ORDER BY o.date_added DESC";

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

	public function getTotalTransactions($data) {
			
		$sql  = "SELECT COUNT(*) as totalnumber FROM `" . DB_PREFIX . "deliveryagent_transaction` st LEFT JOIN `" . DB_PREFIX . "order` o  ON (o.order_id = st.order_id) WHERE 1 = 1";
		
		if (!empty($data['filter_deliveryagent'])) {
			$sql .= " AND st.deliveryagent_id = '" . (int)$data['filter_deliveryagent'] . "'";
		}

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$query = $this->db->query($sql);
		
		return $query->row['totalnumber'];
	}

	public function getExactTotal($data) {

		$returndata = array("commissiontotal"=>"","ordertotal"=>"");
		$returndata2 = array();

		$sql  = "SELECT DISTINCT o.currency_code,o.currency_value FROM `" . DB_PREFIX . "deliveryagent_transaction` st LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = st.order_id) LEFT JOIN `" . DB_PREFIX . "deliveryagent` s ON (s.deliveryagent_id = st.deliveryagent_id) WHERE 1 = 1 ";
		
		if (!empty($data['filter_deliveryagent'])) {
			$sql .= " AND st.deliveryagent_id = '" . (int)$data['filter_deliveryagent'] . "'";
		}

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		
		$query = $this->db->query($sql);

		foreach ($query->rows as $key => $value) {

			$sql  = "SELECT o.order_id,o.currency_code,o.currency_value,SUM(st.amount) as commissiontotal FROM `" . DB_PREFIX . "deliveryagent_transaction` st LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = st.order_id) LEFT JOIN `" . DB_PREFIX . "deliveryagent` s ON (s.deliveryagent_id = st.deliveryagent_id) WHERE 1 = 1 ";

			if (!empty($data['filter_deliveryagent'])) {
				$sql .= " AND st.deliveryagent_id = '" . (int)$data['filter_deliveryagent'] . "'";
			} 

			$sql .= " AND o.currency_code = '".$value['currency_code']."' ";

			if (!empty($data['filter_order_status_id'])) {
				$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
			} else {
				$sql .= " AND o.order_status_id > '0'";
			}

			if (!empty($data['filter_date_start'])) {
				$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
			}

			if (!empty($data['filter_date_end'])) {
				$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
			}
			
			$query1 = $this->db->query($sql);
			
			if($query1->num_rows) {
				$query1->row['commissiontotal'] = $this->currency->format($query1->row['commissiontotal'],$value['currency_code'],$value['currency_value']);
				$returndata2[$key] = $query1->row;
			}
		}

		
		foreach ($query->rows as $key => $value) {
			$sql  = "SELECT DISTINCT o.order_id,o.total FROM `" . DB_PREFIX . "deliveryagent_transaction` st LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = st.order_id) LEFT JOIN `" . DB_PREFIX . "deliveryagent` s ON (s.deliveryagent_id = st.deliveryagent_id) WHERE 1 = 1 ";

			if (!empty($data['filter_deliveryagent'])) {
				$sql .= " AND st.deliveryagent_id = '" . (int)$data['filter_deliveryagent'] . "'";
			} 

			$sql .= " AND o.currency_code = '".$value['currency_code']."' ";

			if (!empty($data['filter_order_status_id'])) {
				$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
			} else {
				$sql .= " AND o.order_status_id > '0'";
			}

			if (!empty($data['filter_date_start'])) {
				$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
			}

			if (!empty($data['filter_date_end'])) {
				$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
			}
			
			$query2 = $this->db->query($sql);
			$total = 0;
			foreach($query2->rows as $key2 => $value2) {
				$total += $value2['total']; 
			}

			$returndata2[$key]['ordertotal'] = $this->currency->format($total,$value['currency_code'],$value['currency_value']);
		}


	
		
		$i = 0;$formating = "";
		foreach ($returndata2 as $key => $value) {
			if($i) {
				$formating = " + ";
			}
			$returndata['commissiontotal'] .= $formating . $value['commissiontotal'];
			$returndata['ordertotal'] .= $formating . $value['ordertotal'];
			$i = 1;
		}
		return $returndata;
	}

	public function getProductCount($order_id) {
		$query = $this->db->query("SELECT SUM(quantity) as total FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");
		return $query->row['total'];
	}
}