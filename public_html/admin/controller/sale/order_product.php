<?php
class ControllerSaleOrderProduct extends Controller {
	private $error = array();

	public function index() {
	
		$this->load->language('sale/order_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/order_product');
		$this->load->model('tool/upload');

		$this->getList();
	}

	protected function getList() {
	
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}
		
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}
		
		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}
		
		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = null;
		}

		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'op.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}	

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('sale/order_product', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL')
		);

		$data['invoice'] = $this->url->link('sale/order_product/invoice', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['history'] = html_entity_decode($this->url->link('sale/order_product/history', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
		$data['products'] = array();
		
		$filter_data = array(
			'filter_order_id'      => $filter_order_id,
			'filter_name'          => $filter_name,
			'filter_model'         => $filter_model,
			'filter_order_status'  => $filter_order_status,
			'filter_total'         => $filter_total,
			'filter_date_added'    => $filter_date_added,			
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);
		
		$products    = $this->model_sale_order_product->getOrderProducts($filter_data);
		$order_total = $this->model_sale_order_product->getTotalOrdersProducts($filter_data);;
		foreach ($products as $product) {
			$option_data = array();

			$options = $this->model_sale_order_product->getOrderOptions($product['order_id'], $product['order_product_id']);

			foreach ($options as $option) {
				if ($option['type'] != 'file') {
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => $option['value'],
						'type'  => $option['type']
					);
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

					if ($upload_info) {
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $upload_info['name'],
							'type'  => $option['type'],
							'href'  => $this->url->link('tool/upload/download', 'user_token=' . $this->session->data['user_token'] . '&code=' . $upload_info['code'], 'SSL')
						);
					}
				}
			}

			$data['products'][] = array(
				'order_product_id' => $product['order_product_id'],
				'order_id' 		   => $product['order_id'],
				'product_id'       => $product['product_id'],
				'name'    	 	   => $product['name'],
				'model'    		   => $product['model'],
				'status'    	   => $product['status'],
				'option'   		   => $option_data,
				'quantity'		   => $product['quantity'],
				'date_added'	   => $product['date_added'],
				'price'    		   => $this->currency->format($product['price'] , $product['currency_code'], $product['currency_value']),
				'tax'    		   => $this->currency->format($product['tax'] , $product['currency_code'], $product['currency_value']),
				'total'    		   => $this->currency->format($product['total'] +  ($product['tax'] * $product['quantity']), $product['currency_code'], $product['currency_value']),
				'href'     		   => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $product['product_id'], 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_missing'] = $this->language->get('text_missing');

		$data['column_order_id'] = $this->language->get('column_order_id');
		$data['column_customer'] = $this->language->get('column_customer');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_unit_price'] = $this->language->get('column_unit_price');
		$data['column_unit_tax'] = $this->language->get('column_unit_tax');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_date_added'] = $this->language->get('column_date_added');

		$data['entry_order_id'] = $this->language->get('entry_order_id');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_notify'] = $this->language->get('entry_notify');
		
		$data['error_select'] = $this->language->get('error_select');

		$data['button_invoice_print'] = $this->language->get('button_invoice_print');
		$data['button_change_status'] = $this->language->get('button_change_status');
	    $data['button_filter'] = $this->language->get('button_filter');
		

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

	
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order'] = $this->url->link('sale/order_product', 'user_token=' . $this->session->data['user_token'] . '&sort=op.order_id' . $url, 'SSL');
		$data['sort_name'] = $this->url->link('sale/order_product', 'user_token=' . $this->session->data['user_token'] . '&sort=op.name' . $url, 'SSL');
		$data['sort_model'] = $this->url->link('sale/order_product', 'user_token=' . $this->session->data['user_token'] . '&sort=op.model' . $url, 'SSL');
		$data['sort_quantity'] = $this->url->link('sale/order_product', 'user_token=' . $this->session->data['user_token'] . '&sort=op.quantity' . $url, 'SSL');
		$data['sort_unit_price'] = $this->url->link('sale/order_product', 'user_token=' . $this->session->data['user_token'] . '&sort=op.price' . $url, 'SSL');
		$data['sort_unit_tax'] = $this->url->link('sale/order_product', 'user_token=' . $this->session->data['user_token'] . '&sort=op.tax' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('sale/order_product', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, 'SSL');
		$data['sort_total'] = $this->url->link('sale/order_product', 'user_token=' . $this->session->data['user_token'] . '&sort=op.total' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('sale/order_product', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('sale/order_product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id'] = $filter_order_id;
		$data['filter_name'] = $filter_name;
		$data['filter_order_status'] = $filter_order_status;
		$data['filter_total'] = $filter_total;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_model'] = $filter_model;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('sale/order_product', $data));
	}
	public function history() {
		$this->language->load('sale/order_product');
		unset($this->session->data['error']);
		unset($this->session->data['success']);
		$this->load->model('sale/order_product');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
		    if (!isset($this->request->post['selected'])) {
				$this->session->data['error'] = $this->language->get('error_selected');
			}
			if (!$this->user->hasPermission('modify', 'sale/order_product')) {
				$this->session->data['error'] = $this->language->get('error_permission');
			}
			if (!isset($this->session->data['error'])) {
				foreach($this->request->post['selected'] as $order_product_id){
					$this->model_sale_order_product->addOrderHistory($order_product_id, $this->request->post);	
				}
				$this->session->data['success'] = $this->language->get('text_success');
			}
			
			$url = '';

			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			
            if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_order_status'])) {
				$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
			}

			if (isset($this->request->get['filter_total'])) {
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
		    $this->response->redirect($this->url->link('sale/order_product', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
        }
    }
	public function invoice() {
		$this->load->language('sale/order_product');

		$data['title'] = $this->language->get('text_invoice');

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');

		$data['text_invoice'] = $this->language->get('text_invoice');
		$data['text_order_detail'] = $this->language->get('text_order_detail');
		$data['text_order_id'] = $this->language->get('text_order_id');
		$data['text_invoice_no'] = $this->language->get('text_invoice_no');
		$data['text_invoice_date'] = $this->language->get('text_invoice_date');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_telephone'] = $this->language->get('text_telephone');
		$data['text_fax'] = $this->language->get('text_fax');
		$data['text_email'] = $this->language->get('text_email');
		$data['text_website'] = $this->language->get('text_website');
		$data['text_to'] = $this->language->get('text_to');


		$data['column_product'] = $this->language->get('column_product');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_price'] = $this->language->get('column_unit_price');
		$data['column_tax'] = $this->language->get('column_unit_tax');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_comment'] = $this->language->get('column_comment');

		$this->load->model('sale/order_product');

		$this->load->model('setting/setting');

		$data['order'] = array();

		$orders = array();

		if (isset($this->request->post['selected'])) {
			$products_id = $this->request->post['selected'];
		}
		$order_info = $this->model_sale_order_product->getOrder($products_id[0]);
		$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

		if ($store_info) {
			$store_address = $store_info['config_address'];
			$store_email = $store_info['config_email'];
			$store_telephone = $store_info['config_telephone'];
			$store_fax = $store_info['config_fax'];
		} else {
			$store_address = $this->config->get('config_address');
			$store_email = $this->config->get('config_email');
			$store_telephone = $this->config->get('config_telephone');
			$store_fax = $this->config->get('config_fax');
		}
		
		$this->load->model('tool/upload');

		$product_data = array();
		$sub_total = 0;
		$tax       = 0;
		foreach ($products_id as $pr_id) {
			$product = $this->model_sale_order_product->getOrderProduct($pr_id);
			$option_data = array();

			$options = $this->model_sale_order_product->getOrderOptions($product['order_id'], $product['order_product_id']);

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
					'value' => $value
				);
			}
			$product_data[] = array(
				'name'     => $product['name'],
				'model'    => $product['model'],
				'option'   => $option_data,
				'quantity' => $product['quantity'],
				'price'    => $this->currency->format($product['price'] , $order_info['currency_code'], $order_info['currency_value']),
				'tax'      => $this->currency->format($product['tax'] , $order_info['currency_code'], $order_info['currency_value']),
				'total'    => $this->currency->format($product['total'] + ($product['tax'] * $product['quantity']), $order_info['currency_code'], $order_info['currency_value'])
			);
			$sub_total+=$product['total'];
			$tax+=$product['tax']*$product['quantity'];
		}
		
	    $data['subtotal'] = $this->currency->format($sub_total , $order_info['currency_code'], $order_info['currency_value']);
	    $data['tax']      = $this->currency->format($tax , $order_info['currency_code'], $order_info['currency_value']);
	    $data['total']    = $this->currency->format($sub_total+$tax , $order_info['currency_code'], $order_info['currency_value']);
		$data['order'] = array(
			'order_id'           => $order_info['order_id'],
			'store_name'         => $order_info['store_name'],
			'store_url'          => rtrim($order_info['store_url'], '/'),
			'store_address'      => nl2br($store_address),
			'store_email'        => $store_email,
			'store_telephone'    => $store_telephone,
			'store_fax'          => $store_fax,				
			'product'            => $product_data				
		);
		$this->response->setOutput($this->load->view('sale/order_product_invoice', $data));
	}
	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])||isset($this->request->get['filter_model'])) {
			$this->load->model('sale/order_product');
			if (isset($this->request->get['filter_name'])){
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = null;
			}
			if (isset($this->request->get['filter_model'])){
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = null;
			}
			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => 5
			);

			$results = $this->model_sale_order_product->getOrderProducts($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'order_product_id'    => $result['order_product_id'],
					'name'                => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'               => strip_tags(html_entity_decode($result['model'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}