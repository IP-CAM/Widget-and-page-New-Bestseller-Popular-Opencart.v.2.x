<?php

	class ModelExtensionModuleBeardedCodeThreePage extends Model
	{
		private $sort;

		public function __construct( $registry )
		{
			parent::__construct( $registry );
			$this->load->model( 'catalog/product' );
		}

		public function getBestSellerProducts( $data )
		{
			$product_data = array();

			$query = $this->db->query( "SELECT op.product_id, SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get( 'config_store_id' ) . "' GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int)$data['start'] . "," . (int)$data['limit'] );

			foreach( $query->rows as $result ) {
				$product_data[ $result['product_id'] ] = $this->model_catalog_product->getProduct( $result['product_id'] );
			}

			return $product_data;
		}

		public function getTotalBestSellerProducts()
		{
			$query = $this->db->query( "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get( 'config_store_id' ) . "'" );

			return $query->row['total'];
		}

		public function getLatestProducts( $data )
		{
			$product_data = array();

			$query = $this->db->query( "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get( 'config_store_id' ) . "' ORDER BY p.date_added DESC LIMIT " . (int)$data['start'] . "," . (int)$data['limit'] );

			foreach( $query->rows as $result ) {
				$product_data[ $result['product_id'] ] = $this->model_catalog_product->getProduct( $result['product_id'] );
			}

			return $product_data;
		}

		public function getTotalLatestProducts()
		{
			$query = $this->db->query( "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get( 'config_store_id' ) . "' ORDER BY p.date_added" );

			return $query->row['total'];
		}

		public function getPopularProducts( $data )
		{
			$product_data = array();

			$query = $this->db->query( "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get( 'config_store_id' ) . "' ORDER BY p.viewed DESC, p.date_added DESC LIMIT "  . (int)$data['start'] . "," . (int)$data['limit'] );

			foreach( $query->rows as $result ) {
				$product_data[ $result['product_id'] ] = $this->model_catalog_product->getProduct( $result['product_id'] );
			}

			return $product_data;
		}

		public function getTotalPopularProducts( )
		{
			$query = $this->db->query( "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get( 'config_store_id' ) . "' ORDER BY p.viewed" );

			return $query->row['total'];
		}
	}
