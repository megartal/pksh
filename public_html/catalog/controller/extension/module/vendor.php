<?php
class ControllerExtensionModuleVendor extends Controller {
	public function index() {
		$this->load->language('module/vendor');

		$data['heading_title'] = $this->language->get('heading_title');

		if (isset($this->request->get['vendor_id'])) {
			$data['vendor_id'] = $this->request->get['vendor_id'];
		} else {
			$data['vendor_id'] = 0;
		}

		$this->load->model('catalog/vendor');

		$data['vendors'] = array();

		$vendors = $this->model_catalog_vendor->getVendors();

		foreach ($vendors as $vendor) {
			$filter_data = array(
				'filter_vendor_id'  => $vendor['vendor_id']
			);

			$data['vendors'][] = array(
				'vendor_id'   => $vendor['vendor_id'],
				'name'        => $vendor['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_vendor->getTotalProducts($filter_data) . ')' : ''),
				'href'        => $this->url->link('product/vendor', 'vendor_id=' . $vendor['vendor_id'])
			);
		}
		return $this->load->view('extension/module/vendor', $data);
	}
}