<?php
class ModelPOMgmtPOEmail extends Model {

	private $data;

	public function getMailTemplate($id = false){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "po_mails WHERE `id` = '".(int)$id."' ")->row;
		return $query;
	}

	public function deletEmailTemplate($id){
		$this->db->query("DELETE FROM " . DB_PREFIX . "po_mails WHERE `id` = '".(int)$id."' ");
	}

	public function gettotal(){

		$sql ="SELECT * FROM " . DB_PREFIX . "po_mails WHERE 1 ";

		$result = $this->db->query($sql);

		return $result->rows;
	}

	public function getPOEmailList($data = array()){
		$sql ="SELECT * FROM " . DB_PREFIX . "po_mails WHERE 1 ";

		if (!empty($data['filter_id'])) {
			$sql .= " AND id = '" . (float)$this->db->escape($data['filter_id']) . "'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND LCASE(name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
		}

		if (!empty($data['filter_subject'])) {
			$sql .= " AND LCASE(subject) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_subject'])) . "%'";
		}

		if (!empty($data['filter_message'])) {
			$sql .= " AND LCASE(message) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_message'])) . "%'";
		}

		$sort_data = array(
			'id',
			'name',
			'subject',
			'message',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY id";
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

		$result=$this->db->query($sql);

		return $result->rows;
	}

	public function getPOEmailTotal($data = array()){
		$sql ="SELECT * FROM " . DB_PREFIX . "po_mails WHERE 1 ";

		if (!empty($data['filter_id'])) {
			$sql .= " AND id = '" . (float)$this->db->escape($data['filter_id']) . "'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND LCASE(name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
		}

		if (!empty($data['filter_subject'])) {
			$sql .= " AND LCASE(subject) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_subject'])) . "%'";
		}

		if (!empty($data['filter_message'])) {
			$sql .= " AND LCASE(message) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_message'])) . "%'";
		}

		$result = $this->db->query($sql);

		return count($result->rows);
	}

	public function addEmailTemplate($data = array()) {
  		if($data['mail_id']){
          $this->db->query("UPDATE " . DB_PREFIX . "po_mails SET `name` = '" . $this->db->escape($data['name']) . "', `message` = '" . $this->db->escape($data['message']) . "', `subject` = '" . $this->db->escape($data['subject']) . "' WHERE id='".(int)$data['mail_id']."'");
      }else{
          $this->db->query("INSERT INTO " . DB_PREFIX . "po_mails SET `name` = '" . $this->db->escape($data['name']) . "', `message` = '" . $this->db->escape($data['message']) . "', `subject` = '" . $this->db->escape($data['subject']) . "'");
      }
	}

	public function sendEmail($data, $value_index = array()){
		$mail_id 		= $data['templateId'];
		$mail_from 	= $data['email_from'];

		$mail_details = $this->getMailTemplate($mail_id);

		if($mail_details){
			$this->load->model('po_mgmt/po_supplier');
			$supplier_info = $this->model_po_mgmt_po_supplier->getSupplier($data['supplier_id']);
			$supplier_details = array(
						'supplier_name'         => $supplier_info['owner_name'],
						'supplier_company_name' => $supplier_info['company_name'],
						'supplier_email'        => $supplier_info['email'],
						'supplier_website'      => $supplier_info['website'],
						'supplier_telephone'    => $supplier_info['telephone'],
						'supplier_fax'          => $supplier_info['fax'],
						'supplier_address'      => $supplier_info['address'],						
						'shipping_address'      => $supplier_info['address'].', '.$supplier_info['postcode'],
			);
			if(!isset($data['email_to']) && !empty($supplier_details)){
					$mail_to 									= $supplier_details['supplier_email'];
			}else{
					$mail_to 									= $data['email_to'];
			}
			$data['store_name'] 			= $this->config->get('config_name');
			$data['store_url'] 				= HTTP_SERVER;
			if($this->config->get('config_logo')){
				$config_logo 						= $this->config->get('config_logo');
			}else{
				$config_logo 						= 'no_image.png';
			}
			$data['logo'] 						= HTTP_SERVER.'image/' . $config_logo;
			if($this->config->get('config_image')){
				$config_image 					= $this->config->get('config_image');
			}else{
				$config_image 					= 'no_image.png';
			}

			$find = array(
				'{quotation_id}',
				'{quotation_details}',
				'{comment}',
				'{new_product}',
				'{update_quotation}',
				'{supplier_name}',
				'{supplier_company_name}',
				'{supplier_email}',
				'{supplier_website}',
				'{supplier_telephone}',
				'{supplier_fax}',
				'{supplier_address}',
				'{shipping_address}',
				'{order_source}',
				'{config_logo}',
				'{config_icon}',
				'{config_currency}',
				'{config_image}',
				'{config_name}',
				'{config_owner}',
				'{config_address}',
				'{config_geocode}',
				'{config_email}',
				'{config_telephone}',
				);

			$replace = array(
				'quotation_id'				=> '#' . $data['quotation_id'],
				'quotation_details'		=> $this->quotation_temp($data['quotation_id']),
				'comment'							=> isset($value_index['comment']) ? $value_index['comment'] : '',
				'new_product'					=> isset($value_index['new_product']) ? $value_index['new_product'] : '',
				'update_quotation'		=> isset($value_index['update_quotation']) ? $value_index['update_quotation'] : '',
				'supplier_name'				=> isset($supplier_details['supplier_name']) ? $supplier_details['supplier_name'] : '',
				'supplier_company_name'	=> isset($supplier_details['supplier_company_name']) ? $supplier_details['supplier_company_name'] : '',
				'supplier_email'			=> isset($supplier_details['supplier_email']) ? $supplier_details['supplier_email'] : '',
				'supplier_website'		=> isset($supplier_details['supplier_website']) ? $supplier_details['supplier_website'] : '',
				'supplier_telephone'	=> isset($supplier_details['supplier_telephone']) ? $supplier_details['supplier_telephone'] : '',
				'supplier_fax'				=> isset($supplier_details['supplier_fax']) ? $supplier_details['supplier_fax'] : '',
				'supplier_address'		=> isset($supplier_details['supplier_address']) ? $supplier_details['supplier_address'] : '',
				'shipping_address'		=> isset($supplier_details['shipping_address']) ? $supplier_details['shipping_address'] : '',
				'order_source'				=> isset($value_index['order_source']) ? $value_index['order_source'] : '',
				'config_logo' 				=> '<a href="'.HTTP_SERVER.'" title="'.$data['store_name'].'"><img src="'.HTTP_CATALOG.'image/' . $config_logo.'" alt="'.$data['store_name'].'" /></a>',
				'config_icon' 				=> '<img src="'.HTTP_CATALOG.'image/' . $this->config->get('config_icon').'" width="'.$this->config->get('theme_default_image_location_width').'" height="'.$this->config->get('theme_default_image_location_height').'">',
				'config_currency' 		=> $this->config->get('config_currency'),
				'config_image' 				=> '<img src="'.HTTP_CATALOG.'image/' . $config_image.'" width="'.$this->config->get('theme_default_image_location_width').'" height="'.$this->config->get('theme_default_image_location_height').'">',
				'config_name' 				=> $this->config->get('config_name'),
				'config_owner' 				=> $this->config->get('config_owner'),
				'config_address' 			=> $this->config->get('config_address'),
				'config_geocode' 			=> $this->config->get('config_geocode'),
				'config_email' 				=> $this->config->get('module_oc_pom_email_id') ? $this->config->get('module_oc_pom_email_id') : $this->config->get('config_email'),
				'config_telephone' 		=> $this->config->get('config_telephone'),
			);
			$value_index 	= array_merge($value_index, $supplier_details);
			$replace 			= array_merge($replace,$value_index);

			if(isset($value_index['seller_comment_combine']) && $value_index['seller_comment_combine']){
					$mail_details['message'] = $mail_details['message'].'<br><p>Quotation Comment: {comment}';
			}
			$mail_details['message'] = trim(str_replace($find, $replace, $mail_details['message']));

			$data['subject'] = $mail_details['subject'];
			$data['message'] = $mail_details['message'];
			$html = $this->load->view('po_mgmt/po_template_view', $data);
			$html = html_entity_decode($html);

			if (preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $mail_to) AND preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $mail_from) ) {
					$mail = new Mail($this->config->get('config_mail_engine'));
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
					$mail->setTo($mail_to);
					$mail->setFrom($mail_from);
					$mail->setSender(html_entity_decode($data['store_name'], ENT_QUOTES, 'UTF-8'));
					$mail->setSubject($mail_details['subject']);
					$mail->setHtml($html);
					$mail->setText(strip_tags($html));
					$mail->send();
			}
		}
	}

	public function quotation_temp($quotation_id = false){
		$templateFormat 	= '';
		$sub_total 				= $grand_total 			= $tax_total = 0;
		$quotation_status = array('1' => 'Pending','2' => 'Convert To PO','3' => 'Confirm Order', '4' => 'Completed','5' => 'Cancel','6' => 'Cancel');
			if($quotation_id){
					$this->load->language('po_mgmt/po_quotation');
					$this->load->model('po_mgmt/po_quotation');
					$quotation_info = $this->model_po_mgmt_po_quotation->getQuotation($quotation_id);

					if(!empty($quotation_info)){
							$getQuoatationProducts = $this->model_po_mgmt_po_quotation->getQuotationProducts($quotation_id);
							$templateFormat .= '<style>
							.quote_table_div{
									background-color: #3c3c3c;border: 1px solid #293138;display: block;margin: 0 auto;overflow: hidden;padding: 0px;width: 60%;font-family:Times New Roman;
							}
							.quote_table_head_section{
									background-color: #293138;color: #fff;display: block;font-size: 20px;font-weight: 600;padding: 10px;
							}
							.quote_table_head_section > span{
									display: inline-block;padding: 5px;width: 50%;
							}
							.quote_details{
								color: #ffffff;display: block;font-size: 16px;padding: 10px 20px;
							}
							.quote_shipping{
								background-color: #00ADDF;display: inline-block;height: 200px;padding: 20px;width: 40%;float:left;
							}
							.quote_shipping .quote_shipping_head{
								background-color: #0095c1;font-size: 18px;font-weight: 600;padding: 15px 10px;text-align: center;
							}
							.quote_details > p{
								font-size: 16px;text-align: left;
							}
							.quote_details span{
								float: right;font-weight: 400;text-align: right;
							}
							.quote_payment{
								background-color: #fd5f51;display: inline-block;float: right;height: 200px;padding: 20px;width: 40%;
							}
							.quote_payment .quote_payment_head{
								background-color: #da4d40;font-size: 18px;font-weight: 600;padding: 15px 10px;text-align: center;
							}
							.quote_product_section{
								display: block;margin-top: 20px;overflow: hidden;padding: 20px 0;width:100%;
							}
							.quote_product_section .quote_item_section{
								background-color: #00ad97;font-size: 18px;font-weight: 600;padding: 15px 10px;text-align: center;width: 98%;
							}
							.quote_table_div table{
								background-color: #00c4ab;border: 1px solid #66d2b7;clear: both;padding: 20px;width: 100%;
							}
							.quote_table_div table .heading{
									border: 1px solid #fff;padding: 10px;
							}
							.quote_table_div table .quote_td{
								border-bottom: 1px solid #fff;padding: 10px;text-align: center;
							}
							.quote_table_div table .quote_td_total{
								text-align: left;
							}
							</style>';
							$templateFormat .= '<div class="quote_table_div">';
							$templateFormat .= '<div class="quote_table_head_section"><span><b>'.$this->language->get('column_quotation_id').':</b>  #'.$quotation_info['id'].'</span><span style="width: 45%;text-align:right;"><b>'.$this->language->get('column_created_date').':</b>  '.$quotation_info['created_date'].'</span></div>';
							$templateFormat .= '<div class="quote_details">';
						    $templateFormat .= '<div class="quote_shipping">';
						        $templateFormat .= '<div class="quote_shipping_head">Shipping Detail</div>';
						        $templateFormat .= '<p>Shipment BY: <span> '.$quotation_info['shipping_method'].' </span></p><p>Shipment Start Date: <span> '.$quotation_info['shipping_start_date'].' </span></p><p>Expected Delivery Date: <span> '.$quotation_info['shipping_expected_date'].' </span></p><p>Shipping Cost: <span> '.$this->currency->format($quotation_info['shipping_cost'], $this->config->get('config_currency')).' </span></p></div>';
						    $templateFormat .= '<div class="quote_payment">';
						      $templateFormat .= '<div class="quote_payment_head">Other Information</div>';
						      $templateFormat .= '<p>Payment Mode: <span> '.$quotation_info['payment_term'].' </span></p><p>Order placed via: <span> '.ucfirst($quotation_info['order_source']).' </span></p><p>Quotation Status: <span> '.$quotation_status[$quotation_info['quotation_status']].' </span></p></div>';

							$templateFormat .= '<div class="quote_product_section"><div class="quote_item_section">Quotation Items</div>';
							$templateFormat .= '<table>';
							$templateFormat .= '<thead><tr>';
							$templateFormat .= '<th class="heading">'.$this->language->get('column_product_name').'</th>';
							$templateFormat .= '<th class="heading">'.$this->language->get('column_supplier_sku').'</th>';
							$templateFormat .= '<th class="heading">'.$this->language->get('column_supplier_cost').'</th>';
							$templateFormat .= '<th class="heading">'.$this->language->get('column_supplier_qty').'</th>';
							$templateFormat .= '<th class="heading">'.$this->language->get('column_received_qty').'</th>';
							$templateFormat .= '<th class="heading">'.$this->language->get('column_supplier_tax').'</th>';
							$templateFormat .= '<th class="heading">'.$this->language->get('column_sub_total').'</th>';
							$templateFormat .= '</tr></thead>';
							$templateFormat .= '<tbody>';
										if(!empty($getQuoatationProducts)){
												$this->load->model('tool/upload');
												foreach ($getQuoatationProducts as $key => $quote_product) {
														$option_data = array();
														$options = $this->model_po_mgmt_po_quotation->getQuotationOptions($quote_product['quotation_id'], $quote_product['id']);

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
																'name'  => $option['name'],
																'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
															);
														}
														$sub_total  		 = $sub_total + $quote_product['sub_total'];
														$tax_total  		 += ($quote_product['sub_total'] * $quote_product['tax']) / 100;
														$templateFormat .= '<tr>';
														$templateFormat .= '<td class="quote_td">'.$quote_product['name'];
														if(!empty($option_data)){
																foreach ($option_data as $option) {
																	$templateFormat .= '<br />';
																	$templateFormat .= '&nbsp;<small> -'. $option['name'] .':'. $option['value'].' </small>';
																}
														}
														$templateFormat .= '</td>';
														$templateFormat .= '<td class="quote_td">'.$quote_product['supplier_sku'].'</td>';
														$templateFormat .= '<td class="quote_td">'.$quote_product['ordered_cost'].'</td>';
														$templateFormat .= '<td class="quote_td">'.$quote_product['ordered_qty'].'</td>';
														$templateFormat .= '<td class="quote_td">'.$quote_product['received_qty'].'</td>';
														$templateFormat .= '<td class="quote_td">'.$quote_product['tax'].'</td>';
														$templateFormat .= '<td class="quote_td">'.$quote_product['sub_total'].'</td>';
														$templateFormat .= '</tr>';
												}
												$grand_total		 = $sub_total + $quotation_info['shipping_cost'] + $tax_total;
												$templateFormat .= '<tr><td></td><td></td><td></td><td></td><td></td><th class="quote_td quote_td_total">'.$this->language->get('column_sub_total').'</th><td class="quote_td">'.$this->currency->format($sub_total, $this->config->get('config_currency')).'</td></tr>';

												$templateFormat .= '<tr><td></td><td></td><td></td><td></td><td></td><th class="quote_td quote_td_total">'.$this->language->get('text_shipping_cost').'</th><td class="quote_td">'.$this->currency->format($quotation_info['shipping_cost'], $this->config->get('config_currency')).'</td></tr>';

												$templateFormat .= '<tr><td></td><td></td><td></td><td></td><td></td><th class="quote_td quote_td_total">'.$this->language->get('text_tax_per').'</th><td class="quote_td">'.$this->currency->format($tax_total, $this->config->get('config_currency')).'</td></tr>';

												$templateFormat .= '<tr><td></td><td></td><td></td><td></td><td></td><th class="quote_td quote_td_total">'.$this->language->get('text_grand_total').'</th><th class="quote_td">'.$this->currency->format($grand_total, $this->config->get('config_currency')).'</th></tr>';
										}
							$templateFormat .= '</tbody>';
							$templateFormat .= '</table></div>';
							$templateFormat .= '</div>';
							$templateFormat .= '</div>';
					}
			}
			return $templateFormat;
	}
}
?>
