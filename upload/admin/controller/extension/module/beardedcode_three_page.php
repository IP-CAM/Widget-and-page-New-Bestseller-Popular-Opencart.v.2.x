<?php

	class ControllerExtensionModuleBeardedCodeThreePage extends Controller
	{
		private $version      = 'Created by Timur T.R / version[ 1.0.0 ]';
		private $error        = array();
		private $form         = array();
		private $page_setting = array();

		public function __construct( $registry )
		{
			parent::__construct( $registry );

			if ( file_exists( DIR_CONFIG . 'beardedcode/three_page_form.json' ) ) {
				$this->form = json_decode( file_get_contents( DIR_CONFIG . 'beardedcode/three_page_form.json' ), true );
			}

			if ( !file_exists( DIR_CONFIG . 'beardedcode/three_page.json' ) ) {
				file_put_contents( DIR_CONFIG . 'beardedcode/three_page.json', [] );
			}

			$this->page_setting = json_decode( file_get_contents( DIR_CONFIG . 'beardedcode/three_page.json' ) );

			$this->load->language( 'extension/module/beardedcode_three_page' );
			$this->load->model( 'setting/setting' );
			$this->load->helper( 'beardedcode/form_create' );
		}

		public function index()
		{
			if ( ( $this->request->server['REQUEST_METHOD'] == 'POST' ) && $this->validate() ) {

				$settings_widget = array();
				$settings_page = array();

				foreach( $this->form as $key => $elements ) {
					foreach( $elements as $index => $element ) {
						if ( $key == 'widget' ) {
							$settings_widget[ 'beardedcode_three_page_' . $element['name'] ] = isset( $this->request->post[ $element['name'] ] ) ? $this->request->post[ $element['name'] ] : '';
							continue;
						}

						$settings_page[ $key ][ $element['name'] ] = $this->request->post[ $key ][ $element['name'] ];
					}
				}

				file_put_contents( DIR_CONFIG . 'beardedcode/three_page.json', json_encode( $settings_page, true ) );

				$this->model_setting_setting->editSetting( 'beardedcode_three_page', $settings_widget );

				$this->session->data['success'] = $this->language->get( 'text_success' );

				$this->response->redirect( $this->url->link( 'extension/module/beardedcode_three_page', 'token=' . $this->session->data['token'], true ) );
			}

			$data['nav_tabs'] = [];
			$data['tab_content'] = [];

			$i = 0;
			foreach( $this->form as $key => $elements ) {
				$parent_name = $key !== 'widget' ? $key : '';

				foreach( $elements as $element ) {

					if ( isset( $this->request->post[ $key ] [ $element['name'] ] ) ) {
						$element['value'] = $this->request->post[ $key ] [ $element['name'] ];
					} else if ( $key == 'widget' ) {
						$element['value'] = $this->config->has( 'beardedcode_three_page_' . $element['name'] ) ? $this->config->get( 'beardedcode_three_page_' . $element['name'] ) : $element['value'];
					} else if ( !empty( $this->page_setting->{$key} ) ) {
						$element['value'] = $this->page_setting->{$key}->{$element['name']};
					}

					if ( !isset( $data['nav_tabs'][ $i ] ) ) {
						$data['nav_tabs'][ $i ] = $this->language->get( $key );
						$data['tab_content'][ $i ] = '';
					}

					$data['tab_content'][ $i ] .= form_help( $element, $this->language->get( $element['name'] ), $parent_name );
				}
				$i++;
			}

			$this->other_variable( $data );

			$this->response->setOutput( $this->load->view( 'extension/module/beardedcode_three_page', $data ) );
		}

		protected function other_variable( &$data )
		{
			$this->document->addStyle( 'view/javascript/beardedcode/codemirror/lib/codemirror.css' );
			$this->document->addStyle( 'view/javascript/beardedcode/codemirror/theme/monokai.css' );

			$this->document->addScript( 'view/javascript/beardedcode/codemirror/lib/codemirror.js' );
			$this->document->addScript( 'view/javascript/beardedcode/codemirror/lib/autorefresh.js' );
			$this->document->addScript( 'view/javascript/beardedcode/codemirror/mode/htmlmixed/htmlmixed.js' );
			$this->document->addScript( 'view/javascript/beardedcode/codemirror/mode/xml/xml.js' );
			$this->document->addScript( 'view/javascript/beardedcode/codemirror/mode/javascript/javascript.js' );
			$this->document->addScript( 'view/javascript/beardedcode/codemirror/mode/css/css.js' );
			$this->document->addScript( 'view/javascript/beardedcode/codemirror/opencart.js' );

			$this->document->setTitle( $this->language->get( 'heading_title' ) );

			if ( isset( $this->error['warning'] ) ) {
				$data['error_warning'] = $this->error['warning'];
			} else {
				$data['error_warning'] = '';
			}

			if ( isset( $this->session->data['success'] ) ) {
				$data['success'] = $this->session->data['success'];
				unset( $this->session->data['success'] );
			} else {
				$data['success'] = '';
			}

			$data['breadcrumbs'] = array(
				array(
					'text' => $this->language->get( 'text_home' ),
					'href' => $this->url->link( 'common/dashboard', 'token=' . $this->session->data['token'], true ),
				),
				array(
					'text' => $this->language->get( 'text_extension' ),
					'href' => $this->url->link( 'extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true ),
				),
				array(
					'text' => $this->language->get( 'heading_title' ),
					'href' => $this->url->link( 'extension/module/beardedcode_three_page', 'token=' . $this->session->data['token'], true ),
				),
			);

			$data['heading_title'] = $this->language->get( 'heading_title' );

			$data['text_edit'] = $this->language->get( 'text_edit' );

			$data['action'] = $this->url->link( 'extension/module/beardedcode_three_page', 'token=' . $this->session->data['token'], true );
			$data['cancel'] = $this->url->link( 'extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true );

			$data['button_save'] = $this->language->get( 'button_save' );
			$data['button_cancel'] = $this->language->get( 'button_cancel' );

			$data['version'] = $this->version;

			$data['header'] = $this->load->controller( 'common/header' );
			$data['column_left'] = $this->load->controller( 'common/column_left' );
			$data['footer'] = $this->load->controller( 'common/footer' );
		}

		protected function validate()
		{
			if ( !$this->user->hasPermission( 'modify', 'extension/module/beardedcode_three_page' ) ) {
				$this->error['warning'] = $this->language->get( 'error_permission' );
			}

			return !$this->error;
		}
		/*
				public function install()
				{
					if ( $this->validate() ) {
						$this->actionEvents('install');
					}
				}

				public function uninstall()
				{
					if ( $this->validate() ) {
						$this->actionEvents('uninstall');
					}
				}

				protected function actionEvents($action) {
					$events = array(//'voucher', 'catalog/model/checkout/order/addOrderHistory/after', 'extension/total/voucher/send'
						'beardedcode_three_page_special' => array(
							'trigger' => 'catalog/model/catalog/product/getProductSpecials/before',
							'action'  => 'extension/module/beardedcode_three_page/getProducts',
						),
						'beardedcode_three_page_total' => array(
							'trigger' => 'catalog/model/catalog/product/getTotalProductSpecials/before',
							'action'  => 'extension/module/beardedcode_three_page/getProducts',
						),
						'beardedcode_three_page_render' => array(//view/product/special/before
							'trigger' => 'catalog/view/product/special/before',
							'action'  => 'extension/module/beardedcode_three_page/page',
						),
					);

					$this->load->model( 'extension/event' );
					foreach( $events as $code => $value ) {
						if ($action == 'install') {
							$this->model_extension_event->deleteEvent($code);
							$this->model_extension_event->addEvent( $code, $value['trigger'], $value['action'], 1 );
						} else {
							$this->model_extension_event->deleteEvent($code);
						}
					}
				}
		*/
	}