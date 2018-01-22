<?php
class ModelAccountVendor extends Model {
	public function addCustomer($data) {
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "vendor` SET  email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', status = '0', product_status='1',  date_added = NOW()");

		$vendor_id = $this->db->getLastId();
		$languages = $this->getLanguages();
		
		if($data['company']){
			$name = $data['company'];	
		} else {
			$name = $data['email'];		
		}
		if (!is_dir('image/catalog/'.$vendor_id)) {
			mkdir('image/catalog/'.$vendor_id, 0777);
		}
		foreach($languages as $language){
			$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_description SET  vendor_id ='". (int)$vendor_id ."', name = '" . $this->db->escape($name) . "', language_id = '". (int)$language['language_id'] ."'");	
		}
		
		$this->load->language('mail/vendor');

		$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

		$message = sprintf($this->language->get('text_welcome'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')) . "\n\n";
		$message .= $this->language->get('text_approval') . "\n";

		$message .= $this->language->get('text_services') . "\n\n";
		$message .= $this->language->get('text_thanks') . "\n";
		$message .= html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			
		$mail->setTo($data['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$mail->setSubject($subject);
		$mail->setText($message);
		$mail->send();


		return $vendor_id;
	}

	public function getTotalCustomersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}
	public function getLanguages(){
     	$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language`");
		return 	$query->rows;	
	}
	

}
