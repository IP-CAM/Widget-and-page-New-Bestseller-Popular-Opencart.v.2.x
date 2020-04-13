<?php

	class ControllerExtensionModuleBeardedCodeThreePage extends Controller
	{
		private static $page_setting;

		public function __construct( $registry )
		{
			parent::__construct( $registry );

			$this->load->language( 'extension/module/beardedcode_three_page' );
			$this->load->model( 'catalog/product' );
			$this->load->model( 'extension/module/beardedcode_three_page' );
			$this->load->model( 'tool/image' );
		}

		public function index()
		{
			$limit = $this->config->get( 'beardedcode_three_page_limit' ) ? $this->config->get( 'beardedcode_three_page_limit' ) : 6;
			$width = $this->config->get( 'beardedcode_three_page_width' ) ? $this->config->get( 'beardedcode_three_page_width' ) : 150;
			$height = $this->config->get( 'beardedcode_three_page_height' ) ? $this->config->get( 'beardedcode_three_page_height' ) : 150;
			$data['timer'] = (int)$this->config->get( 'beardedcode_three_page_timer' ) ? (int)$this->config->get( 'beardedcode_three_page_timer' ) * 1000 : 0;

			$data['ajax'] = (int)$this->config->get( 'beardedcode_three_page_ajax' );

			if ( (int)$this->config->get( 'beardedcode_three_page_ajax' ) === 0 || isset( $this->request->get['ajax'] ) ) {

				$data['widget'] = array(
					[
						'title'        => $this->config->get( 'beardedcode_three_page_bestseller_title' ),
						'products'     => $this->products( [ 'results' => $this->model_catalog_product->getBestSellerProducts( $limit ), 'width' => $width, 'height' => $height ] ),
						'link'         => $this->url->link( 'extension/module/beardedcode_three_page/bestseller', '', true ),
						'button_title' => $this->config->get( 'beardedcode_three_page_bestseller_button' ) ? $this->config->get( 'beardedcode_three_page_bestseller_button' ) : '',
					],
					[
						'title'        => $this->config->get( 'beardedcode_three_page_new_arrival_title' ),
						'products'     => $this->products( [ 'results' => $this->model_catalog_product->getLatestProducts( $limit ), 'width' => $width, 'height' => $height ] ),
						'link'         => $this->url->link( 'extension/module/beardedcode_three_page/new_arrival', '', true ),
						'button_title' => $this->config->get( 'beardedcode_three_page_new_arrival_button' ) ? $this->config->get( 'beardedcode_three_page_new_arrival_button' ) : '',
					],
					[
						'title'        => $this->config->get( 'beardedcode_three_page_top_rated_title' ),
						'products'     => $this->products( [ 'results' => $this->model_catalog_product->getPopularProducts( $limit ), 'width' => $width, 'height' => $height ] ),
						'link'         => $this->url->link( 'extension/module/beardedcode_three_page/top_rated', '', true ),
						'button_title' => $this->config->get( 'beardedcode_three_page_top_rated_button' ) ? $this->config->get( 'beardedcode_three_page_top_rated_button' ) : '',
					],
				);
			}

			if ( $data['ajax'] && isset( $this->request->get['ajax'] ) ) {
				$data['ajax'] = 0;
				return $this->response->setOutput( $this->load->view( 'extension/module/beardedcode/widget', $data ) );
			}

			if ($this->config->get( 'beardedcode_three_page_limit' ) && (int)$this->config->get( 'beardedcode_three_page_limit' ) === 1) {
				$this->document->addStyle( 'catalog/view/javascript/beardedcode_three_page_widget.css' );
			}

			return $this->load->view( 'extension/module/beardedcode/widget', $data );
		}

		public function bestseller()
		{
			$page_setting = json_decode( file_get_contents( DIR_CONFIG . 'beardedcode/three_page.json' ) );
			$this->page_setting = $page_setting->page_bestseller;
			$this->page_setting->product = 'BestSeller';
			$this->page_setting->route = 'extension/module/beardedcode_three_page/bestseller';
			$this->page();
		}

		public function new_arrival()
		{
			$page_setting = json_decode( file_get_contents( DIR_CONFIG . 'beardedcode/three_page.json' ) );
			$this->page_setting = $page_setting->page_new_arrival;
			$this->page_setting->product = 'Latest';
			$this->page_setting->route = 'extension/module/beardedcode_three_page/new_arrival';
			$this->page();
		}

		public function top_rated()
		{
			$page_setting = json_decode( file_get_contents( DIR_CONFIG . 'beardedcode/three_page.json' ) );
			$this->page_setting = $page_setting->page_top_rated;
			$this->page_setting->product = 'Popular';
			$this->page_setting->route = 'extension/module/beardedcode_three_page/top_rated';
			$this->page();
		}

		protected function page()
		{

			if ( $this->page_setting && (int)$this->page_setting->status === 1 ) {

				$this->document->setTitle( $this->page_setting->meta_title );
				$this->document->setDescription( $this->page_setting->meta_description );

				$data['description'] = $this->page_setting->description ? html_entity_decode( $this->page_setting->description, ENT_QUOTES, 'UTF-8' ) : '';

				$doc_script_style = [];

				if ( !empty( $this->page_setting->scripts ) && $style = explode( ',', $this->page_setting->style ) ) {
					array_push( $doc_script_style, [ 'addStyle' => $style ] );
				}

				if ( !empty( $this->page_setting->scripts ) && $script = explode( ',', $this->page_setting->scripts ) ) {
					array_push( $doc_script_style, [ 'addScript' => $script ] );
				}

				foreach( $doc_script_style as $add => $value ) {
					foreach( $value as $init ) {
						$this->document->{$add}( $init );
					}
				}

				if ( isset( $this->request->get['sort'] ) ) {
					$sort = $this->request->get['sort'];
				} else {
					$sort = 'p.sort_order';
				}

				if ( isset( $this->request->get['order'] ) ) {
					$order = $this->request->get['order'];
				} else {
					$order = 'ASC';
				}

				if ( isset( $this->request->get['page'] ) ) {
					$page = $this->request->get['page'];
				} else {
					$page = 1;
				}

				if ( isset( $this->request->get['limit'] ) ) {
					$limit = (int)$this->request->get['limit'];
				} else {
					$limit = $this->config->get( $this->config->get( 'config_theme' ) . '_product_limit' );
				}

				$url = '';

				if ( isset( $this->request->get['sort'] ) ) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if ( isset( $this->request->get['order'] ) ) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if ( isset( $this->request->get['page'] ) ) {
					$url .= '&page=' . $this->request->get['page'];
				}

				if ( isset( $this->request->get['limit'] ) ) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['breadcrumbs'] = array(
					array(
						'text' => $this->language->get( 'text_home' ),
						'href' => $this->url->link( 'common/home' ),
					),
					array(
						'text' => $this->language->get( 'heading_title' ),
						'href' => $this->url->link( $this->page_setting->route, $url ),
					),
				);

				$data['heading_title'] = $this->page_setting->h1 ? $this->page_setting->h1 : $this->page_setting->name;

				$data['text_empty'] = $this->language->get( 'text_empty' );
				$data['text_quantity'] = $this->language->get( 'text_quantity' );
				$data['text_manufacturer'] = $this->language->get( 'text_manufacturer' );
				$data['text_model'] = $this->language->get( 'text_model' );
				$data['text_price'] = $this->language->get( 'text_price' );
				$data['text_tax'] = $this->language->get( 'text_tax' );
				$data['text_points'] = $this->language->get( 'text_points' );
				$data['text_compare'] = sprintf( $this->language->get( 'text_compare' ), ( isset( $this->session->data['compare'] ) ? count( $this->session->data['compare'] ) : 0 ) );
				$data['text_sort'] = $this->language->get( 'text_sort' );
				$data['text_limit'] = $this->language->get( 'text_limit' );

				$data['button_cart'] = $this->language->get( 'button_cart' );
				$data['button_wishlist'] = $this->language->get( 'button_wishlist' );
				$data['button_compare'] = $this->language->get( 'button_compare' );
				$data['button_list'] = $this->language->get( 'button_list' );
				$data['button_grid'] = $this->language->get( 'button_grid' );
				$data['button_continue'] = $this->language->get( 'button_continue' );

				$data['compare'] = $this->url->link( 'product/compare' );

				if ( $limit > 200 ) {
					$limit = 200;
				}

				$filter_data = array(
					'sort'  => $sort,
					'order' => $order,
					'start' => ( $page - 1 ) * $limit,
					'limit' => $limit,
				);

				$product_total = $this->model_extension_module_beardedcode_three_page->{'getTotal' . $this->page_setting->product . 'Products'}();

				$results = $this->model_extension_module_beardedcode_three_page->{'get' . $this->page_setting->product . 'Products'}( $filter_data );

				$data['products'] = $this->products( [
					'results' => $results,
					'width'   => $this->config->get( $this->config->get( 'config_theme' ) . '_image_product_width' ),
					'height'  => $this->config->get( $this->config->get( 'config_theme' ) . '_image_product_height' ),
					'url'     => $url,
				] );

				$url = '';

				if ( isset( $this->request->get['limit'] ) ) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['sorts'] = array();

				$data['sorts'][] = array(
					'text'  => $this->language->get( 'text_default' ),
					'value' => 'p.sort_order-ASC',
					'href'  => $this->url->link( $this->page_setting->route, 'sort=p.sort_order&order=ASC' . $url ),
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get( 'text_name_asc' ),
					'value' => 'pd.name-ASC',
					'href'  => $this->url->link( $this->page_setting->route, 'sort=pd.name&order=ASC' . $url ),
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get( 'text_name_desc' ),
					'value' => 'pd.name-DESC',
					'href'  => $this->url->link( $this->page_setting->route, 'sort=pd.name&order=DESC' . $url ),
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get( 'text_price_asc' ),
					'value' => 'ps.price-ASC',
					'href'  => $this->url->link( $this->page_setting->route, 'sort=ps.price&order=ASC' . $url ),
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get( 'text_price_desc' ),
					'value' => 'ps.price-DESC',
					'href'  => $this->url->link( $this->page_setting->route, 'sort=ps.price&order=DESC' . $url ),
				);

				if ( $this->config->get( 'config_review_status' ) ) {
					$data['sorts'][] = array(
						'text'  => $this->language->get( 'text_rating_desc' ),
						'value' => 'rating-DESC',
						'href'  => $this->url->link( $this->page_setting->route, 'sort=rating&order=DESC' . $url ),
					);

					$data['sorts'][] = array(
						'text'  => $this->language->get( 'text_rating_asc' ),
						'value' => 'rating-ASC',
						'href'  => $this->url->link( $this->page_setting->route, 'sort=rating&order=ASC' . $url ),
					);
				}

				$data['sorts'][] = array(
					'text'  => $this->language->get( 'text_model_asc' ),
					'value' => 'p.model-ASC',
					'href'  => $this->url->link( $this->page_setting->route, 'sort=p.model&order=ASC' . $url ),
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get( 'text_model_desc' ),
					'value' => 'p.model-DESC',
					'href'  => $this->url->link( $this->page_setting->route, 'sort=p.model&order=DESC' . $url ),
				);

				$url = '';

				if ( isset( $this->request->get['sort'] ) ) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if ( isset( $this->request->get['order'] ) ) {
					$url .= '&order=' . $this->request->get['order'];
				}

				$data['limits'] = array();

				$limits = array_unique( array( $this->config->get( $this->config->get( 'config_theme' ) . '_product_limit' ), 25, 50, 75, 100 ) );

				sort( $limits );

				foreach( $limits as $value ) {
					$data['limits'][] = array(
						'text'  => $value,
						'value' => $value,
						'href'  => $this->url->link( $this->page_setting->route, $url . '&limit=' . $value ),
					);
				}

				$url = '';

				if ( isset( $this->request->get['sort'] ) ) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if ( isset( $this->request->get['order'] ) ) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if ( isset( $this->request->get['limit'] ) ) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$pagination = new Pagination();
				$pagination->total = $product_total;
				$pagination->page = $page;
				$pagination->limit = $limit;
				$pagination->url = $this->url->link( $this->page_setting->route, $url . '&page={page}' );

				$data['pagination'] = $pagination->render();

				$data['results'] = sprintf( $this->language->get( 'text_pagination' ), ( $product_total ) ? ( ( $page - 1 ) * $limit ) + 1 : 0, ( ( ( $page - 1 ) * $limit ) > ( $product_total - $limit ) ) ? $product_total : ( ( ( $page - 1 ) * $limit ) + $limit ), $product_total, ceil( $product_total / $limit ) );

				// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
				if ( $page == 1 ) {
					$this->document->addLink( $this->url->link( $this->page_setting->route, '', true ), 'canonical' );
				} else if ( $page == 2 ) {
					$this->document->addLink( $this->url->link( $this->page_setting->route, '', true ), 'prev' );
				} else {
					$this->document->addLink( $this->url->link( $this->page_setting->route, 'page=' . ( $page - 1 ), true ), 'prev' );
				}

				if ( $limit && ceil( $product_total / $limit ) > $page ) {
					$this->document->addLink( $this->url->link( $this->page_setting->route, 'page=' . ( $page + 1 ), true ), 'next' );
				}

				$data['sort'] = $sort;
				$data['order'] = $order;
				$data['limit'] = $limit;

				$data['continue'] = $this->url->link( 'common/home' );

				$data['column_left'] = $this->load->controller( 'common/column_left' );
				$data['column_right'] = $this->load->controller( 'common/column_right' );
				$data['content_top'] = $this->load->controller( 'common/content_top' );
				$data['content_bottom'] = $this->load->controller( 'common/content_bottom' );
				$data['footer'] = $this->load->controller( 'common/footer' );
				$data['header'] = $this->load->controller( 'common/header' );

				$this->response->setOutput( $this->load->view( 'extension/module/beardedcode/page', $data ) );
			}
		}

		protected function products( $setting )
		{
			$products = [];
			foreach( $setting['results'] as $result ) {
				if ( $result['image'] ) {
					$image = $this->model_tool_image->resize( $result['image'], $setting['width'], $setting['height'] );
				} else {
					$image = $this->model_tool_image->resize( 'placeholder.png', $setting['width'], $setting['height'] );
				}

				if ( $this->customer->isLogged() || !$this->config->get( 'config_customer_price' ) ) {
					$price = $this->currency->format( $this->tax->calculate( $result['price'], $result['tax_class_id'], $this->config->get( 'config_tax' ) ), $this->session->data['currency'] );
				} else {
					$price = false;
				}

				if ( (float)$result['special'] ) {
					$special = $this->currency->format( $this->tax->calculate( $result['special'], $result['tax_class_id'], $this->config->get( 'config_tax' ) ), $this->session->data['currency'] );
				} else {
					$special = false;
				}

				if ( $this->config->get( 'config_tax' ) ) {
					$tax = $this->currency->format( (float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency'] );
				} else {
					$tax = false;
				}

				if ( $this->config->get( 'config_review_status' ) ) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$products[] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr( strip_tags( html_entity_decode( $result['description'], ENT_QUOTES, 'UTF-8' ) ), 0, $this->config->get( $this->config->get( 'config_theme' ) . '_product_description_length' ) ) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => ( $result['minimum'] > 0 ) ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link( 'product/product', 'product_id=' . $result['product_id'] . ( isset( $setting['url'] ) ? $setting['url'] : '' ) ),
				);
			}
			return $products;
		}
	}