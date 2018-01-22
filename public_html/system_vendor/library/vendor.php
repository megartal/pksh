<?php
class Vendor {
	private $user_id;
	private $username;
	private $product_status;
	private $permission = array();

	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['vendor_id'])) {
			$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor WHERE vendor_id = '" . (int)$this->session->data['vendor_id'] . "' AND status = '1'");

			if ($user_query->num_rows) {
				$this->user_id = $user_query->row['vendor_id'];
				$this->username = $user_query->row['email'];
				$this->product_status = $user_query->row['product_status'];
				$access_permisions = array(					
					'catalog/attribute',
					'catalog/category',
					'catalog/download',					
					'catalog/filter',					
					'catalog/manufacturer',
					'catalog/option',
					'catalog/product',
					'catalog/review',
					'catalog/recurring',
					'catalog/vendor',
					'common/filemanager',
					'sale/order',
					'sale/order_product',
					'sale/return',			
					'report/sale_return',			
					'report/sale_order',			
					'report/sale_tax',			
					'report/sale_shipping',			
					'report/product_viewed',			
					'report/product_purchased'			
				);
				$modify_permisions = array(
					'catalog/product',
					'catalog/review',
					'catalog/vendor',
					'sale/order_product',			
					'sale/return'			
				);
				$this->permission['access'] = $access_permisions;
				$this->permission['modify'] = $modify_permisions;
			} else {
				$this->logout();
			}
		}
	}

	public function login($username, $password) {
		$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor WHERE email = '" . $this->db->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");

		if ($user_query->num_rows) {
			$this->session->data['vendor_id'] = $user_query->row['vendor_id'];

			$this->user_id = $user_query->row['vendor_id'];
			$this->username = $user_query->row['email'];
			$this->product_status = $user_query->row['product_status'];

			$access_permisions = array(					
				'catalog/attribute',
				'catalog/category',
				'catalog/download',					
				'catalog/filter',					
				'catalog/manufacturer',
				'catalog/option',
				'catalog/product',
				'catalog/review',
				'catalog/recurring',
				'catalog/vendor',
				'common/filemanager',
				'sale/order',
				'sale/order_product',
				'sale/return',			
				'report/sale_return',			
				'report/sale_order',			
				'report/sale_tax',			
				'report/sale_shipping',			
				'report/product_viewed',			
				'report/product_purchased'			
			);
			$modify_permisions = array(
				'catalog/product',
				'catalog/review',
				'catalog/vendor',
				'sale/order_product',			
				'sale/return'			
			);
			$this->permission['access'] = $access_permisions;
			$this->permission['modify'] = $modify_permisions;
			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->session->data['vendor_id']);

		$this->user_id = '';
		$this->username = '';
	}

	public function hasPermission($key, $value) {
		if (isset($this->permission[$key])) {
			return in_array($value, $this->permission[$key]);
		} else {
			return false;
		}
	}

	public function isLogged() {
		return $this->user_id;
	}

	public function getId() {
		return $this->user_id;
	}

	public function getUserName() {
		return $this->username;
	}
	
	public function getProductStatus() {
		return $this->product_status;
	}
}