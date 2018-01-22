<?php
class ModelSaleOrderProduct extends Model {
    public function getOrderProduct($pr_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product op WHERE order_product_id = '".(int)$pr_id."'");
		return $query->row;
	}  
	public function getOrder($order_product_id) {
		$query = $this->db->query("SELECT o.*,op.product_id FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id=o.order_id) WHERE order_product_id = '".(int)$order_product_id."' LIMIT 1");
	    return $query->row;
	}  
	public function getOrderProducts($data = array()) {
		$sql= "SELECT o.date_added,o.currency_code,o.currency_value,op.*,(SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = (CASE WHEN op.order_status_id=0 THEN o.order_status_id ELSE op.order_status_id END) AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON(op.order_id=o.order_id)";
        if (isset($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "(CASE WHEN op.order_status_id=0 THEN o.order_status_id ELSE op.order_status_id END) = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			} else {

			}
		} else {
			$sql .= " WHERE (CASE WHEN op.order_status_id=0 THEN o.order_status_id ELSE op.order_status_id END) > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND op.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND op.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND (op.total+op.tax*op.quantity) = '" . (float)$data['filter_total'] . "'";
		}

		$sort_data = array(
			'op.order_id',
			'op.name',
			'op.model',
			'op.quantity',
			'op.price',
			'op.tax',
			'op.total',
			'status',
			'o.date_added'
			
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if($data['sort']=='op.total'){
				$sql .= " ORDER BY op.total + (op.tax*op.quantity)";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
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

	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}



	public function getTotalOrdersProducts($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id=op.order_id)";
        if (isset($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "(CASE WHEN op.order_status_id=0 THEN o.order_status_id ELSE op.order_status_id END)  = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			} else {

			}
		} else {
			$sql .= " WHERE (CASE WHEN op.order_status_id=0 THEN o.order_status_id ELSE op.order_status_id END) > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND op.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND op.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND (op.total+op.tax*op.quantity) = '" . (float)$data['filter_total'] . "'";
		}
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	public function addOrderHistory($order_product_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET order_status_id = '" . (int)$data['change_status'] . "' WHERE order_product_id = '" . (int)$order_product_id . "'");
		
		
		if (isset($data['notify'])) {
		
		    $order_info = $this->getOrder($order_product_id);
			$order_id = $order_info['order_id'];
			$product_id = $order_info['product_id'];
			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($order_info['language_id']);

			if ($language_info) {
				$order_info['language_directory'] = $language_info['directory'];
			} else {

				$order_info['language_directory'] = '';
			}
			$language = new Language($order_info['language_directory']);
			$language->load('default');
			$language->load('mail/order');

			$subject = sprintf($language->get('text_subject'), $order_info['store_name'], $order_id);

			$message  = $language->get('text_order') . ' ' . $order_id . "\n";
			$message .= $language->get('text_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";

			$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$data['change_status'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

			if ($order_status_query->num_rows) {
				$message .= $language->get('text_order_status') . "\n";
				$message .= $order_status_query->row['name'] . "\n\n";
			}
			
			$message .= $language->get('text_link2') . "\n";
			$message .= html_entity_decode($order_info['store_url'] . 'index.php?route=product/product&product_id=' . $product_id, ENT_QUOTES, 'UTF-8') . "\n\n";
			
			if ($order_info['customer_id']) {				
				$message .= $language->get('text_link') . "\n";
				$message .= html_entity_decode($order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id, ENT_QUOTES, 'UTF-8') . "\n\n";
			}
			
			$message .= $language->get('text_footer');

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			$mail->setTo($order_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($order_info['store_name']);
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}

	}

}