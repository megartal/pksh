<?php
class ModelUserUser extends Model {
	
	public function editPassword($user_id, $password) {
		$this->db->query("UPDATE `" . DB_PREFIX . "vendor` SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "', code = '' WHERE vendor_id = '" . (int)$user_id . "'");
	}

	public function editCode($email, $code) {
		$this->db->query("UPDATE `" . DB_PREFIX . "vendor` SET code = '" . $this->db->escape($code) . "' WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	
	public function getUser($user_id) {
		$query = $this->db->query("SELECT *  FROM `" . DB_PREFIX . "vendor` v WHERE v.vendor_id = '" . (int)$user_id . "'");
        
		return  array(
			'firstname'  => $query->row['email'],
			'lastname'   => '',
			'username'   => '',
			'user_group' => '',
			'image'      => $query->row['image']
		);
	 
	}
	public function getUserByCode($code) {
		$query = $this->db->query("SELECT vendor_id as user_id FROM `" . DB_PREFIX . "vendor` WHERE code = '" . $this->db->escape($code) . "' AND code != ''");

		return $query->row;
	}
	public function getTotalUsersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "vendor` WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}
}