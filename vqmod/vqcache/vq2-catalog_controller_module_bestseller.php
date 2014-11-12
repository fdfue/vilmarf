<?php
class ControllerModuleBestSeller extends Controller {
	protected function index($setting) {
		$this->language->load('module/bestseller');
$this->language->load('product/pds');
$this->data['pds_sku'] = $this->language->get('pds_sku');
$this->data['pds_upc'] = $this->language->get('pds_upc');
$this->data['pds_location'] = $this->language->get('pds_location');
$this->data['pds_model'] = $this->language->get('pds_model');
$this->data['pds_brand'] = $this->language->get('pds_brand');
$this->data['pds_stock'] = $this->language->get('pds_stock');

		$this->data['heading_title'] = $this->language->get('heading_title');
 $this->data['button_wishlist'] = $this->language->get('button_wishlist');
		$this->data['button_compare'] = $this->language->get('button_compare');
		$this->data['button_details'] = $this->language->get('button_details');
				
				
		$this->data['button_cart'] = $this->language->get('button_cart');
		
		$this->load->model('catalog/product');
		
		$this->load->model('tool/image');

		$this->data['products'] = array();

		$results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);
		
		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
			} else {
				$image = false;
			}
			
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = false;
			}
					
			if ((float)$result['special']) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$special = false;
			}	
			
if ($result['quantity'] <= 0) {
                $rstock = $result['stock_status'];
                } elseif ($this->config->get('config_stock_display')) {
                $rstock = "Stock: " . $result['quantity'];
                } else {
                $rstock = $this->language->get('pds_instock');
                }
			if ($this->config->get('config_review_status')) {
				$rating = $result['rating'];
			} else {
				$rating = false;
			}
							
			$this->data['products'][] = array(
				'product_id' => $result['product_id'],
 'description' =>html_entity_decode($result['description']),
				'description1' =>strip_tags (html_entity_decode($result['description'])),
				
				'thumb'   	 => $image,
				'name'    	 => $result['name'],
//produc display settings
					'sku'         => $result['sku'],
					'model'       => $result['model'],
					'brand'       => $result['manufacturer'],
					'location'    => $result['location'],
					'upc'         => $result['upc'],
					'stock'        => $rstock,
					'brand_url'   => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $result['manufacturer_id']),
					//end pds
				'price'   	 => $price,
				'special' 	 => $special,
				'rating'     => $rating,
				'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
				'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
			);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/bestseller.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/bestseller.tpl';
		} else {
			$this->template = 'default/template/module/bestseller.tpl';
		}

		$this->render();
	}
}
?>