<?php
class ControllerModuleFeatured extends Controller {
	protected function index($setting) {
		$this->language->load('module/featured'); 
$this->language->load('product/pds');
$this->data['pds_sku'] = $this->language->get('pds_sku');
$this->data['pds_upc'] = $this->language->get('pds_upc');
$this->data['pds_location'] = $this->language->get('pds_location');
$this->data['pds_model'] = $this->language->get('pds_model');
$this->data['pds_brand'] = $this->language->get('pds_brand');
$this->data['pds_stock'] = $this->language->get('pds_stock');

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['button_cart'] = $this->language->get('button_cart');
 
		$this->data['text_quick'] = $this->language->get('text_quick');
		$this->data['text_price'] = $this->language->get('text_price');
		$this->data['button_wishlist'] = $this->language->get('button_wishlist');
		$this->data['button_compare'] = $this->language->get('button_compare');	
		$this->data['button_details'] = $this->language->get('button_details');
		$this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$this->data['text_category'] = $this->language->get('text_category');
		$this->data['text_model'] = $this->language->get('text_model');
		$this->data['text_availability'] = $this->language->get('text_availability');
		$this->data['text_instock'] = $this->language->get('text_instock');
		$this->data['text_outstock'] = $this->language->get('text_outstock');
		$this->data['reviews'] = $this->language->get('reviews');
		$this->data['text_price'] = $this->language->get('text_price');
		$this->data['text_product'] = $this->language->get('text_product');
		$this->data['text_sale'] = $this->language->get('text_sale');
				
		
		$this->load->model('catalog/product'); 
		
		$this->load->model('tool/image');
 
						$this->load->model('catalog/manufacturer');
						$this->language->load('product/product');
						$this->language->load('product/category');
						$this->load->model('catalog/review');
				

		$this->data['products'] = array();

		$products = explode(',', $this->config->get('featured_product'));		

		if (empty($setting['limit'])) {
			$setting['limit'] = 5;
		}
		
		$products = array_slice($products, 0, (int)$setting['limit']);
		
		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
 
				$review_total = $this->model_catalog_review->getTotalReviewsByProductId($product_info['product_id']);
				
			
			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $setting['image_width'], $setting['image_height']);
				} else {
					$image = false;
				}

 
				if ($product_info['image']) {
					$image1 = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
				} else {
					$image1 = false;
				}
				
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}
						
				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}
				
if ($product_info['quantity'] <= 0) {
                $rstock = $product_info['stock_status'];
                } elseif ($this->config->get('config_stock_display')) {
                $rstock = "Stock: " . $product_info['quantity'];
                } else {
                $rstock = $this->language->get('pds_instock');
                }
				if ($this->config->get('config_review_status')) {
					$rating = $product_info['rating'];
				} else {
					$rating = false;
				}
					
				$this->data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'   	 => $image,
					'name'    	 => $product_info['name'],
//produc display settings
					'sku'         => $product_info['sku'],
					'model'       => $product_info['model'],
					'brand'       => $product_info['manufacturer'],
					'location'    => $product_info['location'],
					'upc'         => $product_info['upc'],
					'stock'        => $rstock,
					'brand_url'   => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $product_info['manufacturer_id']),
					//end pds
					'price'   	 => $price,
					'special' 	 => $special,
					'rating'     => $rating,
					'reviews'    => sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']),
 'description' =>html_entity_decode($product_info['description']),
					'description1' =>strip_tags (html_entity_decode($product_info['description'])),
					'reviews'    => $review_total,
					'author'     => $product_info['manufacturer'],
					'manufacturers' =>$this->data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']),
					'model' => $product_info['model'],
					'text_availability' => $product_info['quantity'],
					'allow' => $product_info['minimum'],
					'thumb1'   	 => $image1,
				
					'href'    	 => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
				);
			}
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/featured.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/featured.tpl';
		} else {
			$this->template = 'default/template/module/featured.tpl';
		}

		$this->render();
	}
}
?>