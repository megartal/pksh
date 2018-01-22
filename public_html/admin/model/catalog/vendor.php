<?php
class ModelCatalogVendor extends Model {
	public function addVendor($data) {
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "vendor SET  email = '" . $this->db->escape($data['email']) . "', zone = '" . $this->db->escape($data['zone']) . "', type = '" . $this->db->escape($data['type']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', address= '" . $this->db->escape($data['address']) . "', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "',  sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "',  product_status = '" . (int)$data['product_status'] . "',date_modified = NOW(), date_added = NOW()");

		$vendor_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "vendor SET image = '" . $this->db->escape($data['image']) . "' WHERE vendor_id = '" . (int)$vendor_id . "'");
		}

		foreach ($data['vendor_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_description SET vendor_id = '" . (int)$vendor_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		
		}
		
		if (isset($data['vendor_layout'])) {
			foreach ($data['vendor_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_to_layout SET vendor_id = '" . (int)$vendor_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		if (isset($data['vendor_seo_url'])) {
			foreach ($data['vendor_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'vendor_id=" . (int)$vendor_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
        if (!is_dir('../image/catalog/'.$vendor_id)) {
			mkdir('../image/catalog/'.$vendor_id, 0777);
		}
		$this->cache->delete('vendor');

		return $vendor_id;
	}

	public function editVendor($vendor_id, $data) {
		
		$this->db->query("UPDATE " . DB_PREFIX . "vendor  SET  email = '" . $this->db->escape($data['email']) . "', zone = '" . $this->db->escape($data['zone']) . "', type = '" . $this->db->escape($data['type']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', address= '" . $this->db->escape($data['address']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "',  product_status = '" . (int)$data['product_status'] . "',date_modified = NOW() WHERE vendor_id = '" . (int)$vendor_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "vendor SET image = '" . $this->db->escape($data['image']) . "' WHERE vendor_id = '" . (int)$vendor_id . "'");
		}
        if ($data['password']) {
			$this->db->query("UPDATE " . DB_PREFIX . "vendor SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE vendor_id = '" . (int)$vendor_id . "'");
		}
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_description WHERE vendor_id = '" . (int)$vendor_id . "'");

		foreach ($data['vendor_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_description SET vendor_id = '" . (int)$vendor_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_to_layout WHERE vendor_id = '" . (int)$vendor_id . "'");

		if (isset($data['vendor_layout'])) {
			foreach ($data['vendor_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_to_layout SET vendor_id = '" . (int)$vendor_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		// SEO URL
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'vendor_id=" . (int)$vendor_id . "'");

		if (isset($data['vendor_seo_url'])) {
			foreach ($data['vendor_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'vendor_id=" . (int)$vendor_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

		$this->cache->delete('vendor');

		
	}

	public function deleteVendor($vendor_id) {		
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor WHERE vendor_id = '" . (int)$vendor_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_description WHERE vendor_id = '" . (int)$vendor_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_to_layout WHERE vendor_id = '" . (int)$vendor_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'category_id=" . (int)$category_id . "'");


		$this->cache->delete('vendor');
	}

	public function getVendor($vendor_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "vendor v LEFT JOIN " . DB_PREFIX . "vendor_description vd ON (v.vendor_id = vd.vendor_id) WHERE v.vendor_id = '" . (int)$vendor_id . "' AND vd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getVendors($data = array()) {
		$sql = "SELECT v.vendor_id AS vendor_id,vd.name AS name, v.sort_order as sort_order FROM  " . DB_PREFIX . "vendor v  LEFT JOIN " . DB_PREFIX . "vendor_description vd ON (vd.vendor_id = v.vendor_id)  WHERE vd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND vd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY v.vendor_id";

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
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

	public function getVendorDescriptions($vendor_id) {
		$vendor_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_description WHERE vendor_id = '" . (int)$vendor_id . "'");

		foreach ($query->rows as $result) {
			$vendor_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description']
			);
		}

		return $vendor_description_data;
	}

	public function getTotalVendors() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor");

		return $query->row['total'];
	}
	
	public function getVendorByEmail($email) {
		$query = $this->db->query("SELECT *  FROM `" . DB_PREFIX . "vendor` WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}
	
	public function getVendorLayouts($vendor_id) {
		$vendor_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_to_layout WHERE vendor_id = '" . (int)$vendor_id . "'");

		foreach ($query->rows as $result) {
			$vendor_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $vendor_layout_data;
	}
	
	public function getTotalVendorsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}

	public function getVendorSeoUrls($vendor_id) {
		$vendor_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'vendor_id=" . (int)$vendor_id . "'");

		foreach ($query->rows as $result) {
			$vendor_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $vendor_seo_url_data;
	}
}